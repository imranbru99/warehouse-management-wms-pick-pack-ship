const CACHE = 'wms-rf-v1';
const SHELL = ['/rf', '/manifest.webmanifest'];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(SHELL)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(event.request).then((cached) => cached || fetch(event.request).catch(() => caches.match('/rf')))
  );
});

self.addEventListener('sync', (event) => {
  if (event.tag === 'rf-scan-sync') {
    event.waitUntil(Promise.resolve());
  }
});
