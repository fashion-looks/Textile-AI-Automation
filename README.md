# Textile-AI-Automation

AI-powered textile merchandising automation for buyer communication, PO tracking, fabric TC follow-up, approvals, and reporting.

## Maintained API modules

- GET /health — service health check.
- GET /api/categories — ecosystem category catalog.
- POST /api/garments/fabric-plan — stateless garment fabric requirement and fabric-only costing estimate.

## Local development

    python -m ecosystem
    python -m unittest discover -s tests -v

See docs/GARMENT_FABRIC_COSTING.md for request fields, calculation assumptions, limits, and security boundaries.
