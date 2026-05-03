<nav id="mainNav" style="font-family:'DM Sans',sans-serif;position:sticky;top:0;z-index:50;
     background:rgba(255,255,255,0.95);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
     border-bottom:1px solid rgba(27,79,168,0.08);transition:box-shadow 0.3s;">

@once
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
*::before,*::after{pointer-events:none;}
#mainNav.scrolled{background:rgba(255,255,255,0.99)!important;box-shadow:0 2px 20px rgba(27,79,168,0.08);}

.nav-link{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:#7A8A9A;
    text-decoration:none;padding:6px 0;position:relative;transition:color 0.2s;white-space:nowrap;}
.nav-link::after{content:'';position:absolute;bottom:-1px;left:0;width:0;height:1.5px;
    background:linear-gradient(90deg,#F5911E,#1B4FA8);transition:width 0.35s cubic-bezier(0.16,1,0.3,1);}
.nav-link:hover,.nav-link.active{color:#1B4FA8;text-decoration:none;}
.nav-link:hover::after,.nav-link.active::after{width:100%;}

.nav-avatar{width:34px;height:34px;border-radius:50%;background:rgba(27,79,168,0.07);
    border:1.5px solid rgba(27,79,168,0.18);display:flex;align-items:center;justify-content:center;
    transition:border-color 0.25s;flex-shrink:0;cursor:pointer;}
.nav-avatar:hover{border-color:#1B4FA8;}

.nav-dropdown{display:none;position:absolute;right:0;top:calc(100% + 10px);
    background:rgba(255,255,255,0.99);backdrop-filter:blur(16px);
    border:1px solid rgba(27,79,168,0.1);border-radius:8px;
    box-shadow:0 12px 40px rgba(27,79,168,0.12);min-width:200px;overflow:hidden;z-index:999;}
.nav-dropdown.open{display:block;animation:dropIn 0.2s ease both;}
@keyframes dropIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}

.nav-dropdown-item{display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:10px;
    letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;
    transition:all 0.2s;font-family:'DM Sans',sans-serif;width:100%;text-align:left;
    background:none;border:none;cursor:pointer;}
.nav-dropdown-item:hover{color:#1B4FA8;background:rgba(27,79,168,0.04);text-decoration:none;}
.nav-dropdown-item.danger:hover{color:#DC2626;background:rgba(220,38,38,0.04);}

#bellPanel{display:none;position:absolute;right:0;top:calc(100% + 10px);
    width:310px;max-width:calc(100vw - 32px);background:rgba(255,255,255,0.99);
    backdrop-filter:blur(16px);border:1px solid rgba(27,79,168,0.1);border-radius:8px;
    box-shadow:0 12px 40px rgba(27,79,168,0.12);overflow:hidden;z-index:999;}
.bell-badge-dot{position:absolute;top:5px;right:5px;width:7px;height:7px;
    border-radius:50%;background:#F5911E;border:1.5px solid #fff;display:none;}
.nav-bell-btn{background:none;border:none;cursor:pointer;padding:7px;color:#AAB8C8;
    position:relative;transition:color 0.2s;display:flex;align-items:center;}
.nav-bell-btn:hover{color:#F5911E;}

.nav-hamburger{background:none;border:none;cursor:pointer;padding:6px;display:none;
    flex-direction:column;gap:5px;align-items:center;justify-content:center;}
.nav-ham-line{display:block;width:22px;height:1.5px;background:#7A8A9A;
    transition:all 0.3s;transform-origin:center;}

.amobile-menu{display:none;border-top:1px solid rgba(27,79,168,0.07);background:rgba(255,255,255,0.99);}
.amobile-menu.open{display:block;animation:slideDown 0.25s ease both;}
@keyframes slideDown{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}

.amobile-nav-link{display:flex;align-items:center;gap:10px;padding:13px 20px;font-size:10px;
    letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;
    border-bottom:1px solid rgba(27,79,168,0.04);transition:all 0.2s;}
.amobile-nav-link svg{flex-shrink:0;opacity:0.6;}
.amobile-nav-link:hover,.amobile-nav-link.active{color:#1B4FA8;background:rgba(27,79,168,0.03);text-decoration:none;}
.amobile-nav-link:hover svg,.amobile-nav-link.active svg{opacity:1;}

@keyframes toastIn{from{opacity:0;transform:translateX(20px) scale(0.96)}to{opacity:1;transform:none}}
@keyframes toastOut{to{opacity:0;transform:translateX(20px) scale(0.96)}}

@media(max-width:900px){
    .nav-desktop-links,.nav-desktop-user-name{display:none!important;}
    .nav-hamburger{display:flex!important;}
}
@media(max-width:500px){#bellPanel{right:-60px;}}
</style>

<div style="max-width:1600px;margin:0 auto;padding:0 20px;">
    <div style="display:flex;align-items:center;height:62px;gap:12px;">

        {{-- Hamburger (mobile) --}}
        <button class="nav-hamburger" onclick="toggleMobileNav()" id="navHamburger">
            <span class="nav-ham-line" id="hl1"></span>
            <span class="nav-ham-line" id="hl2"></span>
            <span class="nav-ham-line" id="hl3"></span>
        </button>

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" style="text-decoration:none;flex-shrink:0;">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity" style="height:34px;width:auto;display:block;">
        </a>

        {{-- Desktop Links --}}
        <div class="nav-desktop-links" style="display:flex;align-items:center;gap:24px;margin-left:8px;flex:1;overflow:hidden;">
            <a href="{{ route('dashboard') }}"          class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            @cando('leads.view')
            <a href="{{ route('leads.index') }}"        class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">Leads</a>
            <a href="{{ route('sales.index') }}"        class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">Sales</a>
            <a href="{{ route('outstanding.index') }}"  class="nav-link {{ request()->routeIs('outstanding.*') ? 'active' : '' }}">Outstanding</a>
            @endcando
        </div>

        {{-- Right --}}
        <div style="display:flex;align-items:center;gap:4px;margin-left:auto;flex-shrink:0;">

            {{-- Bell --}}
            <div style="position:relative;" id="bellWrap">
                <button class="nav-bell-btn" onclick="toggleBell()">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="bell-badge-dot" id="bellBadge"></span>
                </button>

                <div id="bellPanel">
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
                                    onmouseout="this.style.color='#AAB8C8'">Mark all read</button>
                        </form>
                    </div>

                    <div style="max-height:300px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(27,79,168,0.1) transparent;">
                        @if(isset($navNotifications) && $navNotifications->count())
                            @foreach($navNotifications as $notif)
                            @php
                                $nColor = match($notif->related_entity_type ?? '') {
                                    'installment_approved','report_approved' => '#059669',
                                    'installment_rejected','report_rejected' => '#DC2626',
                                    default => '#F5911E'
                                };
                                $nBg = match($notif->related_entity_type ?? '') {
                                    'installment_approved','report_approved' => 'rgba(5,150,105,0.1)',
                                    'installment_rejected','report_rejected' => 'rgba(220,38,38,0.08)',
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

            {{-- User menu --}}
            <div style="position:relative;" id="userMenuWrap">
                <button onclick="toggleUserMenu()"
                        style="background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;
                               padding:4px 6px;border-radius:6px;transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(27,79,168,0.03)'"
                        onmouseout="this.style.background='transparent'">
                    <div class="nav-avatar">
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:#1B4FA8;letter-spacing:1px;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div class="nav-desktop-user-name" style="display:flex;flex-direction:column;align-items:flex-start;line-height:1;">
                        <span style="font-size:12px;font-weight:600;color:#1A2A4A;white-space:nowrap;max-width:110px;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name ?? '' }}</span>
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-top:2px;">CS User</span>
                    </div>
                    <svg style="color:#AAB8C8;flex-shrink:0;" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                </button>

                <div id="userMenuPanel" class="nav-dropdown">
                    <div style="padding:14px 16px 12px;border-bottom:1px solid rgba(27,79,168,0.06);">
                        <div style="font-size:13px;color:#1A2A4A;font-weight:600;">{{ Auth::user()->name ?? '' }}</div>
                        <div style="font-size:11px;color:#AAB8C8;margin-top:2px;">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    <div style="padding:6px 0;">
                        <a href="{{ route('profile.edit') }}" class="nav-dropdown-item">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Profile
                        </a>
                        <div style="height:1px;background:rgba(27,79,168,0.05);margin:4px 0;"></div>
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

{{-- Mobile Menu --}}
<div class="amobile-menu" id="mobileMenu">
    <div style="padding:4px 0;">
        <a href="{{ route('dashboard') }}" class="amobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        @cando('leads.view')
        <a href="{{ route('leads.index') }}" class="amobile-nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Leads
        </a>
        <a href="{{ route('sales.index') }}" class="amobile-nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            Sales
        </a>
        <a href="{{ route('outstanding.index') }}" class="amobile-nav-link {{ request()->routeIs('outstanding.*') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Outstanding
        </a>
        @endcando
    </div>
    <div style="padding:14px 20px;border-top:1px solid rgba(27,79,168,0.06);display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="nav-avatar" style="width:38px;height:38px;">
                <span style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:#1B4FA8;">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
            </div>
            <div>
                <div style="font-size:13px;color:#1A2A4A;font-weight:600;">{{ Auth::user()->name ?? '' }}</div>
                <div style="font-size:10px;color:#AAB8C8;">{{ Auth::user()->email ?? '' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:1px solid rgba(220,38,38,0.2);border-radius:4px;
                    padding:6px 12px;cursor:pointer;font-size:9px;letter-spacing:2px;text-transform:uppercase;
                    color:#DC2626;font-family:'DM Sans',sans-serif;transition:all 0.2s;"
                    onmouseover="this.style.background='rgba(220,38,38,0.04)'"
                    onmouseout="this.style.background='none'">Logout</button>
        </form>
    </div>
</div>

</nav>

<script>
window.addEventListener('scroll',()=>{
    document.getElementById('mainNav')?.classList.toggle('scrolled',window.scrollY>10);
},{passive:true});

// Mobile nav
let mobileNavOpen=false;
function toggleMobileNav(){
    mobileNavOpen=!mobileNavOpen;
    document.getElementById('mobileMenu').classList.toggle('open',mobileNavOpen);
    const[hl1,hl2,hl3]=['hl1','hl2','hl3'].map(id=>document.getElementById(id));
    if(mobileNavOpen){
        hl1.style.cssText='transform:translateY(6.5px) rotate(45deg);background:#1B4FA8';
        hl2.style.opacity='0';
        hl3.style.cssText='transform:translateY(-6.5px) rotate(-45deg);background:#1B4FA8';
    }else{[hl1,hl2,hl3].forEach(l=>l.style.cssText='');}
    document.getElementById('userMenuPanel')?.classList.remove('open');
    document.getElementById('bellPanel').style.display='none';
}

// Bell
function toggleBell(){
    const p=document.getElementById('bellPanel');
    const open=p.style.display==='block';
    document.getElementById('userMenuPanel')?.classList.remove('open');
    p.style.display=open?'none':'block';
    if(!open)p.style.animation='dropIn 0.2s ease both';
}

// User menu
function toggleUserMenu(){
    document.getElementById('userMenuPanel').classList.toggle('open');
    document.getElementById('bellPanel').style.display='none';
}

// Close on outside click
document.addEventListener('click',(e)=>{
    if(!document.getElementById('bellWrap')?.contains(e.target))
        document.getElementById('bellPanel').style.display='none';
    if(!document.getElementById('userMenuWrap')?.contains(e.target))
        document.getElementById('userMenuPanel')?.classList.remove('open');
});

// Badge
const unreadCount={{isset($navUnreadCount)?(int)$navUnreadCount:0}};
if(unreadCount>0)document.getElementById('bellBadge').style.display='block';

async function markRead(id){
    try{await fetch(`/notifications/${id}/read`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content,'Accept':'application/json'}});}catch(e){}
}

// Toast + sound
const prevUnread={{$navPrevUnread??0}};
const currUnread={{isset($navUnreadCount)?(int)$navUnreadCount:0}};
if(currUnread>prevUnread){
    try{
        const ctx=new(window.AudioContext||window.webkitAudioContext)();
        const osc=ctx.createOscillator(),gain=ctx.createGain();
        osc.connect(gain);gain.connect(ctx.destination);
        osc.frequency.value=520;
        gain.gain.setValueAtTime(0.2,ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001,ctx.currentTime+0.4);
        osc.start(ctx.currentTime);osc.stop(ctx.currentTime+0.4);
    }catch(e){}
    const t=document.createElement('div');
    t.innerHTML=`<div style="position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 18px;background:rgba(255,255,255,0.99);border:1px solid rgba(27,79,168,0.1);border-left:3px solid #F5911E;border-radius:8px;box-shadow:0 8px 32px rgba(27,79,168,0.15);animation:toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;font-family:'DM Sans',sans-serif;min-width:240px;">
        <div style="width:32px;height:32px;border-radius:50%;background:rgba(245,145,30,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F5911E" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div><div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#F5911E;margin-bottom:3px;">New Notification</div><div style="font-size:13px;color:#1A2A4A;font-weight:500;">You have a new notification</div></div>
        <button onclick="this.closest('div').parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#AAB8C8;font-size:18px;line-height:1;padding:0 2px;">×</button>
    </div>`;
    document.body.appendChild(t);
    setTimeout(()=>{t.firstElementChild.style.animation='toastOut 0.3s ease forwards';setTimeout(()=>t.remove(),300);},4500);
}
</script>