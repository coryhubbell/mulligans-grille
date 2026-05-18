# Mulligan's Grille — Worker Context

## Project Identity

- **Client:** Mulligan's Grille
- **Domain:** mulligans-grille.com
- **Host:** Cloudways (static-site app)
- **Stack:** Cloudflare → nginx (Cloudways edge) → Apache → static HTML in document root
- **GitHub:** {{GITHUB_REPO}}
- **Status:** Active deployment

## Critical Constraints

These constraints are NON-NEGOTIABLE. Violating them will cause performance degradation or broken functionality.

1. **Static HTML deployment — no CMS**
   - Pages are plain HTML files served directly from the document root
   - No WordPress, no Yoast, no Elementor, no plugins
   - Repo is the source of truth; the production filesystem mirrors it

2. **`.htaccess` is committed and authoritative**
   - Repo `/.htaccess` contains the security headers, caching rules, and 301s
   - Do not let server-level `.htaccess` (above the document root) shadow these — request Cloudways support to keep the AllowOverride scope wide

3. **Pre-generate WebP locally before deploy**
   - All HTML references both `.png` and `.png.webp` siblings; both must exist on disk
   - No image optimization plugin runs on the server

4. **Schema validation is a release gate**
   - `scripts/lint-schema.js` runs on every push/PR via `.github/workflows/schema-lint.yml`
   - Broken JSON-LD blocks the merge

## Deployment Protocol

1. Edit HTML in repo (locally or via Claude Code)
2. Commit + push to `main` on {{GITHUB_REPO}}
3. Either:
   - Manual: SSH to Cloudways and `git pull` in the deploy path
   - Automated: re-enable `.github/workflows/deploy.yml.disabled` (requires GitHub secrets — see `DEPLOYMENT.md`)
4. Verify: `curl -A "Mozilla/5.0..." https://mulligans-grille.com/<changed-page>` returns the new bytes
5. Purge Cloudflare cache if HTML changes don't appear within 60s (use `/purge-cache` skill)

## Performance Targets

| Metric | Target |
|--------|--------|
| PageSpeed Performance | 90+ |
| LCP | < 2.5s |
| FCP | < 1.5s |
| TBT | < 200ms |
| CLS | < 0.1 |

## Edge & Origin Settings (must hold)

**Cloudflare:**

- Bot Fight Mode: **Off** (a regression of this on a similar project once hid an entire site from Googlebot/GPTBot/ClaudeBot)
- Country challenge: **Off** for SEO/LLM crawl traffic
- Security Level: Essentially Off or Low
- Email Address Obfuscation (Scrape Shield): **Off** (otherwise team emails are XOR-encoded and invisible to LLMs)
- AI Crawl Control: GPTBot, ClaudeBot, PerplexityBot, Google-Extended **Allowed**

**Cloudways:**

- Application → Bot Protection: ensure ClaudeBot and Amazonbot are NOT blocked (default list blocks them)
- Application → Varnish/Breeze: HTML cache TTL ≤ 1 hour so deploys propagate quickly

## Key Files

- **Repo root HTML:** `index.html` plus `<slug>/index.html` directories per page
- **Deploy config:** `.htaccess` (Apache headers/caching/redirects), `.github/workflows/deploy.yml.disabled`
- **LLM/AI strategy:** `llms.txt`, `feed.xml`, `sitemap.xml`, `robots.txt`
- **Service worker:** retired. `sw.js` is a self-destruct shim (unregisters + purges caches on activate); `main.js` does not register a SW. Cache freshness is owned by `.htaccess` + Cloudflare/Varnish.
- **Schema validator:** `scripts/lint-schema.js`

## Propulsion Principle

> If you find work on your hook, YOU RUN IT.

Execute tasks immediately without waiting for confirmation. Work is not done until pushed.
