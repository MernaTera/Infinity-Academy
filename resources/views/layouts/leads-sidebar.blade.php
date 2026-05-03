<style>
/* ── SIDEBAR VARIABLES ── */
:root {
    --sb-w: 230px;
    --sb-w-collapsed: 64px;
    --sb-bg: #fff;
    --sb-border: rgba(27,79,168,0.08);
    --sb-blue: #1B4FA8;
    --sb-orange: #F5911E;
    --sb-text: #5A6A7A;
    --sb-active-bg: rgba(27,79,168,0.06);
    --sb-active-text: #1B4FA8;
    --sb-hover-bg: rgba(27,79,168,0.03);
    --sb-label: #AAB8C8;
    --sb-transition: 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

/* ── SIDEBAR BASE ── */
#mainSidebar {
    width: var(--sb-w);
    flex-shrink: 0;
    background: var(--sb-bg);
    border-right: 1px solid var(--sb-border);
    position: sticky;
    top: 62px;
    height: calc(100vh - 62px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: width var(--sb-transition);
    z-index: 40;
    box-shadow: 2px 0 12px rgba(27,79,168,0.04);
    font-family: 'DM Sans', sans-serif;
}
#mainSidebar.collapsed { width: var(--sb-w-collapsed); }

/* ── SCROLL AREA ── */
.sb-scroll {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 24px;
    scrollbar-width: thin;
    scrollbar-color: rgba(27,79,168,0.1) transparent;
}
.sb-scroll::-webkit-scrollbar { width: 3px; }
.sb-scroll::-webkit-scrollbar-track { background: transparent; }
.sb-scroll::-webkit-scrollbar-thumb { background: rgba(27,79,168,0.12); border-radius: 2px; }

/* ── COLLAPSE TOGGLE ── */
.sb-toggle {
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 16px;
    border-top: 1px solid var(--sb-border);
    flex-shrink: 0;
}
.sb-toggle-btn {
    width: 28px; height: 28px;
    border: 1px solid var(--sb-border);
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--sb-label);
    transition: all 0.2s;
}
.sb-toggle-btn:hover { background: var(--sb-hover-bg); color: var(--sb-blue); border-color: rgba(27,79,168,0.2); }

/* ── SECTION HEADERS ── */
.sb-section { margin-top: 4px; }
.sb-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px 6px;
    cursor: pointer;
    user-select: none;
}
.sb-section-label {
    font-size: 8px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--sb-orange);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity var(--sb-transition);
}
.sb-section-chevron {
    color: var(--sb-label);
    transition: transform 0.2s;
    flex-shrink: 0;
    opacity: 0.6;
}
.sb-section.collapsed-section .sb-section-chevron { transform: rotate(-90deg); }
.sb-section-body {
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    max-height: 600px;
}
.sb-section.collapsed-section .sb-section-body { max-height: 0; }

/* ── LINKS ── */
.sl-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 18px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--sb-text);
    text-decoration: none;
    transition: all 0.18s;
    border-left: 2px solid transparent;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
}
.sl-link:hover {
    color: var(--sb-active-text);
    background: var(--sb-hover-bg);
    border-left-color: rgba(27,79,168,0.2);
    text-decoration: none;
}
.sl-link.active {
    color: var(--sb-active-text);
    background: var(--sb-active-bg);
    border-left-color: var(--sb-blue);
    font-weight: 600;
}
.sl-link svg {
    flex-shrink: 0;
    opacity: 0.55;
    transition: opacity 0.18s;
    min-width: 14px;
}
.sl-link:hover svg, .sl-link.active svg { opacity: 1; }
.sl-link-text { transition: opacity var(--sb-transition), width var(--sb-transition); overflow: hidden; }

.sl-div { height: 1px; background: var(--sb-border); margin: 6px 16px; }

/* ── COLLAPSED STATE ── */
#mainSidebar.collapsed .sl-link-text,
#mainSidebar.collapsed .sb-section-label,
#mainSidebar.collapsed .sb-section-chevron { opacity: 0; width: 0; pointer-events: none; }
#mainSidebar.collapsed .sl-link { padding: 10px 0; justify-content: center; border-left-color: transparent !important; }
#mainSidebar.collapsed .sl-link.active { background: var(--sb-active-bg); }
#mainSidebar.collapsed .sb-section-header { justify-content: center; padding: 10px 0 4px; }
#mainSidebar.collapsed .sb-section-body { max-height: 600px !important; }
#mainSidebar.collapsed .sb-toggle-btn svg { transform: rotate(180deg); }

