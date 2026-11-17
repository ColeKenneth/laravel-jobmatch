// Cache only the self-contained offline fallback, never account pages or API data.
const OFFLINE_CACHE = 'wcc-jobmatch-offline-v21';
const OFFLINE_URL = new URL('offline.html', self.location.href).href;

self.addEventListener('install', event => {
    event.waitUntil((async () => {
        const cache = await caches.open(OFFLINE_CACHE);
        await cache.add(new Request(OFFLINE_URL, {cache: 'reload'}));
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', event => {
    event.waitUntil((async () => {
        const names = await caches.keys();
        await Promise.all(names.filter(name => name !== OFFLINE_CACHE &&
            (name.startsWith('wcc-jobmatch-offline-') || name.startsWith('wcc-jobmatch-static-')))
            .map(name => caches.delete(name)));
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', event => {
    const request = event.request;
    if (request.method !== 'GET' || request.mode !== 'navigate' ||
        new URL(request.url).origin !== self.location.origin) return;
    event.respondWith((async () => {
        try {
            return await fetch(request);
        } catch {
            const cache = await caches.open(OFFLINE_CACHE);
            return await cache.match(OFFLINE_URL) || new Response('You are offline. Reconnect and try again.', {
                status: 503, headers: {'Content-Type': 'text/plain; charset=utf-8'}
            });
        }
    })());
});
