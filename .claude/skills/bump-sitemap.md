---
description: Update <lastmod> in sitemap.xml for pages changed in the current git diff
user-invocable: true
---

# /bump-sitemap — Refresh sitemap lastmod

Update `<lastmod>` dates in `sitemap.xml` for pages that changed since the last commit.

## Instructions

1. **Identify changed HTML files**:
   ```bash
   git diff --name-only HEAD
   git diff --cached --name-only
   ```
   Combine and dedupe. Filter to `*/index.html` paths only.

2. **Map each changed HTML to its URL**:
   - `index.html` → `/`
   - `<slug>/index.html` → `/<slug>/`

3. **Read `sitemap.xml`**, find each URL's `<url>` block, update its `<lastmod>` to today's date (YYYY-MM-DD format).

4. **For new pages not yet in the sitemap**: report them so the user can add a proper `<url>` block (with priority and changefreq).

5. **Save** the updated sitemap.

6. **Stage** the change for commit:
   ```bash
   git add sitemap.xml
   ```

## Output

- Number of URLs updated
- List of URLs missing from sitemap (if any) — prompt user to add via `/new-page` or manually
- Reminder: sitemap.xml change is staged but not committed; run `git commit` when ready

## Notes

- Don't update `<lastmod>` on the homepage just because *any* page changed — only when `index.html` itself is in the diff
- If `<changefreq>` is `weekly` for the homepage, that's fine — Google uses it as a hint