/* Tooltips on collapsed */
#mainSidebar.collapsed .sl-link::after {
    content: attr(data-label);
    position: absolute;
    left: calc(var(--sb-w-collapsed) + 8px);
    top: 50%; transform: translateY(-50%);
    background: #1A2A4A; color: #fff;
    font-size: 10px; letter-spacing: 1px; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 999;
}
#mainSidebar.collapsed .sl-link:hover::after { opacity: 1; }

/* ── MOBILE OVERLAY ── */
.sb-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(10,20,40,0.4);
    backdrop-filter: blur(2px);
    z-index: 39;
}
.sb-overlay.show { display: block; }

/* ── MOBILE ── */
@media(max-width: 900px) {
    #mainSidebar {
        position: fixed; top: 0; left: 0; height: 100vh;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform var(--sb-transition), width var(--sb-transition);
        width: var(--sb-w) !important;
        box-shadow: 4px 0 24px rgba(27,79,168,0.15);
    }
    #mainSidebar.mobile-open { transform: translateX(0); }
    .sb-toggle { display: none; }
}
</style>

<div class="sb-overlay" id="sbOverlay" onclick="closeMobileSidebar()"></div>

<aside id="mainSidebar">
    <div class="sb-scroll">

        {{-- Overview --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Overview</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('dashboard') }}" class="sl-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span class="sl-link-text">General Dashboard</span>
                </a>
            </div>
        </div>

        <div class="sl-div"></div>

        {{-- Leads --}}
        @cando('leads.view')
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Leads</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('leads.dashboard') }}" class="sl-link {{ request()->routeIs('leads.dashboard') ? 'active' : '' }}" data-label="Leads Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    <span class="sl-link-text">Leads Dashboard</span>
                </a>
                <a href="{{ route('leads.index') }}" class="sl-link {{ request()->routeIs('leads.index') ? 'active' : '' }}" data-label="My Leads">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span class="sl-link-text">My Leads</span>
                </a>
                <a href="{{ route('leads.public') }}" class="sl-link {{ request()->routeIs('leads.public') ? 'active' : '' }}" data-label="Public Leads">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span class="sl-link-text">Public Leads</span>
                </a>
                <a href="{{ route('leads.archived') }}" class="sl-link {{ request()->routeIs('leads.archived') ? 'active' : '' }}" data-label="Archived">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    <span class="sl-link-text">Archived</span>
                </a>
                <a href="{{ route('leads.create') }}" class="sl-link {{ request()->routeIs('leads.create') ? 'active' : '' }}" data-label="Add Lead">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <span class="sl-link-text">Add Lead</span>
                </a>
            </div>
        </div>

        <div class="sl-div"></div>

        {{-- Sales --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Sales</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('sales.index') }}" class="sl-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" data-label="Sales Table">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                    <span class="sl-link-text">Sales Table</span>
                </a>
                <a href="{{ route('outstanding.index') }}" class="sl-link {{ request()->routeIs('outstanding.*') ? 'active' : '' }}" data-label="Outstanding">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="sl-link-text">Outstanding</span>
                </a>
                <a href="{{ route('student-care.refunds.index') }}" class="sl-link {{ request()->routeIs('student-care.refunds.*') ? 'active' : '' }}" data-label="Refunds">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <span class="sl-link-text">Refunds</span>
                </a>
            </div>
        </div>
        @endcando

    </div>

    {{-- Collapse toggle --}}
    <div class="sb-toggle">
        <button class="sb-toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>
</aside>

<script>
// ── Collapse ──
const sidebar  = document.getElementById('mainSidebar');
const sbOverlay = document.getElementById('sbOverlay');
const PREF_KEY = 'cs_sb_collapsed';

if (localStorage.getItem(PREF_KEY) === '1' && window.innerWidth > 900) {
    sidebar.classList.add('collapsed');
}
function toggleSidebar() {
    sidebar.classList.toggle('collapsed');
    localStorage.setItem(PREF_KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
}

// ── Mobile ──
function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    sbOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    sbOverlay.classList.remove('show');
    document.body.style.overflow = '';
}

// ── Section collapse ──
function toggleSection(header) {
    const section = header.closest('.sb-section');
    const key = 'sb_sec_cs_' + header.querySelector('.sb-section-label').textContent.trim();
    section.classList.toggle('collapsed-section');
    localStorage.setItem(key, section.classList.contains('collapsed-section') ? '1' : '0');
}

// Restore section states
document.querySelectorAll('.sb-section-header').forEach(header => {
    const key = 'sb_sec_cs_' + header.querySelector('.sb-section-label').textContent.trim();
    if (localStorage.getItem(key) === '1') {
        header.closest('.sb-section').classList.add('collapsed-section');
    }
});

// Close on ESC
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMobileSidebar(); });

// Auto-close on mobile nav
document.querySelectorAll('.sl-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 900) closeMobileSidebar();
    });
});
</script>