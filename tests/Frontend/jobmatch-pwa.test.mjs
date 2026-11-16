import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

const source = fs.readFileSync(new URL('../../public/jobmatch-sw.js', import.meta.url), 'utf8');
function worker(fetchImpl = async () => new Response('online')) {
    const listeners = {};
    const stored = new Map();
    const writes = [];
    const deleted = [];
    const cache = {
        async add(request) { writes.push(request.url); stored.set(request.url, new Response('static')); },
        async match(url) { return stored.get(url)?.clone(); },
        async put(url, response) { writes.push(url); stored.set(url, response); }
    };
    const caches = {
        async open() { return cache; },
        async match(url) { return stored.get(url); },
        async keys() { return ['wcc-jobmatch-static-v12', 'wcc-jobmatch-static-v20', 'unrelated-app-cache']; },
        async delete(key) { deleted.push(key); }
    };
    vm.runInNewContext(source, {URL, Request, Response, Set, Promise, caches, fetch: fetchImpl, self: {
        location: new URL('https://example.test/jobmatch-sw.js'), async skipWaiting() {}, clients: {async claim() {}},
        addEventListener(name, handler) { listeners[name] = handler; }
    }});
    return {
        writes, deleted, stored,
        async lifecycle(name) { const tasks = []; listeners[name]({waitUntil(p) { tasks.push(p); }}); await Promise.all(tasks); },
        async request(url, options = {}) {
            let result;
            const tasks = [];
            listeners.fetch({request: {url, method: 'GET', mode: 'cors', ...options}, respondWith(p) { result = p; }, waitUntil(p) { tasks.push(p); }});
            const response = result ? await result : undefined;
            await Promise.all(tasks);
            return response;
        }
    };
}

test('installation caches only the generic offline document, not account pages or assets', async () => {
    const app = worker(); await app.lifecycle('install');
    assert(app.writes.some(url => url.endsWith('/offline.html')));
    assert.deepEqual(app.writes, ['https://example.test/offline.html']);
    assert(app.writes.every(url => !/portal|dashboard|reports|login|resume/.test(url)));
});
test('cleanup removes superseded JobMatch static caches and preserves unrelated caches', async () => {
    const app = worker(); await app.lifecycle('activate');
    assert.deepEqual(app.deleted, ['wcc-jobmatch-static-v12', 'wcc-jobmatch-static-v20']);
});
test('account navigation is fetched but never written to a cache', async () => {
    const app = worker(async () => new Response('private record'));
    const response = await app.request('https://example.test/portal/profile', {mode: 'navigate'});
    assert.equal(await response.text(), 'private record'); assert.equal(app.writes.length, 0);
});
test('offline account navigation shows static fallback without saved account data', async () => {
    const app = worker(async () => { throw new Error('offline'); });
    await app.lifecycle('install'); app.writes.length = 0;
    const response = await app.request('https://example.test/portal/profile', {mode: 'navigate'});
    assert.equal(await response.text(), 'static'); assert.equal(app.writes.length, 0);
});
test('mutations and cross-origin requests are not intercepted', async () => {
    const app = worker(() => { throw new Error('should not fetch'); });
    assert.equal(await app.request('https://example.test/portal/profile', {method: 'POST'}), undefined);
    assert.equal(await app.request('https://maps.example.test/tile.png'), undefined);
});
test('private downloads, CSV exports and API calls are not intercepted', async () => {
    const app = worker(() => { throw new Error('should not fetch'); });
    for (const path of ['/portal/resume/download', '/portal/reports', '/api/user']) {
        assert.equal(await app.request('https://example.test' + path), undefined);
    }
});
test('static asset requests stay with the browser and do not populate the offline cache', async () => {
    const app = worker(async () => new Response('new css'));
    const response = await app.request('https://example.test/css/jobmatch-stitch.css?v=11');
    assert.equal(response, undefined);
    assert.deepEqual(app.writes, []);
});
test('login redirects are never stored as a static asset', async () => {
    const response = new Response('login'); Object.defineProperty(response, 'redirected', {value: true});
    const app = worker(async () => response);
    await app.request('https://example.test/css/jobmatch-stitch.css'); assert.equal(app.writes.length, 0);
});
test('offline static assets are not replaced with an HTML fallback', async () => {
    const app = worker(async () => { throw new Error('offline'); }); await app.lifecycle('install');
    const response = await app.request('https://example.test/css/jobmatch-stitch.css?v=11');
    assert.equal(response, undefined);
    assert.deepEqual(app.writes, ['https://example.test/offline.html']);
});
test('manifest has real square PNG icons, same-origin start URL and standalone mode', () => {
    const manifest = JSON.parse(fs.readFileSync(new URL('../../public/manifest.webmanifest', import.meta.url)));
    assert.equal(manifest.display, 'standalone');
    assert.equal(new URL(manifest.start_url, 'https://example.test/').origin, 'https://example.test');
    for (const icon of manifest.icons) {
        const bytes = fs.readFileSync(new URL('../../public/' + icon.src, import.meta.url));
        const [width, height] = icon.sizes.split('x').map(Number);
        assert.equal(bytes.readUInt32BE(16), width); assert.equal(bytes.readUInt32BE(20), height);
        assert.equal(width, height); assert.equal(icon.type, 'image/png');
    }
});

