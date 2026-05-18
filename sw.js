/**
 * Mulligan's Grille — Service Worker (self-destruct)
 *
 * The previous SW pre-cached /, shared.css, page-home.css, main.js with a
 * cache-first strategy on static assets. That caused fresh HTML to load
 * referencing stale CSS/JS — a "flash of old site" on every visit until
 * users hard-refreshed. SW is retired in favor of standard browser + CDN
 * caching driven by .htaccess headers.
 *
 * This file stays in place (do not delete) so existing clients with the
 * old SW installed can fetch this script during their next update check,
 * activate it, unregister themselves, and purge their caches. New visits
 * never reach this file because main.js no longer calls register().
 *
 * Once production telemetry shows no remaining controlled clients (give it
 * a few weeks), this file can be removed.
 */

self.addEventListener('install', (event) => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const cacheNames = await caches.keys();
    await Promise.all(cacheNames.map((name) => caches.delete(name)));
    await self.registration.unregister();
    await self.clients.claim();
    const clients = await self.clients.matchAll({ type: 'window' });
    for (const client of clients) {
      try { client.navigate(client.url); } catch (_) { /* noop */ }
    }
  })());
});
