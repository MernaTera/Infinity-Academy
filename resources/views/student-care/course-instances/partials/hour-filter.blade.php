<style>
    .hour-bar { display:flex; align-items:center; gap:6px; margin:-6px 0 16px; overflow-x:auto; padding-bottom:4px; scrollbar-width:thin; }
    .hour-bar:empty { display:none; }
    .hour-bar-label { font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-right:4px; white-space:nowrap; }
    .hour-chip { flex:0 0 auto; display:flex; flex-direction:column; align-items:center; gap:2px; min-width:58px; padding:7px 10px; background:var(--card); border:1px solid var(--border); border-radius:12px; cursor:pointer; font-size:12px; color:var(--text); transition:border-color .2s, background .2s, color .2s; }
    .hour-chip .hc-count { font-size:10px; color:var(--muted); }
    .hour-chip:hover { border-color:var(--blue); }
    .hour-chip.is-empty { opacity:.45; }
    .hour-chip.is-active { background:var(--blue); border-color:var(--blue); color:#fff; }
    .hour-chip.is-active .hc-count { color:rgba(255,255,255,.85); }
    .hour-chip.hc-all { min-width:auto; justify-content:center; }
</style>

<div class="hour-bar" id="ciHourBar"></div>

<script>
let ciHour = null;

function ciCardRanges(card) {
    return (card.dataset.times || '').split(',').filter(Boolean).map(r => r.split('-').map(Number));
}

function ciHourMatches(card, hour) {
    if (hour === null) return true;
    const from = hour * 60, to = from + 60;
    return ciCardRanges(card).some(([s, e]) => s < to && e > from);
}

function ciHourLabel(h) {
    const suffix = h < 12 || h === 24 ? 'AM' : 'PM';
    const hh = h % 12 === 0 ? 12 : h % 12;
    return hh + ' ' + suffix;
}

function ciSetHour(hour) {
    ciHour = (ciHour === hour) ? null : hour;
    applyFilters();
}

function renderHourBar(passesOtherFilters) {
    const bar = document.getElementById('ciHourBar');
    if (!bar) return;

    const allCards = [...document.querySelectorAll('.ci-card')];
    let minH = 24, maxH = -1;
    allCards.forEach(card => ciCardRanges(card).forEach(([s, e]) => {
        minH = Math.min(minH, Math.floor(s / 60));
        maxH = Math.max(maxH, Math.ceil(e / 60) - 1);
    }));
    if (maxH < minH) { bar.innerHTML = ''; return; }

    const panel = document.querySelector('.tab-panel.active');
    const pool  = panel ? [...panel.querySelectorAll('.ci-card')].filter(passesOtherFilters) : [];

    let html = '<span class="hour-bar-label">Time</span>'
        + `<button type="button" class="hour-chip hc-all ${ciHour === null ? 'is-active' : ''}" onclick="ciSetHour(null)">All<span class="hc-count">${pool.length}</span></button>`;

    for (let h = minH; h <= maxH; h++) {
        const count = pool.filter(card => ciHourMatches(card, h)).length;
        const cls = ['hour-chip', ciHour === h ? 'is-active' : '', count === 0 ? 'is-empty' : ''].join(' ');
        html += `<button type="button" class="${cls}" onclick="ciSetHour(${h})" title="Courses running between ${ciHourLabel(h)} and ${ciHourLabel(h + 1)}">${ciHourLabel(h)}<span class="hc-count">${count} course${count === 1 ? '' : 's'}</span></button>`;
    }
    bar.innerHTML = html;
}
</script>
