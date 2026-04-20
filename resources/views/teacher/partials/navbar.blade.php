<nav style="position:sticky;top:0;z-index:50;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid rgba(21,128,61,0.1);font-family:'DM Sans',sans-serif;">

    @once
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @endonce

    <style>
        .t-nav-link{font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;font-weight:400;padding:4px 0;transition:color 0.3s;position:relative}
        .t-nav-link::after{content:'';position:absolute;bottom:-2px;left:0;width:0;height:1.5px;background:linear-gradient(90deg,#059669,#10B981);transition:width 0.35s cubic-bezier(0.16,1,0.3,1)}
        .t-nav-link:hover,.t-nav-link.active{color:#059669}
        .t-nav-link:hover::after,.t-nav-link.active::after{width:100%}
        .t-avatar{width:32px;height:32px;border-radius:50%;background:rgba(5,150,105,0.1);border:1.5px solid rgba(5,150,105,0.3);display:flex;align-items:center;justify-content:center}
        .t-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:rgba(5,150,105,0.1);border:1px solid rgba(5,150,105,0.2);border-radius:20px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;font-weight:500}
        .t-dropdown{position:absolute;right:0;top:calc(100% + 10px);min-width:210px;background:rgba(255,255,255,0.97);backdrop-filter:blur(16px);border:1px solid rgba(5,150,105,0.12);border-radius:6px;box-shadow:0 12px 40px rgba(5,150,105,0.1);overflow:hidden;display:none}
        .t-dropdown.show{display:block}
        .t-dropdown-item{display:block;padding:10px 20px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#7A8A9A;text-decoration:none;transition:color 0.2s,background 0.2s;white-space:nowrap;font-family:'DM Sans',sans-serif;background:none;border:none;cursor:pointer;width:100%;text-align:left}
        .t-dropdown-item:hover{color:#059669;background:rgba(5,150,105,0.04)}
    </style>

    <div style="max-width:100%;padding:0 24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;height:62px;">

            {{-- LOGO --}}
            <a href="{{ route('teacher.dashboard') }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
                <img src="{{ asset('images/logo.png') }}" alt="Infinity" style="height:36px;width:auto;object-fit:contain;">

            </a>

            {{-- DESKTOP LINKS --}}
            <div style="display:flex;align-items:center;gap:28px;">
                <a href="{{ route('teacher.dashboard') }}"
                   class="t-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('teacher.schedule') }}"
                   class="t-nav-link {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}">
                    Schedule
                </a>
                <a href="{{ route('teacher.courses') }}"
                   class="t-nav-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}">
                    Courses
                </a>
                <a href="{{ route('teacher.reports.index') }}"
                   class="t-nav-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}">
                    Reports
                </a>
            </div>

            {{-- RIGHT --}}
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:1px;height:24px;background:rgba(5,150,105,0.1)"></div>
                <div style="position:relative;" id="teacherDropWrap">
                    <button onclick="toggleTeacherDrop()"
                        style="display:flex;align-items:center;gap:10px;background:none;border:none;cursor:pointer;outline:none;">
                        <div class="t-avatar">
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:13px;color:#059669;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-start;line-height:1;">
                            <span style="font-size:12px;font-weight:500;color:#1A2A4A;">{{ Auth::user()->name }}</span>
                            <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#059669;margin-top:2px;">Instructor</span>
                        </div>
                        <svg style="color:#AAB8C8" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                    </button>

                    <div class="t-dropdown" id="teacherDropMenu">
                        <div style="padding:14px 20px 12px;border-bottom:1px solid rgba(5,150,105,0.06);">
                            <div style="font-size:13px;color:#1A2A4A;font-weight:500;">{{ Auth::user()->name }}</div>
                            <div style="font-size:10px;color:#AAB8C8;margin-top:2px;">{{ Auth::user()->email }}</div>
                        </div>
                        <div style="padding:8px 0;">
                            <a href="{{ route('profile.edit') }}" class="t-dropdown-item">
                                <svg style="display:inline;vertical-align:middle;margin-right:8px;opacity:0.4" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Profile
                            </a>
                            <div style="height:1px;background:rgba(5,150,105,0.06);margin:4px 0;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="t-dropdown-item">
                                    <svg style="display:inline;vertical-align:middle;margin-right:8px;opacity:0.4" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    function toggleTeacherDrop() {
        document.getElementById('teacherDropMenu').classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('teacherDropWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('teacherDropMenu').classList.remove('show');
        }
    });
    </script>
</nav>