"""Supplier delivery performance analytics for textile purchase orders.

The report is computed only from caller-supplied PO snapshots. It does not
persist records or infer missing delivery dates.
"""
from datetime import date
from decimal import Decimal

from .purchase_orders import MAX_BATCH_ITEMS, assess_purchase_order


def _parse_actual_delivery_date(order, index):
    raw = order.get("actual_delivery_date")
    if raw is None or raw == "":
        return None
    if not isinstance(raw, str):
        raise ValueError(
            f"purchase_orders[{index}].actual_delivery_date must use YYYY-MM-DD"
        )
    try:
        parsed = date.fromisoformat(raw)
    except ValueError:
        raise ValueError(
            f"purchase_orders[{index}].actual_delivery_date must use YYYY-MM-DD"
        ) from None
    if parsed.isoformat() != raw:
        raise ValueError(
            f"purchase_orders[{index}].actual_delivery_date must use YYYY-MM-DD"
        )
    return parsed


def summarize_supplier_performance(payload, *, today=None):
    """Summarize supplier delivery outcomes from 1–50 PO snapshots.

    Each row uses the fields required by assess_purchase_order and may
    include actual_delivery_date. A completed PO without an actual date is
    counted as unverified and excluded from the on-time-rate denominator.
    """
    if not isinstance(payload, dict):
        raise ValueError("request body must be a JSON object")

    orders = payload.get("purchase_orders")
    if not isinstance(orders, list):
        raise ValueError("purchase_orders must be a list")
    if not 1 <= len(orders) <= MAX_BATCH_ITEMS:
        raise ValueError(
            f"purchase_orders must contain between 1 and {MAX_BATCH_ITEMS} items"
        )

    as_of = today or date.today()
    if not isinstance(as_of, date):
        raise ValueError("today must be a date")

    suppliers = {}
    seen_po_numbers = set()

    for index, order in enumerate(orders):
        try:
            assessment = assess_purchase_order(order, today=as_of)
        except ValueError as error:
            raise ValueError(f"purchase_orders[{index}]: {error}") from None

        po_key = assessment["po_number"].casefold()
        if po_key in seen_po_numbers:
            raise ValueError(
                f"purchase_orders[{index}]: duplicate po_number "
                f"{assessment['po_number']!r}"
            )
        seen_po_numbers.add(po_key)

        actual_date = _parse_actual_delivery_date(order, index)
        balance = Decimal(str(assessment["balance_qty"]))
        if actual_date is not None:
            if actual_date > as_of:
                raise ValueError(
                    f"purchase_orders[{index}].actual_delivery_date cannot be in the future"
                )
            if balance > 0:
                raise ValueError(
                    f"purchase_orders[{index}].actual_delivery_date requires a fully received PO"
                )

        supplier_name = assessment["supplier"]
        supplier_key = supplier_name.casefold()
        if supplier_key not in suppliers:
            suppliers[supplier_key] = {
                "supplier": supplier_name,
                "total_pos": 0,
                "completed_pos": 0,
                "verified_deliveries": 0,
                "unverified_completed_pos": 0,
                "on_time_deliveries": 0,
                "late_deliveries": 0,
                "open_pos": 0,
                "overdue_open_pos": 0,
                "_order_qty": Decimal("0"),
                "_received_qty": Decimal("0"),
                "_balance_qty": Decimal("0"),
                "_late_days": 0,
            }

        row = suppliers[supplier_key]
        row["total_pos"] += 1
        row["_order_qty"] += Decimal(str(assessment["order_qty"]))
        row["_received_qty"] += Decimal(str(assessment["received_qty"]))
        row["_balance_qty"] += balance

        if balance == 0:
            row["completed_pos"] += 1
            if actual_date is None:
                row["unverified_completed_pos"] += 1
            else:
                row["verified_deliveries"] += 1
                due_date = date.fromisoformat(assessment["committed_delivery_date"])
                delay_days = max(0, (actual_date - due_date).days)
                if delay_days == 0:
                    row["on_time_deliveries"] += 1
                else:
                    row["late_deliveries"] += 1
                    row["_late_days"] += delay_days
        else:
            row["open_pos"] += 1
            due_date = date.fromisoformat(assessment["committed_delivery_date"])
            if due_date < as_of:
                row["overdue_open_pos"] += 1

    summaries = []
    for key in sorted(suppliers):
        row = suppliers[key]
        verified = row["verified_deliveries"]
        late = row["late_deliveries"]
        total = row["total_pos"]
        summaries.append({
            "supplier": row["supplier"],
            "total_pos": total,
            "completed_pos": row["completed_pos"],
            "completion_rate_pct": round(row["completed_pos"] / total * 100, 2),
            "verified_deliveries": verified,
            "unverified_completed_pos": row["unverified_completed_pos"],
            "on_time_deliveries": row["on_time_deliveries"],
            "late_deliveries": late,
            "on_time_delivery_rate_pct": (
                round(row["on_time_deliveries"] / verified * 100, 2)
                if verified else None
            ),
            "average_late_days": (
                round(row["_late_days"] / late, 2) if late else 0.0
            ),
            "open_pos": row["open_pos"],
            "overdue_open_pos": row["overdue_open_pos"],
            "total_order_qty": float(row["_order_qty"]),
            "total_received_qty": float(row["_received_qty"]),
            "balance_qty": float(row["_balance_qty"]),
        })

    return {
        "as_of_date": as_of.isoformat(),
        "total_purchase_orders": len(orders),
        "total_suppliers": len(summaries),
        "suppliers": summaries,
    }
