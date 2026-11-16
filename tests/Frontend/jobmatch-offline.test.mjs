import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

const workerSource = fs.readFileSync(new URL('../../public/jobmatch-sw.js', import.meta.url), 'utf8');
const uiSource = fs.readFileSync(new URL('../../public/js/jobmatch-current.js', import.meta.url), 'utf8');
function worker({online = true, missingFallback = false} = {}) {
    const listeners = {};
    const entries = new Map();
    const removed = [];
    let networkRequests = 0;
    let claimed = false;
    let skipped = false;
    const cache = {
        async add(request) { if (!missingFallback) entries.set(request.url, new Response('Offline fallback only')); },
        async match(url) { return entries.get(url)?.clone(); }
    };
    const context = {
        URL, Request, Response,
        self: {
            location: new URL('https://example.test/jobmatch/jobmatch-sw.js?v=offline-21'),
            addEventListener(name, callback) { listeners[name] = callback; },
            async skipWaiting() { skipped = true; },
            clients: {async claim() { claimed = true; }}
        },
        caches: {
            async open() { return cache; },
            async keys() { return ['wcc-jobmatch-static-v1', 'wcc-jobmatch-offline-v20', 'unrelated-cache', 'wcc-jobmatch-offline-v21']; },
            async delete(name) { removed.push(name); return true; }
        },
        async fetch() { networkRequests++; if (!online) throw new Error('offline'); return new Response('PRIVATE ACCOUNT RESPONSE'); }
    };
    vm.runInNewContext(workerSource, context);
    async function lifecycle(name) { let work; listeners[name]({waitUntil(promise) { work = promise; }}); await work; }
    async function navigate({method = 'GET', mode = 'navigate', url = 'https://example.test/jobmatch/portal/profile'} = {}) {
        let response; listeners.fetch({request: {method, mode, url}, respondWith(promise) { response = promise; }}); return response ? await response : null;
    }
    return {entries, removed, lifecycle, navigate, get networkRequests() { return networkRequests; }, get claimed() { return claimed; }, get skipped() { return skipped; }};
}

test('installation stores only the self-contained offline HTML under a subdirectory', async () => {
    const state = worker(); await state.lifecycle('install');
    assert.deepEqual([...state.entries.keys()], ['https://example.test/jobmatch/offline.html']);
    assert.equal(state.skipped, true);
});
test('activation replaces owned old caches and leaves unrelated caches intact', async () => {
    const state = worker(); await state.lifecycle('activate');
    assert.deepEqual(state.removed.sort(), ['wcc-jobmatch-offline-v20', 'wcc-jobmatch-static-v1']);
    assert.equal(state.claimed, true);
});
test('online private navigation returns network content without caching it', async () => {
    const state = worker(); await state.lifecycle('install');
    assert.equal(await (await state.navigate()).text(), 'PRIVATE ACCOUNT RESPONSE');
    assert.equal(state.entries.size, 1);
    assert.equal(state.entries.has('https://example.test/jobmatch/portal/profile'), false);
});
test('offline private navigation serves only the generic fallback', async () => {
    const state = worker({online: false}); await state.lifecycle('install');
    assert.equal(await (await state.navigate()).text(), 'Offline fallback only');
    assert.equal(state.entries.size, 1);
});
test('POST, API fetches and other origins are not intercepted', async () => {
    const state = worker();
    assert.equal(await state.navigate({method: 'POST'}), null);
    assert.equal(await state.navigate({mode: 'cors'}), null);
    assert.equal(await state.navigate({url: 'https://other.test/'}), null);
    assert.equal(state.networkRequests, 0);
});
test('missing fallback responds with a readable 503 instead of a private page', async () => {
    const state = worker({online: false, missingFallback: true});
    const response = await state.navigate(); assert.equal(response.status, 503); assert.match(await response.text(), /offline/i);
});
function registration({secure = true, support = true, origin = 'https://example.test', workerUrl = 'https://example.test/jobmatch/jobmatch-sw.js?v=offline-21'} = {}) {
    const listeners = {}; const registrations = [];
    const context = {
        URL,
        window: {isSecureContext: secure, location: {href: origin + '/jobmatch/dashboard', origin}, addEventListener(name, callback) { listeners[name] = callback; }},
        navigator: support ? {serviceWorker: {register(url, options) { registrations.push({url, scope: options.scope}); return Promise.resolve(); }}} : {},
        document: {querySelector(selector) { return selector === 'script[data-jobmatch-offline]' ? {getAttribute() { return workerUrl; }} : null; }}
    };
    // Exercise only the standalone registration appended to the existing UI file.
    vm.runInNewContext(uiSource.slice(uiSource.indexOf('// Offline fallback registration;')), context);
    listeners.load?.();
    return registrations;
}
test('registration uses the configured same-origin worker and application scope', () => {
    assert.deepEqual(registration(), [{url: 'https://example.test/jobmatch/jobmatch-sw.js?v=offline-21', scope: '/jobmatch/'}]);
});
test('registration does not use an insecure context, unsupported browser or another origin', () => {
    assert.equal(registration({secure: false}).length, 0);
    assert.equal(registration({support: false}).length, 0);
    assert.equal(registration({workerUrl: 'https://other.test/jobmatch-sw.js'}).length, 0);
});
