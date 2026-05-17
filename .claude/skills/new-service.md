---
description: Scaffold a new service page with pricing block and Z4 feature list
user-invocable: true
---

# /new-service — Service page scaffold

Scaffold a new service page with the service-specific prompts.

## Usage

`/new-service <slug>`

## Instructions

Prompt for service-specific fields:

1. **Service name** — for the hero `<h1>` (e.g., "Voice Over Recording")
2. **Service tagline** — 1 sentence under the hero
3. **Service description** — 80–150 word entity declaration paragraph
4. **3 features** — each with title + 1-sentence description (for Z4 feature list)
5. **Pricing tiers** — hourly, half-day, full-day (or whichever fits the service)
6. **Service hero image filename** — `assets/images/<slug>-hero.jpg`
7. **Detail image filename** — `assets/images/<slug>-detail.jpg`

Then delegate to `page-builder` with archetype=`service`. The agent populates the placeholders, registers in sitemap (priority 0.9), and runs schema lint.

## After scaffolding

- Add the service to the homepage Z3 services grid
- Add a sitemap entry on a `/services/` index page if one exists
- If competitive-keyword-targeted: invoke `seo-auditor` to verify the page's competitive completeness
- Use `content-writer` to refine the entity declaration paragraph in the project's voice
