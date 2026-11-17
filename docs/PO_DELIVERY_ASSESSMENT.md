# Purchase-order delivery assessment

The textile operations API provides single-order and bounded batch assessment
from submitted PO snapshots. It is stateless and does not persist records.

## Single-order endpoint

\`POST /api/purchase-orders/assess\`

Send \`Content-Type: application/json\`. The request body is limited to 16 KiB.

Required fields:

| Field | Type | Rule |
|---|---|---|
| \`po_number\` | string | Non-empty, up to 64 characters |
| \`supplier\` | string | Non-empty, up to 120 characters |
| \`order_qty\` | number | Greater than zero; maximum 3 decimal places |
| \`received_qty\` | number | Zero or greater; cannot exceed ordered quantity |
| \`committed_delivery_date\` | string | Exact \`YYYY-MM-DD\` date |

Optional text fields: \`buyer\`, \`style\`, and \`fabric\`.

## Batch endpoint

\`POST /api/purchase-orders/assess-batch\`

Submit a JSON object containing \`purchase_orders\`, an array of 1–50 PO
snapshots. The same field validation as the single-order endpoint applies to
each row.

Example:

\`\`\`bash
curl -X POST http://127.0.0.1:8000/api/purchase-orders/assess-batch \\
  -H 'Content-Type: application/json' \\
  -d '{
    "purchase_orders": [
      {
        "po_number": "FL-PO-2001",
        "supplier": "Example Textile Mill",
        "order_qty": 5000,
        "received_qty": 1500,
        "committed_delivery_date": "2099-12-31"
      },
      {
        "po_number": "FL-PO-2002",
        "supplier": "Another Mill",
        "order_qty": 2000,
        "received_qty": 2500,
        "committed_delivery_date": "2099-12-31"
      }
    ]
  }'
\`\`\`

The batch response contains:
- \`total\`, \`valid\`, and \`invalid\` row counts.
- A \`summary\` count for each delivery status and invalid rows.
- \`results\` in original input order, with a zero-based \`index\`.
- Valid rows include the full \`assessment\`; invalid rows include a validation
  message and do not prevent other rows from being assessed.

A batch must contain 1–50 rows and the full JSON request must remain within
the 16 KiB request limit. Split larger imports into multiple requests.

## Calculated response fields

- \`balance_qty\`: ordered quantity minus received quantity.
- \`overdue_days\`: calendar days after the committed date while a balance remains; zero for completed orders.
- \`status\`: \`complete\`, \`overdue\`, \`due_today\`, \`in_progress\`, or \`pending\`.

The assessment uses the API server's local calendar date. Configure the host
timezone to the operating timezone before production use.

## Important data boundary

These endpoints do not save records, change a supplier commitment, send a
message, or connect to an external service. They calculate only from submitted
snapshots. Store source records in an authenticated database service before
using the result as a shared operational system.

## Validation and security

- Reject non-JSON requests, malformed JSON, invalid dates, negative quantities,
  over-receipts, and unsupported quantity precision.
- Reject request bodies larger than 16 KiB and batches larger than 50 rows.
- Do not log request bodies or supplier/customer records.
- Responses include \`Cache-Control: no-store\` and \`X-Content-Type-Options: nosniff\`.
- The API binds to \`127.0.0.1\` by default. Do not expose it publicly without
  authentication, authorization, TLS, rate limiting, and an operational review.

## Tests

Run:

\`\`\`bash
python -m compileall -q ecosystem tests
python -m unittest discover -s tests -v
\`\`\`
