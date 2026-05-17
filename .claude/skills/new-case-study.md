---
description: Scaffold a new case study, prompting for client / challenge / approach / results / metrics
user-invocable: true
---

# /new-case-study — Case study scaffold

Scaffold a new portfolio case study with the structured prompt.

## Usage

`/new-case-study <slug>`

## Instructions

Prompt the user for these case-study-specific fields:

1. **Project title** — what it'll appear as in the hero
2. **Client name** — appears as eyebrow above the title
3. **Summary** — 1-sentence what-it-is for the hero subhead
4. **Lead paragraph** — 50–100 words, the "why this project mattered" opener
5. **Challenge** — what was the constraint?
6. **Approach** — how did you solve it?
7. **Results** — what was the outcome (specifics)?
8. **Metrics** — 3–4 callouts (impressions, runtime, deliverables, etc.)
9. **Tools used** — for the tech-specs grid
10. **Deliverables** — for the tech-specs grid
11. **Team** — for the tech-specs grid
12. **Testimonial quote + attribution** (optional)
13. **Gallery image filenames** (optional, can be added later)

Then delegate to the `page-builder` agent with archetype=`case-study`. After scaffolding, the agent fills the placeholders with the prompted values, runs schema lint, and reports.

## After scaffolding

Required follow-ups:
- Drop hero image at `assets/images/<slug>-hero.jpg` (1920×1080+)
- Drop OG image at `assets/images/<slug>-og.jpg` (1200×630)
- Add 3–4 reciprocal Z7-related cards on existing case studies
- Run `/audit-links` to verify reciprocity
