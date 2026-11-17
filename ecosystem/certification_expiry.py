"""Stateless certification expiry assessment for Fashion Looks.

This module classifies caller-supplied certificate snapshots. It does not
verify certificate authenticity, accreditation, scope, or ongoing compliance.
"""
from datetime import date

from .purchase_orders import MAX_BATCH_ITEMS

MAX_WARNING_DAYS = 365
DEFAULT_WARNING_DAYS = 60


def _text(payload, field, *, limit=120):
    value = payload.get(field)
    if not isinstance(value, str):
        raise ValueError(f"{field} must be text")
    value = value.strip()
    if not value:
        raise ValueError(f"{field} is required")
    if len(value) > limit:
        raise ValueError(f"{field} must be {limit} characters or fewer")
    return value


def _iso_date(payload, field):
    raw = payload.get(field)
    if not isinstance(raw, str):
        raise ValueError(f"{field} must use YYYY-MM-DD")
    try:
        parsed = date.fromisoformat(raw)
    except ValueError:
        raise ValueError(f"{field} must use YYYY-MM-DD") from None
    if parsed.isoformat() != raw:
        raise ValueError(f"{field} must use YYYY-MM-DD")
    return parsed


def _validate_warning_days(value):
    if isinstance(value, bool) or not isinstance(value, int):
        raise ValueError("warning_days must be an integer")
    if not 0 <= value <= MAX_WARNING_DAYS:
        raise ValueError(f"warning_days must be between 0 and {MAX_WARNING_DAYS}")
    return value


def assess_certificate(payload, *, today=None, warning_days=DEFAULT_WARNING_DAYS):
    """Validate one certificate snapshot and classify its expiry window.

    Required fields: company, standard, certificate_number, issue_date,
    expiry_date. Dates must be exact YYYY-MM-DD values.
    """
    if not isinstance(payload, dict):
        raise ValueError("certificate must be a JSON object")

    company = _text(payload, "company", limit=120)
    standard = _text(payload, "standard", limit=80)
    certificate_number = _text(payload, "certificate_number", limit=100)
    issue_date = _iso_date(payload, "issue_date")
    expiry_date = _iso_date(payload, "expiry_date")

    as_of = today or date.today()
    if not isinstance(as_of, date):
        raise ValueError("today must be a date")
    window = _validate_warning_days(warning_days)

    if issue_date > as_of:
        raise ValueError("issue_date cannot be in the future")
    if expiry_date < issue_date:
        raise ValueError("expiry_date cannot be before issue_date")

    days_to_expiry = (expiry_date - as_of).days
    if days_to_expiry < 0:
        status = "expired"
    elif days_to_expiry <= window:
        status = "expiring_soon"
    else:
        status = "valid"

    return {
        "company": company,
        "standard": standard,
        "certificate_number": certificate_number,
        "issue_date": issue_date.isoformat(),
        "expiry_date": expiry_date.isoformat(),
        "days_to_expiry": days_to_expiry,
        "warning_days": window,
        "status": status,
    }


def assess_certificate_batch(payload, *, today=None):
    """Assess 1–50 certificate snapshots with row-level validation results."""
    if not isinstance(payload, dict):
        raise ValueError("request body must be a JSON object")

    certificates = payload.get("certificates")
    if not isinstance(certificates, list):
        raise ValueError("certificates must be a list")
    if not 1 <= len(certificates) <= MAX_BATCH_ITEMS:
        raise ValueError(
            f"certificates must contain between 1 and {MAX_BATCH_ITEMS} items"
        )

    warning_days = _validate_warning_days(
        payload.get("warning_days", DEFAULT_WARNING_DAYS)
    )
    as_of = today or date.today()
    if not isinstance(as_of, date):
        raise ValueError("today must be a date")

    summary = {"valid": 0, "expiring_soon": 0, "expired": 0, "invalid": 0}
    results = []

    for index, certificate in enumerate(certificates):
        try:
            assessment = assess_certificate(
                certificate, today=as_of, warning_days=warning_days
            )
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
        "as_of_date": as_of.isoformat(),
        "warning_days": warning_days,
        "total": len(certificates),
        "valid": len(certificates) - summary["invalid"],
        "invalid": summary["invalid"],
        "summary": summary,
        "results": results,
    }
