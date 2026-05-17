# SEO.md — Ready-to-paste markup

Drop these into `<head>` and just before `</body>` respectively.

---

## Meta tags (paste into `<head>`)

```html
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="theme-color" content="#1a1410" />

<title>Mulligan's Grille — Wings, Burgers & Cold Beer in Port Orange, FL</title>
<meta name="description" content="Mulligan's Grille is Port Orange's casual American sports grill. Scratch-made soups and sauces, hand-cut steaks, award-winning wings and burgers. 3830 S Nova Rd. Open 7 days." />
<meta name="keywords" content="Mulligan's Grille, Port Orange restaurant, sports bar Port Orange, wings Port Orange, burgers Port Orange, Nova Road restaurant" />

<link rel="canonical" href="https://mulligans-grille.com/" />

<!-- Open Graph -->
<meta property="og:type" content="restaurant" />
<meta property="og:site_name" content="Mulligan's Grille" />
<meta property="og:title" content="Mulligan's Grille — Port Orange, FL" />
<meta property="og:description" content="Wings, burgers, cold beer, good people. Port Orange's casual American sports grill." />
<meta property="og:url" content="https://mulligans-grille.com/" />
<meta property="og:image" content="https://mulligans-grille.com/assets/og-image.jpg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:locale" content="en_US" />

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Mulligan's Grille — Port Orange, FL" />
<meta name="twitter:description" content="Wings, burgers, cold beer, good people. Open 7 days in Port Orange." />
<meta name="twitter:image" content="https://mulligans-grille.com/assets/og-image.jpg" />

<!-- Favicon set — generate from realfavicongenerator.net once a logo exists -->
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
```

---

## JSON-LD: Restaurant schema (paste before `</body>`)

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Restaurant",
  "name": "Mulligan's Grille",
  "image": "https://mulligans-grille.com/assets/og-image.jpg",
  "@id": "https://mulligans-grille.com/",
  "url": "https://mulligans-grille.com/",
  "telephone": "+1-386-788-3268",
  "priceRange": "$$",
  "servesCuisine": ["American", "Bar Food", "Sports Bar"],
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "3830 S Nova Rd, Ste B1",
    "addressLocality": "Port Orange",
    "addressRegion": "FL",
    "postalCode": "32127",
    "addressCountry": "US"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 29.1383,
    "longitude": -81.0042
  },
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Sunday"],
      "opens": "11:00",
      "closes": "23:00"
    },
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Friday", "Saturday"],
      "opens": "11:00",
      "closes": "01:00"
    }
  ],
  "sameAs": [
    "https://www.facebook.com/mulligansgrillepo/",
    "https://www.instagram.com/mulligansgrille/"
  ],
  "acceptsReservations": "False",
  "menu": "https://mulligans-grille.com/menu.pdf"
}
</script>
```

**Note on coordinates:** the lat/long above is approximate for the address. Claude Code should verify them via a quick geocode lookup before deploying, or pull from the Google Maps embed URL once configured.

---

## robots.txt

```
User-agent: *
Allow: /

Sitemap: https://mulligans-grille.com/sitemap.xml
```

## sitemap.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://mulligans-grille.com/</loc>
    <lastmod>2026-05-17</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>
```

Update `lastmod` to the actual launch date.
