# Development

## Prerequisites

- Node.js 20+ (only used by `scripts/lint-schema.js` and `scripts/dev-server.js` — no `npm install` needed)
- macOS or Linux shell (BSD or GNU sed both work)
- PHP 8+ if you want to test `api/contact.php` locally

## Local dev server

```bash
node scripts/dev-server.js
# → http://localhost:5173/
```

Or use the `/dev` skill in Claude Code.

The server replicates production URL rewriting (`/slug/` → `<slug>/index.html`) and serves `404.html` for missing paths. Mirror project-specific 301s by editing `LEGACY_REDIRECTS` in `scripts/dev-server.js`.

## Creating a new page

The fast path:

```
/new-page <slug> <archetype>
```

Where archetype is one of: `service`, `case-study`, `content`. The skill copies `_archetypes/<archetype>/index.html` to `<slug>/index.html`, replaces page-specific placeholders, and registers the page in `sitemap.xml` + adds breadcrumb structured data.

For specialized presets:

- `/new-service <slug>` — service page with pricing block
- `/new-case-study <slug>` — prompts for client/challenge/approach/results/metrics

The manual path:

1. `mkdir <slug>`
2. `cp _archetypes/content/index.html <slug>/index.html`
3. Replace `{{PAGE_SLUG}}`, `{{PAGE_TITLE}}`, `{{PAGE_DESCRIPTION}}`
4. Add a `<url>` block to `sitemap.xml`
5. Update breadcrumbs on related pages to link back

## Page-creation checklist

- [ ] HTML uses correct archetype as starting point
- [ ] All `{{PLACEHOLDER}}` markers replaced
- [ ] `<title>`, `<meta description>`, `<link canonical>` are unique to this page
- [ ] OG + Twitter card meta tags filled
- [ ] BreadcrumbList JSON-LD present
- [ ] Primary content schema present (Service / Article / etc.)
- [ ] Z7 related-content section has 3–4 reciprocal sibling links
- [ ] Sitemap entry added with appropriate priority + lastmod
- [ ] If a service: pricing block reflects current rates
- [ ] If a case study: hero image is 1920×1080+, OG image is 1200×630
- [ ] `/schema-lint` passes
- [ ] `/audit-links` reports the new page is reciprocally linked

## Image preparation

Before adding images:

```bash
# Generate WebP from PNG/JPG (requires cwebp)
cwebp -q 80 image.png -o image.png.webp
cwebp -q 80 photo.jpg -o photo.jpg.webp
```

Or use the `/og-image` skill to generate a 1200×630 social card from a title.

Both originals and WebP variants must be committed. The `<picture>` element fetches WebP first, falls back to the original.

## Editing CSS

- Add zone-scoped rules to `assets/css/page-<type>.css`
- Add cross-page tokens / utilities to `assets/css/shared.css`
- Add below-fold / non-critical rules to `assets/css/deferred.css`
- **Never** add inline `<style>` blocks to HTML except for dynamic background images
- **Never** introduce a CSS preprocessor — this is plain CSS by design

## Schema validation

```bash
node scripts/lint-schema.js
```

Or `/schema-lint`. Fails CI if any JSON-LD block doesn't parse.

## Common gotchas

- **Trailing slash matters** — `/services/` is canonical, `/services` 301-redirects (Apache rule). The dev server mirrors this.
- **`.htaccess` caching is aggressive** — CSS/JS get 1-year `immutable`. When you edit them in production, append a version query string (`?v=20260507`) to the `<link>` href so browsers fetch the new file.
- **Service worker is retired** — `sw.js` is a self-destruct shim that unregisters any existing SW and purges its caches, and `main.js` no longer calls `register()`. HTML/CSS/JS cache freshness is driven entirely by `.htaccess` headers + Cloudflare/Varnish.
- **Cloudflare email obfuscation** — if you see `[email&nbsp;protected]` instead of an address, Cloudflare is mangling it. Disable Email Address Obfuscation in Scrape Shield.
