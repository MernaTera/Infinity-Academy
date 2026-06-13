<style>
:root {
    --sb-w: 220px;
    --sb-w-col: 56px;
    --sb-bg: #fff;
    --sb-border: rgba(27,79,168,0.08);
    --sb-blue: #1B4FA8;
    --sb-text: #5A6A7A;
    --sb-active-bg: rgba(27,79,168,0.07);
    --sb-hover-bg: rgba(27,79,168,0.03);
    --sb-label: #C4CDD8;
    --sb-ease: 0.22s cubic-bezier(0.16,1,0.3,1);
}

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
    transition: width var(--sb-ease);
    z-index: 40;
    box-shadow: 2px 0 10px rgba(27,79,168,0.04);
    font-family: 'DM Sans', sans-serif;
}
#adminSidebar.collapsed { width: var(--sb-w-col); }

/* ── NAV AREA ── */
.sb-nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 10px 0 6px;
}

/* ── GROUP ── */
.sb-group { margin-bottom: 2px; }
.sb-group-label {
    font-size: 9.5px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--sb-label);
    font-weight: 700;
    padding: 8px 14px 3px;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity var(--sb-ease);
    display: block;
}
#adminSidebar.collapsed .sb-group-label { opacity: 0; height: 0; padding: 0; }

/* ── LINK ── */
.sl {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 7px 14px;
    font-size: 13px;
    letter-spacing: 0.5px;
    color: var(--sb-text);
    text-decoration: none;
    transition: all 0.16s;
    border-left: 2px solid transparent;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
}
.sl:hover {
    color: var(--sb-blue);
    background: var(--sb-hover-bg);
    border-left-color: rgba(27,79,168,0.2);
    text-decoration: none;
}
.sl.active {
    color: var(--sb-blue);
    background: var(--sb-active-bg);
    border-left-color: var(--sb-blue);
    font-weight: 600;
}
.sl svg { flex-shrink:0; opacity:0.5; min-width:14px; transition:opacity 0.16s; }
.sl:hover svg, .sl.active svg { opacity:1; }
.sl-txt { transition: opacity var(--sb-ease); overflow:hidden; }
.sl-bdg {
    margin-left:auto; font-size:10px; font-weight:700;
    padding:1px 5px; border-radius:8px;
    background:#F5911E; color:#fff; flex-shrink:0;
    transition: opacity var(--sb-ease);
}

/* ── DIVIDER ── */
.sb-div { height:1px; background:var(--sb-border); margin:4px 12px; }

/* ── TOGGLE BTN ── */
.sb-foot {
    border-top: 1px solid var(--sb-border);
    padding: 8px 10px;
    display: flex;
    justify-content: flex-end;
    flex-shrink: 0;
}
.sb-tog {
    width:26px; height:26px; border:1px solid var(--sb-border);
    border-radius:5px; background:transparent; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    color:var(--sb-label); transition:all 0.18s;
}
.sb-tog:hover { background:var(--sb-hover-bg); color:var(--sb-blue); border-color:rgba(27,79,168,0.2); }

/* ── COLLAPSED ── */
#adminSidebar.collapsed .sl-txt,
#adminSidebar.collapsed .sl-bdg { opacity:0; width:0; pointer-events:none; }
#adminSidebar.collapsed .sl {
    padding: 8px 0;
    justify-content: center;
    border-left-color: transparent !important;
}
#adminSidebar.collapsed .sl.active { background: var(--sb-active-bg); }
#adminSidebar.collapsed .sb-tog svg { transform: rotate(180deg); }

/* Tooltip */
#adminSidebar.collapsed .sl::after {
    content: attr(data-tip);
    position: absolute;
    left: calc(var(--sb-w-col) + 8px);
    top: 50%; transform: translateY(-50%);
    background: #1A2A4A; color: #fff;
    font-size: 12px; letter-spacing: 1px;
    text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 999;
}
#adminSidebar.collapsed .sl:hover::after { opacity: 1; }

/* ── MOBILE ── */
.sb-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(10,20,40,0.4); backdrop-filter:blur(2px); z-index:39;
}
.sb-overlay.show { display:block; }

@media(max-width:768px){
    #adminSidebar {
        position:fixed; top:0; left:0; height:100vh;
        z-index:50; transform:translateX(-100%);
        transition:transform var(--sb-ease), width var(--sb-ease);
        width:var(--sb-w) !important;
        box-shadow:4px 0 24px rgba(27,79,168,0.15);
    }
    #adminSidebar.mobile-open { transform:translateX(0); }
    .sb-foot { display:none; }
}
</style>

<div class="sb-overlay" id="sbOverlay" onclick="closeMobileSidebar()"></div>

