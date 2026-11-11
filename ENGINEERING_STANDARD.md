# Fashion Looks — Engineering Standard

**Standard:** Fresh Code. Verified Build. Secure GitHub.

This policy applies to new features and maintenance changes in this repository.

## Fresh code
- Inspect existing architecture and affected files before editing.
- Prefer small, focused changes; do not duplicate existing functionality.
- Preserve working behavior and public interfaces unless a change is intentional and documented.
- Use supported dependencies and lock versions for reproducible builds.

## Verification
- Add or update tests for changed behavior.
- Run formatting, linting, type checks, unit tests and build checks where supported.
- Never report a build or deployment as successful until its actual result is verified.
- Document known gaps and manual test steps.

## Security
- Never commit API keys, passwords, tokens, private keys, or production data.
- Keep secrets in GitHub Actions Secrets/Environments or a managed secret store; never expose them to browser code.
- Validate untrusted input on the server, use least privilege, and parameterized database queries.
- Review dependency and workflow permissions before adding third-party actions.
- Keep production deployments approval-gated with a rollback plan.

## GitHub workflow
- Use feature branches and pull requests for non-trivial changes.
- Require CI checks to pass before merging.
- Use descriptive commit messages and update documentation when behavior changes.
- Do not force-push shared branches or overwrite unrelated work.

## Definition of done
- [ ] Scope and acceptance criteria are clear.
- [ ] Existing implementation was inspected.
- [ ] Code is focused, readable and documented where needed.
- [ ] Relevant automated tests pass.
- [ ] Security and secret-handling checks are reviewed.
- [ ] Documentation/configuration are updated if needed.
- [ ] CI result is verified.
- [ ] Deployment is separately approved and rollback-ready.
