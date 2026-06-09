# Mulligan's Grille — Session Changelog

Cross-session provenance record. One entry per session that produces commit-worthy changes.
Newest entry at top. Format: ISO date · subject line · body · files · verification.

---

## 2026-06-09 — feat: inline full menu replaces PDF link on home page

**Session:** 98e60cd2-3e60-426b-b468-3dc1fb684aa2
**Source material:** printed menu photo `/Users/laurenhubbell/Downloads/IMG_20260608_131608.jpg`

### Commit subject (ready to paste)

```
feat(menu): render full inline menu on home page, remove menu.pdf
```

### Commit body

Replaced the linked PDF menu with the complete menu rendered as HTML articles
inside `#menu` on the home page. Six category cards were rewritten to match
the printed menu exactly: 01 Wings, 02 Par-Tee Starters, 03 In The Ruff,
04 Handhelds, 05 Desserts, 06 Sides.

Section subhead changed from "Sample below. Daily specials and the
soup-of-the-day at the table." to "The whole menu, right here. Daily specials
announced at the table." The "View the Full Menu" PDF button was removed;
`.menu-cta-note` is retained. Footer "Full Menu" anchor repointed from
`/menu.pdf` to `/#menu` in both `index.html` and `legal/index.html`.

JSON-LD Menu node updated: removed the `"url": "menu.pdf"` property and
replaced `hasMenuSection` with the six new sections. `menu.pdf` deleted from
repo root. `assets/docs/mulligan-history.pdf` was not touched.

### Files changed

| File | Change |
|---|---|
| `index.html` | Rewrote #menu six article cards; updated subhead; removed PDF button; repointed footer link; updated JSON-LD Menu node |
| `legal/index.html` | Repointed footer "Full Menu" link from `/menu.pdf` to `/#menu` |
| `menu.pdf` | **DELETED** |

### Verification performed (pre-commit gate passed)

- `node scripts/lint-schema.js` → 7 HTML files, 7 JSON-LD blocks, 0 invalid
- `grep` for `menu.pdf` across all HTML/JSON → zero references remain
- Dev server (localhost:5173) renders six new menu headings and updated subhead
- `/audit-links` (Z7 reciprocity) → 0 outbound edges, 0 orphans

### Pending (not done yet)

- [ ] Commit + push to GitHub (`main`)
- [ ] SSH to Cloudways → `git pull`
- [ ] `curl https://mulligans-grille.com/` — confirm inline menu sections present, no PDF link
- [ ] Purge Cloudflare cache if changes don't appear within 60 s

---

## 2026-06-09 — feat: Z1 header mobile-menu UX overhaul

### Commit subject (ready to paste)

```
feat(z1): mobile-menu UX — X close icon, tablet breakpoint 1024, phone SVG
```

### Commit body

Replaces CSS bar-morph toggle with a dedicated Font Awesome 6 `fa-xmark` inline SVG
that swaps glyphs on open/close. Raises mobile-menu breakpoint from 820 px to 1024 px
so tablets share the mobile nav. Phone CTA keeps full pill on tablet (481–1024 px) and
collapses to icon-only circle only on narrowest phones (≤480 px). Replaces the 📞 emoji
with a self-hosted inline FA6 `fa-phone` SVG using `fill: currentColor`, so the existing
hover rule turns it white. CDN imports were not viable due to CSP restrictions.

Propagated to legal/index.html (previously broken — no toggle, id-less nav) and all
three _archetypes templates so they don't regress.

JS: aria-label on `.z1-mobile-toggle` now flips between "Open navigation" /
"Close navigation".

### Files changed

| File | Change |
|---|---|
| `assets/css/shared.css` | Breakpoint 820→1024; new ≤480 phone-collapse block; toggle X show/hide (bar-morph removed); phone-icon SVG sizing + currentColor |
| `index.html` | Phone icon → FA6 SVG; toggle → added `.z1-x` FA6 SVG; inlined critical CSS `<style>` block in `<head>` synced to match (reflects 1024/480/z1-x rules) |
| `assets/js/main.js` | `initMobileNav`: aria-label open/close flip |
| `legal/index.html` | Phone emoji→SVG; added `id="primary-nav"` + `.z1-mobile-toggle` button (with X SVG) |
| `_archetypes/service/index.html` | Added `.z1-x` FA6 SVG to toggle |
| `_archetypes/content/index.html` | Added `.z1-x` FA6 SVG to toggle |
| `_archetypes/case-study/index.html` | Added `.z1-x` FA6 SVG to toggle |

### Verification performed (pre-commit gate passed)

- `node scripts/lint-schema.js` → 7 files, 7 JSON-LD blocks, 0 invalid
- Dev server (localhost:5173) curl checks: phone SVG + X SVG present in all 5 headers,
  zero leftover 📞 emoji, no leftover `820px` breakpoint string,
  critical CSS in `<head>` reflects 1024/480/z1-x rules, `/legal/` returns 200

### Pending (not done yet)

- [ ] Commit + push to GitHub (`main`)
- [ ] SSH to Cloudways → `git pull`
- [ ] `curl https://mulligans-grille.com/` — confirm X SVG and updated breakpoints in HTML
- [ ] Purge Cloudflare cache if changes don't appear in 60 s
