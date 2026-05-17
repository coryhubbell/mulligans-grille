#!/usr/bin/env node
/**
 * lint-schema.js — Validate every <script type="application/ld+json"> block
 * in the site's HTML files parses as valid JSON.
 *
 * Catches the trailing-comma / bad-escape / unbalanced-brace class of bug
 * before it ships. CI runs this on every push and PR to main.
 *
 * Usage:
 *   node scripts/lint-schema.js [root]
 *
 * Exits 0 on success, 1 on any invalid JSON-LD, 2 on script error.
 * No npm dependencies — uses only node: built-ins.
 */

import { readdir, readFile } from "node:fs/promises";
import { join, resolve, relative } from "node:path";

const ROOT = resolve(process.argv[2] || ".");

// Top-level directories that don't ship HTML for crawlers.
const SKIP_DIRS = new Set([
  "node_modules",
  ".git",
  ".github",
  ".beads",
  ".claude",
  "assets",
  "api",
  "scripts",
]);

const JSON_LD_RE =
  /<script\b[^>]*type=["']application\/ld\+json["'][^>]*>([\s\S]*?)<\/script>/gi;

async function* walkHtml(dir) {
  const entries = await readdir(dir, { withFileTypes: true });
  for (const e of entries) {
    const full = join(dir, e.name);
    if (e.isDirectory()) {
      if (SKIP_DIRS.has(e.name)) continue;
      yield* walkHtml(full);
    } else if (e.isFile() && e.name.endsWith(".html")) {
      yield full;
    }
  }
}

async function lintFile(file) {
  const html = await readFile(file, "utf8");
  const failures = [];
  let blocks = 0;
  let m;
  JSON_LD_RE.lastIndex = 0;
  while ((m = JSON_LD_RE.exec(html))) {
    blocks++;
    try {
      JSON.parse(m[1]);
    } catch (err) {
      failures.push({ blockIndex: blocks, error: err.message });
    }
  }
  return { file, blocks, failures };
}

async function main() {
  const results = [];
  for await (const file of walkHtml(ROOT)) {
    results.push(await lintFile(file));
  }

  let totalBlocks = 0;
  let totalFailures = 0;
  let filesWithFailures = 0;
  for (const r of results) {
    totalBlocks += r.blocks;
    if (r.failures.length) {
      filesWithFailures++;
      totalFailures += r.failures.length;
      const rel = relative(ROOT, r.file);
      for (const f of r.failures) {
        console.error(`::error file=${rel}::JSON-LD block ${f.blockIndex} invalid: ${f.error}`);
      }
    }
  }

  const summary =
    `Linted ${results.length} HTML files, ${totalBlocks} JSON-LD blocks, ` +
    `${totalFailures} invalid across ${filesWithFailures} file(s).`;

  if (totalFailures > 0) {
    console.error(`\n${summary}`);
    process.exit(1);
  }
  console.log(summary);
}

main().catch((err) => {
  console.error("lint-schema.js failed:", err);
  process.exit(2);
});
