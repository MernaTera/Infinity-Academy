<nav id="teachNav" style="font-family:'DM Sans',sans-serif;position:sticky;top:0;z-index:50;
     background:rgba(255,255,255,0.95);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
     border-bottom:1px solid rgba(27,79,168,0.08);transition:box-shadow 0.3s;">

@once
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
*::before,*::after{pointer-events:none;}
#teachNav.scrolled{background:rgba(255,255,255,0.99)!important;box-shadow:0 2px 20px rgba(27,79,168,0.08);}

/* ═══════════════════════════════════════════════════════════════
   CONTAINER
═══════════════════════════════════════════════════════════════ */
.anav-container{margin:0 auto;padding:0 clamp(12px,2vw,24px);}
.anav-inner{display:flex;align-items:center;height:62px;gap:clamp(8px,1.5vw,14px);}

/* ═══════════════════════════════════════════════════════════════
   LOGO — subtle lift on hover, feels alive
═══════════════════════════════════════════════════════════════ */
.anav-logo{transition:transform 0.25s cubic-bezier(0.16,1,0.3,1),filter 0.25s;}
.anav-logo:hover{transform:translateY(-1px) scale(1.02);filter:drop-shadow(0 2px 6px rgba(27,79,168,0.15));}

/* ═══════════════════════════════════════════════════════════════
   AVATAR + USER DROPDOWN
═══════════════════════════════════════════════════════════════ */
.anav-avatar{width:34px;height:34px;border-radius:50%;background:rgba(245,145,30,0.08);
    border:1.5px solid rgba(245,145,30,0.25);display:flex;align-items:center;justify-content:center;
    transition:border-color 0.2s,transform 0.2s;flex-shrink:0;cursor:pointer;}
