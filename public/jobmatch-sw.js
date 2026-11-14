// Retire registrations and static caches from earlier releases.
self.addEventListener('install', event => {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', event => {
    event.waitUntil((async () => {
        const names = await caches.keys();
        await Promise.all(names.filter(name => name.startsWith('wcc-jobmatch-static-')).map(name => caches.delete(name)));
        await self.registration.unregister();
    })());
});
