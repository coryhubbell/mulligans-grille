/**
 * Mulligan's Grille - Service Worker
 * Cache-first strategy for static assets, network-first for HTML
 */

const CACHE_VERSION = 'site-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

// Assets to pre-cache on install
// Update font filenames + logo paths as they get added to the project.
const PRECACHE_ASSETS = [
  '/',
  '/assets/css/shared.css',
  '/assets/css/deferred.css',
  '/assets/images/favicon-32x32.png',
  '/assets/images/apple-touch-icon.png',
  '/manifest.json',
  '/offline.html'
];

// Install event - pre-cache static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => {
        console.log('[ServiceWorker] Pre-caching static assets');
        return cache.addAll(PRECACHE_ASSETS.filter(url => url !== '/offline.html'))
          .catch((error) => {
            console.log('[ServiceWorker] Pre-cache failed for some assets:', error);
            // Continue installation even if some assets fail
            return Promise.resolve();
          });
      })
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((cacheName) => cacheName.startsWith('site-') && cacheName !== STATIC_CACHE && cacheName !== RUNTIME_CACHE)
            .map((cacheName) => {
              console.log('[ServiceWorker] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            })
        );
      })
      .then(() => self.clients.claim())
  );
});

// Fetch event - cache-first for assets, network-first for HTML
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Only handle same-origin requests
  if (url.origin !== location.origin) {
    return;
  }

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Determine caching strategy based on request type
  if (isStaticAsset(url.pathname)) {
    // Cache-first for static assets (CSS, JS, fonts, images)
    event.respondWith(cacheFirst(request));
  } else if (isHTMLRequest(request)) {
    // Network-first for HTML pages
    event.respondWith(networkFirst(request));
  }
});

/**
 * Check if pathname is a static asset
 */
function isStaticAsset(pathname) {
  return /\.(css|js|woff2?|ttf|otf|eot|png|jpe?g|gif|svg|webp|ico)(\?.*)?$/i.test(pathname) ||
         pathname.startsWith('/assets/');
}

/**
 * Check if request is for HTML
 */
function isHTMLRequest(request) {
  const acceptHeader = request.headers.get('Accept') || '';
  return acceptHeader.includes('text/html') || request.url.endsWith('/');
}

/**
 * Cache-first strategy
 * Returns cached response if available, otherwise fetches from network
 */
async function cacheFirst(request) {
  const cachedResponse = await caches.match(request);

  if (cachedResponse) {
    // Return cached response and update cache in background
    updateCache(request);
    return cachedResponse;
  }

  // Not in cache, fetch from network and cache
  try {
    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(RUNTIME_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('[ServiceWorker] Fetch failed:', error);
    return new Response('Offline', { status: 503 });
  }
}

/**
 * Network-first strategy
 * Tries network first, falls back to cache if offline
 */
async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request);

    if (networkResponse.ok) {
      // Cache successful responses
      const cache = await caches.open(RUNTIME_CACHE);
      cache.put(request, networkResponse.clone());
    }

    return networkResponse;
  } catch (error) {
    // Network failed, try cache
    const cachedResponse = await caches.match(request);

    if (cachedResponse) {
      return cachedResponse;
    }

    // No cache, return offline fallback for HTML requests
    const offlinePage = await caches.match('/offline.html');
    if (offlinePage) {
      return offlinePage;
    }

    // Ultimate fallback
    return new Response('Offline - Page not available', {
      status: 503,
      headers: { 'Content-Type': 'text/html' }
    });
  }
}

/**
 * Update cache in background (stale-while-revalidate pattern)
 */
async function updateCache(request) {
  try {
    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(RUNTIME_CACHE);
      cache.put(request, networkResponse);
    }
  } catch (error) {
    // Silently fail - we already have cached version
  }
}
