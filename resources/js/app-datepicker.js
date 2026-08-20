/* =========================================================
   APP DATE — global date / datetime-local picker enhancer
   Turns every <input type="date"> and <input type="datetime-local">
   into a floating calendar picker with the same visual language
   as app-select (pill control, floating card, blue accent).

   - Zero markup changes needed on existing pages.
   - The real <input> stays in the DOM and keeps working:
     input.value, name="", required, onchange="", min/max, etc.
   - Works with inputs added dynamically after page load.

   Opt a specific input out with: data-no-enhance
   ========================================================= */
(function () {
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const WEEKDAYS = ['S','M','T','W','T','F','S'];

    const ICON_CAL =
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
        'stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/>' +
        '<path d="M16 2v4M8 2v4M3 10h18"/></svg>';
    const ICON_CLEAR =
        '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" ' +
        'stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
    const ICON_PREV =
        '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" ' +
        'stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>';
    const ICON_NEXT =
        '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" ' +
        'stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';

    function pad2(n) { return String(n).padStart(2, '0'); }

    // parse 'YYYY-MM-DD' / 'YYYY-MM-DDTHH:MM' into local-time parts
    // WITHOUT going through `new Date(string)` (avoids UTC/timezone shift bugs)
    function parseValue(value) {
        if (!value) return null;
        const [datePart, timePart] = value.split('T');
        const [y, m, d] = datePart.split('-').map(Number);
        if (!y || !m || !d) return null;
        let h = 0, min = 0;
        if (timePart) {
            const [hh, mm] = timePart.split(':').map(Number);
            h = hh || 0;
            min = mm || 0;
        }
        return { y, m, d, h, min };
    }

    function toDateValue(p) {
        return p.y + '-' + pad2(p.m) + '-' + pad2(p.d);
    }

    function toDateTimeValue(p) {
        return toDateValue(p) + 'T' + pad2(p.h) + ':' + pad2(p.min);
    }

    function sameDay(a, b) {
        return a && b && a.y === b.y && a.m === b.m && a.d === b.d;
    }

    function formatLabel(p, hasTime) {
        let s = p.d + ' ' + MONTHS_SHORT[p.m - 1] + ' ' + p.y;
        if (hasTime) s += ', ' + pad2(p.h) + ':' + pad2(p.min);
        return s;
    }

    function daysInMonth(y, m) {
        return new Date(y, m, 0).getDate(); // m is 1-based here
    }

    function closeAllPanels() {
        document.querySelectorAll('.app-date__panel.is-open').forEach(function (p) {
            p.classList.remove('is-open');
        });
        document.querySelectorAll('.app-date__control.is-open').forEach(function (c) {
            c.classList.remove('is-open');
        });
    }

    function buildDateInput(input) {
        if (!input || input.tagName !== 'INPUT') return;
        if (input.dataset.appDateInit) return;
        if (input.hasAttribute('data-no-enhance')) return;
        const isDateTime = input.type === 'datetime-local';
        if (input.type !== 'date' && !isDateTime) return;
        input.dataset.appDateInit = '1';

        const wrap = document.createElement('div');
        wrap.className = 'app-date';
        if (input.disabled) wrap.classList.add('app-date--disabled');

        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);
        input.classList.add('app-date__native');
        input.setAttribute('tabindex', '-1');
        input.setAttribute('aria-hidden', 'true');

        const control = document.createElement('div');
        control.className = 'app-date__control';
        control.setAttribute('role', 'button');
        control.tabIndex = input.disabled ? -1 : 0;

        const icon = document.createElement('span');
        icon.className = 'app-date__icon';
        icon.innerHTML = ICON_CAL;

        const label = document.createElement('span');
        label.className = 'app-date__label';

        const clearBtn = document.createElement('span');
        clearBtn.className = 'app-date__clear';
        clearBtn.innerHTML = ICON_CLEAR;
        clearBtn.style.display = 'none';
        clearBtn.title = 'Clear';

        control.appendChild(icon);
        control.appendChild(label);
        control.appendChild(clearBtn);
        wrap.appendChild(control);

        const panel = document.createElement('div');
        panel.className = 'app-date__panel';
        // appended to <body> so it can never be clipped by a parent card
        document.body.appendChild(panel);

        const nav = document.createElement('div');
        nav.className = 'app-date__nav';
        const prevBtn = document.createElement('span');
        prevBtn.className = 'app-date__nav-btn';
        prevBtn.innerHTML = ICON_PREV;
        const monthLabel = document.createElement('span');
        monthLabel.className = 'app-date__month-label';
        const nextBtn = document.createElement('span');
        nextBtn.className = 'app-date__nav-btn';
        nextBtn.innerHTML = ICON_NEXT;
        nav.appendChild(prevBtn);
        nav.appendChild(monthLabel);
        nav.appendChild(nextBtn);

        const weekdaysRow = document.createElement('div');
        weekdaysRow.className = 'app-date__weekdays';
        WEEKDAYS.forEach(function (w) {
            const el = document.createElement('div');
            el.className = 'app-date__weekday';
            el.textContent = w;
            weekdaysRow.appendChild(el);
        });

        const grid = document.createElement('div');
        grid.className = 'app-date__grid';

        panel.appendChild(nav);
        panel.appendChild(weekdaysRow);
        panel.appendChild(grid);

        let timeNative = null;
        if (isDateTime) {
            const timeRow = document.createElement('div');
            timeRow.className = 'app-date__time-row';
            const timeLbl = document.createElement('span');
            timeLbl.className = 'app-date__time-label';
            timeLbl.textContent = 'Time';
            timeNative = document.createElement('input');
            timeNative.type = 'time';
            timeNative.className = 'app-date__time-native';
            timeRow.appendChild(timeLbl);
            timeRow.appendChild(timeNative);
            panel.appendChild(timeRow);
        }

        const footer = document.createElement('div');
        footer.className = 'app-date__footer';
        const clearQuick = document.createElement('span');
        clearQuick.className = 'app-date__quick-btn is-muted';
        clearQuick.textContent = 'Clear';
        const todayBtnOrDone = document.createElement('span');
        if (isDateTime) {
            todayBtnOrDone.className = 'app-date__done-btn';
            todayBtnOrDone.textContent = 'Done';
        } else {
            todayBtnOrDone.className = 'app-date__quick-btn';
            todayBtnOrDone.textContent = 'Today';
        }
        footer.appendChild(clearQuick);
        footer.appendChild(todayBtnOrDone);
        panel.appendChild(footer);

        // ── state ──
        let selected = parseValue(input.value); // {y,m,d,h,min} or null
        const today = new Date();
        const todayParts = { y: today.getFullYear(), m: today.getMonth() + 1, d: today.getDate() };
        let viewY = (selected || todayParts).y;
        let viewM = (selected || todayParts).m;

        function currentMinMax(attr) {
            const v = input.getAttribute(attr);
            return v ? parseValue(v) : null;
        }

        function isOutOfRange(p) {
            const min = currentMinMax('min');
            const max = currentMinMax('max');
            const key = p.y * 10000 + p.m * 100 + p.d;
            if (min && key < min.y * 10000 + min.m * 100 + min.d) return true;
            if (max && key > max.y * 10000 + max.m * 100 + max.d) return true;
            return false;
        }

        function renderGrid() {
            monthLabel.textContent = MONTHS[viewM - 1] + ' ' + viewY;
            grid.innerHTML = '';

            const firstWeekday = new Date(viewY, viewM - 1, 1).getDay();
            const totalDays = daysInMonth(viewY, viewM);
            const prevMonthDays = daysInMonth(viewY, viewM - 1 === 0 ? 12 : viewM - 1);

            const cells = [];
            for (let i = firstWeekday - 1; i >= 0; i--) {
                const d = prevMonthDays - i;
                const dt = new Date(viewY, viewM - 2, d);
                cells.push({ y: dt.getFullYear(), m: dt.getMonth() + 1, d: dt.getDate(), muted: true });
            }
            for (let d = 1; d <= totalDays; d++) cells.push({ y: viewY, m: viewM, d: d, muted: false });
            let nextD = 1;
            while (cells.length < 42) {
                const dt = new Date(viewY, viewM, nextD);
                cells.push({ y: dt.getFullYear(), m: dt.getMonth() + 1, d: dt.getDate(), muted: true });
                nextD++;
            }

            cells.forEach(function (c) {
                const cell = document.createElement('div');
                cell.className = 'app-date__cell';
                if (c.muted) cell.classList.add('is-muted');
                if (sameDay(c, todayParts)) cell.classList.add('is-today');
                if (selected && sameDay(c, selected)) cell.classList.add('is-selected');
                if (isOutOfRange(c)) cell.classList.add('is-disabled');
                cell.textContent = c.d;
                cell.addEventListener('click', function (e) {
                    e.stopPropagation();
                    selected = { y: c.y, m: c.m, d: c.d, h: (selected && selected.h) || 0, min: (selected && selected.min) || 0 };
                    viewY = c.y;
                    viewM = c.m;
                    if (isDateTime && timeNative.value) {
                        const [hh, mm] = timeNative.value.split(':').map(Number);
                        selected.h = hh || 0;
                        selected.min = mm || 0;
                    }
                    commit();
                    renderGrid();
                    if (!isDateTime) closePanel();
                });
                grid.appendChild(cell);
            });
        }

        function commit() {
            if (!selected) {
                input.value = '';
            } else {
                input.value = isDateTime ? toDateTimeValue(selected) : toDateValue(selected);
            }
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            syncLabel();
        }

        function syncLabel() {
            if (selected) {
                label.textContent = formatLabel(selected, isDateTime);
                label.classList.remove('is-placeholder');
                clearBtn.style.display = 'inline-flex';
            } else {
                label.textContent = input.placeholder || (isDateTime ? 'Select date & time' : 'Select date');
                label.classList.add('is-placeholder');
                clearBtn.style.display = 'none';
            }
            if (isDateTime && timeNative) {
                timeNative.value = selected ? pad2(selected.h) + ':' + pad2(selected.min) : '';
            }
        }

        function positionPanel() {
            const rect = control.getBoundingClientRect();
            const panelHeight = panel.offsetHeight;
            const panelWidth = panel.offsetWidth;
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            const openUpward = spaceBelow < panelHeight + 12 && spaceAbove > spaceBelow;
            const top = openUpward ? rect.top - panelHeight - 6 : rect.bottom + 6;

            let left = rect.left;
            if (left + panelWidth > window.innerWidth - 8) left = window.innerWidth - panelWidth - 8;
            if (left < 8) left = 8;

            panel.style.top = Math.max(8, top) + 'px';
            panel.style.left = left + 'px';
        }

        function openPanel() {
            if (input.disabled) return;
            closeAllPanels();
            document.querySelectorAll('.app-select__panel.is-open').forEach(function (p) { p.classList.remove('is-open'); });
            viewY = (selected || todayParts).y;
            viewM = (selected || todayParts).m;
            renderGrid();
            panel.classList.add('is-open');
            control.classList.add('is-open');
            positionPanel();
        }

        function closePanel() {
            panel.classList.remove('is-open');
            control.classList.remove('is-open');
        }

        function togglePanel(e) {
            if (input.disabled) return;
            e.stopPropagation();
            if (panel.classList.contains('is-open')) closePanel();
            else openPanel();
        }

        prevBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            viewM--;
            if (viewM < 1) { viewM = 12; viewY--; }
            renderGrid();
        });
        nextBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            viewM++;
            if (viewM > 12) { viewM = 1; viewY++; }
            renderGrid();
        });

        clearQuick.addEventListener('click', function (e) {
            e.stopPropagation();
            selected = null;
            commit();
            closePanel();
        });

        todayBtnOrDone.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isDateTime) {
                closePanel();
            } else {
                selected = { y: todayParts.y, m: todayParts.m, d: todayParts.d, h: 0, min: 0 };
                viewY = todayParts.y;
                viewM = todayParts.m;
                commit();
                closePanel();
            }
        });

        if (isDateTime) {
            timeNative.addEventListener('click', function (e) { e.stopPropagation(); });
            timeNative.addEventListener('change', function () {
                if (!timeNative.value) return;
                const [hh, mm] = timeNative.value.split(':').map(Number);
                if (!selected) selected = { y: todayParts.y, m: todayParts.m, d: todayParts.d, h: 0, min: 0 };
                selected.h = hh || 0;
                selected.min = mm || 0;
                commit();
            });
            panel.addEventListener('click', function (e) { e.stopPropagation(); });
        } else {
            panel.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        control.addEventListener('click', togglePanel);
        control.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); togglePanel(e); }
            if (e.key === 'Escape') closePanel();
        });
        control.addEventListener('focus', function () { control.classList.add('is-focus'); });
        control.addEventListener('blur', function () { control.classList.remove('is-focus'); });

        clearBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            selected = null;
            commit();
        });

        // keep in sync if some page script sets input.value directly
        input.addEventListener('change', function () {
            selected = parseValue(input.value);
            syncLabel();
        });

        const attrObserver = new MutationObserver(function () {
            wrap.classList.toggle('app-date--disabled', input.disabled);
            control.tabIndex = input.disabled ? -1 : 0;
        });
        attrObserver.observe(input, { attributes: true, attributeFilter: ['disabled'] });

        input._appDateRefresh = function () {
            selected = parseValue(input.value);
            syncLabel();
        };

        syncLabel();
    }

    function enhanceAll(root) {
        (root || document).querySelectorAll('input[type="date"], input[type="datetime-local"]').forEach(buildDateInput);
    }

    // patch the native `value` setter so that ANY page script setting
    // input.value = '...' keeps the custom widget in sync
    const dateProto = window.HTMLInputElement.prototype;
    const valueDesc = Object.getOwnPropertyDescriptor(dateProto, 'value');
    if (valueDesc && valueDesc.set) {
        Object.defineProperty(dateProto, 'value', {
            get: valueDesc.get,
            configurable: true,
            set: function (v) {
                valueDesc.set.call(this, v);
                if (this._appDateRefresh) this._appDateRefresh();
            },
        });
    }

    document.addEventListener('click', closeAllPanels);
    document.addEventListener(
        'scroll',
        function (e) {
            if (e.target && e.target.closest && e.target.closest('.app-date__panel')) return;
            closeAllPanels();
        },
        true
    );
    window.addEventListener('resize', closeAllPanels);

    document.addEventListener(
        'reset',
        function (e) {
            if (e.target && e.target.tagName === 'FORM') {
                setTimeout(function () {
                    e.target.querySelectorAll('input[type="date"], input[type="datetime-local"]').forEach(function (i) {
                        if (i._appDateRefresh) i._appDateRefresh();
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
                    if (node.tagName === 'INPUT') buildDateInput(node);
                    if (node.querySelectorAll) node.querySelectorAll('input[type="date"], input[type="datetime-local"]').forEach(buildDateInput);
                });
            });
        });
        bodyObserver.observe(document.body, { childList: true, subtree: true });
    });

    window.AppDate = {
        enhance: enhanceAll,
        refresh: function (input) {
            if (input && input._appDateRefresh) input._appDateRefresh();
        },
    };
})();