.anav-avatar:hover{border-color:#F5911E;transform:scale(1.05);}
.anav-avatar:active{transform:scale(0.96);}
.nav-dropdown{display:none;position:absolute;right:0;top:calc(100% + 10px);
    background:rgba(255,255,255,0.99);backdrop-filter:blur(16px);
    border:1px solid rgba(27,79,168,0.1);border-radius:8px;
    box-shadow:0 12px 40px rgba(27,79,168,0.12);min-width:200px;overflow:hidden;z-index:999;}
.nav-dropdown.open{display:block;animation:dropIn 0.22s cubic-bezier(0.16,1,0.3,1) both;}
@keyframes dropIn{from{opacity:0;transform:translateY(-6px) scale(0.98)}to{opacity:1;transform:none}}
.nav-dropdown-item{display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:10px;letter-spacing:2px;
    text-transform:uppercase;color:#7A8A9A;text-decoration:none;transition:all 0.18s;
    font-family:'DM Sans',sans-serif;width:100%;text-align:left;background:none;border:none;cursor:pointer;}
.nav-dropdown-item:hover{color:#1B4FA8;background:rgba(27,79,168,0.05);text-decoration:none;padding-left:20px;}
.nav-dropdown-item.danger:hover{color:#DC2626;background:rgba(220,38,38,0.05);}

/* ═══════════════════════════════════════════════════════════════
   BELL PANEL
═══════════════════════════════════════════════════════════════ */
#abellPanel{display:none;position:absolute;right:0;top:calc(100% + 10px);
    width:320px;max-width:calc(100vw - 32px);background:rgba(255,255,255,0.99);
    backdrop-filter:blur(16px);border:1px solid rgba(27,79,168,0.1);border-radius:8px;
    box-shadow:0 12px 40px rgba(27,79,168,0.12);overflow:hidden;z-index:999;}
.bell-badge-dot{position:absolute;top:5px;right:5px;width:7px;height:7px;
    border-radius:50%;background:#F5911E;border:1.5px solid #fff;display:none;
    animation:pulseDot 1.8s ease-in-out infinite;}
@keyframes pulseDot{0%,100%{box-shadow:0 0 0 0 rgba(245,145,30,0.5)}50%{box-shadow:0 0 0 4px rgba(245,145,30,0)}}
.nav-bell-btn{background:none;border:none;cursor:pointer;padding:7px;color:#AAB8C8;
    position:relative;transition:color 0.2s,transform 0.15s;display:flex;align-items:center;border-radius:8px;}
.nav-bell-btn:hover{color:#F5911E;background:rgba(245,145,30,0.06);}
.nav-bell-btn:active{transform:scale(0.92);}
.nav-bell-btn.ringing svg{animation:ring 0.5s ease;}
@keyframes ring{0%,100%{transform:rotate(0)}20%{transform:rotate(14deg)}40%{transform:rotate(-10deg)}60%{transform:rotate(6deg)}80%{transform:rotate(-4deg)}}

/* ═══════════════════════════════════════════════════════════════
   SIDEBAR TOGGLE + HAMBURGER
═══════════════════════════════════════════════════════════════ */
.sb-nav-toggle{background:none;border:1px solid rgba(27,79,168,0.12);border-radius:6px;cursor:pointer;
    padding:6px 8px;color:#AAB8C8;transition:all 0.2s;display:flex;align-items:center;justify-content:center;}
.sb-nav-toggle:hover{background:rgba(27,79,168,0.04);color:#1B4FA8;border-color:rgba(27,79,168,0.2);}
.nav-hamburger{background:none;border:1px solid rgba(27,79,168,0.12);border-radius:6px;cursor:pointer;
    padding:8px;display:none;flex-direction:column;gap:5px;align-items:center;justify-content:center;
    transition:background 0.2s,border-color 0.2s;}
.nav-hamburger:hover{background:rgba(27,79,168,0.04);border-color:rgba(27,79,168,0.2);}
.nav-ham-line{display:block;width:20px;height:1.5px;background:#7A8A9A;
    transition:all 0.32s cubic-bezier(0.65,0,0.35,1);transform-origin:center;}

/* ═══════════════════════════════════════════════════════════════
   MOBILE DRAWER — full concept: slide-in panel + backdrop
═══════════════════════════════════════════════════════════════ */
.amobile-backdrop{position:fixed;inset:0;background:rgba(10,20,40,0.42);backdrop-filter:blur(2px);
    z-index:59;opacity:0;pointer-events:none;transition:opacity 0.28s ease;}
.amobile-backdrop.open{opacity:1;pointer-events:auto;}

.amobile-menu{position:fixed;top:0;right:0;height:100vh;width:min(320px,86vw);z-index:60;
    background:rgba(255,255,255,0.99);backdrop-filter:blur(18px);
    box-shadow:-8px 0 34px rgba(27,79,168,0.14);
    transform:translateX(100%);transition:transform 0.32s cubic-bezier(0.16,1,0.3,1);
    display:flex;flex-direction:column;overflow:hidden;}
.amobile-menu.open{transform:translateX(0);}

.amobile-head{display:flex;align-items:center;justify-content:space-between;
    padding:16px 18px;border-bottom:1px solid rgba(27,79,168,0.07);flex-shrink:0;}
.amobile-head img{height:28px;}
.amobile-close{background:none;border:1px solid rgba(27,79,168,0.12);border-radius:7px;
    width:30px;height:30px;display:flex;align-items:center;justify-content:center;
    cursor:pointer;color:#7A8A9A;transition:all 0.2s;}
.amobile-close:hover{background:rgba(220,38,38,0.06);color:#DC2626;border-color:rgba(220,38,38,0.2);}

.amobile-user{display:flex;align-items:center;gap:10px;padding:14px 18px;
    border-bottom:1px solid rgba(27,79,168,0.07);flex-shrink:0;background:rgba(27,79,168,0.02);}
.amobile-user .anav-avatar{width:38px;height:38px;}

.amobile-scroll{flex:1;overflow-y:auto;padding:6px 0 10px;scrollbar-width:thin;
    scrollbar-color:rgba(27,79,168,0.15) transparent;}

/* Accordion groups */
.amgroup{border-bottom:1px solid rgba(27,79,168,0.05);}
.amgroup-head{width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;
    justify-content:space-between;padding:12px 18px;font-family:'DM Sans',sans-serif;
    font-size:9.5px;letter-spacing:2.5px;text-transform:uppercase;color:#AAB8C8;font-weight:700;
    transition:color 0.2s;}
.amgroup-head:hover{color:#1B4FA8;}
.amgroup-chevron{transition:transform 0.28s cubic-bezier(0.16,1,0.3,1);opacity:0.6;flex-shrink:0;}
.amgroup.open .amgroup-chevron{transform:rotate(180deg);}
.amgroup-body{max-height:0;overflow:hidden;transition:max-height 0.32s cubic-bezier(0.16,1,0.3,1);}

.amobile-nav-link{display:flex;align-items:center;gap:11px;padding:10px 18px 10px 22px;font-size:12px;
    color:#5A6A7A;text-decoration:none;transition:all 0.16s;position:relative;
    border-left:2px solid transparent;opacity:0;transform:translateX(8px);}
.amgroup.open .amobile-nav-link{animation:slideInLink 0.3s cubic-bezier(0.16,1,0.3,1) forwards;}
@keyframes slideInLink{to{opacity:1;transform:translateX(0);}}
.amobile-nav-link svg{flex-shrink:0;opacity:0.55;transition:opacity 0.16s;}
.amobile-nav-link:hover,.amobile-nav-link.active{color:#1B4FA8;background:rgba(27,79,168,0.05);text-decoration:none;border-left-color:#1B4FA8;}
.amobile-nav-link:hover svg,.amobile-nav-link.active svg{opacity:1;}
.amobile-nav-link.active{font-weight:600;background:rgba(27,79,168,0.06);}
.amobile-bdg{margin-left:auto;font-size:9px;font-weight:700;padding:1px 6px;border-radius:9px;
    background:#F5911E;color:#fff;flex-shrink:0;}

.amobile-foot{padding:12px 18px 16px;border-top:1px solid rgba(27,79,168,0.07);flex-shrink:0;}
.amobile-logout{width:100%;display:flex;align-items:center;justify-content:center;gap:8px;
    background:none;border:1px solid rgba(220,38,38,0.22);border-radius:7px;padding:10px;
    cursor:pointer;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#DC2626;
    font-family:'DM Sans',sans-serif;font-weight:600;transition:all 0.2s;}
.amobile-logout:hover{background:rgba(220,38,38,0.06);}

/* ═══════════════════════════════════════════════════════════════
   RESPONSIVE — unified breakpoint w/ sidebar's 768px collapse
═══════════════════════════════════════════════════════════════ */
.anav-desktop-user-name{display:flex;}
@media(max-width:768px){
    .anav-desktop-user-name{display:none!important;}
    .nav-hamburger{display:flex!important;}
    .sb-nav-toggle{display:none!important;}
}
@media(max-width:500px){#abellPanel{right:-70px;}}
</style>

<div class="anav-container">
    <div class="anav-inner">

        {{-- Sidebar toggle (desktop only) --}}
        <button class="sb-nav-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        {{-- Hamburger (mobile only — opens the full drawer) --}}
        <button class="nav-hamburger" onclick="openMobileNav()" id="navHamburger" aria-label="Open menu">
            <span class="nav-ham-line" id="hl1"></span>
            <span class="nav-ham-line" id="hl2"></span>
            <span class="nav-ham-line" id="hl3"></span>
        </button>

        {{-- Logo → Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" class="anav-logo" style="text-decoration:none;flex-shrink:0;" title="Go to Dashboard">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity" style="height:34px;width:auto;display:block;">
        </a>

        {{-- ═══════════════════════════════════════════════════════
             RIGHT — Bell + User menu
        ═══════════════════════════════════════════════════════ --}}
        <div style="display:flex;align-items:center;gap:4px;margin-left:auto;flex-shrink:0;">

            {{-- Bell --}}
            <div style="position:relative;" id="abellWrap">
                <button class="nav-bell-btn" onclick="toggleBell()" id="navBellBtn">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="bell-badge-dot" id="abellBadge"></span>
                </button>

                <div id="abellPanel">
                    <div style="padding:14px 16px 10px;border-bottom:1px solid rgba(27,79,168,0.06);
                                display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;color:#1B4FA8;">Notifications</span>
                            @if(isset($navUnreadCount) && $navUnreadCount > 0)
                            <span style="background:#F5911E;color:#fff;font-size:9px;padding:2px 7px;border-radius:20px;letter-spacing:0;">{{ $navUnreadCount }}</span>
                            @endif
                        </div>
                        <form method="POST" action="/notifications/mark-all-read">
                            @csrf
                            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:9px;
                                    letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;
                                    font-family:'DM Sans',sans-serif;transition:color 0.2s;"
                                    onmouseover="this.style.color='#1B4FA8'"
                                    onmouseout="this.style.color='#AAB8C8'">
                                Mark all read
                            </button>
                        </form>
                    </div>
                    <div style="max-height:300px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(27,79,168,0.1) transparent;">
                        @if(isset($navNotifications) && $navNotifications->count())
                            @foreach($navNotifications as $notif)
                            @php
                                $nColor = match($notif->related_entity_type ?? '') {
                                    'installment_approved','report_approved','refund_approved' => '#059669',
                                    'installment_rejected','report_rejected','refund_rejected' => '#DC2626',
                                    default => '#F5911E'
                                };
                                $nBg = match($notif->related_entity_type ?? '') {
                                    'installment_approved','report_approved','refund_approved' => 'rgba(5,150,105,0.1)',
                                    'installment_rejected','report_rejected','refund_rejected' => 'rgba(220,38,38,0.08)',
                                    default => 'rgba(245,145,30,0.1)'
                                };
                            @endphp
                            <a href="{{ $notif->url ?? '#' }}"
                               onclick="markRead({{ $notif->user_notification_id }})"
                               style="display:flex;align-items:flex-start;gap:11px;padding:11px 16px;
                                      text-decoration:none;border-bottom:1px solid rgba(27,79,168,0.04);
                                      background:{{ $notif->is_read ? 'transparent' : 'rgba(27,79,168,0.025)' }};
                                      transition:background 0.15s;"
                               onmouseover="this.style.background='rgba(27,79,168,0.04)'"
                               onmouseout="this.style.background='{{ $notif->is_read ? 'transparent' : 'rgba(27,79,168,0.025)' }}'">
                                <div style="width:30px;height:30px;border-radius:50%;flex-shrink:0;
                                            display:flex;align-items:center;justify-content:center;background:{{ $nBg }};">
                                    @if(str_contains($notif->related_entity_type ?? '', 'approved'))
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $nColor }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    @elseif(str_contains($notif->related_entity_type ?? '', 'rejected'))
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $nColor }}" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    @else
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $nColor }}" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:12px;color:#1A2A4A;font-weight:{{ $notif->is_read ? '400' : '600' }};margin-bottom:2px;line-height:1.4;">{{ $notif->title }}</div>
                                    <div style="font-size:11px;color:#7A8A9A;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px;">{{ Str::limit($notif->message, 55) }}</div>
                                    <div style="font-size:10px;color:#AAB8C8;">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                                </div>
                                @if(!$notif->is_read)
                                <div style="width:6px;height:6px;border-radius:50%;background:#F5911E;flex-shrink:0;margin-top:5px;"></div>
                                @endif
                            </a>
                            @endforeach
                        @else
                        <div style="padding:36px 16px;text-align:center;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DDE3EC" stroke-width="1.5" style="display:block;margin:0 auto 10px;">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            <div style="font-size:11px;color:#AAB8C8;letter-spacing:1px;text-transform:uppercase;">No notifications</div>
                        </div>
                        @endif
                    </div>
                    @if(isset($navUnreadCount) && $navUnreadCount > 0)
                    <div style="padding:10px 16px;border-top:1px solid rgba(27,79,168,0.05);text-align:center;">
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#1B4FA8;">{{ $navUnreadCount }} unread</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- User menu (desktop dropdown) --}}
            <div style="position:relative;" id="auserMenuWrap">
                <button onclick="toggleUserMenu()"
                        style="background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;
                               padding:4px 6px;border-radius:6px;transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(27,79,168,0.03)'"
                        onmouseout="this.style.background='transparent'">
                    <div class="anav-avatar">
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:#C47010;letter-spacing:1px;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </span>
                    </div>
                    <div class="anav-desktop-user-name" style="flex-direction:column;align-items:flex-start;line-height:1;">
                        <span style="font-size:12px;font-weight:600;color:#1A2A4A;white-space:nowrap;max-width:110px;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name ?? '' }}</span>
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#C47010;margin-top:2px;">Instructor</span>
                    </div>
                    <svg style="color:#AAB8C8;flex-shrink:0;" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                </button>

                <div id="auserMenuPanel" class="nav-dropdown">
                    <div style="padding:14px 16px 12px;border-bottom:1px solid rgba(27,79,168,0.06);">
                        <div style="font-size:13px;color:#1A2A4A;font-weight:600;">{{ Auth::user()->name ?? '' }}</div>
                        <div style="font-size:11px;color:#AAB8C8;margin-top:2px;">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    <div style="padding:6px 0;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-dropdown-item danger">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</nav>
{{-- ═══════════════════════════════════════════════════════════════
     MOBILE DRAWER — mirrors every link in the sidebar (26 total),
     grouped exactly like the sidebar, as a smooth accordion.
═══════════════════════════════════════════════════════════════ --}}
<div class="amobile-backdrop" id="amobileBackdrop" onclick="closeMobileNav()"></div>

<div class="amobile-menu" id="amobileMenu">
    <div class="amobile-head">
        <img src="{{ asset('images/logo.png') }}" alt="Infinity">
        <button class="amobile-close" onclick="closeMobileNav()" aria-label="Close menu">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="amobile-user">
        <div class="anav-avatar">
            <span style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:#C47010;">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
        </div>
        <div style="min-width:0;">
            <div style="font-size:13px;color:#1A2A4A;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name ?? '' }}</div>
            <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#C47010;">Instructor</div>
        </div>
    </div>

    @php
        $__pi  = \App\Models\Finance\InstallmentApprovalLog::whereHas('enrollment')->where('status','Pending')->count();
        $__pr  = \App\Models\Finance\RefundRequest::where('status','Pending')->count();
        $__prp = \App\Models\Reports\Report::where('status','Submitted')->count();
        $__pp  = \App\Models\Enrollment\Postponement::where('status','Active')->count();
    @endphp

    <div class="amobile-scroll">

        {{-- Overview --}}
        <div class="amgroup" data-group>
            <button class="amgroup-head" onclick="toggleAmGroup(this)">Overview
                <svg class="amgroup-chevron" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
            </button>
            <div class="amgroup-body">
                <a href="{{ route('teacher.dashboard') }}" class="amobile-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span class="amobile-nav-link-text">Dashboard</span>
                </a>

            </div>
        </div>

        {{-- Academic --}}
        <div class="amgroup" data-group>
            <button class="amgroup-head" onclick="toggleAmGroup(this)">Academic 
                <svg class="amgroup-chevron" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
            </button>
            <div class="amgroup-body">
                <a href="{{ route('teacher.schedule') }}" class="amobile-nav-link {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}" data-label="Patch Schedule">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span class="amobile-nav-link-text">Patch Schedule</span>
                </a>
                <a href="{{ route('teacher.courses') }}" class="amobile-nav-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}" data-label="My Courses">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span class="amobile-nav-link-text">My Courses</span>
                </a>
                <a href="{{ route('teacher.pending-approvals') }}" 
                class="amobile-nav-link {{ request()->routeIs('teacher.pending-approvals') ? 'active' : '' }}" 
                data-label="Pending Approvals">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="amobile-nav-link-text">Pending Approvals</span>
                    @php
                        $teacher = \App\Models\HR\Teacher::where('employee_id',
                            \App\Models\HR\Employee::where('user_id', auth()->id())->value('employee_id')
                        )->value('teacher_id');
                        $pendingCount = $teacher ? \App\Models\Academic\CourseInstance::where('teacher_id', $teacher)->where('status','Pending_Approval')->count() : 0;
                    @endphp
                    @if($pendingCount > 0)
                        <span class="tsl-badge">{{ $pendingCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Reports --}}
        <div class="amgroup" data-group>
            <button class="amgroup-head" onclick="toggleAmGroup(this)">Reports
                <svg class="amgroup-chevron" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
            </button>
            <div class="amgroup-body">
                <a href="{{ route('teacher.reports.index') }}" class="amobile-nav-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}" data-label="Reports">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span class="amobile-nav-link-text">Reports</span>
                </a>
            </div>
        </div>
    </div>

    <div class="amobile-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="amobile-logout">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log Out
            </button>
        </form>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════
   SCROLL SHADOW
═══════════════════════════════════════════════════════════════ */
window.addEventListener('scroll',()=>{
    document.getElementById('teachNav')?.classList.toggle('scrolled',window.scrollY>10);
},{passive:true});

/* ═══════════════════════════════════════════════════════════════
   MOBILE DRAWER OPEN/CLOSE
═══════════════════════════════════════════════════════════════ */
let mobileNavOpen=false;
function openMobileNav(){
    mobileNavOpen=true;
    document.getElementById('amobileMenu').classList.add('open');
    document.getElementById('amobileBackdrop').classList.add('open');
    document.body.style.overflow='hidden';

    const[hl1,hl2,hl3]=['hl1','hl2','hl3'].map(id=>document.getElementById(id));
    hl1.style.cssText='transform:translateY(6.5px) rotate(45deg);background:#1B4FA8';
    hl2.style.opacity='0';
    hl3.style.cssText='transform:translateY(-6.5px) rotate(-45deg);background:#1B4FA8';

    document.getElementById('auserMenuPanel')?.classList.remove('open');
    document.getElementById('abellPanel').style.display='none';

    // auto-open the group containing the active link, collapse the rest
    document.querySelectorAll('.amgroup').forEach(g=>{
        const hasActive=g.querySelector('.amobile-nav-link.active');
        setAmGroup(g,!!hasActive || g===document.querySelector('.amgroup'));
    });
}
function closeMobileNav(){
    mobileNavOpen=false;
    document.getElementById('amobileMenu').classList.remove('open');
    document.getElementById('amobileBackdrop').classList.remove('open');
    document.body.style.overflow='';
    [document.getElementById('hl1'),document.getElementById('hl2'),document.getElementById('hl3')].forEach(l=>l.style.cssText='');
}

/* Accordion groups inside the drawer */
function setAmGroup(group,open){
    const body=group.querySelector('.amgroup-body');
    group.classList.toggle('open',open);
    body.style.maxHeight=open?(body.scrollHeight+'px'):'0px';
}
function toggleAmGroup(btn){
    const group=btn.closest('.amgroup');
    setAmGroup(group,!group.classList.contains('open'));
}

/* ═══════════════════════════════════════════════════════════════
   BELL TOGGLE
═══════════════════════════════════════════════════════════════ */
function toggleBell(){
    const p=document.getElementById('abellPanel');
    const open=p.style.display==='block';
    document.getElementById('auserMenuPanel')?.classList.remove('open');
    p.style.display=open?'none':'block';
    if(!open)p.style.animation='dropIn 0.2s ease both';
}

/* ═══════════════════════════════════════════════════════════════
   USER MENU TOGGLE
═══════════════════════════════════════════════════════════════ */
function toggleUserMenu(){
    document.getElementById('auserMenuPanel').classList.toggle('open');
    document.getElementById('abellPanel').style.display='none';
}

/* ═══════════════════════════════════════════════════════════════
   CLOSE DROPDOWNS ON OUTSIDE CLICK / ESC
═══════════════════════════════════════════════════════════════ */
document.addEventListener('click',(e)=>{
    if(!document.getElementById('abellWrap')?.contains(e.target))
        document.getElementById('abellPanel').style.display='none';
    if(!document.getElementById('auserMenuWrap')?.contains(e.target))
        document.getElementById('auserMenuPanel')?.classList.remove('open');
});
document.addEventListener('keydown',(e)=>{
    if(e.key==='Escape' && mobileNavOpen) closeMobileNav();
});

/* ═══════════════════════════════════════════════════════════════
   NOTIFICATIONS: BADGE + MARK-AS-READ + SOUND + TOAST
═══════════════════════════════════════════════════════════════ */
const unread={{isset($navUnreadCount)?(int)$navUnreadCount:0}};
if(unread>0)document.getElementById('abellBadge').style.display='block';

async function markRead(id){
    try{
        await fetch(`/notifications/${id}/read`,{
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept':'application/json'
            }
        });
    }catch(e){}
}

const prevUnread={{$navPrevUnread??0}};
if(unread>prevUnread){
    // Ring the bell icon
    const bellBtn=document.getElementById('navBellBtn');
    bellBtn.classList.add('ringing');
    setTimeout(()=>bellBtn.classList.remove('ringing'),600);

    // Play notification sound
    try{
        const ctx=new(window.AudioContext||window.webkitAudioContext)();
        const osc=ctx.createOscillator(),gain=ctx.createGain();
        osc.connect(gain);gain.connect(ctx.destination);
        osc.frequency.value=520;
        gain.gain.setValueAtTime(0.2,ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001,ctx.currentTime+0.4);
        osc.start(ctx.currentTime);osc.stop(ctx.currentTime+0.4);
    }catch(e){}

    // Show toast
    const t=document.createElement('div');
    t.innerHTML=`<div style="position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 18px;background:rgba(255,255,255,0.99);border:1px solid rgba(27,79,168,0.1);border-left:3px solid #F5911E;border-radius:8px;box-shadow:0 8px 32px rgba(27,79,168,0.15);animation:toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;font-family:'DM Sans',sans-serif;min-width:240px;">
        <div style="width:32px;height:32px;border-radius:50%;background:rgba(245,145,30,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div><div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:3px;">New Notification</div><div style="font-size:13px;color:#1A2A4A;font-weight:500;">You have a new notification</div></div>
        <button onclick="this.closest('div').parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#AAB8C8;font-size:18px;line-height:1;padding:0 2px;">×</button>
    </div>`;
    document.body.appendChild(t);
    setTimeout(()=>{
        t.firstElementChild.style.animation='toastOut 0.3s ease forwards';
        setTimeout(()=>t.remove(),300);
    },4500);
}
</script>

<style>
@keyframes toastIn{from{opacity:0;transform:translateX(20px) scale(0.96)}to{opacity:1;transform:none}}
@keyframes toastOut{to{opacity:0;transform:translateX(20px) scale(0.96)}}
</style>