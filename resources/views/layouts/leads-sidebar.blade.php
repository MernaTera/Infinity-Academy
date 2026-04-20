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
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        Leads Dashboard
    </a>
    <a href="{{ route('leads.index') }}" class="sl-link {{ request()->routeIs('leads.index') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
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
            <polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/>
            <line x1="10" y1="12" x2="14" y2="12"/>
        </svg>
        Archived
    </a>

    <div class="sl-div"></div>

    <a href="{{ route('leads.create') }}" class="sl-link {{ request()->routeIs('leads.create') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Add Lead
    </a>

    <div class="sl-div"></div>
    <span class="sl-label">Sales</span>

    <a href="{{ route('sales.index') }}" class="sl-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="20" x2="12" y2="10"/>
            <line x1="18" y1="20" x2="18" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="16"/>
        </svg>
        Sales Table
    </a>
    @endcando

</aside>