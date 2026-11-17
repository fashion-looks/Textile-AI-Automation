# Certification expiry assessment

Endpoint: `POST /api/certifications/assess-batch`

This stateless endpoint classifies submitted certification records by their
expiry dates. It is an administrative reminder aid only: it does not verify
certificate authenticity, accreditation, scope, transaction certificates,
chain of custody, or ongoing compliance.

## Request

Send `Content-Type: application/json`. The shared API request limit is 16 KiB.
Submit 1–50 records in the `certificates` array.

Required fields for each record:

| Field | Type | Rule |
|---|---|---|
| `company` | string | Non-empty, up to 120 characters |
| `standard` | string | Non-empty, up to 80 characters; e.g. GOTS, GRS, OCS, RCS, RWS, OEKO-TEX, BCI |
| `certificate_number` | string | Non-empty, up to 100 characters |
| `issue_date` | string | Exact `YYYY-MM-DD` date; cannot be in the future |
| `expiry_date` | string | Exact `YYYY-MM-DD` date; cannot precede issue date |

Optional top-level field: `warning_days`, an integer from 0 to 365. The default
is 60 days. Invalid top-level warning windows reject the request; invalid
certificate rows are returned individually with `status: invalid`.

## Example

```bash
curl -X POST http://127.0.0.1:8000/api/certifications/assess-batch \
  -H 'Content-Type: application/json' \
  -d '{
    "warning_days": 60,
    "certificates": [
      {
        "company": "Example Textile Mill",
        "standard": "GOTS",
        "certificate_number": "GOTS-IND-2026-001",
        "issue_date": "2026-01-01",
        "expiry_date": "2026-12-15"
      }
    ]
  }'
```

## Status rules

- `expired`: expiry date is before the assessment date.
- `expiring_soon`: expiry date is today or within the configured warning window.
- `valid`: expiry date is beyond the warning window.
- `invalid`: the row fails input validation.

The response includes `days_to_expiry` (negative for an expired record),
`as_of_date`, the configured warning window, per-row results in input order,
and summary counts. The API server's local calendar date is used.

## Data and security boundaries

- The endpoint is stateless and does not save certificate records or documents.
- Do not treat this result as proof that a certificate is authentic or valid
  for a particular product, site, scope, shipment, or transaction.
- Do not upload certificate scans or personal data to this endpoint.
- Request bodies are not written to application logs.
- The shared API enforces JSON content type, a 16 KiB request limit, and a
  maximum of 50 records. Responses use `Cache-Control: no-store` and
  `X-Content-Type-Options: nosniff`.
- The service binds to `127.0.0.1` by default. Do not expose it publicly
  without authentication, authorization, TLS, rate limiting, and a production
  deployment review.

## Tests

```bash
python -m compileall -q ecosystem tests
python -m unittest discover -s tests -v
```
