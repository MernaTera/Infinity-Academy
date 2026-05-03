<nav id="teacherNav" style="font-family:'DM Sans',sans-serif;position:sticky;top:0;z-index:50;
     background:rgba(255,255,255,0.95);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
     border-bottom:1px solid rgba(5,150,105,0.1);transition:box-shadow 0.3s;">

@once
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endonce

<style>
*::before,*::after{pointer-events:none;}
#teacherNav.scrolled{background:rgba(255,255,255,0.99)!important;box-shadow:0 2px 20px rgba(5,150,105,0.08);}

.t-nav-link{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:#7A8A9A;
    text-decoration:none;padding:6px 0;position:relative;transition:color 0.2s;white-space:nowrap;}
.t-nav-link::after{content:'';position:absolute;bottom:-1px;left:0;width:0;height:1.5px;
    background:linear-gradient(90deg,#10B981,#059669);transition:width 0.35s cubic-bezier(0.16,1,0.3,1);}
.t-nav-link:hover,.t-nav-link.active{color:#059669;text-decoration:none;}
.t-nav-link:hover::after,.t-nav-link.active::after{width:100%;}

.t-avatar{width:34px;height:34px;border-radius:50%;background:rgba(5,150,105,0.08);
    border:1.5px solid rgba(5,150,105,0.25);display:flex;align-items:center;justify-content:center;
    transition:border-color 0.25s;flex-shrink:0;cursor:pointer;}
.t-avatar:hover{border-color:#059669;}

.t-nav-dropdown{display:none;position:absolute;right:0;top:calc(100% + 10px);
    background:rgba(255,255,255,0.99);backdrop-filter:blur(16px);
    border:1px solid rgba(5,150,105,0.12);border-radius:8px;
    box-shadow:0 12px 40px rgba(5,150,105,0.1);min-width:200px;overflow:hidden;z-index:999;}
.t-nav-dropdown.open{display:block;animation:tDropIn 0.2s ease both;}
@keyframes tDropIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}

.t-dropdown-item{display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:10px;
    letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;
    transition:all 0.2s;font-family:'DM Sans',sans-serif;width:100%;text-align:left;
    background:none;border:none;cursor:pointer;}
.t-dropdown-item:hover{color:#059669;background:rgba(5,150,105,0.04);text-decoration:none;}
.t-dropdown-item.danger:hover{color:#DC2626;background:rgba(220,38,38,0.04);}

#tBellPanel{display:none;position:absolute;right:0;top:calc(100% + 10px);
    width:310px;max-width:calc(100vw - 32px);background:rgba(255,255,255,0.99);
    backdrop-filter:blur(16px);border:1px solid rgba(5,150,105,0.12);border-radius:8px;
    box-shadow:0 12px 40px rgba(5,150,105,0.1);overflow:hidden;z-index:999;}
.t-bell-dot{position:absolute;top:5px;right:5px;width:7px;height:7px;
    border-radius:50%;background:#059669;border:1.5px solid #fff;display:none;}
.t-bell-btn{background:none;border:none;cursor:pointer;padding:7px;color:#AAB8C8;
    position:relative;transition:color 0.2s;display:flex;align-items:center;}
.t-bell-btn:hover{color:#059669;}

.t-hamburger{background:none;border:none;cursor:pointer;padding:6px;display:none;
    flex-direction:column;gap:5px;align-items:center;justify-content:center;}
.t-ham-line{display:block;width:22px;height:1.5px;background:#7A8A9A;
    transition:all 0.3s;transform-origin:center;}

.t-mobile-menu{display:none;border-top:1px solid rgba(5,150,105,0.08);background:rgba(255,255,255,0.99);}
.t-mobile-menu.open{display:block;animation:tSlideDown 0.25s ease both;}
@keyframes tSlideDown{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}

.t-mobile-nav-link{display:flex;align-items:center;gap:10px;padding:13px 20px;font-size:10px;
    letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;
    border-bottom:1px solid rgba(5,150,105,0.05);transition:all 0.2s;}
.t-mobile-nav-link svg{flex-shrink:0;opacity:0.6;}
.t-mobile-nav-link:hover,.t-mobile-nav-link.active{color:#059669;background:rgba(5,150,105,0.03);text-decoration:none;}
.t-mobile-nav-link:hover svg,.t-mobile-nav-link.active svg{opacity:1;}

@keyframes toastIn{from{opacity:0;transform:translateX(20px) scale(0.96)}to{opacity:1;transform:none}}
@keyframes toastOut{to{opacity:0;transform:translateX(20px) scale(0.96)}}

@media(max-width:900px){
    .t-nav-desktop-links,.t-nav-desktop-name{display:none!important;}
    .t-hamburger{display:flex!important;}
}
@media(max-width:500px){#tBellPanel{right:-60px;}}
</style>

<div style="max-width:1600px;margin:0 auto;padding:0 20px;">
    <div style="display:flex;align-items:center;height:62px;gap:12px;">

        {{-- Hamburger (mobile) --}}
        <button class="t-hamburger" onclick="toggleTeacherMobileNav()" id="tHamburger">
            <span class="t-ham-line" id="thl1"></span>
            <span class="t-ham-line" id="thl2"></span>
            <span class="t-ham-line" id="thl3"></span>
        </button>

        {{-- Logo --}}
        <a href="{{ route('teacher.dashboard') }}" style="text-decoration:none;flex-shrink:0;">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity" style="height:34px;width:auto;display:block;">
        </a>

        {{-- Desktop Links --}}
        <div class="t-nav-desktop-links" style="display:flex;align-items:center;gap:24px;margin-left:8px;flex:1;overflow:hidden;">
            <a href="{{ route('teacher.dashboard') }}"      class="t-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('teacher.schedule') }}"       class="t-nav-link {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}">Schedule</a>
            <a href="{{ route('teacher.courses') }}"        class="t-nav-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}">Courses</a>
            <a href="{{ route('teacher.reports.index') }}"  class="t-nav-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}">Reports</a>
        </div>

        {{-- Right --}}
        <div style="display:flex;align-items:center;gap:4px;margin-left:auto;flex-shrink:0;">

            {{-- Bell --}}
            <div style="position:relative;" id="tBellWrap">
                <button class="t-bell-btn" onclick="toggleTeacherBell()">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="t-bell-dot" id="tBellBadge"></span>
                </button>

                <div id="tBellPanel">
                    <div style="padding:14px 16px 10px;border-bottom:1px solid rgba(5,150,105,0.07);
                                display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:3px;color:#059669;">Notifications</span>
                            @if(isset($navUnreadCount) && $navUnreadCount > 0)
                            <span style="background:#059669;color:#fff;font-size:9px;padding:2px 7px;border-radius:20px;letter-spacing:0;">{{ $navUnreadCount }}</span>
                            @endif
                        </div>
                        <form method="POST" action="/notifications/mark-all-read">
                            @csrf
                            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:9px;
                                    letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;
                                    font-family:'DM Sans',sans-serif;transition:color 0.2s;"
                                    onmouseover="this.style.color='#059669'"
                                    onmouseout="this.style.color='#AAB8C8'">Mark all read</button>
                        </form>
                    </div>

                    <div style="max-height:300px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(5,150,105,0.1) transparent;">
                        @if(isset($navNotifications) && $navNotifications->count())
                            @foreach($navNotifications as $notif)
                            @php
                                $nColor = match($notif->related_entity_type ?? '') {
                                    'report_approved' => '#059669',
                                    'report_rejected' => '#DC2626',
                                    default => '#10B981'
                                };
                                $nBg = match($notif->related_entity_type ?? '') {
                                    'report_approved' => 'rgba(5,150,105,0.1)',
                                    'report_rejected' => 'rgba(220,38,38,0.08)',
                                    default => 'rgba(16,185,129,0.1)'
                                };
                            @endphp
                            <a href="{{ $notif->url ?? '#' }}"
                               onclick="tMarkRead({{ $notif->user_notification_id }})"
                               style="display:flex;align-items:flex-start;gap:11px;padding:11px 16px;
                                      text-decoration:none;border-bottom:1px solid rgba(5,150,105,0.05);
                                      background:{{ $notif->is_read ? 'transparent' : 'rgba(5,150,105,0.025)' }};
                                      transition:background 0.15s;"
                               onmouseover="this.style.background='rgba(5,150,105,0.04)'"
                               onmouseout="this.style.background='{{ $notif->is_read ? 'transparent' : 'rgba(5,150,105,0.025)' }}'">
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
                                <div style="width:6px;height:6px;border-radius:50%;background:#059669;flex-shrink:0;margin-top:5px;"></div>
                                @endif
                            </a>
                            @endforeach
                        @else
                        <div style="padding:36px 16px;text-align:center;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(5,150,105,0.2)" stroke-width="1.5" style="display:block;margin:0 auto 10px;">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            <div style="font-size:11px;color:#AAB8C8;letter-spacing:1px;text-transform:uppercase;">No notifications</div>
                        </div>
                        @endif
                    </div>

                    @if(isset($navUnreadCount) && $navUnreadCount > 0)
                    <div style="padding:10px 16px;border-top:1px solid rgba(5,150,105,0.06);text-align:center;">
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;">{{ $navUnreadCount }} unread</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- User menu --}}
            <div style="position:relative;" id="tUserWrap">
                <button onclick="toggleTeacherDrop()"
                        style="background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;
                               padding:4px 6px;border-radius:6px;transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(5,150,105,0.04)'"
                        onmouseout="this.style.background='transparent'">
                    <div class="t-avatar">
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:#059669;letter-spacing:1px;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}
                        </span>
                    </div>
                    <div class="t-nav-desktop-name" style="display:flex;flex-direction:column;align-items:flex-start;line-height:1;">
                        <span style="font-size:12px;font-weight:600;color:#1A2A4A;white-space:nowrap;max-width:110px;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name ?? '' }}</span>
                        <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;margin-top:2px;">Instructor</span>
                    </div>
                    <svg style="color:#AAB8C8;flex-shrink:0;" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                </button>

                <div id="tUserMenu" class="t-nav-dropdown">
                    <div style="padding:14px 16px 12px;border-bottom:1px solid rgba(5,150,105,0.07);">
                        <div style="font-size:13px;color:#1A2A4A;font-weight:600;">{{ Auth::user()->name ?? '' }}</div>
                        <div style="font-size:11px;color:#AAB8C8;margin-top:2px;">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    <div style="padding:6px 0;">
                        <!-- <a href="{{ route('profile.edit') }}" class="t-dropdown-item">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Profile
                        </a> -->
                        <div style="height:1px;background:rgba(5,150,105,0.06);margin:4px 0;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="t-dropdown-item danger">
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
<div class="t-mobile-menu" id="tMobileMenu">
    <div style="padding:4px 0;">
        <a href="{{ route('teacher.dashboard') }}"     class="t-mobile-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('teacher.schedule') }}"      class="t-mobile-nav-link {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Schedule
        </a>
        <a href="{{ route('teacher.courses') }}"       class="t-mobile-nav-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Courses
        </a>
        <a href="{{ route('teacher.reports.index') }}" class="t-mobile-nav-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Reports
        </a>
    </div>
    <div style="padding:14px 20px;border-top:1px solid rgba(5,150,105,0.07);display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="t-avatar" style="width:38px;height:38px;">
                <span style="font-family:'Bebas Neue',sans-serif;font-size:16px;color:#059669;">{{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}</span>
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
    document.getElementById('teacherNav')?.classList.toggle('scrolled',window.scrollY>10);
},{passive:true});

let tMobileOpen=false;
function toggleTeacherMobileNav(){
    tMobileOpen=!tMobileOpen;
    document.getElementById('tMobileMenu').classList.toggle('open',tMobileOpen);
    const[hl1,hl2,hl3]=['thl1','thl2','thl3'].map(id=>document.getElementById(id));
    if(tMobileOpen){
        hl1.style.cssText='transform:translateY(6.5px) rotate(45deg);background:#059669';
        hl2.style.opacity='0';
        hl3.style.cssText='transform:translateY(-6.5px) rotate(-45deg);background:#059669';
    }else{[hl1,hl2,hl3].forEach(l=>l.style.cssText='');}
    document.getElementById('tUserMenu')?.classList.remove('open');
    document.getElementById('tBellPanel').style.display='none';
}

function toggleTeacherBell(){
    const p=document.getElementById('tBellPanel');
    const open=p.style.display==='block';
    document.getElementById('tUserMenu')?.classList.remove('open');
    p.style.display=open?'none':'block';
    if(!open)p.style.animation='tDropIn 0.2s ease both';
}

function toggleTeacherDrop(){
    document.getElementById('tUserMenu').classList.toggle('open');
    document.getElementById('tBellPanel').style.display='none';
}

document.addEventListener('click',(e)=>{
    if(!document.getElementById('tBellWrap')?.contains(e.target))
        document.getElementById('tBellPanel').style.display='none';
    if(!document.getElementById('tUserWrap')?.contains(e.target))
        document.getElementById('tUserMenu')?.classList.remove('open');
});

const tUnread={{isset($navUnreadCount)?(int)$navUnreadCount:0}};
if(tUnread>0)document.getElementById('tBellBadge').style.display='block';

async function tMarkRead(id){
    try{await fetch(`/notifications/${id}/read`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content,'Accept':'application/json'}});}catch(e){}
}

const tPrevUnread={{$navPrevUnread??0}};
if(tUnread>tPrevUnread){
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
    t.innerHTML=`<div style="position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:12px;padding:14px 18px;background:rgba(255,255,255,0.99);border:1px solid rgba(5,150,105,0.15);border-left:3px solid #059669;border-radius:8px;box-shadow:0 8px 32px rgba(5,150,105,0.15);animation:toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;font-family:'DM Sans',sans-serif;min-width:240px;">
        <div style="width:32px;height:32px;border-radius:50%;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
        <div><div style="font-size:9px;letter-spacing:3px;text-transform:uppercase;color:#059669;margin-bottom:3px;">New Notification</div><div style="font-size:13px;color:#1A2A4A;font-weight:500;">You have a new notification</div></div>
        <button onclick="this.closest('div').parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#AAB8C8;font-size:18px;line-height:1;padding:0 2px;">×</button>
    </div>`;
    document.body.appendChild(t);
    setTimeout(()=>{t.firstElementChild.style.animation='toastOut 0.3s ease forwards';setTimeout(()=>t.remove(),300);},4500);
}
</script>