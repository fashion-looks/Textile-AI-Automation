# Fashion Looks — ADLC Studio

A responsive, browser-local dashboard inspired by the SDLC → ADLC lifecycle diagram.

## Features
- Eight-stage agentic development lifecycle with interactive completion tracking.
- SDLC vs ADLC comparison.
- Task board with backlog, in-progress and completed states.
- Production readiness checklist.
- JSON export and browser-local persistence.
- Mobile-responsive layout and accessible native controls.
- GitHub Actions workflow validates required app content and JavaScript syntax.

## Run locally
Open `index.html` in a modern browser. No package installation is required.

## Deploy
The app is a static site. It can be published from the `adlc/` directory using GitHub Pages or any static hosting provider.

## Scope and security
This starter is a frontend workflow dashboard. It does **not** execute coding agents, connect PostgreSQL/Redis, provision cloud resources, or deploy applications. Those require a backend worker, authenticated APIs, secret storage, observability, and explicit production approval. Never place credentials in browser JavaScript or commit secrets.

## CI
The workflow at `.github/workflows/adlc-ci.yml` runs on relevant pushes and pull requests.
