# Mulligan's Grille — Brand Standards

## Identity

- **Name:** Mulligan's Grille
- **Tagline:** Wings, burgers, cold beer, good people.
- **Domain:** mulligans-grille.com
- **Aesthetic:** Classic American roadhouse, modern execution. Dark wood, warm amber, neon-sign warmth, generous whitespace. Not Irish-themed — American casual sports grill.

## Color Tokens

Defined in `assets/css/shared.css` as `:root` CSS custom properties.

| Token | Value | Use |
|---|---|---|
| `--bg` | `#1a1410` | Page background — near-black warm brown |
| `--bg-elevated` | `#221a14` | Card / alternating section bg |
| `--bg-soft` | `#2a2018` | Hover, dividers |
| `--text` | `#f5ead8` | Main copy — warm off-white |
| `--text-muted` | `#b8a890` | Secondary text — warm grey-tan |
| `--text-dim` | `#7a6a55` | Tertiary, fine print |
| `--amber` | `#d4a548` | Primary accent — neon-warm gold |
| `--amber-bright` | `#f0bf58` | Hover state |
| `--rust` | `#b85c2a` | Secondary accent (sparing use) |
| `--cream` | `#ede1c4` | Light surface for contrast |
| `--border` | `rgba(245, 234, 216, 0.12)` | Borders, dividers |

Use amber **sparingly** — borders, button fills, headline accents. Never for body text or entire sections.

## Typography

| Role | Font | Stack | Use |
|---|---|---|---|
| Display | Bebas Neue | `var(--font-display)` | H1, H2 section headings (all-caps, condensed) |
| Body | Source Serif 4 | `var(--font-body)` | Paragraphs (readable serif, warm/editorial) |
| UI | Inter Tight | `var(--font-ui)` | Nav, buttons, fine print |

Loaded from Google Fonts with `display=swap`. H1 line-height 0.95, tight tracking — feels like a sign painted on the building.

### Type scale (mobile-first, fluid)

```css
--text-xs:   0.8125rem;
--text-sm:   0.9375rem;
--text-base: 1.0625rem;             /* body 17px — generous */
--text-lg:   1.25rem;
--text-xl:   clamp(1.5rem, 2.5vw, 2rem);
--text-2xl:  clamp(2rem, 4vw, 3rem);
--text-3xl:  clamp(2.75rem, 6vw, 4.5rem);
--text-hero: clamp(3.5rem, 10vw, 8rem);
```

## Voice & Tone

- **Voice:** plainspoken, neighborhood, confident — not corporate, not Irish-themed kitsch
- **Person:** First-person plural for the restaurant ("we"), second-person for the reader ("you")
- **Avoid:** "Welcome to our restaurant," generic bistro language, shamrocks/leprechauns, "elevated dining"

## Motion

- Hero load-in: H1 fade-up + slight letter-spacing decrease over 800ms; subhead + CTAs stagger 150ms apart
- Scroll reveals: section headings fade-up via IntersectionObserver (opacity 0→1 + translateY 12px→0, 600ms ease-out)
- No parallax. No autoplay video. No scroll-jacking.
- Respect `prefers-reduced-motion: reduce` — disable all transitions/animations.

## Accessibility

- Body copy contrast against `--bg`: 7:1 minimum (WCAG AAA)
- Interactive elements: 4.5:1 minimum (WCAG AA)
- Visible focus rings on every interactive element — never `outline: none` without an alternative
