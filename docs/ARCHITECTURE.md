# Architecture

## Initial modular layout
- `apps/web/`: responsive web user interface (planned)
- `apps/api/`: API service (planned)
- `packages/core/`: shared validation and domain rules
- `integrations/`: optional third-party adapters, each disabled until configured
- `tests/`: automated checks
- `docs/`: architecture, operations, and category roadmap
- `.github/workflows/`: CI and bounded scheduled maintenance

## Data domains for Fashion Looks
- Leads and client accounts
- Purchase orders and styles
- Fabric requirements, received quantity, balance quantity and ETA
- Certification type, issuer, certificate reference, validity dates and evidence
- Follow-up history, owner, next action and approval status

Use a relational database for production multi-user use. A local file or browser storage is suitable only for a prototype. Add authentication, authorization, backups and migration scripts before connecting customer data.
