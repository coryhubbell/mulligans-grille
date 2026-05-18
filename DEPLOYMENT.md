# Deployment

## Pipeline

```
Local dev  →  GitHub  →  Cloudways
   |             |            |
node dev-server  git push  git pull (via SSH or GitHub Action)
```

## One-time setup

### GitHub

1. Create the repo at {{GITHUB_REPO}}
2. Push the initial commit
3. (Optional, for automated deploy) Add these repo secrets:
   - `CLOUDWAYS_HOST` — server IP or hostname
   - `CLOUDWAYS_USER` — SSH user (typically the application's master user)
   - `CLOUDWAYS_KEY` — private SSH key (paired with a public key on the Cloudways server)
   - `CLOUDWAYS_PATH` — full path to the application's `public_html` (or wherever the document root lives)

### Cloudways

1. Create application: PHP / static (PHP is required if you keep `api/contact.php` or `api/indexnow.php`)
2. SSH into the server, navigate to the document root
3. `git clone {{GITHUB_REPO}} .` (the `.` puts files in the current dir, not a subdirectory)
4. Confirm `.htaccess` is honored: `httpd -M | grep rewrite_module` should show enabled. If `AllowOverride` is `None` higher up, contact Cloudways support.

### Cloudflare (recommended)

Settings that *must* hold (regression-prone):

| Setting | Required value | Why |
|---|---|---|
| Bot Fight Mode | **Off** | Otherwise GPTBot/ClaudeBot are blocked |
| Country challenge | Off (or scoped to non-SEO routes) | Hides site from international crawlers |
| Email Address Obfuscation (Scrape Shield) | **Off** | Otherwise team emails get XOR-encoded and disappear from LLM crawls |
| AI Crawl Control | GPTBot / ClaudeBot / PerplexityBot / Google-Extended **Allowed** | Same reason |
| HTML cache TTL | ≤ 1 hour (or use `/purge-cache` after deploys) | So updates propagate |

## Cache layers cheat sheet

There are **four** independent caches sitting in front of a request to `/`:

1. **Browser cache** — driven by `.htaccess` (`*.html` is `no-cache, no-store`; `*.css|*.js` is `max-age=31536000, immutable`).
2. **Cloudflare edge** — `cf-cache-status` should be `DYNAMIC` for HTML, `HIT` for assets. Purge from CF dashboard or the `/purge-cache` skill.
3. **Cloudways Varnish** — reports as `x-cache: HIT/MISS` with an `age:` header. Caches HTML by default. Purge from **Cloudways panel → Application → Application Settings → Purge Varnish Cache**, or via the `cloudways__app_purge_cache` MCP tool.
4. **Service worker** — **retired** (see `sw.js`). The file remains as a self-destruct shim for legacy clients; new visitors get no SW. Do not reintroduce a fetch-intercepting SW without solving cache versioning first — the previous one caused fresh HTML to load against stale CSS/JS and forced hard-refreshes on every deploy.

**Rule of thumb after every deploy:** Cloudways Pull Latest Code → purge Varnish → (optionally) purge Cloudflare. If you skip the Varnish purge, expect 1–30 minutes of stale HTML at the edge depending on Varnish TTL.

**Long-term option:** in the Cloudways app settings, exclude `*.html` and `/` from Varnish so deploys propagate without a manual purge step. Trade-off is a small TTFB hit on HTML.

## Per-deploy workflow

### Manual

```bash
# locally
git add .
git commit -m "Describe the change"
git push origin main

# on Cloudways
ssh {{CLOUDWAYS_USER}}@{{CLOUDWAYS_HOST}}
cd {{DOCUMENT_ROOT}}
git pull origin main
exit

# verify
curl -s -A "Mozilla/5.0" https://mulligans-grille.com/<changed-page> | grep "<title>"

# purge Cloudflare if needed
# (use the /purge-cache skill, or hit the Cloudflare API directly)
```

### Automated (GitHub Actions)

Rename `.github/workflows/deploy.yml.disabled` → `deploy.yml` after configuring repo secrets. Pushes to `main` then auto-deploy via SSH.

## Pre-deploy checks

Run before every push:

- [ ] `node scripts/lint-schema.js` — JSON-LD validates
- [ ] `/audit-links` — Z7-related-card reciprocity intact
- [ ] `node scripts/dev-server.js` and click through changed pages locally
- [ ] If touching `.htaccess`: invoke `htaccess-guardian` agent for safety review

See `HEALTH-CHECK.md` for the full pre-deploy gate.

## Rollback

```bash
# locally
git revert <bad-commit-sha>
git push origin main

# Cloudways auto-pulls (or pull manually)
```

Avoid `git reset --hard` on `main` once the bad commit has been pushed; revert is safer because it leaves a clear audit trail.
