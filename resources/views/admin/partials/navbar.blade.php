<nav id="adminNav"
     style="font-family:'DM Sans',sans-serif; position:sticky; top:0; z-index:50;
            background:rgba(255,255,255,0.92); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px);
            border-bottom:1px solid rgba(27,79,168,0.08);
            transition:box-shadow 0.3s, background 0.3s;">

    @once
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @endonce

    <style>
        #adminNav.scrolled {
            background: rgba(255,255,255,0.98) !important;
            box-shadow: 0 2px 20px rgba(27,79,168,0.08);
        }
        .anav-link {
            font-size: 10px; letter-spacing: 3px; text-transform: uppercase;
            color: #7A8A9A; text-decoration: none; font-weight: 400;
            padding: 6px 0; position: relative; transition: color 0.25s;
            white-space: nowrap;
        }
        .anav-link::after {
            content: ''; position: absolute; bottom: -1px; left: 0;
            width: 0; height: 1.5px;
            background: linear-gradient(90deg, #F5911E, #1B4FA8);
            transition: width 0.35s cubic-bezier(0.16,1,0.3,1);
        }
        .anav-link:hover, .anav-link.active { color: #1B4FA8; text-decoration: none; }
        .anav-link:hover::after, .anav-link.active::after { width: 100%; }

        .anav-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(245,145,30,0.08);
            border: 1.5px solid rgba(245,145,30,0.25);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: border-color 0.25s; flex-shrink: 0;
        }
        .anav-avatar:hover { border-color: #F5911E; }

        .auser-dropdown {
            display: none; position: absolute; right: 0; top: calc(100% + 8px);
            background: rgba(255,255,255,0.98); backdrop-filter: blur(16px);
            border: 1px solid rgba(27,79,168,0.1); border-radius: 8px;
            box-shadow: 0 12px 40px rgba(27,79,168,0.12);
            min-width: 200px; overflow: hidden; z-index: 999;
        }
        .auser-dropdown.open { display: block; animation: adropIn 0.2s ease both; }
        @keyframes adropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }

        .auser-dropdown-item {
            display: block; padding: 10px 16px;
            font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
            color: #7A8A9A; text-decoration: none;
            transition: color 0.2s, background 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .auser-dropdown-item:hover { color: #1B4FA8; background: rgba(27,79,168,0.04); text-decoration: none; }
        .auser-dropdown-item.danger:hover { color: #DC2626; background: rgba(220,38,38,0.04); }

        /* Mobile */
        .amobile-menu { display: none; border-top: 1px solid rgba(27,79,168,0.07); background: rgba(255,255,255,0.98); }
        .amobile-menu.open { display: block; animation: aslideDown 0.25s ease both; }
        @keyframes aslideDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:none} }

        .amobile-nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 20px; font-size: 10px; letter-spacing: 3px;
            text-transform: uppercase; color: #7A8A9A; text-decoration: none;
            border-bottom: 1px solid rgba(27,79,168,0.04);
            transition: color 0.2s, background 0.2s;
        }
        .amobile-nav-link:hover, .amobile-nav-link.active { color: #1B4FA8; background: rgba(27,79,168,0.03); }

        .ahamburger { background: none; border: none; cursor: pointer; padding: 6px; display: none; }
        @media (max-width: 768px) {
            .anav-desktop-links { display: none !important; }
            .ahamburger { display: flex; flex-direction: column; gap: 5px; }
            #abellPanel {
                position: fixed !important;
                left: 16px !important; right: 16px !important;
                width: auto !important; top: 60px !important;
            }
        }
        .aham-line {
            display: block; width: 22px; height: 1.5px;
            background: #7A8A9A; transition: all 0.3s; transform-origin: center;
        }
        @keyframes toastIn {
            from { opacity:0; transform:translateX(20px) scale(0.96); }
            to   { opacity:1; transform:none; }
        }
    </style>

    <div style="max-width:1400px; margin:0 auto; padding:0 24px;">
        <div style="display:flex; align-items:center; justify-content:space-between; height:60px; gap:20px;">

            {{-- Logo --}}
            <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; flex-shrink:0;">
                <img src="{{ asset('images/logo.png') }}" alt="Infinity" style="height:36px; width:auto; display:block;">
            </a>

            {{-- Desktop links --}}
            <div class="anav-desktop-links" style="display:flex; align-items:center; gap:28px;">
                <a href="{{ route('admin.dashboard') }}"         class="anav-link {{ request()->routeIs('admin.dashboard')      ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.employees.index') }}"   class="anav-link {{ request()->routeIs('admin.employees.*')    ? 'active' : '' }}">Employees</a>
                <a href="{{ route('admin.sales.index') }}" class="anav-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">Sales</a>
                <a href="{{ route('admin.courses.index') }}"     class="anav-link {{ request()->routeIs('admin.courses.*')      ? 'active' : '' }}">Courses</a>
                <a href="{{ route('admin.installments.index') }}" class="anav-link {{ request()->routeIs('admin.installments.*') ? 'active' : '' }}">
                    Approvals
                    @if(isset($navUnreadCount) && $navUnreadCount > 0)
                    <span style="display:inline-block;background:#F5911E;color:#fff;font-size:8px;
                                 padding:1px 5px;border-radius:20px;margin-left:4px;vertical-align:middle;">
                        {{ $navUnreadCount }}
                    </span>
                    @endif
                </a>
                <a href="{{ route('admin.outstanding.index') }}" class="anav-link {{ request()->routeIs('admin.outstanding.*')  ? 'active' : '' }}">Outstanding</a>
                <a href="{{ route('admin.audit.index') }}"       class="anav-link {{ request()->routeIs('admin.audit.*')        ? 'active' : '' }}">Audit</a>
            </div>

            {{-- Right side --}}
            <div style="display:flex; align-items:center; gap:12px; flex-shrink:0;">

                {{-- Notification Bell --}}
                <div style="position:relative;" id="abellWrap">
                    <button onclick="toggleAdminBell()"
                            style="background:none; border:none; cursor:pointer; padding:6px;
                                   color:#AAB8C8; position:relative; transition:color 0.2s;"
                            onmouseover="this.style.color='#F5911E'"
                            onmouseout="this.style.color='#AAB8C8'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <span id="abellBadge" style="position:absolute; top:4px; right:4px;
                              width:7px; height:7px; border-radius:50%;
                              background:#F5911E; border:1.5px solid #fff; display:none;">
                        </span>
                    </button>

                    <div id="abellPanel" style="display:none; position:absolute; right:0; top:calc(100% + 8px);
                            width:320px; max-width:calc(100vw - 80px);
                            background:rgba(255,255,255,0.98); backdrop-filter:blur(16px);
                            border:1px solid rgba(27,79,168,0.1); border-radius:8px;
                            box-shadow:0 12px 40px rgba(27,79,168,0.12); overflow:hidden; z-index:999;">

                        {{-- Header --}}
                        <div style="padding:14px 16px 12px; border-bottom:1px solid rgba(27,79,168,0.06);
                                    display:flex; align-items:center; justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:3px; color:#1B4FA8;">Notifications</span>
                                @if(isset($navUnreadCount) && $navUnreadCount > 0)
                                <span style="background:#F5911E;color:#fff;font-size:9px;padding:2px 7px;border-radius:20px;">
                                    {{ $navUnreadCount }}
                                </span>
                                @endif
                            </div>
                            <form method="POST" action="/notifications/mark-all-read" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none;border:none;cursor:pointer;font-size:9px;
                                        letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;font-family:'DM Sans',sans-serif;">
                                    Mark all read
                                </button>
                            </form>
                        </div>

                        {{-- List --}}
                        <div style="max-height:320px;overflow-y:auto;">
                            @if(isset($navNotifications) && $navNotifications->count())
                                @foreach($navNotifications as $notif)
                                <a href="{{ $notif->url ?? '#' }}"
                                   onclick="markRead({{ $notif->user_notification_id }})"
                                   style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;
                                          text-decoration:none;border-bottom:1px solid rgba(27,79,168,0.04);
                                          background:{{ $notif->is_read ? 'transparent' : 'rgba(27,79,168,0.025)' }};
                                          transition:background 0.2s;"
                                   onmouseover="this.style.background='rgba(27,79,168,0.04)'"
                                   onmouseout="this.style.background='{{ $notif->is_read ? 'transparent' : 'rgba(27,79,168,0.025)' }}'">

                                    <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;
                                                display:flex;align-items:center;justify-content:center;
                                                background:{{ $notif->related_entity_type === 'installment_approved' ? 'rgba(5,150,105,0.1)' : ($notif->related_entity_type === 'installment_rejected' ? 'rgba(220,38,38,0.08)' : 'rgba(245,145,30,0.1)') }};">
                                        @if($notif->related_entity_type === 'installment_approved')
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                        @elseif($notif->related_entity_type === 'installment_rejected')
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        @else
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        @endif
                                    </div>

                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:12px;color:#1A2A4A;font-weight:{{ $notif->is_read ? '400' : '500' }};margin-bottom:3px;">
                                            {{ $notif->title }}
                                        </div>
                                        <div style="font-size:11px;color:#7A8A9A;line-height:1.5;margin-bottom:4px;
                                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ Str::limit($notif->message, 65) }}
                                        </div>
                                        <div style="font-size:10px;color:#AAB8C8;">
                                            {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                        </div>
                                    </div>

                                    @if(!$notif->is_read)
                                    <div style="width:6px;height:6px;border-radius:50%;background:#F5911E;flex-shrink:0;margin-top:4px;"></div>
                                    @endif
                                </a>
                                @endforeach
                            @else
                                <div style="padding:36px 16px;text-align:center;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#AAB8C8" stroke-width="1.5" style="margin:0 auto 10px;display:block;">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                    </svg>
                                    <div style="font-size:11px;color:#AAB8C8;letter-spacing:1px;">No notifications</div>
                                </div>
                            @endif
                        </div>

                        @if(isset($navNotifications) && $navNotifications->count())
                        <div style="padding:10px 16px;border-top:1px solid rgba(27,79,168,0.05);text-align:center;">
                            <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#1B4FA8;">
                                {{ $navUnreadCount ?? 0 }} unread
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- User menu --}}
                <div style="position:relative;" id="auserMenuWrap">
                    <button onclick="toggleAdminUserMenu()"
                            style="background:none; border:none; cursor:pointer; display:flex; align-items:center; gap:10px; padding:4px;">
                        <div class="anav-avatar">
                            <span style="font-family:'Bebas Neue',sans-serif; font-size:14px; color:#C47010; letter-spacing:1px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="anav-desktop-links" style="display:flex; flex-direction:column; align-items:flex-start; line-height:1;">
                            <span style="font-size:12px; font-weight:500; color:#1A2A4A; white-space:nowrap;">{{ Auth::user()->name }}</span>
                            <span style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#C47010; margin-top:2px;">Administrator</span>
                        </div>
                        <svg style="color:#AAB8C8;" width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>

                    <div id="auserMenuPanel" class="auser-dropdown">
                        <div style="padding:14px 16px 12px; border-bottom:1px solid rgba(27,79,168,0.06);">
                            <div style="font-size:13px; color:#1A2A4A; font-weight:500;">{{ Auth::user()->name }}</div>
                            <div style="font-size:10px; color:#AAB8C8; margin-top:2px; letter-spacing:0.3px;">{{ Auth::user()->email }}</div>
                        </div>
                        <div style="padding:6px 0;">
                            <a href="{{ route('profile.edit') }}" class="auser-dropdown-item">Profile</a>
                            <div style="height:1px; background:rgba(27,79,168,0.05); margin:4px 0;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="auser-dropdown-item danger"
                                        style="background:none; border:none; cursor:pointer; width:100%; text-align:left;">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="amobile-menu" id="amobileMenu">
        <div style="padding:8px 0;">
            <a href="{{ route('admin.dashboard') }}"          class="amobile-nav-link {{ request()->routeIs('admin.dashboard')      ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.employees.index') }}"    class="amobile-nav-link {{ request()->routeIs('admin.employees.*')    ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Employees
            </a>
            <a href="{{ route('admin.sales.index') }}" class="amobile-nav-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Sales
            </a>
            <a href="{{ route('admin.courses.index') }}"      class="amobile-nav-link {{ request()->routeIs('admin.courses.*')      ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Courses
            </a>
            <a href="{{ route('admin.installments.index') }}" class="amobile-nav-link {{ request()->routeIs('admin.installments.*') ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Approvals
            </a>
            <a href="{{ route('admin.outstanding.index') }}"  class="amobile-nav-link {{ request()->routeIs('admin.outstanding.*')  ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Outstanding
            </a>
            <a href="{{ route('admin.audit.index') }}"        class="amobile-nav-link {{ request()->routeIs('admin.audit.*')        ? 'active' : '' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Audit
            </a>
        </div>
        <div style="padding:14px 20px; border-top:1px solid rgba(27,79,168,0.06); display:flex; align-items:center; gap:12px;">
            <div class="anav-avatar" style="width:38px; height:38px;">
                <span style="font-family:'Bebas Neue',sans-serif; font-size:16px; color:#C47010;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
            </div>
            <div>
                <div style="font-size:13px; color:#1A2A4A; font-weight:500;">{{ Auth::user()->name }}</div>
                <div style="font-size:10px; color:#AAB8C8; letter-spacing:0.3px;">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>
