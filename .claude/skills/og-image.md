---
description: Generate a 1200x630 Open Graph social-card image from a title and accent color
user-invocable: true
---

# /og-image — OG image generator

Generate a 1200×630 Open Graph image from a title.

## Usage

`/og-image <slug> "<title>"`

## Instructions

Generate an SVG-based OG image with:
- 1200×630 canvas
- Brand background color from `--brand-purple-dim` (read from `assets/css/shared.css`)
- Brand wordmark or logo top-left (or top-center)
- Title text, display font, white, large, centered or left-aligned
- Domain in small text bottom-right

Approach options:

1. **SVG → PNG via headless rendering**: write an SVG file, then convert with a node tool (`sharp`, `playwright`, or even a `<canvas>` script). Output `assets/images/<slug>-og.jpg`.
2. **Pure SVG with rasterization fallback**: save the SVG, instruct the user to convert via online tool or CLI.
3. **Manual fallback**: produce a fully-formed SVG file at `assets/images/<slug>-og.svg` and tell the user to rasterize via Figma / Photopea / `rsvg-convert`.

If `sharp` is not installed (no `node_modules/`), default to option 3 — produce the SVG, give exact rasterization instructions:

```bash
# macOS, requires librsvg via brew install librsvg
rsvg-convert -w 1200 -h 630 -f png assets/images/<slug>-og.svg > assets/images/<slug>-og.jpg
```

## Templates

Use a clean two-line layout:

```
┌─────────────────────────────────────────┐
│  [logo]                                  │
│                                          │
│       <Title goes here, large,           │
│        wraps to 2 lines if needed>       │
│                                          │
│                                          │
│                          mulligans-grille.com      │
└─────────────────────────────────────────┘
```

After generation, suggest the user update the page's `og:image` and `twitter:image` meta tags if they don't already point at the right path.
