# Health Check — Pre-Deploy Gate

Run before every meaningful push to `main`. Skip individual checks only when you have a reason.

## Code

- [ ] `node scripts/lint-schema.js` exits 0 (or `/schema-lint`)
- [ ] No console errors when loading the site in the dev server
- [ ] No new files committed accidentally (`git status` clean)
- [ ] No secrets in committed files (`grep -r "POSTMARK_TOKEN\|CLOUDFLARE_API_TOKEN" .` returns only `.env.example`)

## Content

- [ ] Every page has a unique `<title>`
- [ ] Every page has a unique `<meta description>` (120–160 chars)
- [ ] Every page has a `<link canonical>` matching its URL
- [ ] OG image renders correctly when sharing (test in https://www.opengraph.xyz/)

## Internal linking

- [ ] `/audit-links` reports complete Z7 reciprocity
- [ ] No 404s in internal links: `curl` each newly-introduced URL
- [ ] Breadcrumbs match the URL structure

## Sitemap & feed

- [ ] New pages added to `sitemap.xml`
- [ ] `<lastmod>` updated on changed pages (or run `/bump-sitemap`)
- [ ] If a news/blog post: added to `feed.xml`

## Performance

- [ ] PageSpeed Insights: 90+ Performance on mobile (https://pagespeed.web.dev/)
- [ ] LCP < 2.5s
- [ ] FCP < 1.5s
- [ ] CLS < 0.1
- [ ] No render-blocking CSS that should be in `deferred.css`

## Deploy hygiene

- [ ] Commit message describes *why*, not just *what*
- [ ] Push to `main` (or open a PR if working on a branch)
- [ ] If automated deploy is wired up, watch the Action complete
- [ ] If manual: SSH + `git pull` on Cloudways, verify with `curl`
- [ ] `/purge-cache` if Cloudflare is in front and changes don't appear in 60s

## Post-deploy

- [ ] Visit the changed page on production, confirm new content
- [ ] Check sitemap: `curl https://mulligans-grille.com/sitemap.xml | grep <new-slug>`
- [ ] If a notable update: ping IndexNow via `api/indexnow.php`
- [ ] Check Cloudflare Analytics for any spike in 4xx/5xx
- [ ] Check console.log on the live page (open DevTools, reload)

## Quarterly

- [ ] Review Cloudflare settings against `.beads/PRIME.md` "Edge & Origin Settings"
- [ ] Run security review: `/security-review` or audit `.htaccess` CSP
- [ ] Update font files if upstream foundry has new versions
- [ ] Rebuild OG default image if branding changed
