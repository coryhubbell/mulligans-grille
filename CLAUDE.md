# Mulligan's Grille

Static HTML website for {{BUSINESS_DESCRIPTION}}.

## Stack

- Hand-coded static HTML (zero build step)
- Cloudflare → nginx (Cloudways edge) → Apache → static HTML in document root
- Z1–Z9 zone CSS system (see `DESIGN_SYSTEM.md`)
- JSON-LD validated in CI via `scripts/lint-schema.js`

## Deployment

- **GitHub:** {{GITHUB_REPO}}
- **Host:** Cloudways ({{CLOUDWAYS_HOST}})
- **Workflow:** Local → GitHub → Cloudways (`git pull` on the server, or `.github/workflows/deploy.yml.disabled` if you re-enable it)

## Local development

```bash
node scripts/dev-server.js  # http://localhost:5173
```

Or invoke the `/dev` skill in Claude Code.

## Pre-flight checks

Before pushing:

- `node scripts/lint-schema.js` — JSON-LD validation
- `/audit-links` — Z7-related-card reciprocity
- See `HEALTH-CHECK.md` for the full pre-deploy gate

## Reference docs

- `BRAND.md` — color tokens, typography, voice/tone
- `DESIGN_SYSTEM.md` — Z1–Z9 zone catalog, component vocabulary
- `CONTENT_TEMPLATES.md` — section outlines per archetype
- `SEO_CONVENTIONS.md` — JSON-LD patterns, meta tags
- `DEPLOYMENT.md` — full deploy walkthrough
- `DEVELOPMENT.md` — local dev + page-creation checklist
- `.beads/PRIME.md` — non-negotiable constraints + edge/origin settings
- `PROJECT_BRIEF.md` — client kickoff facts

@RTK.md
