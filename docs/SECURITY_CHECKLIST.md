# Security Checklist

- [ ] Store secrets only in GitHub Actions secrets or a dedicated secret manager.
- [ ] Use least-privilege tokens and rotate them after suspected exposure.
- [ ] Never log credentials, full customer documents or payment data.
- [ ] Validate uploaded files, content types, size and filenames.
- [ ] Add authentication and role-based authorization before production use.
- [ ] Separate development, test and production credentials.
- [ ] Use a payment provider's hosted checkout; never store raw card numbers.
- [ ] Require human approval for external messages and production deployments.
- [ ] Review dependencies, licenses and vulnerability alerts.
- [ ] Test backup restoration, not only backup creation.
- [ ] Keep certification evidence and status updates auditable.
- [ ] Document incident response and data deletion procedures.
