/* Dashboard SuperAdmin: kontrol tab (patuh CSP: tanpa inline script / onclick). */
(function () {
    'use strict';

    Array.prototype.forEach.call(document.querySelectorAll('select[data-autosubmit]'), function (el) {
        el.addEventListener('change', function () { if (el.form) { el.form.submit(); } });
    });

    var STORAGE_KEY = 'magkom_superadmin_active_tab';
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.dashboard-tab-bar .tab-btn[data-tab]'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('.tab-content-panel'));

    if (!tabs.length) { return; }

    function names() {
        return tabs.map(function (t) { return t.getAttribute('data-tab'); });
    }

    function activate(name, focus) {
        if (names().indexOf(name) === -1) { return; }

        panels.forEach(function (p) { p.classList.toggle('active', p.id === 'tab-' + name); });
        tabs.forEach(function (t) {
            var on = t.getAttribute('data-tab') === name;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
            t.setAttribute('tabindex', on ? '0' : '-1');
            if (on && focus) { t.focus(); }
        });

        try { window.localStorage.setItem(STORAGE_KEY, name); } catch (e) { /* storage dibatasi */ }
    }

    tabs.forEach(function (t, i) {
        t.addEventListener('click', function () { activate(t.getAttribute('data-tab')); });
        t.addEventListener('keydown', function (e) {
            var next = null;
            if (e.key === 'ArrowRight') { next = tabs[(i + 1) % tabs.length]; }
            if (e.key === 'ArrowLeft') { next = tabs[(i - 1 + tabs.length) % tabs.length]; }
            if (next) { e.preventDefault(); activate(next.getAttribute('data-tab'), true); }
        });
    });

    Array.prototype.forEach.call(document.querySelectorAll('[data-tab-link]'), function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            activate(a.getAttribute('data-tab-link'));
            var bar = document.querySelector('.dashboard-tab-bar');
            if (bar && bar.scrollIntoView) { bar.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });

    var initial = window.location.hash.replace('#', '');
    if (names().indexOf(initial) === -1) {
        initial = null;
        try { initial = window.localStorage.getItem(STORAGE_KEY); } catch (e) { /* abaikan */ }
    }
    activate(names().indexOf(initial) !== -1 ? initial : names()[0]);
})();
