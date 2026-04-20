<aside style="width:220px;flex-shrink:0;background:rgba(255,255,255,0.85);
              backdrop-filter:blur(12px);border-right:1px solid rgba(27,79,168,0.08);
              padding:28px 0;position:sticky;top:62px;height:calc(100vh - 62px);
              overflow-y:auto;font-family:'DM Sans',sans-serif;">

    <style>
        .sl-label { font-size:8px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;padding:0 20px;margin-bottom:8px;margin-top:20px;display:block; }
        .sl-label:first-child { margin-top:0; }
        .sl-link { display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;transition:all 0.2s;border-left:2px solid transparent; }
        .sl-link:hover { color:#1B4FA8;background:rgba(27,79,168,0.03);border-left-color:rgba(27,79,168,0.2);text-decoration:none; }
        .sl-link.active { color:#1B4FA8;background:rgba(27,79,168,0.05);border-left-color:#1B4FA8;font-weight:500; }
        .sl-link svg { flex-shrink:0;opacity:0.6; }
        .sl-link.active svg { opacity:1; }
        .sl-div { height:1px;background:rgba(27,79,168,0.06);margin:12px 20px; }
    </style>

    {{-- Overview --}}
    <span class="sl-label">Overview</span>
    <a href="{{ route('admin.dashboard') }}" class="sl-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>

    <div class="sl-div"></div>

    {{-- HR --}}
    <span class="sl-label">Human Resources</span>
    <a href="{{ route('admin.employees.index') }}" class="sl-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Employees
    </a>

    <div class="sl-div"></div>

    {{-- Academic --}}
    <span class="sl-label">Academic</span>
    <a href="{{ route('admin.courses.index') }}" class="sl-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        Courses
    </a>
    <a href="{{ route('admin.patches.index') }}" class="sl-link {{ request()->routeIs('admin.patches.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Patches
    </a>

    <div class="sl-div"></div>

    {{-- Financial --}}
    <span class="sl-label">Financial</span>
    <a href="{{ route('admin.payment-policy.index') }}" class="sl-link {{ request()->routeIs('admin.payment-policy.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Payment Plans
    </a>
    <a href="{{ route('admin.installments.index') }}" class="sl-link {{ request()->routeIs('admin.installments.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Installment Approvals
    </a>
    <a href="{{ route('admin.outstanding.index') }}" class="sl-link {{ request()->routeIs('admin.outstanding.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Outstanding Risk
    </a>
    <a href="{{ route('admin.offers.index') }}" class="sl-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        Offers
    </a>

    <div class="sl-div"></div>

    {{-- Monitoring --}}
    <span class="sl-label">Monitoring</span>
    <a href="{{ route('admin.audit.index') }}" class="sl-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Audit Logs
    </a>

</aside>