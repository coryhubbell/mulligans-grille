---
description: Scaffold a new page from an archetype, register it in sitemap and breadcrumbs
user-invocable: true
---

# /new-page — Scaffold a new page

Scaffold a new page from one of the archetypes.

## Usage

`/new-page <slug> <archetype>`

Where:
- `<slug>` — `lowercase-with-hyphens` (becomes the directory name and URL)
- `<archetype>` — one of: `service`, `case-study`, `content`

## Instructions

Delegate to the `page-builder` agent with these inputs:

1. The slug and archetype from the user
2. Prompt for: page title, page description (120–160 chars), parent breadcrumb (Services / Portfolio / Home)
3. Today's date (for `<lastmod>`)

The `page-builder` agent will:
1. Create `<slug>/` directory
2. Copy `_archetypes/<archetype>/index.html` to `<slug>/index.html`
3. Replace placeholders
4. Add to `sitemap.xml`
5. Identify Z7 sibling candidates (both directions) and propose updates
6. Run `node scripts/lint-schema.js` to verify

Refuse if:
- The slug already exists
- The slug isn't `lowercase-with-hyphens`
- The archetype isn't valid

## After scaffolding

Suggest follow-up:
- Use `/dev` to view the new page locally
- Replace hero image at `assets/images/<slug>-hero.jpg`
- For case studies: replace OG image at `assets/images/<slug>-og.jpg`
- Use the `content-writer` agent to fill prose
