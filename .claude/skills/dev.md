---
description: Start the local development server with clean URLs on port 5173
user-invocable: true
---

# /dev — Local Development Server

Start the local development server.

## What this does

Launches a Node.js server (`scripts/dev-server.js`) that:
- Runs on **port 5173**
- Replicates `.htaccess` URL rewriting (clean URLs like `/portfolio/` → `portfolio/index.html`)
- Handles project-specific 301 redirects (configured in `LEGACY_REDIRECTS` inside `scripts/dev-server.js`)
- Serves `404.html` for missing pages
- No caching on HTML (always fresh)
- CORS enabled for local debugging

## Instructions

Run this in the background:

```bash
node scripts/dev-server.js
```

Then output to the user:

**Dev server running at http://localhost:5173/**

Suggest a few starting URLs based on what's in the project's `sitemap.xml`:
- http://localhost:5173/ (Homepage)
- (additional service or content pages from sitemap)
- http://localhost:5173/contact/

If the user is mid-task on a specific page, link directly to that page.
