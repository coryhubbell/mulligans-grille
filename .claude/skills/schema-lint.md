---
description: Validate every JSON-LD block on the site parses as JSON
user-invocable: true
---

# /schema-lint — JSON-LD validation

Run the schema lint check.

## Instructions

```bash
node scripts/lint-schema.js
```

Output the result. If it exits 0, report the file count and block count cleanly:

> ✓ Linted N HTML files, M JSON-LD blocks, 0 invalid.

If it exits 1, surface the failures:

- File path (relative to repo root)
- Block index within the file
- Error message from `JSON.parse`

For each failure, suggest the most likely cause (trailing comma, unbalanced brace, smart quotes, unescaped quote) based on the error.

After fixing failures, re-run automatically to confirm green.

## Use proactively

Run `/schema-lint` automatically:
- After any agent (especially `schema-architect`) modifies JSON-LD
- Before `/deploy` (the `deploy-runner` agent runs this in pre-flight)
- After any large content drop where multiple pages were edited
