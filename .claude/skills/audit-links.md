---
description: Verify Z7-related-card reciprocity across all case studies and content pages
user-invocable: true
---

# /audit-links — Internal link reciprocity audit

Verify that every Z7-related-card link is reciprocal.

## What "reciprocal" means

If page A's `.z7-related-grid` contains a card linking to page B, then page B's `.z7-related-grid` should also contain a card linking back to A.

Why: bidirectional internal linking is the SEO/LLM-discovery pattern that surfaces clusters of related work. Broken reciprocity creates dead-end pages that crawlers under-rank.

## Instructions

1. **Find all HTML files** containing a `.z7-related-grid`:
   ```bash
   grep -l 'z7-related-grid' --include="*.html" -r .
   ```

2. **For each such file**, extract the URLs in its `.z7-related-grid` `<a href>` attributes.

3. **Build a directed graph**: source page → list of target URLs.

4. **For each edge A → B**, check whether B → A also exists in the graph.

5. **Report**:
   - Total edges (one-way links)
   - Reciprocal pairs (A ↔ B both directions)
   - Orphan edges (A → B with no B → A)
   - For each orphan: suggest adding a card on B's Z7 grid linking to A

## Output format

```
Z7 Link Audit
=============

Pages with Z7 grids: N
Total outbound edges: M
Reciprocal: P pairs
Orphan: Q edges

Orphans (A → B but not B → A):
  /case-study-1/ → /case-study-2/   (suggest: add card on /case-study-2/ linking to /case-study-1/)
  /service-x/ → /case-study-3/      (suggest: add card on /case-study-3/ linking to /service-x/)
  ...

Run with --fix to auto-add reciprocal cards (NOT YET IMPLEMENTED — add by hand for now).
```

## When to use

- Before every deploy (auto-runs as part of `deploy-runner` pre-flight)
- After scaffolding a new case study (the new page typically has its own outbound Z7 cards but isn't yet referenced from siblings)
- During a quarterly content audit
