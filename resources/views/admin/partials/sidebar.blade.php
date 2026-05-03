<style>
/* ── SIDEBAR VARIABLES ── */
:root {
    --sb-w: 240px;
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
#adminSidebar {
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

#adminSidebar.collapsed {
    width: var(--sb-w-collapsed);
}

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

/* ── SECTION LABELS ── */
.sb-section {
    margin-top: 4px;
}
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
    color: var(--sb-label);
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
.sb-section.collapsed-section .sb-section-chevron {
    transform: rotate(-90deg);
}
.sb-section-body {
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    max-height: 600px;
}
.sb-section.collapsed-section .sb-section-body {
    max-height: 0;
}

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
    position: relative;
    white-space: nowrap;
    overflow: hidden;
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
.sl-link:hover svg,
.sl-link.active svg { opacity: 1; }

.sl-link-text {
    transition: opacity var(--sb-transition), width var(--sb-transition);
    overflow: hidden;
}

/* ── BADGE ── */
.sl-badge {
    margin-left: auto;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    background: #F5911E;
    color: #fff;
    letter-spacing: 0;
    flex-shrink: 0;
    line-height: 1.4;
    transition: opacity var(--sb-transition);
}

/* ── DIVIDER ── */
.sl-div {
    height: 1px;
    background: var(--sb-border);
    margin: 6px 16px;
}

/* ── COLLAPSED STATE ── */
#adminSidebar.collapsed .sl-link-text,
#adminSidebar.collapsed .sb-section-label,
#adminSidebar.collapsed .sb-section-chevron,
#adminSidebar.collapsed .sl-badge {
    opacity: 0;
    width: 0;
    pointer-events: none;
}
#adminSidebar.collapsed .sl-link {
    padding: 10px 0;
    justify-content: center;
    border-left-color: transparent !important;
}
#adminSidebar.collapsed .sl-link.active {
    background: var(--sb-active-bg);
}
#adminSidebar.collapsed .sb-section-header {
    justify-content: center;
    padding: 10px 0 4px;
}
#adminSidebar.collapsed .sb-section-body {
    max-height: 600px !important;
}
#adminSidebar.collapsed .sb-toggle-btn svg {
    transform: rotate(180deg);
}

/* Tooltip on collapsed */
#adminSidebar.collapsed .sl-link {
    position: relative;
}
#adminSidebar.collapsed .sl-link::after {
    content: attr(data-label);
    position: absolute;
    left: calc(var(--sb-w-collapsed) + 8px);
    top: 50%;
    transform: translateY(-50%);
    background: #1A2A4A;
    color: #fff;
    font-size: 10px;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 5px 10px;
    border-radius: 4px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.15s;
    z-index: 999;
}
#adminSidebar.collapsed .sl-link:hover::after {
    opacity: 1;
}

/* ── MOBILE OVERLAY ── */
.sb-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10,20,40,0.4);
    backdrop-filter: blur(2px);
    z-index: 39;
}
.sb-overlay.show { display: block; }

/* ── MOBILE ── */
@media (max-width: 768px) {
    #adminSidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform var(--sb-transition), width var(--sb-transition);
        width: var(--sb-w) !important;
        box-shadow: 4px 0 24px rgba(27,79,168,0.15);
    }
    #adminSidebar.mobile-open {
        transform: translateX(0);
    }
    .sb-toggle { display: none; }
}
</style>

<div class="sb-overlay" id="sbOverlay" onclick="closeMobileSidebar()"></div>

