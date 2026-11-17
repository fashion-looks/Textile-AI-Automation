"""Validated fabric requirement and costing calculations for garment orders."""
from decimal import Decimal, InvalidOperation, ROUND_HALF_UP, ROUND_UP

CENT = Decimal("0.01")
AVERAGE_PRECISION = Decimal("0.0001")
MAX_ORDER_QTY = 1_000_000
MAX_CONSUMPTION = Decimal("100")
MAX_WASTAGE_PERCENT = Decimal("100")
MAX_RATE = Decimal("1000000000")
MAX_SIZE_ROWS = 20


def _required_text(payload, key, limit):
    value = payload.get(key)
    if not isinstance(value, str):
        raise ValueError(f"{key} must be text")
    value = value.strip()
    if not value:
        raise ValueError(f"{key} is required")
    if len(value) > limit:
        raise ValueError(f"{key} must be {limit} characters or fewer")
    return value


def _decimal_number(payload, key):
    value = payload.get(key)
    if isinstance(value, bool) or not isinstance(value, (int, float)):
        raise ValueError(f"{key} must be a JSON number")
    try:
        result = Decimal(str(value))
    except (InvalidOperation, ValueError):
        raise ValueError(f"{key} must be a valid number") from None
    if not result.is_finite():
        raise ValueError(f"{key} must be finite")
    return result


def _size_mix_base(payload, order_qty):
    """Validate a size mix and return exact base metres plus display rows."""
    if "consumption_m_per_piece" in payload:
        raise ValueError(
            "provide either consumption_m_per_piece or size_breakdown, not both"
        )

    rows = payload.get("size_breakdown")
    if not isinstance(rows, list) or not 1 <= len(rows) <= MAX_SIZE_ROWS:
        raise ValueError(
            f"size_breakdown must contain between 1 and {MAX_SIZE_ROWS} rows"
        )

    seen_sizes = set()
    total_pieces = 0
    total_base = Decimal("0")
    normalized_rows = []

    for index, row in enumerate(rows):
        if not isinstance(row, dict):
            raise ValueError(f"size_breakdown[{index}] must be an object")

        size = _required_text(row, "size", 24)
        normalized_size = size.casefold()
        if normalized_size in seen_sizes:
            raise ValueError("size_breakdown must not contain duplicate sizes")
        seen_sizes.add(normalized_size)

        pieces = row.get("order_qty_pcs")
        if isinstance(pieces, bool) or not isinstance(pieces, int):
            raise ValueError(f"size_breakdown[{index}].order_qty_pcs must be an integer")
        if not 1 <= pieces <= order_qty:
            raise ValueError(
                f"size_breakdown[{index}].order_qty_pcs must be between 1 and order_qty"
            )

        consumption = _decimal_number(row, "consumption_m_per_piece")
        if not Decimal("0") < consumption <= MAX_CONSUMPTION:
            raise ValueError(
                f"size_breakdown[{index}].consumption_m_per_piece must be greater than 0 and at most 100"
            )

        line_base = Decimal(pieces) * consumption
        total_pieces += pieces
        total_base += line_base
        normalized_rows.append({
            "size": size,
            "order_qty_pcs": pieces,
            "consumption_m_per_piece": str(consumption),
            "base_meters_exact": format(line_base, "f"),
        })

    if total_pieces != order_qty:
        raise ValueError("size_breakdown quantities must sum to order_qty")

    average_consumption = (total_base / Decimal(order_qty)).quantize(
        AVERAGE_PRECISION, rounding=ROUND_HALF_UP
    )
    return total_base, average_consumption, normalized_rows


def calculate_fabric_plan(payload):
    """Return rounded procurement metres and fabric-only cost for one style.

    Callers may supply either one average consumption value or a validated
    size_breakdown. Size-mix totals are calculated from unrounded line values.
    """
    if not isinstance(payload, dict):
        raise ValueError("request body must be a JSON object")

    style_code = _required_text(payload, "style_code", 80)
    fabric_name = _required_text(payload, "fabric_name", 120)

    order_qty = payload.get("order_qty")
    if isinstance(order_qty, bool) or not isinstance(order_qty, int):
        raise ValueError("order_qty must be an integer")
    if not 1 <= order_qty <= MAX_ORDER_QTY:
        raise ValueError(f"order_qty must be between 1 and {MAX_ORDER_QTY}")

    if "size_breakdown" in payload:
        base_raw, consumption, size_rows = _size_mix_base(payload, order_qty)
        consumption_basis = "size_breakdown"
    else:
        consumption = _decimal_number(payload, "consumption_m_per_piece")
        if not Decimal("0") < consumption <= MAX_CONSUMPTION:
            raise ValueError(
                "consumption_m_per_piece must be greater than 0 and at most 100"
            )
        base_raw = Decimal(order_qty) * consumption
        size_rows = None
        consumption_basis = "single_rate"

    wastage = _decimal_number(payload, "wastage_percent")
    rate = _decimal_number(payload, "rate_per_meter")

    if not Decimal("0") <= wastage <= MAX_WASTAGE_PERCENT:
        raise ValueError("wastage_percent must be between 0 and 100")
    if not Decimal("0") <= rate <= MAX_RATE:
        raise ValueError("rate_per_meter must be between 0 and 1000000000")

    base_meters = base_raw.quantize(CENT, rounding=ROUND_HALF_UP)
    planned_meters = (
        base_raw * (Decimal("1") + wastage / Decimal("100"))
    ).quantize(CENT, rounding=ROUND_UP)
    wastage_meters = planned_meters - base_meters
    total_cost = (planned_meters * rate).quantize(CENT, rounding=ROUND_HALF_UP)
    cost_per_piece = (total_cost / Decimal(order_qty)).quantize(
        CENT, rounding=ROUND_HALF_UP
    )

    result = {
        "style_code": style_code,
        "fabric_name": fabric_name,
        "order_qty_pcs": order_qty,
        "consumption_m_per_piece": str(consumption),
        "consumption_basis": consumption_basis,
        "wastage_percent": str(wastage),
        "base_meters": f"{base_meters:.2f}",
        "wastage_meters": f"{wastage_meters:.2f}",
        "planned_meters": f"{planned_meters:.2f}",
        "rate_per_meter": f"{rate:.2f}",
        "total_fabric_cost": f"{total_cost:.2f}",
        "fabric_cost_per_piece": f"{cost_per_piece:.2f}",
    }
    if size_rows is not None:
        result["size_breakdown"] = size_rows
    return result
