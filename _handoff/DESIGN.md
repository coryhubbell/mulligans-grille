# DESIGN.md — Visual Direction

## Aesthetic concept: "Classic American Roadhouse, modern execution"

Not Irish-themed (despite the name) — the existing brand is American casual / sports grill. Don't lean leprechaun. Lean **dark wood, warm amber, neon-sign warmth, generous whitespace** — a confident, modern take on a roadhouse menu board.

The competition (chain sports bars, generic Squarespace restaurant templates) all do glossy stock photos on white backgrounds. We do the opposite: **dark, warm, confident, typographic**.

---

## Color tokens (use CSS custom properties)

```css
:root {
  /* Base */
  --bg:           #1a1410;   /* near-black warm brown */
  --bg-elevated:  #221a14;   /* card/section background */
  --bg-soft:      #2a2018;   /* hover, dividers */

  /* Foreground */
  --text:         #f5ead8;   /* warm off-white, main copy */
  --text-muted:   #b8a890;   /* warm grey-tan, secondary */
  --text-dim:     #7a6a55;   /* tertiary, fine print */

  /* Brand accents */
  --amber:        #d4a548;   /* primary accent — neon-warm gold */
  --amber-bright: #f0bf58;   /* hover state */
  --rust:         #b85c2a;   /* secondary accent, sparing use */
  --cream:        #ede1c4;   /* light surface for contrast cards */

  /* Functional */
  --border:       rgba(245, 234, 216, 0.12);
  --shadow:       0 20px 60px -20px rgba(0,0,0,0.6);
}
```

Use amber **sparingly** — it should pop. Borders, button fills, the underline on the wordmark, headline accents. Not for body text. Not for entire sections.

---

## Typography

**Display:** `"Bebas Neue"`, fallback `Impact, sans-serif` — for the H1 and section headings. All-caps, condensed, tight tracking. Reads like a vintage menu board.

**Body:** `"Source Serif 4"`, fallback `Georgia, serif` — for paragraphs. Readable serif keeps it warm and editorial, not corporate-sans.

**UI / nav / small caps:** `"Inter Tight"`, fallback `system-ui, sans-serif` — for nav, buttons, fine print only.

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Serif+4:wght@400;600&family=Inter+Tight:wght@500;600&display=swap" rel="stylesheet">
```

Scale (mobile-first, fluid):
```css
--text-xs:   0.8125rem;
--text-sm:   0.9375rem;
--text-base: 1.0625rem;     /* body 17px — generous */
--text-lg:   1.25rem;
--text-xl:   clamp(1.5rem, 2.5vw, 2rem);
--text-2xl:  clamp(2rem, 4vw, 3rem);
--text-3xl:  clamp(2.75rem, 6vw, 4.5rem);
--text-hero: clamp(3.5rem, 10vw, 8rem);   /* H1 only */
```

H1 is in Bebas, all-caps, line-height 0.95, tight tracking. It should feel like a sign painted on the side of the building.

---

## Layout & spacing

- Max content width: 1200px, centered.
- Section vertical padding: clamp(4rem, 10vw, 8rem) top/bottom.
- Horizontal gutter: clamp(1.25rem, 4vw, 2.5rem).
- Use CSS Grid for the menu highlight cards and the events cards. 1 col mobile → 2 col tablet → 3 col desktop.
- Generous whitespace. Don't fill every inch.

---

## Visual texture (lift it out of "default dark site" territory)

- **Background:** subtle dark amber radial gradient at the top of the page, fading to the base bg. Add a `noise` SVG overlay at ~3% opacity over the whole page (gives the dark areas warmth instead of flat black).
- **Section dividers:** thin amber line (1px, `--amber`) at 20% opacity, or a custom SVG flourish (a stylized golf-tee or simple geometric mark — the name is Mulligan's, a golf reference is on-brand but should be subtle, not central).
- **Cards:** `--bg-elevated` background, 1px border in `--border`, large radius (16–20px), soft shadow on hover.
- **Hover states:** amber underline grows in from left under links (300ms ease). Buttons lift 2px and brighten.

---

## Motion

Keep it tasteful. One coordinated load-in, then quiet.

- **Hero entrance:** H1 fades up + slight letter-spacing decrease over 800ms. Subhead and CTAs stagger in 150ms apart after.
- **Scroll reveals:** section headings fade-up when they enter the viewport (Intersection Observer, opacity 0→1 + translateY 12px→0, 600ms ease-out).
- **No parallax. No autoplay video. No scroll-jacking.**
- Respect `prefers-reduced-motion: reduce` — kill all transitions/animations.

---

## Components inventory

1. **Sticky top nav** — transparent over hero, gains background on scroll. Logo left, anchor links + phone CTA right. Hamburger under 768px.
2. **Hero** — full viewport height on desktop, ~75vh on mobile. Background image with dark overlay + amber gradient mix.
3. **Section header** — eyebrow (small caps, amber, letter-spaced) + H2 + optional intro line.
4. **Menu card** — image area optional, category name in display font, 2-line description.
5. **Hours table** — clean two-column list, current day highlighted in amber.
6. **Address card** — copy + map embed side-by-side on desktop, stacked on mobile.
7. **Event card** — icon/emoji + title + one-line description.
8. **Button (primary)** — amber fill, dark text, slight rounded corners.
9. **Button (secondary/ghost)** — transparent, amber border, amber text, fills on hover.
10. **Footer** — three columns desktop, stacked mobile.

---

## What to avoid

- ❌ Stock photo of a cheeseburger floating on white
- ❌ Generic Squarespace "Bistro" template feel
- ❌ Irish pub clichés (shamrocks, leprechauns, green-on-green) — the brand is American sports grill, not Irish
- ❌ Inter on white background — see notes in skill, this is the AI-default we are explicitly avoiding
- ❌ Purple gradients (also AI-default)
- ❌ Hero carousels
- ❌ "Welcome to our restaurant" hero copy
- ❌ Glossy gradient buttons with drop shadows
