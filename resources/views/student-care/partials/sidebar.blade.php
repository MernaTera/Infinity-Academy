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

    <span class="sl-label">Student Care</span>

    <a href="{{ route('student-care.dashboard') }}"
       class="sl-link {{ request()->routeIs('student-care.dashboard') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        Dashboard
    </a>


    <div class="sl-div"></div>

    <span class="sl-label">Enrollment</span>

    <a href="{{ route('student-care.waiting-list') }}"
       class="sl-link {{ request()->routeIs('student-care.waiting-list') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
        </svg>
        Waiting List
    </a>

    <div class="sl-div"></div>

    <span class="sl-label">Courses</span>

    <a href="{{ route('student-care.instances') }}"
    class="sl-link {{ request()->routeIs('student-care.instances') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
        Active Courses
    </a>

</aside>