</nav>

<script>
// Scroll
window.addEventListener('scroll', function() {
    const nav = document.getElementById('adminNav');
    if (nav) nav.classList.toggle('scrolled', window.scrollY > 10);
});

// User menu
function toggleAdminUserMenu() {
    document.getElementById('auserMenuPanel').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap  = document.getElementById('auserMenuWrap');
    const panel = document.getElementById('auserMenuPanel');
    if (wrap && !wrap.contains(e.target) && panel) panel.classList.remove('open');
});

// Bell
function toggleAdminBell() {
    const panel = document.getElementById('abellPanel');
    const isOpen = panel.style.display !== 'none';
    document.getElementById('auserMenuPanel')?.classList.remove('open');
    panel.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) panel.style.animation = 'adropIn 0.2s ease both';
}
document.addEventListener('click', function(e) {
    const wrap  = document.getElementById('abellWrap');
    const panel = document.getElementById('abellPanel');
    if (wrap && !wrap.contains(e.target) && panel) panel.style.display = 'none';
});

// Badge
const aUnreadCount = {{ isset($navUnreadCount) ? $navUnreadCount : 0 }};
if (aUnreadCount > 0) {
    const badge = document.getElementById('abellBadge');
    if (badge) badge.style.display = 'block';
}

async function markRead(id) {
    await fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
    });
}

