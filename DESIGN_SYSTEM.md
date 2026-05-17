# Design System — Z1–Z9 Zone Architecture

The site uses a numbered "zone" CSS naming convention. Every page section is one of nine zones, each with predictable layout rules.

## Zone catalog

| Zone | Purpose | Class prefix | Typical content |
|---|---|---|---|
| **Z1** | Header / nav | `.z1-` | Sticky logo, primary nav, mobile toggle |
| **Z2** | Hero | `.z2-` | Full-bleed background, overlaid headline + sub + CTAs |
| **Z3** | Main content | `.z3-` | Service grids, prose, forms, pricing, FAQ |
| **Z4** | Client logos | `.z4-` | Logo strip / wall, feature lists with images |
| **Z5** | Reviews | `.z5-` | Testimonial cards, star ratings |
| **Z6** | Team | `.z6-` | Team grids, individual member cards |
| **Z7** | Related / FAQ | `.z7-` | Related-content grid, FAQ accordion |
| **Z8** | CTA | `.z8-` | Dark-background full-width CTA section |
| **Z9** | Footer | `.z9-` | Footer columns, legal, social |

Every zone has a `.zN-inner` centering element that respects `--container` (1400px) and `--container-padding` (24px desktop / 16px mobile).

## Component vocabulary

| Component | Class | Where defined |
|---|---|---|
| Primary button | `.btn-primary` | `shared.css` |
| Outline button | `.btn-outline` | `shared.css` |
| Light primary button | `.btn-primary-light` | `shared.css` |
| Dark outline button | `.btn-outline-dark` | `shared.css` |
| Breadcrumb | `.breadcrumb ol` / `.breadcrumb a` | `shared.css` |
| Service card | `.z3-service-card` | `page-home.css` |
| Review card | `.z5-review-card` | `page-home.css` |
| Team card | `.z3-team-card` | `page-content.css` |
| FAQ item | `.z3-faq-item` / `.z3-faq-question` / `.z3-faq-answer` | `page-content.css` |
| Logo wall | `.z4-logo-wall` / `.z4-client-logo` | `page-home.css` |
| Related card | `.z7-related-card` (in `.z7-related-grid`) | `page-content.css` |
| Case study hero | `.brand-hero` / `.brand-hero-image` / `.brand-hero-content` | `page-content.css` |
| Case study metric | `.brand-metric-value` / `.brand-metric-label` | `page-content.css` |

## Spacing & rhythm

- **Container max:** `var(--container)` = 1400px (or `--container-wide` = 1600px)
- **Container padding:** `var(--container-padding)` = 24px desktop, 16px mobile
- **Section vertical padding:** 80px top/bottom desktop, 48px mobile
- **Heading rhythm:** display font, generous line-height (1.1 for h1, 1.25 for h2, 1.4 for h3)

## Animation tokens

- `--ease-out: cubic-bezier(0.16, 1, 0.3, 1)` — standard motion
- `--ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1)` — playful overshoot

## Responsive breakpoints

- Mobile: `< 768px`
- Tablet: `768px – 1024px`
- Desktop: `>= 1024px`
- Wide: `>= 1400px` (rarely needed)

## Patterns to keep consistent

- **Buttons** always pair: a primary + an outline. Never two primaries side-by-side.
- **Hero CTAs** appear in Z2 and Z8 — the second is a closer for users who scrolled past the first.
- **Z7 related-content cards** are bidirectional — each linked page should also list the linker as a sibling.
- **No utility classes** — extend a zone's vocabulary in the CSS, don't sprinkle `mt-4 px-2` style helpers.
- **Component classes are flat** — no BEM. `.z3-service-card`, not `.service-card__header`.

## Forbidden patterns

- Inline `style="..."` attributes (except where unavoidable for dynamic background images)
- `!important` (except in service-worker offline overrides)
- Tailwind, CSS-in-JS, or any preprocessor — this is plain CSS
- New CSS variables without adding them to `BRAND.md`
