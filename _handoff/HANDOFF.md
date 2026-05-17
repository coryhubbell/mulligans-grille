# Mulligan's Grille — One-Pager Rebuild · Handoff Brief

**For:** Claude Code
**From:** Discovery / content prep pass
**Goal:** Ship a clean, fast, single-page marketing site at **mulligans-grille.com** to replace the destroyed legacy site.

> Read this file first. Then `CONTENT.md` (copy + data) and `DESIGN.md` (visual direction). `TASKS.md` is the checklist.

---

## 1. Situation

- We acquired the business **Mulligan's Grille** (Port Orange, FL).
- Previous owners destroyed the old website at `mulligansgrille.com` (SSL is broken — domain confirmed dead in fetch attempts).
- New domain: **`mulligans-grille.com`** (hyphenated).
- Old reference: Wayback snapshot https://web.archive.org/web/20251212032824/https://mulligansgrille.com/ (Wayback was blocked from direct fetch in this environment — content below was reconstructed from public sources: Yelp, Tripadvisor, Restaurant Guru, Sirved, Uber Eats, DoorDash, Facebook, the local Hometown News profile, and indexed page titles from the old site).

## 2. Scope: one-pager, fast

**In scope:**
- One HTML page (no CMS, no build pipeline unless trivial).
- Sections: Hero → About → Menu Highlights → Hours & Location → Events → Contact / Footer.
- Mobile-first, fully responsive.
- Working Google Maps embed + click-to-call + click-to-email.
- Lighthouse: aim for 95+ on Performance, Accessibility, Best Practices, SEO.

**Out of scope (for this pass):**
- Online ordering integration (link out to existing DoorDash / Uber Eats listings instead).
- Reservations system.
- Photo gallery beyond a hero image + a few accents.
- Blog / CMS / admin.
- Full menu with prices (we don't have authoritative current pricing — show categories + signature items only, with a "view full menu" CTA to a PDF placeholder).

## 3. Verified business facts (use these — no others)

| Field | Value | Source |
|---|---|---|
| Name | Mulligan's Grille | Multiple |
| Address | 3830 S Nova Rd, Ste B1, Port Orange, FL 32127 | Yelp, Sirved, DoorDash |
| Phone | (386) 788-3268 | Yelp, Sirved |
| Cuisine | American / sports bar / pub grub | Yelp, Tripadvisor |
| Known for | Wings, burgers, hand-cut steaks, scratch-made soups & sauces, fish & chips | Yelp specialties listing |
| Vibe | Casual, family-friendly, sports bar, dog-friendly patio, moderate noise | Yelp attributes, reviews |
| Entertainment | Live trivia (Challenge Entertainment), karaoke Fri/Sat 9pm, occasional comedy & live music | Yelp, Challenge Entertainment, reviews |
| Hours (current best estimate) | Mon–Thu 11am–11pm · Fri–Sat 11am–1am · Sun 11am–11pm | Yelp (most current source) |
| Breakfast | Thu–Sun starting 7am (historical — **CONFIRM with owner before launch**) | Hometown News 2022 |
| Domain (new) | mulligans-grille.com | Owner directive |
| Facebook | facebook.com/mulligansgrillepo/ | Confirmed live |
| Instagram | @mulligansgrille | Confirmed live |

**⚠️ Confirm with owner before launch:** exact current hours, whether breakfast is still served, current entertainment schedule, official email address, and any new ownership messaging they want surfaced.

## 4. Tech recommendation

**Single static HTML file** + one CSS file + (optional) a few KB of vanilla JS for mobile menu toggle.

- No framework needed — a one-pager doesn't justify React/Next overhead.
- Host on Netlify, Cloudflare Pages, or Vercel static. Point `mulligans-grille.com` at it.
- Use system-font fallback stack with one webfont pair loaded from Google Fonts with `display=swap`.
- Inline critical CSS if Lighthouse asks for it; otherwise keep CSS external for cacheability.

If Claude Code prefers, an Astro project is fine — it produces the same static output with nicer DX. Do **not** reach for Next.js / Gatsby / WordPress for this.

## 5. Deliverables checklist

See `TASKS.md`. High level:
1. Scaffold project (`index.html`, `styles.css`, `script.js`, `/assets`).
2. Implement sections per `CONTENT.md` in the order listed.
3. Apply visual direction from `DESIGN.md`.
4. Add SEO meta, Open Graph, JSON-LD `Restaurant` schema (template in `SEO.md`).
5. Add a `robots.txt` and `sitemap.xml`.
6. Add a placeholder `menu.pdf` link (owner will provide the real PDF).
7. Test on mobile (375px), tablet (768px), desktop (1280px+).
8. Run Lighthouse, fix anything below 95.

## 6. Files in this handoff package

- `HANDOFF.md` — this file
- `CONTENT.md` — every word and data point for the page, section by section
- `DESIGN.md` — visual direction, color, type, motion
- `SEO.md` — meta tags, OG, JSON-LD schema, ready to paste
- `TASKS.md` — ordered build checklist
- `OPEN_QUESTIONS.md` — items to confirm with owner before launch
