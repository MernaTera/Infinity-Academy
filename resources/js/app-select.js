/* =========================================================
   APP SELECT — global dropdown enhancer
   Turns every <select> in the system into the same styled
   pill-control + popup-panel used by the Leads status dropdown.

   - Zero markup changes needed on existing pages.
   - The real <select> stays in the DOM and keeps working:
     select.value, onchange="", required, name="" for forms,
     window.location=this.value, etc. all keep working as-is.
   - Works with selects added dynamically after page load
     (AJAX-populated dropdowns, JS-generated form rows, modals).

   Opt a specific <select> out with: data-no-enhance
   ========================================================= */
(function () {
    const CHEVRON_SVG =
        '<svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>';
    const CHECK_SVG =
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" ' +
        'stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';

    function closeAllPanels() {
        document.querySelectorAll('.app-select__panel.is-open').forEach(function (p) {
            p.classList.remove('is-open');
        });
        document.querySelectorAll('.app-select__control.is-open').forEach(function (c) {
            c.classList.remove('is-open');
        });
    }

    function buildSelect(select) {
        if (!select || select.tagName !== 'SELECT') return;
        if (select.dataset.appSelectInit) return;
        if (select.hasAttribute('data-no-enhance')) return;
        if (select.multiple) return; // native multi-selects are left as-is
        select.dataset.appSelectInit = '1';

        const wrap = document.createElement('div');
        wrap.className = 'app-select';
        if (select.disabled) wrap.classList.add('app-select--disabled');

        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(select);
        select.classList.add('app-select__native');
        select.setAttribute('tabindex', '-1');
        select.setAttribute('aria-hidden', 'true');

        const control = document.createElement('div');
        control.className = 'app-select__control';
        control.setAttribute('role', 'button');
        control.tabIndex = select.disabled ? -1 : 0;

        const label = document.createElement('span');
        label.className = 'app-select__label';

        const chevron = document.createElement('span');
        chevron.className = 'app-select__chevron';
        chevron.innerHTML = CHEVRON_SVG;

        control.appendChild(label);
        control.appendChild(chevron);
        wrap.appendChild(control);

        const panel = document.createElement('div');
        panel.className = 'app-select__panel';
        panel.setAttribute('role', 'listbox');
        // appended to <body> (not to wrap) so it can never be clipped by
        // a parent card/section with overflow:hidden or a low z-index
        document.body.appendChild(panel);

        let activeIndex = -1;

        function positionPanel() {
            const rect = control.getBoundingClientRect();
            const panelHeight = panel.offsetHeight;
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            const openUpward = spaceBelow < panelHeight + 12 && spaceAbove > spaceBelow;
            const top = openUpward ? rect.top - panelHeight - 6 : rect.bottom + 6;

            panel.style.left = rect.left + 'px';
            panel.style.width = rect.width + 'px';
            panel.style.top = Math.max(8, top) + 'px';
        }

        function renderOptions() {
            panel.innerHTML = '';
            // Skip options hidden by page logic (e.g. rooms filtered by
            // Online/Offline mode). A hidden <option> must not appear in the
            // custom panel — otherwise the styled dropdown shows choices the
            // native select is hiding.
            const options = Array.from(select.options).filter(function (o) {
                return !o.hidden && o.style.display !== 'none';
            });

            if (!options.length) {
                const empty = document.createElement('div');
                empty.className = 'app-select__panel-empty';
                empty.textContent = 'No options';
                panel.appendChild(empty);
                return;
            }

            options.forEach(function (opt) {
                const idx  = opt.index; // real index in select.options
                const item = document.createElement('div');
                item.className = 'app-select__item';
                item.setAttribute('role', 'option');
                if (opt.disabled) item.classList.add('is-disabled');
                if (idx === select.selectedIndex) item.classList.add('is-selected');

                const text = document.createElement('span');
                text.className = 'app-select__item-text';
                text.textContent = opt.textContent;

                const check = document.createElement('span');
                check.className = 'app-select__item-check';
                check.innerHTML = CHECK_SVG;

                item.appendChild(text);
                item.appendChild(check);

                if (!opt.disabled) {
                    item.addEventListener('click', function (e) {
                        e.stopPropagation();
                        if (select.selectedIndex !== idx) {
                            select.selectedIndex = idx;
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        closePanel();
                        control.focus();
                    });
                }

                panel.appendChild(item);
            });
        }

        function syncLabel() {
            const opt = select.options[select.selectedIndex];
            label.textContent = opt ? opt.textContent : '';
            label.classList.toggle('is-placeholder', !!opt && opt.value === '');
            renderOptions();
        }

        function openPanel() {
            if (select.disabled) return;
            closeAllPanels();
            panel.classList.add('is-open');
            control.classList.add('is-open');
            positionPanel();
            activeIndex = select.selectedIndex;
        }

        function closePanel() {
            panel.classList.remove('is-open');
            control.classList.remove('is-open');
        }

        function togglePanel(e) {
            if (select.disabled) return;
            e.stopPropagation();
            if (panel.classList.contains('is-open')) closePanel();
            else openPanel();
        }

        control.addEventListener('click', togglePanel);

        control.addEventListener('keydown', function (e) {
            const items = Array.from(panel.querySelectorAll('.app-select__item:not(.is-disabled)'));
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                togglePanel(e);
            } else if (e.key === 'Escape') {
                closePanel();
            } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!panel.classList.contains('is-open')) {
                    openPanel();
                    return;
                }
                const dir = e.key === 'ArrowDown' ? 1 : -1;
                const currentIdx = items.findIndex(function (i) { return i.classList.contains('is-active'); });
                let nextIdx = currentIdx + dir;
                if (nextIdx < 0) nextIdx = items.length - 1;
                if (nextIdx >= items.length) nextIdx = 0;
                items.forEach(function (i) { i.classList.remove('is-active'); });
                if (items[nextIdx]) {
                    items[nextIdx].classList.add('is-active');
                    items[nextIdx].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Tab') {
                closePanel();
            }
        });

        control.addEventListener('focus', function () { control.classList.add('is-focus'); });
        control.addEventListener('blur', function () { control.classList.remove('is-focus'); });

        // keep the widget in sync if some page script changes options/value/disabled directly
        select.addEventListener('change', syncLabel);

        // Re-render when options are added/removed AND when their attributes
        // change (hidden/disabled/style) — e.g. mode filtering rooms. Without
        // `subtree`+`attributes` the panel wouldn't update when a page script
        // just hides an <option> instead of removing it.
        const optsObserver = new MutationObserver(syncLabel);
        optsObserver.observe(select, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['hidden', 'disabled', 'style'],
            characterData: true,
        });

        const attrObserver = new MutationObserver(function () {
            wrap.classList.toggle('app-select--disabled', select.disabled);
            control.tabIndex = select.disabled ? -1 : 0;
        });
        attrObserver.observe(select, { attributes: true, attributeFilter: ['disabled'] });

        select._appSelectRefresh = syncLabel;
        syncLabel();
    }

    function enhanceAll(root) {
        (root || document).querySelectorAll('select').forEach(buildSelect);
    }

    // patch the native `value` / `selectedIndex` setters so that ANY page
    // script setting select.value = '...' keeps the custom widget in sync,
    // without needing to touch existing code across the app
    ['value', 'selectedIndex'].forEach(function (prop) {
        const desc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, prop);
        if (!desc || !desc.set) return;
        Object.defineProperty(HTMLSelectElement.prototype, prop, {
            get: desc.get,
            configurable: true,
            set: function (v) {
                desc.set.call(this, v);
                if (this._appSelectRefresh) this._appSelectRefresh();
            },
        });
    });

    document.addEventListener('click', closeAllPanels);

    // scrolling anywhere (except inside the open panel's own option list)
    // or resizing the window closes the panel, since it's fixed-positioned
    // and no longer tied to its control's scroll container
    document.addEventListener(
        'scroll',
        function (e) {
            if (e.target && e.target.closest && e.target.closest('.app-select__panel')) return;
            closeAllPanels();
        },
        true
    );
    window.addEventListener('resize', closeAllPanels);

    // form.reset() doesn't go through the value/selectedIndex setters above
    document.addEventListener(
        'reset',
        function (e) {
            if (e.target && e.target.tagName === 'FORM') {
                setTimeout(function () {
                    e.target.querySelectorAll('select').forEach(function (s) {
                        if (s._appSelectRefresh) s._appSelectRefresh();
                    });
                }, 0);
            }
        },
        true
    );

    document.addEventListener('DOMContentLoaded', function () {
        enhanceAll();

        const bodyObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.tagName === 'SELECT') buildSelect(node);
                    if (node.querySelectorAll) node.querySelectorAll('select').forEach(buildSelect);
                });
            });
        });
        bodyObserver.observe(document.body, { childList: true, subtree: true });
    });

    window.AppSelect = {
        enhance: enhanceAll,
        refresh: function (select) {
            if (select && select._appSelectRefresh) select._appSelectRefresh();
        },
    };
})();