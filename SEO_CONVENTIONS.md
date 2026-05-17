# SEO Conventions

The site is engineered for both classic search (Google) and LLM/AI-discovery (GPT, Claude, Perplexity, Gemini).

## JSON-LD layers

Every page should include **at minimum** Layer 1 + Layer 2. Pages with rich content add more.

### Layer 0: Place (locations only)
For local businesses — geographic context with `geo`, `containedInPlace`, Wikidata `sameAs`.

### Layer 1: WebSite + SearchAction
Sitewide. Declares the site as a `WebSite` with potential `SearchAction`. Goes on every page (or just the homepage if scoping is enforced via `@id`).

### Layer 2: Organization (or Organization + LocalBusiness)
The brand entity. `@id: "https://mulligans-grille.com/#organization"`. Other schema entities reference this `@id` instead of redefining.

### Layer 3: Service / Article / Product
The page's primary content type:
- Service pages → `Service`
- Case studies / portfolio entries → `Article` (or `CreativeWork`)
- Blog/news articles → `NewsArticle`
- FAQ pages → `FAQPage` with nested `Question`/`Answer`
- Team member pages → `Person`

### Layer 4: BreadcrumbList
Every non-homepage page. Three-level minimum: Home → Section → Page.

### Layer 5: DefinedTermSet (optional, for glossary-rich sites)
A controlled vocabulary of domain terms with `@id` anchors. Other schema can reference these (`knowsAbout: [{ "@id": "#term-x" }, …]`) for entity stacking.

## Validation gate

`scripts/lint-schema.js` validates every `<script type="application/ld+json">` block parses as JSON. CI runs it on every push/PR (`.github/workflows/schema-lint.yml`). Broken schema blocks the merge.

Common failure modes it catches:
- Trailing commas
- Unescaped quotes in strings
- Unbalanced braces
- Smart quotes from copy/paste

## Meta tag patterns

Every page must have:

```html
<title>{{Page Title}} · Mulligan's Grille</title>
<meta name="description" content="{{120–160 char description}}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://mulligans-grille.com/{{slug}}/">
<link rel="alternate" type="application/atom+xml" href="/feed.xml">

<!-- Open Graph -->
<meta property="og:type" content="article">  <!-- or "website" for homepage -->
<meta property="og:site_name" content="Mulligan's Grille">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:url" content="https://mulligans-grille.com/{{slug}}/">
<meta property="og:image" content="https://mulligans-grille.com/assets/images/og-default.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="...">
<meta name="twitter:description" content="...">
<meta name="twitter:image" content="https://mulligans-grille.com/assets/images/og-default.jpg">
```

## Sitemap tiers

`sitemap.xml` priorities:

| Tier | Priority | Changefreq | Pages |
|---|---|---|---|
| Homepage | 1.0 | weekly | `/` |
| Top services / category indexes | 0.9 | monthly | `/service-x/`, `/portfolio/` |
| Case studies / portfolio entries | 0.8 | yearly | `/<slug>/` |
| Content pages (about, FAQ) | 0.7 | monthly | `/about/`, `/faq/` |
| Blog/news posts | 0.5 | yearly | `/post-slug/` |

`<lastmod>` on every entry. Update via `/bump-sitemap` skill or by hand.

## Internal linking — Z7-related cards

Every case study, service page, and major content page should include a Z7 "related" section with 3–4 cards linking to siblings. **Reciprocity is mandatory:** if A links to B, B should link back to A.

Run `/audit-links` to check reciprocity before deploying.

## AI-crawler discoverability

`robots.txt` must explicitly allow:
- `GPTBot`, `ChatGPT-User`, `CCBot`
- `anthropic-ai`, `ClaudeBot`, `Claude-Web`
- `Amazonbot`, `Bytespider`, `cohere-ai`
- `Google-Extended`, `PerplexityBot`

`llms.txt` (root) provides a markdown summary specifically for LLM ingestion — brand identity, services, contact, sitemap pointer.

## Performance is SEO

- LCP < 2.5s
- CLS < 0.1
- Self-host fonts (no Google Fonts CDN — counts against LCP)
- WebP images with JPEG/PNG fallback
- Lazy-load below-fold images (`loading="lazy"`)
- `assets/css/deferred.css` is preloaded but applied async to avoid render-blocking