<aside id="adminSidebar">
<nav class="sb-nav">

    {{-- OVERVIEW --}}
    <div class="sb-group">
        <span class="sb-group-label">Overview</span>
        <a href="{{ route('admin.dashboard') }}" data-tip="Dashboard"
           class="sl {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span class="sl-txt">Dashboard</span>
        </a>
    </div>

    <div class="sb-div"></div>

    {{-- HR --}}
    <div class="sb-group">
        <span class="sb-group-label">Human Resources</span>
        <a href="{{ route('admin.employees.index') }}" data-tip="Employees"
           class="sl {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span class="sl-txt">Employees</span>
        </a>
        <a href="{{ route('admin.students.index') }}" data-tip="Students"
           class="sl {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <span class="sl-txt">Students</span>
        </a>
        <a href="{{ route('admin.contract-types.index') }}" data-tip="Contract Types"
           class="sl {{ request()->routeIs('admin.contract-types.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span class="sl-txt">Contract Types</span>
        </a>
    </div>

    <div class="sb-div"></div>

    {{-- ACADEMIC --}}
    <div class="sb-group">
        <span class="sb-group-label">Academic</span>
        <a href="{{ route('admin.patches.index') }}" data-tip="Patches"
           class="sl {{ request()->routeIs('admin.patches.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span class="sl-txt">Patches</span>
        </a>
        <a href="{{ route('admin.courses.index') }}" data-tip="Courses"
           class="sl {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span class="sl-txt">Courses</span>
        </a>
        <a href="{{ route('admin.rooms.index') }}" data-tip="Rooms"
           class="sl {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="sl-txt">Rooms</span>
        </a>
        <a href="{{ route('admin.materials.index') }}" data-tip="Materials"
           class="sl {{ request()->routeIs('admin.materials.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <span class="sl-txt">Materials</span>
        </a>
        <a href="{{ route('admin.test-fees.index') }}" data-tip="Test Fees"
           class="sl {{ request()->routeIs('admin.test-fees.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><circle cx="12" cy="12" r="10"/></svg>
            <span class="sl-txt">Test Fees</span>
        </a>
        <a href="{{ route('admin.english-levels.index') }}" data-tip="English Levels"
           class="sl {{ request()->routeIs('admin.english-levels.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span class="sl-txt">English Levels</span>
        </a>
    </div>

    <div class="sb-div"></div>

    {{-- FINANCIAL --}}
    <div class="sb-group">
        <span class="sb-group-label">Financial</span>
        <a href="{{ route('admin.payment-policy.index') }}" data-tip="Payment Plans"
           class="sl {{ request()->routeIs('admin.payment-policy.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span class="sl-txt">Payment Plans</span>
        </a>
        <a href="{{ route('admin.outstanding.index') }}" data-tip="Outstanding"
           class="sl {{ request()->routeIs('admin.outstanding.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span class="sl-txt">Outstanding</span>
        </a>
        <a href="{{ route('admin.installments.index') }}" data-tip="Installments"
           class="sl {{ request()->routeIs('admin.installments.*') ? 'active' : '' }}">
            @php $pi = \App\Models\Finance\InstallmentApprovalLog::where('status','Pending')->count(); @endphp
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span class="sl-txt">Installments</span>
            @if($pi > 0)<span class="sl-bdg">{{ $pi }}</span>@endif
        </a>
        <a href="{{ route('admin.refunds.index') }}" data-tip="Refunds"
           class="sl {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
            @php $pr = \App\Models\Finance\RefundRequest::where('status','Pending')->count(); @endphp
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/></svg>
            <span class="sl-txt">Refunds</span>
            @if($pr > 0)<span class="sl-bdg">{{ $pr }}</span>@endif
        </a>
        <a href="{{ route('admin.sales.index') }}" data-tip="Sales"
           class="sl {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span class="sl-txt">Sales</span>
        </a>
        <a href="{{ route('admin.offers.index') }}" data-tip="Offers"
           class="sl {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <span class="sl-txt">Offers</span>
        </a>
        <a href="{{ route('admin.packages.index') }}" data-tip="Packages"
           class="sl {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            <span class="sl-txt">Packages</span>
        </a>
        <a href="{{ route('admin.bundles.index') }}" data-tip="Bundles"
           class="sl {{ request()->routeIs('admin.bundles.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            <span class="sl-txt">Bundles</span>
        </a>
    </div>

    <div class="sb-div"></div>

    {{-- MONITORING --}}
    <div class="sb-group">
        <span class="sb-group-label">Monitoring</span>
        <a href="{{ route('admin.reports.index') }}" data-tip="Reports"
           class="sl {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            @php $prp = \App\Models\Reports\Report::where('status','Submitted')->count(); @endphp
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span class="sl-txt">Reports</span>
            @if($prp > 0)<span class="sl-bdg">{{ $prp }}</span>@endif
        </a>
        <a href="{{ route('admin.audit.index') }}" data-tip="Audit"
           class="sl {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span class="sl-txt">Audit Logs</span>
        </a>
    </div>

</nav>

<div class="sb-foot">
    <button class="sb-tog" onclick="toggleSidebar()" title="Toggle sidebar">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
</div>
</aside>

<script>
const _sb  = document.getElementById('adminSidebar');
const _sbo = document.getElementById('sbOverlay');
const _KEY = 'adm_sb_col';

if (localStorage.getItem(_KEY) === '1' && window.innerWidth > 768) _sb.classList.add('collapsed');

function toggleSidebar() {
    _sb.classList.toggle('collapsed');
    localStorage.setItem(_KEY, _sb.classList.contains('collapsed') ? '1' : '0');
}
function openMobileSidebar()  { _sb.classList.add('mobile-open');    _sbo.classList.add('show');    document.body.style.overflow='hidden'; }
function closeMobileSidebar() { _sb.classList.remove('mobile-open'); _sbo.classList.remove('show'); document.body.style.overflow=''; }
</script>