"""Validated purchase-order delivery assessment for textile operations.

This module is intentionally stateless: it calculates operational status from
source facts but does not store supplier records or infer commitments.
"""
from datetime import date
from decimal import Decimal, InvalidOperation

_QUANTUM = Decimal("0.001")
_MAX_QUANTITY = Decimal("1000000000000")
MAX_BATCH_ITEMS = 50


def _text(payload, field, *, required=False, limit=120):
    value = payload.get(field, "")
    if value is None and not required:
        return ""
    if not isinstance(value, str):
        raise ValueError(f"{field} must be text")
    value = value.strip()
    if required and not value:
        raise ValueError(f"{field} is required")
    if len(value) > limit:
        raise ValueError(f"{field} must be {limit} characters or fewer")
    return value


def _quantity(payload, field, *, required=False):
    value = payload.get(field)
    if value is None and not required:
        value = 0
    if isinstance(value, bool) or not isinstance(value, (int, float)):
        raise ValueError(f"{field} must be a number")
    try:
        amount = Decimal(str(value))
    except (InvalidOperation, ValueError):
        raise ValueError(f"{field} must be a valid number") from None
    if not amount.is_finite() or amount < 0 or amount > _MAX_QUANTITY:
        raise ValueError(f"{field} must be between 0 and {_MAX_QUANTITY}")
    rounded = amount.quantize(_QUANTUM)
    if rounded != amount:
        raise ValueError(f"{field} supports at most 3 decimal places")
    return rounded


def assess_purchase_order(payload, *, today=None):
    """Validate one PO snapshot and calculate balance, status and overdue days.

    Required JSON fields: po_number, supplier, order_qty, received_qty,
    committed_delivery_date (YYYY-MM-DD). Optional: buyer, style, fabric.
    """
    if not isinstance(payload, dict):
        raise ValueError("request body must be a JSON object")

    po_number = _text(payload, "po_number", required=True, limit=64)
    supplier = _text(payload, "supplier", required=True, limit=120)
    buyer = _text(payload, "buyer", limit=120)
    style = _text(payload, "style", limit=80)
    fabric = _text(payload, "fabric", limit=120)

    order_qty = _quantity(payload, "order_qty", required=True)
    received_qty = _quantity(payload, "received_qty")
    if order_qty <= 0:
        raise ValueError("order_qty must be greater than zero")
    if received_qty > order_qty:
        raise ValueError("received_qty cannot exceed order_qty")

    raw_due_date = payload.get("committed_delivery_date")
    if not isinstance(raw_due_date, str):
        raise ValueError("committed_delivery_date must use YYYY-MM-DD")
    try:
        due_date = date.fromisoformat(raw_due_date)
    except ValueError:
        raise ValueError("committed_delivery_date must use YYYY-MM-DD") from None
    if due_date.isoformat() != raw_due_date:
        raise ValueError("committed_delivery_date must use YYYY-MM-DD")

    as_of = today or date.today()
    if not isinstance(as_of, date):
        raise ValueError("today must be a date")

    balance = (order_qty - received_qty).quantize(_QUANTUM)
    overdue_days = max(0, (as_of - due_date).days) if balance > 0 else 0

    if balance == 0:
        status = "complete"
    elif overdue_days > 0:
        status = "overdue"
    elif due_date == as_of:
        status = "due_today"
    elif received_qty > 0:
        status = "in_progress"
    else:
        status = "pending"

    return {
        "po_number": po_number,
        "supplier": supplier,
        "buyer": buyer,
        "style": style,
        "fabric": fabric,
        "order_qty": float(order_qty),
        "received_qty": float(received_qty),
        "balance_qty": float(balance),
        "committed_delivery_date": due_date.isoformat(),
        "overdue_days": overdue_days,
        "status": status,
    }


def assess_purchase_order_batch(payload, *, today=None):
    """Assess 1–50 purchase orders, returning per-row validation results.

    Invalid rows do not discard valid rows. The function remains stateless.
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

    summary = {
        "complete": 0,
        "overdue": 0,
        "due_today": 0,
        "in_progress": 0,
        "pending": 0,
        "invalid": 0,
    }
    results = []

    for index, order in enumerate(orders):
        try:
            assessment = assess_purchase_order(order, today=today)
        except ValueError as error:
            summary["invalid"] += 1
            results.append({
                "index": index,
                "status": "invalid",
                "error": str(error),
            })
            continue

        status = assessment["status"]
        summary[status] += 1
        results.append({
            "index": index,
            "status": status,
            "assessment": assessment,
        })

    return {
        "total": len(orders),
        "valid": len(orders) - summary["invalid"],
        "invalid": summary["invalid"],
        "summary": summary,
        "results": results,
    }
