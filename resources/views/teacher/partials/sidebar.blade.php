<aside style="width:210px;flex-shrink:0;background:rgba(255,255,255,0.85);
              backdrop-filter:blur(12px);border-right:1px solid rgba(5,150,105,0.08);
              padding:28px 0;position:sticky;top:62px;height:calc(100vh - 62px);
              overflow-y:auto;font-family:'DM Sans',sans-serif;">

    <style>
        .tsl-label{font-size:8px;letter-spacing:3px;text-transform:uppercase;color:#059669;padding:0 20px;margin-bottom:8px;margin-top:20px;display:block}
        .tsl-label:first-child{margin-top:0}
        .tsl-link{display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;transition:all 0.2s;border-left:2px solid transparent}
        .tsl-link:hover{color:#059669;background:rgba(5,150,105,0.03);border-left-color:rgba(5,150,105,0.2);text-decoration:none}
        .tsl-link.active{color:#059669;background:rgba(5,150,105,0.05);border-left-color:#059669;font-weight:500}
        .tsl-link svg{flex-shrink:0;opacity:0.6}
        .tsl-link.active svg{opacity:1}
        .tsl-div{height:1px;background:rgba(5,150,105,0.06);margin:12px 20px}
        .tsl-badge{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;border-radius:9px;background:rgba(220,38,38,0.1);color:#DC2626;font-size:9px;font-weight:600;margin-left:auto}
    </style>

    {{-- Overview --}}
    <span class="tsl-label">Overview</span>
    <a href="{{ route('teacher.dashboard') }}"
       class="tsl-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        Dashboard
    </a>

    <div class="tsl-div"></div>

    {{-- Academic --}}
    <span class="tsl-label">Academic</span>

    <a href="{{ route('teacher.schedule') }}"
       class="tsl-link {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Patch Schedule
        <span style="font-size:8px;letter-spacing:1px;color:#AAB8C8;margin-left:auto;text-transform:uppercase">View</span>
    </a>

    <a href="{{ route('teacher.courses') }}"
       class="tsl-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
        My Courses
    </a>

    <div class="tsl-div"></div>

    {{-- Reports --}}
    <span class="tsl-label">Reports</span>

    <a href="{{ route('teacher.reports.index') }}"
       class="tsl-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
        </svg>
        Reports
    </a>

</aside>