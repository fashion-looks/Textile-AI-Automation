# Supplier delivery performance analytics

Endpoint: POST /api/suppliers/performance

Submit a JSON object with a purchase_orders array containing 1–50 PO snapshots.
Each row must include the standard fields required by the PO assessment endpoint:
po_number, supplier, order_qty, received_qty, and committed_delivery_date.
The optional actual_delivery_date field records the date the full order was
received, using the exact YYYY-MM-DD format.

## Metrics

Each supplier summary includes:

- total_pos and completed_pos.
- completion_rate_pct: completed POs divided by all submitted POs.
- verified_deliveries and unverified_completed_pos.
- on_time_deliveries and late_deliveries.
- on_time_delivery_rate_pct: on-time verified deliveries divided by all verified
  completed deliveries. It is null when no completed delivery has an actual date.
- average_late_days: average calendar days late, calculated only across late
  verified deliveries; zero when there are no late verified deliveries.
- open_pos and overdue_open_pos.
- total_order_qty, total_received_qty, and balance_qty.

Supplier names are grouped without case sensitivity. Duplicate PO numbers are
rejected case-insensitively so a repeated row cannot inflate the report.

## Data-quality rules

- A completed PO without actual_delivery_date is counted as unverified and is
  excluded from the on-time-rate denominator.
- An actual_delivery_date is accepted only when the PO is fully received.
- Actual delivery dates cannot be in the future.
- Invalid rows reject the report with an indexed validation message. Correct
  the source row before retrying rather than silently excluding it.
- The report is calculated against the API server's current local date.

## Example request

    curl -X POST http://127.0.0.1:8000/api/suppliers/performance       -H 'Content-Type: application/json'       -d '{
        "purchase_orders": [
          {
            "po_number": "FL-1001",
            "supplier": "Mill A",
            "order_qty": 1000,
            "received_qty": 1000,
            "committed_delivery_date": "2026-11-20",
            "actual_delivery_date": "2026-11-19"
          },
          {
            "po_number": "FL-1002",
            "supplier": "Mill A",
            "order_qty": 500,
            "received_qty": 250,
            "committed_delivery_date": "2026-11-22"
          }
        ]
      }'

## Operational and security boundaries

This endpoint is stateless: it analyses only the PO snapshots included in the
request. It does not save supplier history, update commitments, send messages,
or connect to external services. Include the reporting period's complete source
records in each request; a persistent history store is a separate future feature.

The existing API rejects non-JSON requests, malformed input, request bodies
larger than 16 KiB, and more than 50 rows. Request bodies and supplier records
are not written to application logs. Responses use Cache-Control: no-store and
X-Content-Type-Options: nosniff.

The service binds to 127.0.0.1 by default. Do not expose it publicly without
authentication, authorization, TLS, rate limiting, and a production deployment
review. Treat supplier performance metrics as operational indicators, not
automatic supplier decisions.
