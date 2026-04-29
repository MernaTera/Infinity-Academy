<style>
    /* ══ SIDEBAR ══ */
    .sidebar {
        width: 220px;
        flex-shrink: 0;
        background: rgba(255,255,255,0.88);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-right: 1px solid rgba(27,79,168,0.08);
        padding: 24px 0 40px;
        position: sticky;
        top: 60px;
        height: calc(100vh - 60px);
        overflow-y: auto;
        overflow-x: hidden;
        font-family: 'DM Sans', sans-serif;
        scrollbar-width: thin;
        scrollbar-color: rgba(27,79,168,0.1) transparent;
        transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), width 0.3s;
        z-index: 40;
    }

    .sidebar::-webkit-scrollbar { width: 3px; }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(27,79,168,0.12); border-radius: 3px; }

    .sl-label {
        font-size: 8px; letter-spacing: 3px; text-transform: uppercase;
        color: #F5911E; padding: 0 20px; margin-bottom: 4px; margin-top: 20px;
        display: block; font-weight: 500;
    }
    .sl-label:first-child { margin-top: 4px; }

    .sl-link {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 20px;
        font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;
        color: #7A8A9A; text-decoration: none;
        transition: color 0.2s, background 0.2s, border-color 0.2s;
        border-left: 2px solid transparent;
        white-space: nowrap;
    }
    .sl-link:hover { color: #1B4FA8; background: rgba(27,79,168,0.03); border-left-color: rgba(27,79,168,0.2); text-decoration: none; }
    .sl-link.active { color: #1B4FA8; background: rgba(27,79,168,0.05); border-left-color: #1B4FA8; font-weight: 500; }
    .sl-link svg { flex-shrink: 0; opacity: 0.55; transition: opacity 0.2s; }
    .sl-link:hover svg, .sl-link.active svg { opacity: 1; }

    .sl-div { height: 1px; background: rgba(27,79,168,0.06); margin: 10px 20px; }

    /* ── Mobile sidebar overlay ── */
    .sidebar-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(8,15,35,0.4);
        backdrop-filter: blur(4px);
        z-index: 39;
    }

    /* ── Mobile toggle button ── */
    .sidebar-toggle-btn {
        display: none;
        position: fixed;
        bottom: 24px; left: 24px;
        width: 44px; height: 44px;
        background: #1B4FA8;
        border: none; border-radius: 50%;
        box-shadow: 0 4px 16px rgba(27,79,168,0.35);
        cursor: pointer;
        align-items: center; justify-content: center;
        z-index: 41;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .sidebar-toggle-btn:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(27,79,168,0.4); }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .sidebar {
            position: fixed;
            top: 60px; left: 0;
            height: calc(100vh - 60px);
            transform: translateX(-100%);
            box-shadow: 4px 0 24px rgba(27,79,168,0.12);
            z-index: 40;
        }
        .sidebar.open {
            transform: translateX(0);
            animation: sidebarIn 0.3s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes sidebarIn { from{transform:translateX(-100%)} to{transform:translateX(0)} }

        .sidebar-overlay.open { display: block; animation: fadeIn 0.25s ease both; }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }

        .sidebar-toggle-btn { display: flex; }
    }
</style>

{{-- Sidebar --}}
<aside class="sidebar" id="mainSidebar">

    @cando('leads.view')
    <span class="sl-label">Overview</span>
    <a href="{{ route('dashboard') }}" class="sl-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        General Dashboard
    </a>

    <div class="sl-div"></div>
    <span class="sl-label">Leads</span>

    <a href="{{ route('leads.dashboard') }}" class="sl-link {{ request()->routeIs('leads.dashboard') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        Leads Dashboard
    </a>
    <a href="{{ route('leads.index') }}" class="sl-link {{ request()->routeIs('leads.index') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        My Leads
    </a>
    <a href="{{ route('leads.public') }}" class="sl-link {{ request()->routeIs('leads.public') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        Public Leads
    </a>
    <a href="{{ route('leads.archived') }}" class="sl-link {{ request()->routeIs('leads.archived') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="21 8 21 21 3 21 3 8"/>
            <rect x="1" y="3" width="22" height="5"/>
            <line x1="10" y1="12" x2="14" y2="12"/>
        </svg>
        Archived
    </a>

    <div class="sl-div"></div>

    <a href="{{ route('leads.create') }}" class="sl-link {{ request()->routeIs('leads.create') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Add Lead
    </a>

    <div class="sl-div"></div>
    <span class="sl-label">Sales</span>

    <a href="{{ route('sales.index') }}" class="sl-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="20" x2="12" y2="10"/>
            <line x1="18" y1="20" x2="18" y2="4"/>
            <line x1="6"  y1="20" x2="6"  y2="16"/>
        </svg>
        Sales Table
    </a>
    <a href="{{ route('outstanding.index') }}" class="sl-link {{ request()->routeIs('outstanding.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8"  x2="12"    y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Outstanding
    </a>
    <a href="{{ route('student-care.refunds.index') }}" class="sl-link {{ request()->routeIs('student-care.refunds.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg>
        Refunds
    </a>
    @endcando

</aside>

{{-- Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- Mobile toggle --}}
<button class="sidebar-toggle-btn" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Menu">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
        <line x1="3" y1="6"  x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
</button>

<script>
function toggleSidebar() {
    const sidebar  = document.getElementById('mainSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const isOpen   = sidebar.classList.contains('open');
    if (isOpen) {
        closeSidebar();
    } else {
        sidebar.classList.add('open');
        overlay.classList.add('open');
    }
}
function closeSidebar() {
    document.getElementById('mainSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
// Close on ESC
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
// Auto-close on nav link click (mobile)
document.querySelectorAll('.sl-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 900) closeSidebar();
    });
});
</script>