const clientSource = fs.readFileSync(new URL('../../public/js/jobmatch-current.js', import.meta.url), 'utf8').split('// Device integration.')[1];
function appClient({secure = true, ios = false, standalone = false, offlineAttribute = false} = {}) {
    const events = {};
    const buttonEvents = {};
    const message = {textContent: ''};
    const dialog = {open: false, querySelector() { return message; }, querySelectorAll() { return []; }, addEventListener() {}, showModal() { this.open = true; }, close() { this.open = false; }};
    const button = {hidden: false, addEventListener(name, fn) { buttonEvents[name] = fn; }};
    const registrations = [];
    vm.runInNewContext(clientSource, {
        URL,
        navigator: {userAgent: ios ? 'iPhone' : 'Chrome', platform: ios ? 'iPhone' : 'Linux', maxTouchPoints: 1, serviceWorker: {register(...args) { registrations.push(args); return Promise.resolve(); }}},
        window: {location: new URL('https://example.test/dashboard'), isSecureContext: secure, matchMedia() { return {matches: standalone, addEventListener() {}}; }, addEventListener(name, fn) { (events[name] ??= []).push(fn); }},
        document: {querySelector(selector) { if (selector === 'link[rel="manifest"]') return {href: 'https://example.test/manifest.webmanifest'}; if (selector === 'script[data-jobmatch-offline]') return offlineAttribute ? {getAttribute() { return '/jobmatch-sw.js?v=offline-21'; }} : null; return selector === '[data-install-dialog]' ? dialog : null; }, querySelectorAll() { return [button]; }}
    });
    return {events, button, buttonEvents, dialog, message, registrations, emit(name, event) { for (const fn of events[name] ?? []) fn(event); }, registration: () => registrations.at(-1)};
}
test('installation prompt is shown only after the user chooses Install', async () => {
    const app = appClient(); let prompted = 0; let prevented = false;
    app.emit('beforeinstallprompt', {preventDefault() { prevented = true; }, async prompt() { prompted++; }, userChoice: Promise.resolve({outcome: 'dismissed'})});
    assert(prevented); assert.equal(prompted, 0);
    await app.buttonEvents.click(); assert.equal(prompted, 1);
});
test('installed apps hide the Install action', () => {
    const app = appClient({standalone: true}); assert.equal(app.button.hidden, true);
});
test('insecure phone addresses receive setup help without registering a service worker', async () => {
    const app = appClient({secure: false}); await app.buttonEvents.click();
    assert(app.message.textContent.includes('HTTPS')); assert(app.dialog.open); assert.equal(app.events.load, undefined);
});
test('iPhone install help explains Safari Add to Home Screen', async () => {
    const app = appClient({ios: true}); await app.buttonEvents.click();
    assert(app.message.textContent.includes('Safari')); assert(app.message.textContent.includes('Add to Home Screen'));
});
test('service worker registration is scoped to the same application', () => {
    const app = appClient(); app.emit('load');
    assert.equal(app.registration()[0], 'https://example.test/jobmatch-sw.js'); assert.equal(app.registration()[1].scope, '/');
});


test('versioned offline worker registers once when both integration blocks are present', () => {
    const app = appClient({offlineAttribute: true});
    app.emit('load');
    assert.equal(app.registrations.length, 1);
    assert.equal(app.registration()[0], 'https://example.test/jobmatch-sw.js?v=offline-21');
    assert.equal(app.registration()[1].scope, '/');
});
test('appinstalled hides Install and closes an open help dialog', async () => {
    const app = appClient();
    await app.buttonEvents.click();
    assert.equal(app.dialog.open, true);
    app.emit('appinstalled');
    assert.equal(app.button.hidden, true);
    assert.equal(app.dialog.open, false);
});
test('a rejected install prompt opens browser help without breaking navigation', async () => {
    const app = appClient();
    app.emit('beforeinstallprompt', {preventDefault() {}, async prompt() { throw new Error('blocked'); }});
    await app.buttonEvents.click();
    assert.equal(app.dialog.open, true);
    assert.match(app.message.textContent, /browser menu/);
});
