---
description: Purge the Cloudflare cache for the site after a deploy
user-invocable: true
---

# /purge-cache — Cloudflare cache purge

Purge the Cloudflare cache so a fresh deploy is immediately visible.

## Usage

`/purge-cache` — purge everything
`/purge-cache <url>` — purge a single URL
`/purge-cache <url1> <url2> ...` — purge a list

## Instructions

1. **Read credentials** from `.env`:
   - `CLOUDFLARE_ZONE_ID`
   - `CLOUDFLARE_API_TOKEN`

   If missing, abort with a clear message: "Add `CLOUDFLARE_ZONE_ID` and `CLOUDFLARE_API_TOKEN` to `.env` first. Token needs `Zone.Cache Purge` permission."

2. **Build the API call**:

   For full purge:
   ```bash
   curl -s -X POST \
     "https://api.cloudflare.com/client/v4/zones/$CLOUDFLARE_ZONE_ID/purge_cache" \
     -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{"purge_everything": true}'
   ```

   For specific URLs:
   ```bash
   curl -s -X POST \
     "https://api.cloudflare.com/client/v4/zones/$CLOUDFLARE_ZONE_ID/purge_cache" \
     -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
     -H "Content-Type: application/json" \
     --data '{"files": ["https://mulligans-grille.com/page1/", "https://mulligans-grille.com/page2/"]}'
   ```

3. **Parse response** for `success: true`. If false, surface the error message.

4. **Verify**: after a successful purge, hit one of the purged URLs to confirm fresh content:
   ```bash
   curl -sI https://mulligans-grille.com/<page>/ | grep -E "cf-cache-status|HTTP"
   ```
   `cf-cache-status: MISS` confirms the cache was cleared.

## When to use

- Immediately after a `git pull` on Cloudways
- When HTML content changes don't appear within 60s of deploy
- After updating `sitemap.xml` or `robots.txt` (these are often heavily cached)

## When *not* to use

- Don't full-purge on every deploy — burns origin bandwidth. Prefer specific URLs.
- Don't use as a substitute for proper cache headers — if you find yourself purging the same URLs daily, fix the `Cache-Control` instead.
