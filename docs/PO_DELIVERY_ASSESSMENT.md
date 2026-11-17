# Purchase-order delivery assessment

The first textile operations API feature is a stateless assessment endpoint for a
single purchase-order snapshot.

## Endpoint

`POST /api/purchase-orders/assess`

Send `Content-Type: application/json`. The request body is limited to 16 KiB.

### Request

Required fields:

| Field | Type | Rule |
|---|---|---|
| `po_number` | string | Non-empty, up to 64 characters |
| `supplier` | string | Non-empty, up to 120 characters |
| `order_qty` | number | Greater than zero; maximum 3 decimal places |
| `received_qty` | number | Zero or greater; cannot exceed ordered quantity |
| `committed_delivery_date` | string | Exact `YYYY-MM-DD` date |

Optional text fields: `buyer`, `style`, and `fabric`.

Example:

```bash
curl -X POST http://127.0.0.1:8000/api/purchase-orders/assess \
  -H 'Content-Type: application/json' \
  -d '{
    "po_number": "FL-PO-1002",
    "supplier": "Example Textile Mill",
    "buyer": "Global Buyer",
    "style": "TS-01",
    "fabric": "Cotton Jersey",
    "order_qty": 5000,
    "received_qty": 1500,
    "committed_delivery_date": "2099-12-31"
  }'
```

### Calculated response fields

- `balance_qty`: ordered quantity minus received quantity.
- `overdue_days`: calendar days after the committed date while a balance remains; zero for completed orders.
- `status`: `complete`, `overdue`, `due_today`, `in_progress`, or `pending`.

The assessment uses the API server's local calendar date. Configure the host
timezone to the operating timezone before production use.

## Important data boundary

This endpoint does not save records, change a supplier commitment, send a
message, or connect to an external service. It calculates only from the
submitted snapshot. Store source records in an authenticated database service
before using the result as a shared operational system.

## Validation and security

- Reject non-JSON requests, malformed JSON, invalid dates, negative quantities,
  over-receipts, and unsupported quantity precision.
- Reject request bodies larger than 16 KiB.
- Do not log request bodies or supplier/customer records.
- Responses include `Cache-Control: no-store` and `X-Content-Type-Options: nosniff`.
- The API binds to `127.0.0.1` by default. Do not expose it publicly without
  authentication, authorization, TLS, rate limiting, and an operational review.

## Tests

Run:

```bash
python -m compileall -q ecosystem tests
python -m unittest discover -s tests -v
```
