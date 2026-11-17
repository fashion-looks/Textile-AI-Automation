"""Validated fabric requirement and costing calculations for garment orders."""
from decimal import Decimal, InvalidOperation, ROUND_HALF_UP, ROUND_UP

CENT = Decimal("0.01")
MAX_ORDER_QTY = 1_000_000
MAX_CONSUMPTION = Decimal("100")
MAX_WASTAGE_PERCENT = Decimal("100")
MAX_RATE = Decimal("1000000000")


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


def calculate_fabric_plan(payload):
    """Return rounded procurement metres and fabric-only cost for one style."""
    if not isinstance(payload, dict):
        raise ValueError("request body must be a JSON object")

    style_code = _required_text(payload, "style_code", 80)
    fabric_name = _required_text(payload, "fabric_name", 120)

    order_qty = payload.get("order_qty")
    if isinstance(order_qty, bool) or not isinstance(order_qty, int):
        raise ValueError("order_qty must be an integer")
    if not 1 <= order_qty <= MAX_ORDER_QTY:
        raise ValueError(f"order_qty must be between 1 and {MAX_ORDER_QTY}")

    consumption = _decimal_number(payload, "consumption_m_per_piece")
    wastage = _decimal_number(payload, "wastage_percent")
    rate = _decimal_number(payload, "rate_per_meter")

    if not Decimal("0") < consumption <= MAX_CONSUMPTION:
        raise ValueError("consumption_m_per_piece must be greater than 0 and at most 100")
    if not Decimal("0") <= wastage <= MAX_WASTAGE_PERCENT:
        raise ValueError("wastage_percent must be between 0 and 100")
    if not Decimal("0") <= rate <= MAX_RATE:
        raise ValueError("rate_per_meter must be between 0 and 1000000000")

    base_raw = Decimal(order_qty) * consumption
    base_meters = base_raw.quantize(CENT, rounding=ROUND_HALF_UP)
    planned_meters = (
        base_raw * (Decimal("1") + wastage / Decimal("100"))
    ).quantize(CENT, rounding=ROUND_UP)
    wastage_meters = planned_meters - base_meters
    total_cost = (planned_meters * rate).quantize(CENT, rounding=ROUND_HALF_UP)
    cost_per_piece = (total_cost / Decimal(order_qty)).quantize(
        CENT, rounding=ROUND_HALF_UP
    )

    return {
        "style_code": style_code,
        "fabric_name": fabric_name,
        "order_qty_pcs": order_qty,
        "consumption_m_per_piece": str(consumption),
        "wastage_percent": str(wastage),
        "base_meters": f"{base_meters:.2f}",
        "wastage_meters": f"{wastage_meters:.2f}",
        "planned_meters": f"{planned_meters:.2f}",
        "rate_per_meter": f"{rate:.2f}",
        "total_fabric_cost": f"{total_cost:.2f}",
        "fabric_cost_per_piece": f"{cost_per_piece:.2f}",
    }