// Mobile menu
let amobileOpen = false;
function toggleAdminMobileMenu() {
    amobileOpen = !amobileOpen;
    document.getElementById('amobileMenu').classList.toggle('open', amobileOpen);
    const hl1 = document.getElementById('ahl1');
    const hl2 = document.getElementById('ahl2');
    const hl3 = document.getElementById('ahl3');
    if (amobileOpen) {
        hl1.style.transform = 'translateY(6.5px) rotate(45deg)'; hl1.style.background = '#1B4FA8';
        hl2.style.opacity = '0';
        hl3.style.transform = 'translateY(-6.5px) rotate(-45deg)'; hl3.style.background = '#1B4FA8';
    } else {
        hl1.style.transform = ''; hl1.style.background = '';
        hl2.style.opacity = '';
        hl3.style.transform = ''; hl3.style.background = '';
    }
}
// Notification sound + popup
const prevUnread = {{ $navPrevUnread ?? 0 }};
const currUnread = {{ isset($navUnreadCount) ? $navUnreadCount : 0 }};

if (currUnread > prevUnread) {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.frequency.value = 520;
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
    osc.start(ctx.currentTime);
    osc.stop(ctx.currentTime + 0.4);

    // Popup toast
    const toast = document.createElement('div');
    toast.innerHTML = `
        <div style="position:fixed;bottom:24px;right:24px;z-index:99999;
                    display:flex;align-items:center;gap:12px;
                    padding:14px 18px;
                    background:rgba(255,255,255,0.98);
                    border:1px solid rgba(27,79,168,0.12);
                    border-left:3px solid #F5911E;
                    border-radius:8px;
                    box-shadow:0 8px 32px rgba(27,79,168,0.15);
                    animation:toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
                    font-family:'DM Sans',sans-serif;">
            <div style="width:32px;height:32px;border-radius:50%;background:rgba(245,145,30,0.1);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <div>
                <div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:2px;">New Notification</div>
                <div style="font-size:13px;color:#1A2A4A;">You have a new notification</div>
            </div>
        </div>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>