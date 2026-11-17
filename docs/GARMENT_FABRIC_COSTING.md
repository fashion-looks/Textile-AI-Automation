# Garment Fabric Requirement & Costing API

## Endpoint

POST /api/garments/fabric-plan

This stateless calculator estimates fabric procurement for one garment style.

### Request

    {
      "style_code": "FL-TEE-001",
      "fabric_name": "Single Jersey 180 GSM",
      "order_qty": 1000,
      "consumption_m_per_piece": 1.25,
      "wastage_percent": 5,
      "rate_per_meter": 120.50
    }

### Calculation

- Base metres = order quantity × fabric consumption per piece.
- Planned procurement metres = base metres × (1 + wastage percentage / 100), rounded up to 0.01 metre so the estimate does not understate the requirement.
- Wastage metres = planned procurement metres − rounded base metres.
- Total fabric cost = planned procurement metres × rate per metre.
- Fabric cost per piece = total fabric cost / order quantity.

Metre and monetary outputs are returned as decimal strings to avoid JSON floating-point ambiguity. Rate and returned cost use the same caller-supplied currency; the API does not infer a currency.

## Validation and limits

- order_qty: integer from 1 to 1,000,000 pieces.
- consumption_m_per_piece: greater than 0 and at most 100.
- wastage_percent: from 0 to 100.
- rate_per_meter: from 0 to 1,000,000,000.
- Style and fabric labels are required and length-bounded.
- JSON request body is limited to 16 KiB.

## Run and test

    python -m ecosystem
    python -m unittest discover -s tests -v

## Security and operational boundaries

- The endpoint is stateless and does not persist styles, order quantities, rates, or customer data.
- It does not log request bodies; responses carry Cache-Control: no-store.
- It does not authenticate callers. Keep it bound to a private network until authentication, authorization, TLS, rate limiting, and deployment monitoring are implemented.
- Treat the output as a planning estimate. Actual consumption depends on marker efficiency, size ratio, fabric width, shrinkage, GSM tolerance, defects, and approved cutting-room standards. Do not use this estimate as a purchase authorization without production review.
