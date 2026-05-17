---
description: Interactively gather brand info and write tokens to shared.css and BRAND.md
user-invocable: true
---

# /brand-init — Brand identity bootstrap

Set up the project's brand identity in code.

## When to use

- Right after `bin/silenthubb-init.sh` if you didn't pass brand flags
- Whenever a brand refresh changes the color palette or fonts

## Instructions

Delegate to the `brand-tokenizer` agent. Before delegation, gather these from the user via prompts (use `AskUserQuestion` for choice-style questions):

1. **Primary color** — hex (the new "primary accent / CTA / link" color)
2. **Primary color, dark variant** — hex (for headers and dark CTAs)
3. **Secondary accent** — hex (sparingly used)
4. **Display font** — name (and confirm WOFF2 file exists in `assets/fonts/`)
5. **Body font** — name (and confirm WOFF2 file exists)
6. **Voice description** — 1–2 sentences ("confident and plainspoken", "warm and conversational", etc.)
7. **Forbidden words** — comma-separated

Then pass all 7 to the `brand-tokenizer` agent.

The agent will:
1. Update `:root` tokens in `assets/css/shared.css`
2. Add or update `@font-face` blocks
3. Fill placeholders in `BRAND.md`
4. Update `manifest.json` `theme_color`
5. Flag any stale hardcoded colors elsewhere

After the agent completes, suggest the user run `/dev` and visit the homepage to verify the new identity renders correctly.

## Refusal

Refuse to run `/brand-init` if `BRAND.md` is already filled (no `{{PLACEHOLDER}}` markers) — that means the brand is set, and re-running could overwrite intentional refinements. In that case, suggest editing `BRAND.md` and `shared.css` directly, or invoke `brand-tokenizer` agent manually with explicit confirmation.
