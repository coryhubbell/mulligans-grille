---
description: Show the site's URL structure as a tree, derived from sitemap.xml
user-invocable: true
---

# /sitemap — Site Structure Reference

Display the project's URL structure derived from `sitemap.xml`.

## Instructions

1. Read `sitemap.xml`
2. Extract all `<loc>` URLs
3. Group by tier based on `<priority>`:
   - 1.0: Homepage
   - 0.9: Top services / category indexes
   - 0.8: Portfolio / case studies
   - 0.7: Content pages (about, FAQ)
   - 0.5: Blog / news posts
4. Print as an indented tree, with each URL annotated by its `<lastmod>` date

## Example output

```
Mulligan's Grille — site map
==========================

Homepage (1.0)
  / [2026-05-07]

Services (0.9)
  /service-one/ [2026-05-01]
  /service-two/ [2026-04-22]

Portfolio (0.8)
  /case-study-1/ [2026-03-15]
  /case-study-2/ [2026-02-28]

Content (0.7)
  /about/ [2026-01-10]
  /faq/ [2026-01-10]

Total: 7 pages
```

If the sitemap is empty or only has the homepage entry, prompt the user to scaffold pages with `/new-page`, `/new-service`, or `/new-case-study`.
