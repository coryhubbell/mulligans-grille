# TASKS.md — Build Checklist

Work top to bottom. Each numbered item is a discrete commit-worthy task.

## Phase 1 — Scaffold (15 min)

- [ ] 1. Create project folder. Suggested structure:
  ```
  mulligans-grille/
  ├── index.html
  ├── styles.css
  ├── script.js
  ├── robots.txt
  ├── sitemap.xml
  ├── menu.pdf            (placeholder — owner to provide)
  └── assets/
      ├── og-image.jpg    (placeholder 1200×630)
      ├── hero.jpg        (placeholder dark amber gradient)
      └── favicon.svg     (placeholder simple M monogram)
  ```
- [ ] 2. Set up `index.html` skeleton with the meta tags from `SEO.md` in `<head>`.
- [ ] 3. Add Google Fonts preconnect + stylesheet link (Bebas Neue, Source Serif 4, Inter Tight — see `DESIGN.md`).
- [ ] 4. Drop CSS custom property block from `DESIGN.md` into top of `styles.css`. Set base body styles (font-family, background, color, smooth scroll).

## Phase 2 — Layout (60–90 min)

- [ ] 5. Build sticky top nav. Transparent on hero, gains `--bg` background on scroll past 80px. Mobile: hamburger that toggles a full-screen overlay menu (vanilla JS, no library).
- [ ] 6. Build Hero section. Full content from `CONTENT.md` § Section 1. Background: dark image with amber-tinted gradient overlay until owner provides photography. Two CTAs.
- [ ] 7. Build About section. Two-paragraph body + optional 3-up stat strip.
- [ ] 8. Build Menu Highlights section. 6-card grid (1 col mobile → 2 col @ 640px → 3 col @ 960px). Each card: category title in Bebas, 2-line serif description, optional icon.
- [ ] 9. Build Hours & Location section. Two-column layout (address+hours on left, Google Maps embed on right) on desktop; stacked on mobile. Highlight the current day of the week in `--amber` via small JS snippet that compares to `new Date().getDay()`.
- [ ] 10. Build Events section. 4-card grid, same responsive breakpoints as menu cards.
- [ ] 11. Build Footer. Three-column desktop, stacked mobile. Include social links and bottom legal bar.

## Phase 3 — Interactions & polish (45 min)

- [ ] 12. Smooth-scroll anchor links (`scroll-behavior: smooth` + JS fallback for older Safari).
- [ ] 13. Intersection Observer for fade-up reveals on section headings.
- [ ] 14. Hero entrance animation (staggered fade-up).
- [ ] 15. `prefers-reduced-motion: reduce` media query — disable all transitions and animations.
- [ ] 16. Active-day highlighting in the hours table (small JS).
- [ ] 17. Click-to-call (`tel:`) and click-to-email (`mailto:`) on all phone/email mentions.

## Phase 4 — SEO & metadata (15 min)

- [ ] 18. Paste JSON-LD Restaurant schema from `SEO.md` before `</body>`.
- [ ] 19. Verify lat/long in JSON-LD against the Google Maps embed (Claude Code: geocode `3830 S Nova Rd, Port Orange, FL 32127` if uncertain).
- [ ] 20. Create `robots.txt` and `sitemap.xml` from `SEO.md`.
- [ ] 21. Add `alt` text to every image. Add `aria-label` to every icon-only button.

## Phase 5 — QA (30 min)

- [ ] 22. Test at 375px (iPhone SE), 768px (iPad), 1024px (laptop), 1440px (desktop). No horizontal scroll at any width.
- [ ] 23. Test keyboard navigation: tab through every interactive element, visible focus rings everywhere.
- [ ] 24. Test screen reader on the hero + nav.
- [ ] 25. Run Lighthouse (mobile preset). Target: Performance ≥90, Accessibility ≥95, Best Practices ≥95, SEO ≥95. Fix anything below.
- [ ] 26. Validate HTML at validator.w3.org. Zero errors.
- [ ] 27. Verify all external links open in a new tab with `rel="noopener noreferrer"`.

## Phase 6 — Deploy (15 min)

- [ ] 28. Push to a repo (GitHub or GitLab).
- [ ] 29. Connect Netlify / Cloudflare Pages / Vercel for static deploy.
- [ ] 30. Point `mulligans-grille.com` DNS at the deploy. Verify SSL provisions cleanly (don't repeat the previous owner's mistake).
- [ ] 31. Submit sitemap to Google Search Console.
- [ ] 32. Set up a redirect or DNS for the old `mulligansgrille.com` if we control it — point to the new site.

## Phase 7 — Hand back to owner

- [ ] 33. Send a 1-page summary of what was built, what needs their input (see `OPEN_QUESTIONS.md`), and instructions for swapping in real photography and the final menu PDF.
