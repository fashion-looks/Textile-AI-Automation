# Fashion Looks A–Z Ecosystem Starter

## Run locally
Requires Python 3.11+; no external packages are required.

```bash
python -m ecosystem
```

Open `http://127.0.0.1:8000/health` and `http://127.0.0.1:8000/api/categories`.

## Test
```bash
python -m unittest discover -s tests -v
```

## Automation scope
GitHub Actions runs bounded validation on repository events. It does not keep a server running 24/7. External services such as AI providers, payments, cloud, email, messaging, maps and hardware need separate credentials, configuration, tests and authorization before use. No external side effects are enabled by this starter.
