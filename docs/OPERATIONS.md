# Operations and safe deployment

## Local checks
```bash
python -m compileall -q .
python -m unittest discover -s tests -v
```

## GitHub Actions
The CI workflow runs on pushes, pull requests, and manual dispatch. Daily Maintenance runs daily (UTC) and can also be triggered manually.

## Secrets and external services
1. Never put API keys, passwords, OAuth tokens, or customer records in source files.
2. Add only the required credentials under repository Settings → Secrets and variables → Actions.
3. Verify the official provider documentation and exact API scopes before implementing an integration.
4. Test in a sandbox; use a human approval step for outbound emails, buyer/vendor updates, payments, or production deployments.
5. Review logs for accidental confidential data before sharing them.

## Extending the textile workflow
A recommended first data model includes PO number, buyer, style, fabric, required quantity, available quantity, committed delivery date, current status, last follow-up date, next action, and evidence/source for the latest update. Do not treat generated estimates as supplier commitments.