<aside id="adminSidebar">

    <div class="sb-scroll">

        {{-- Overview --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Overview</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('admin.dashboard') }}"
                   class="sl-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   data-label="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span class="sl-link-text">Dashboard</span>
                </a>
            </div>
        </div>

        <div class="sl-div"></div>

        {{-- HR --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Human Resources</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('admin.employees.index') }}"
                   class="sl-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"
                   data-label="Employees">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span class="sl-link-text">Employees</span>
                </a>
            </div>
        </div>

        <div class="sl-div"></div>

        {{-- Academic --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Academic</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('admin.courses.index') }}"
                   class="sl-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"
                   data-label="Courses">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span class="sl-link-text">Courses</span>
                </a>
                <a href="{{ route('admin.rooms.index') }}"
                   class="sl-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}"
                   data-label="Rooms">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span class="sl-link-text">Rooms</span>
                </a>
                <a href="{{ route('admin.materials.index') }}"
                   class="sl-link {{ request()->routeIs('admin.materials.*') ? 'active' : '' }}"
                   data-label="Materials">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    <span class="sl-link-text">Materials</span>
                </a>
                <a href="{{ route('admin.patches.index') }}"
                   class="sl-link {{ request()->routeIs('admin.patches.*') ? 'active' : '' }}"
                   data-label="Patches">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span class="sl-link-text">Patches</span>
                </a>
                <a href="{{ route('admin.english-levels.index') }}"
                   class="sl-link {{ request()->routeIs('admin.english-levels.*') ? 'active' : '' }}"
                   data-label="English Levels">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    <span class="sl-link-text">English Levels</span>
                </a>
            </div>
        </div>

        <div class="sl-div"></div>

        {{-- Financial --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Financial</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('admin.payment-policy.index') }}"
                   class="sl-link {{ request()->routeIs('admin.payment-policy.*') ? 'active' : '' }}"
                   data-label="Payment Plans">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <span class="sl-link-text">Payment Plans</span>
                </a>
                <a href="{{ route('admin.packages.index') }}"
                   class="sl-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}"
                   data-label="Level Packages">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    <span class="sl-link-text">Level Packages</span>
                </a>
                <a href="{{ route('admin.bundles.index') }}"
                   class="sl-link {{ request()->routeIs('admin.bundles.*') ? 'active' : '' }}"
                   data-label="Private Bundles">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    <span class="sl-link-text">Private Bundles</span>
                </a>
                <a href="{{ route('admin.installments.index') }}"
                   class="sl-link {{ request()->routeIs('admin.installments.*') ? 'active' : '' }}"
                   data-label="Installments">
                    @php $pendingInstallments = \App\Models\Finance\InstallmentApprovalLog::where('status','Pending')->count(); @endphp
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    <span class="sl-link-text">Installments</span>
                    @if($pendingInstallments > 0)
                    <span class="sl-badge">{{ $pendingInstallments }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.refunds.index') }}"
                   class="sl-link {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}"
                   data-label="Refunds">
                    @php $pendingRefunds = \App\Models\Finance\RefundRequest::where('status','Pending')->count(); @endphp
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <span class="sl-link-text">Refunds</span>
                    @if($pendingRefunds > 0)
                    <span class="sl-badge">{{ $pendingRefunds }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.outstanding.index') }}"
                   class="sl-link {{ request()->routeIs('admin.outstanding.*') ? 'active' : '' }}"
                   data-label="Outstanding">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="sl-link-text">Outstanding Risk</span>
                </a>
                <a href="{{ route('admin.sales.index') }}"
                   class="sl-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}"
                   data-label="Sales Revenue">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span class="sl-link-text">Sales Revenue</span>
                </a>
                <a href="{{ route('admin.offers.index') }}"
                   class="sl-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}"
                   data-label="Offers">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    <span class="sl-link-text">Offers</span>
                </a>
            </div>
        </div>

        <div class="sl-div"></div>

        {{-- Monitoring --}}
        <div class="sb-section">
            <div class="sb-section-header" onclick="toggleSection(this)">
                <span class="sb-section-label">Monitoring</span>
                <svg class="sb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="sb-section-body">
                <a href="{{ route('admin.reports.index') }}"
                   class="sl-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                   data-label="Student Reports">
                    @php $pendingReports = \App\Models\Reports\Report::where('status','Submitted')->count(); @endphp
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span class="sl-link-text">Student Reports</span>
                    @if($pendingReports > 0)
                    <span class="sl-badge">{{ $pendingReports }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.audit.index') }}"
                   class="sl-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}"
                   data-label="Audit Logs">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    <span class="sl-link-text">Audit Logs</span>
                </a>
            </div>
        </div>

    </div>

    {{-- Collapse Toggle --}}
    <div class="sb-toggle">
        <button class="sb-toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>

</aside>

<script>
// ── Sidebar collapse ──
const sidebar   = document.getElementById('adminSidebar');
const overlay   = document.getElementById('sbOverlay');
const PREF_KEY  = 'admin_sb_collapsed';

// Restore state
if (localStorage.getItem(PREF_KEY) === '1' && window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
}

function toggleSidebar() {
    sidebar.classList.toggle('collapsed');
    localStorage.setItem(PREF_KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
}

// Mobile
function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
}

// ── Section collapse ──
function toggleSection(header) {
    const section  = header.closest('.sb-section');
    const key      = 'sb_sec_' + header.querySelector('.sb-section-label').textContent.trim();
    section.classList.toggle('collapsed-section');
    localStorage.setItem(key, section.classList.contains('collapsed-section') ? '1' : '0');
}

// Restore section states
document.querySelectorAll('.sb-section-header').forEach(header => {
    const key  = 'sb_sec_' + header.querySelector('.sb-section-label').textContent.trim();
    const val  = localStorage.getItem(key);
    if (val === '1') header.closest('.sb-section').classList.add('collapsed-section');
});

// pointer-events fix for all ::before buttons
document.querySelectorAll('button, a').forEach(el => {
    el.style.setProperty('--before-pe', 'none');
});
</script>