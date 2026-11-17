# Size-Mix Fabric Planning

## Overview

The garment fabric planning endpoint supports two mutually exclusive calculation modes:

- **Single-rate mode:** one average fabric consumption value for the complete order.
- **Size-breakdown mode:** a separate consumption value and piece quantity for each size.

Endpoint: `POST /api/garments/fabric-plan`

## Size-breakdown request

```json
{
  "style_code": "FL-TEE-002",
  "fabric_name": "Single Jersey 180 GSM",
  "order_qty": 100,
  "size_breakdown": [
    {"size": "S", "order_qty_pcs": 20, "consumption_m_per_piece": 1.1},
    {"size": "M", "order_qty_pcs": 40, "consumption_m_per_piece": 1.2},
    {"size": "L", "order_qty_pcs": 30, "consumption_m_per_piece": 1.3},
    {"size": "XL", "order_qty_pcs": 10, "consumption_m_per_piece": 1.4}
  ],
  "wastage_percent": 5,
  "rate_per_meter": 100
}
```

The quantities across all size rows must equal `order_qty`. Each size label must be unique, ignoring letter case. The API accepts 1–20 size rows.

## Calculation

For each size, the engine multiplies its piece quantity by its own consumption. It sums those unrounded line values to get base metres, then applies the wastage percentage and rounds the procurement requirement upward to 0.01 metre. Cost is calculated from the planned procurement metres and caller-supplied rate.

For the example above:

- Base requirement: 123.00 m
- Wastage at 5%: 6.15 m
- Planned procurement: 129.15 m
- Fabric-only cost at 100 per metre: 12,915.00
- Weighted average consumption: 1.2300 m per piece

The existing `consumption_m_per_piece` response field contains the weighted average in size-breakdown mode. `consumption_basis` identifies the mode, and `size_breakdown` returns the validated size rows with exact line-level base metres.

## Validation

- Supply either `consumption_m_per_piece` or `size_breakdown`, never both.
- Each size row requires a non-empty size label (maximum 24 characters), integer piece quantity, and finite positive consumption no greater than 100 m per piece.
- Size labels must be unique case-insensitively.
- Size-row piece quantities must add up exactly to `order_qty`.
- Existing order, wastage, rate, and request-size limits still apply.

## Security and operational boundaries

- The calculation is stateless and does not persist or log order or size data.
- Treat the result as a planning estimate, not a purchase authorization.
- This API is unauthenticated; keep it on a private network until authentication, authorization, TLS, rate limiting, and monitoring are implemented.
- The result does not replace an approved marker plan or account for marker efficiency, fabric width, shrinkage, GSM tolerance, defects, or cutting-room allowances unless those are already reflected in the submitted per-size consumption.
