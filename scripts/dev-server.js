#!/usr/bin/env node
/**
 * dev-server.js — Local static-file dev server with .htaccess-style URL rewriting.
 *
 * Replicates the production Apache rewriting rules:
 *   - /slug          → /slug/index.html (clean URLs)
 *   - /slug/         → /slug/index.html
 *   - missing path   → /404.html (200 status preserved for crawlers? no — 404)
 *   - HTML: no caching; everything else: ETag default
 *
 * Project-specific 301 redirects can be added to LEGACY_REDIRECTS below to
 * mirror entries in .htaccess so local dev matches production behavior.
 *
 * Usage:
 *   node scripts/dev-server.js [port]
 *
 * Default port: 5173.
 * No npm dependencies — uses only node: built-ins.
 */

import { createServer } from "node:http";
import { readFile, stat } from "node:fs/promises";
import { extname, join, resolve, normalize } from "node:path";

const PORT = Number(process.argv[2]) || 5173;
const ROOT = resolve(".");

// Mirror project-specific .htaccess RewriteRule entries here.
// Format: { from: "old-slug", to: "/new-slug/" }
const LEGACY_REDIRECTS = [
  // { from: "old-slug", to: "/new-slug/" },
];

const MIME = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".js": "application/javascript; charset=utf-8",
  ".json": "application/json; charset=utf-8",
  ".xml": "application/xml; charset=utf-8",
  ".txt": "text/plain; charset=utf-8",
  ".svg": "image/svg+xml",
  ".png": "image/png",
  ".jpg": "image/jpeg",
  ".jpeg": "image/jpeg",
  ".webp": "image/webp",
  ".gif": "image/gif",
  ".ico": "image/x-icon",
  ".woff": "font/woff",
  ".woff2": "font/woff2",
  ".pdf": "application/pdf",
  ".php": "text/html; charset=utf-8",
  ".mp4": "video/mp4",
  ".webm": "video/webm",
  ".mov": "video/quicktime",
};

function mimeFor(p) {
  return MIME[extname(p).toLowerCase()] || "application/octet-stream";
}

async function tryFile(p) {
  try {
    const s = await stat(p);
    if (s.isFile()) return p;
  } catch {}
  return null;
}

async function resolveRequest(urlPath) {
  // Strip query string
  const clean = urlPath.split("?")[0];

  // Legacy redirects
  const slug = clean.replace(/^\/|\/$/g, "");
  for (const r of LEGACY_REDIRECTS) {
    if (slug === r.from) return { redirect: r.to };
  }

  // Root
  if (clean === "/" || clean === "") {
    return { file: join(ROOT, "index.html") };
  }

  // Normalize and prevent path traversal
  const safe = normalize(clean).replace(/^(\.\.[\/\\])+/, "");
  const target = join(ROOT, safe);

  // Direct file hit
  const direct = await tryFile(target);
  if (direct) return { file: direct };

  // Directory with index.html
  const indexHtml = await tryFile(join(target, "index.html"));
  if (indexHtml) {
    if (!clean.endsWith("/")) return { redirect: clean + "/" };
    return { file: indexHtml };
  }

  // Slug-as-html fallback (/foo → foo.html)
  const asHtml = await tryFile(target + ".html");
  if (asHtml) return { file: asHtml };

  return { file: null };
}

const server = createServer(async (req, res) => {
  try {
    const result = await resolveRequest(req.url);

    if (result.redirect) {
      res.writeHead(301, { Location: result.redirect });
      res.end();
      return;
    }

    if (!result.file) {
      const fallback = await tryFile(join(ROOT, "404.html"));
      if (fallback) {
        const body = await readFile(fallback);
        res.writeHead(404, {
          "Content-Type": "text/html; charset=utf-8",
          "Cache-Control": "no-cache",
        });
        res.end(body);
      } else {
        res.writeHead(404, { "Content-Type": "text/plain" });
        res.end("404 Not Found");
      }
      return;
    }

    const body = await readFile(result.file);
    const type = mimeFor(result.file);
    const headers = {
      "Content-Type": type,
      "Access-Control-Allow-Origin": "*",
    };
    if (type.startsWith("text/html")) {
      headers["Cache-Control"] = "no-cache, no-store, must-revalidate";
    }
    res.writeHead(200, headers);
    res.end(body);
  } catch (err) {
    res.writeHead(500, { "Content-Type": "text/plain" });
    res.end("500 " + err.message);
  }
});

server.listen(PORT, () => {
  console.log(`Dev server running at http://localhost:${PORT}/`);
  console.log(`Serving from ${ROOT}`);
  console.log(`Edit LEGACY_REDIRECTS in scripts/dev-server.js to mirror .htaccess`);
});
