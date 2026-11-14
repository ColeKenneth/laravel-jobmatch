import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

const source = fs.readFileSync(new URL('../../public/js/jobmatch-current.js', import.meta.url), 'utf8');
function initialize({search = null, cards = [], empty = null, images = [], profile = null, reducedMotion = true, observer} = {}) {
    const listeners = {};
    const context = {
        document: {
            addEventListener(name, handler) { (listeners[name] ??= []).push(handler); },
            querySelector(selector) { if (selector === '[data-dashboard-search]') return search; if (selector === '[data-dashboard-empty]') return empty; return null; },
            querySelectorAll(selector) {
                if (selector === '[data-dashboard-job]') return cards;
                if (selector === '[data-image-fallback]') return images;
                if (selector === '[data-profile-form]') return profile ? [profile] : [];
                return [];
            }
        },
        window: {matchMedia() { return {matches: reducedMotion, addEventListener() {}}; }, addEventListener() {}},
        navigator: {},
        FormData: class { constructor(form) { this.form = form; } entries() { return Object.entries(this.form.values); } },
        requestAnimationFrame(fn) { fn(); },
        setTimeout() {},
        IntersectionObserver: observer
    };
    vm.runInNewContext(source, context);
    return listeners;
}

test('dashboard search matches titles and companies case-insensitively and shows an empty state', () => {
    let input;
    const search = {addEventListener(name, handler) { input = handler; }};
    const cards = [{dataset: {jobSearch: 'python developer sample company'}}, {dataset: {jobSearch: 'nurse health company'}}];
    const empty = {hidden: true};
    initialize({search, cards, empty});
    input({target: {value: '  PYTHON  '}});
    assert.equal(cards[0].hidden, false); assert.equal(cards[1].hidden, true); assert.equal(empty.hidden, true);
    input({target: {value: 'no matching job'}});
    assert.equal(empty.hidden, false);
    input({target: {value: ''}});
    assert(cards.every(card => !card.hidden)); assert.equal(empty.hidden, true);
});

test('password visibility toggles type and the accessible button label', () => {
    const input = {type: 'password'};
    const attributes = {};
    const button = {parentElement: {querySelector() { return input; }}, setAttribute(name, value) { attributes[name] = value; }};
    const listeners = initialize();
    const event = {target: {closest(selector) { return selector === '[data-password]' ? button : null; }}};
    listeners.click.forEach(handler => handler(event));
    assert.equal(input.type, 'text'); assert.equal(attributes['aria-label'], 'Hide password');
    listeners.click.forEach(handler => handler(event));
    assert.equal(input.type, 'password'); assert.equal(attributes['aria-label'], 'Show password');
});

test('broken images activate a local fallback without changing their source', () => {
    const classes = [];
    const image = {complete: true, naturalWidth: 0, parentElement: {classList: {add(name) { classes.push(name); }}}};
    initialize({images: [image]});
    assert.deepEqual(classes, ['image-failed']);
});

test('profile dirty feedback clears when values return to their original state', () => {
    const events = {};
    const state = {textContent: 'Review your details.'};
    const profile = {values: {first_name: 'Original'}, dataset: {}, querySelector() { return state; }, addEventListener(name, fn) { events[name] = fn; }};
    initialize({profile});
    profile.values.first_name = 'Updated'; events.input();
    assert.equal(profile.dataset.dirty, 'true'); assert.equal(state.textContent, 'Unsaved changes');
    profile.values.first_name = 'Original'; events.reset();
    assert.equal(profile.dataset.dirty, 'false'); assert.equal(state.textContent, 'Review your details.');
});

test('reduced-motion preference prevents scroll animation observers', () => {
    let created = 0;
    initialize({reducedMotion: true, observer: class { constructor() { created++; } }});
    assert.equal(created, 0);
});
