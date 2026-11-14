// Only public, static assets are stored. Account pages and records stay online-only.
const CACHE_NAME = 'wcc-jobmatch-static-v13';
const ROOT = new URL('./', self.location.href);
const assetPaths = [
    'offline.html',
    'css/jobmatch-current.css',
    'css/jobmatch-stitch.css',
    'js/jobmatch-current.js',
    'js/jobmatch-app.js',
    'images/jobmatch/current/wcc-logo.png',
    'images/jobmatch/current/wcc-footer-transparent.png',
    'images/jobmatch/current/app-icon-192.png',
    'images/jobmatch/current/app-icon-512.png'
];
const staticUrls = new Set(assetPaths.map(path => new URL(path, ROOT).pathname));
self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(assetPaths.map(path => new URL(path, ROOT).href))));
});
self.addEventListener('activate', event => {
    event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key.startsWith('wcc-jobmatch-static-') && key !== CACHE_NAME).map(key => caches.delete(key)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;
    const url = new URL(event.request.url);
    if (url.origin !== ROOT.origin) return;
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match(new URL('offline.html', ROOT).href)));
        return;
    }
    if (!staticUrls.has(url.pathname)) return;
    event.respondWith(fetch(event.request).then(response => {
        // Pre-cached static assets are a fallback; never store a login redirect as CSS or JS.
        if (response.ok && !response.redirected) {
            const copy = response.clone();
            event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.put(new URL(url.pathname, ROOT.origin).href, copy)));
        }
        return response;
    }).catch(() => caches.match(new URL(url.pathname, ROOT.origin).href)));
});
