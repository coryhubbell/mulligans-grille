# Mulligan's Grille — Brand Standards

## Identity

- **Name:** Mulligan's Grille
- **Tagline:** Take a mulligan. Wings, burgers, cold beer — and a do-over whenever you need one.
- **Domain:** mulligans-grille.com
- **Owners:** Tracy & Bobby Parks (took over 2026)
- **Theme:** Golf-themed neighborhood grill in Port Orange, FL. The mulligan = a do-over. Imagery leans on golf balls, clubs, putting greens, fairway green, with a neon-sign / sports-bar nighttime mood layered on top. **Not Irish-themed** despite the name — the name comes from the golf mulligan (David Mulligan / Buddy Mulligan, 1920s–30s).
- **Audiences:** Locals (lunch, neighborhood regulars), families (kids' coloring menu), bar crowd (live music, Bobby Friss Band), Bike Week visitors (Daytona Beach is 15 min away).

## Color Tokens

Defined in `assets/css/shared.css` as `:root` CSS custom properties.

| Token | Value | Use |
|---|---|---|
| `--bg` | `#ffffff` | Page background (matches the print menu) |
| `--bg-elevated` | `#f6f3ea` | Alternating cream-tinted section |
| `--bg-soft` | `#ecf2ec` | Faint green tint, hover states |
| `--bg-dark` | `#0e1a10` | Hero / footer / events — dark brick / forest-shadow |
| `--bg-darker` | `#061008` | Deepest accent under hero |
| `--text` | `#1a1a1a` | Body copy on white |
| `--text-on-dark` | `#f5f5f0` | Body copy on dark sections |
| `--text-muted` | `#5a5a5a` | Secondary copy on white |
| `--text-muted-on-dark` | `#b8bcb0` | Secondary copy on dark |
| `--green` | `#3FB54A` | Primary green — wordmark stroke, links, accent |
| `--green-deep` | `#1C6F36` | Forest green — headings, buttons, shadows |
| `--green-neon` | `#2BE863` | Hero neon glow only |
| `--purple-neon` | `#A855F7` | Hero / neon accent (sparing) |
| `--purple-deep` | `#6B21A8` | Deeper purple companion |
| `--ink` | `#0a0a0a` | High-contrast headlines |
| `--border` | `rgba(28,111,54,0.16)` | Card borders, dividers on white |
| `--border-dark` | `rgba(245,245,240,0.12)` | Dividers on dark sections |

Use neon greens and purples **only in the hero**. The rest of the site is print-menu green-on-white: forest green for headings/buttons, lime green for accents, dark green-tinted borders.

## Typography

| Role | Font | Stack | Use |
|---|---|---|---|
| Script wordmark | Sacramento | `var(--font-script)` | Hero H1, story H2, footer wordmark — anywhere the "Mulligan's Grille" handwritten feel is wanted |
| Display | Oswald | `var(--font-display)` | Caps section headings (H2/H3 in menu, events, location) |
| Body | Inter | `var(--font-body)` | Paragraphs, nav, buttons, fine print |

Loaded from Google Fonts (`display=swap`). Apply the `.script` class to override the all-caps Oswald default when you want the script feel.

### Type scale (mobile-first, fluid)

```css
--text-xs:        0.8125rem;
--text-sm:        0.9375rem;
--text-base:      1.0625rem;
--text-lg:        1.25rem;
--text-xl:        clamp(1.5rem, 2.5vw, 2rem);
--text-2xl:       clamp(2rem, 4vw, 3rem);
--text-3xl:       clamp(2.5rem, 5.5vw, 4rem);
--text-hero:      clamp(3rem, 9vw, 7rem);
--text-script-xl: clamp(3.5rem, 10vw, 8rem);
```

## Visual Motifs

- **Script wordmark** with light-green + forest-green dual-stroke underline (the printed-menu signature)
- **Golf ball + MG monogram** in a green circle (logo lockup)
- **Neon sign** on dark brick (the iconic in-restaurant sign) — used as the hero background
- **Crossed golf clubs** (neon sign, mascot hats)
- **Putting-green flagstick** for wing-count divisions on the print menu
- **Retro cartoon golf-ball mascots** (boy in green shorts, girl in pink skirt) — family-friendly accent only

## Voice & Tone

- **Voice:** plainspoken, neighborhood, golf-pun-friendly. Not corporate, not Irish-themed kitsch.
- **Person:** First-person plural for the restaurant ("we"); second-person for the reader ("you").
- **Menu language:** lean into the puns — *Partee Starters*, *Club ✕ Wings*, *Hole-In-One Burger*, *In the Ruff*, *Goat Cheese Golf Balls*, *Mulligan's Special Sauce*. Don't strip the personality.
- **Avoid:** "Welcome to our restaurant," generic bistro language, shamrocks/leprechauns, "elevated dining."

## Motion

- Hero load-in: title fades up with green/purple neon glow; eyebrow, tagline, sub, and CTAs stagger 150ms apart
- Scroll reveals: section content fades up via IntersectionObserver (`.reveal` → `.is-visible`)
- No parallax. No autoplay video. No scroll-jacking.
- Respect `prefers-reduced-motion: reduce` — animations and transitions disabled.

## Accessibility

- Body copy contrast against `--bg`: AAA on white where possible
- Neon text on dark hero relies on the glow + drop-shadow stack — never use neon on white without re-checking contrast
- Visible focus rings on every interactive element — `outline: 2px solid var(--green)`
