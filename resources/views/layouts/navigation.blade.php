<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
     :class="scrolled ? 'shadow-md bg-white/95 backdrop-blur-md border-b border-blue-100' : 'bg-white/80 backdrop-blur-sm border-b border-blue-50'"
     class="sticky top-0 z-50 transition-all duration-500"
     style="font-family: 'DM Sans', sans-serif;">

    @once
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
    @endonce

    <style>
        .nav-link-item {
            position: relative; font-size: 11px; letter-spacing: 3px;
            text-transform: uppercase; color: #7A8A9A; text-decoration: none;
            font-weight: 400; padding: 4px 0; transition: color 0.3s;
        }
        .nav-link-item::after {
            content: ''; position: absolute; bottom: -2px; left: 0;
            width: 0; height: 1.5px;
            background: linear-gradient(90deg, #F5911E, #1B4FA8);
            transition: width 0.35s cubic-bezier(0.16,1,0.3,1);
        }
        .nav-link-item:hover, .nav-link-item.active { color: #1B4FA8; }
        .nav-link-item:hover::after, .nav-link-item.active::after { width: 100%; }

        .nav-dropdown-panel {
            background: rgba(255,255,255,0.97); backdrop-filter: blur(16px);
            border: 1px solid rgba(27,79,168,0.12); border-radius: 6px;
            box-shadow: 0 12px 40px rgba(27,79,168,0.1); overflow: hidden;
        }
        .nav-dropdown-item {
            display: block; padding: 10px 20px; font-size: 11px;
            letter-spacing: 2px; text-transform: uppercase; color: #7A8A9A;
            text-decoration: none; transition: color 0.2s, background 0.2s;
            white-space: nowrap; font-family: 'DM Sans', sans-serif;
        }
        .nav-dropdown-item:hover { color: #1B4FA8; background: rgba(27,79,168,0.04); }

        .hamburger-line {
            display: block; width: 22px; height: 1.5px;
            background: #7A8A9A; transition: all 0.3s; transform-origin: center;
        }
        .nav-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(27,79,168,0.08); border: 1.5px solid rgba(27,79,168,0.2);
            display: flex; align-items: center; justify-content: center;
            transition: border-color 0.3s; flex-shrink: 0;
        }
        .nav-avatar:hover { border-color: #1B4FA8; }
        .nav-bell {
            color: #AAB8C8; transition: color 0.2s;
            background: none; border: none; cursor: pointer; padding: 0;
        }
        .nav-bell:hover { color: #F5911E; }
        .logo-wrap img { height: 38px; width: auto; object-fit: contain; display: block; }

        /* ── Leads pill ── */
        .nav-leads-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px;
            background: rgba(27,79,168,0.05);
            border: 1px solid rgba(27,79,168,0.15);
            border-radius: 20px;
            font-size: 10px; letter-spacing: 2.5px; text-transform: uppercase;
            color: #1B4FA8; text-decoration: none; font-weight: 500;
            transition: all 0.3s; position: relative; overflow: hidden;
        }
        .nav-leads-pill::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(90deg, #1B4FA8, #2D6FDB);
            transform: scaleX(0); transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
        }
        .nav-leads-pill:hover::before,
        .nav-leads-pill.active::before { transform: scaleX(1); }
        .nav-leads-pill:hover,
        .nav-leads-pill.active { color: #fff; border-color: transparent; text-decoration: none; }
        .nav-leads-pill > * { position: relative; z-index: 1; }
    </style>

    <div class="mx-auto px-6 lg:px-8 mx-4">
        <div class="flex items-center justify-between h-[62px]">

            {{-- LOGO --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group" style="text-decoration:none;">
                <div class="logo-wrap">
                    <img src="{{ asset('images/logo.png') }}" alt="Infinity Logo">
                </div>
            </a>

            {{-- DESKTOP LINKS --}}
            <div class="hidden sm:flex items-center gap-8">
                <a href="{{ route('dashboard') }}"
                   class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                @cando('leads.view')
                <a href="{{ route('leads.index') }}"
                   class="nav-link-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                    <span>Leads</span>
                </a>

                <a href="{{ route('sales.index') }}"
                class="nav-link-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    Sales
                </a>
                @endcando
            </div>

            {{-- RIGHT SIDE --}}
            <div class="hidden sm:flex items-center gap-5">
                <button class="nav-bell">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </button>
                <div style="width:1px;height:24px;background:rgba(27,79,168,0.1);"></div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3" style="background:none;border:none;cursor:pointer;outline:none;">
                            <div class="nav-avatar">
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:13px;color:#1B4FA8;letter-spacing:1px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex flex-col items-start leading-none">
                                <span style="font-size:12px;font-weight:500;color:#1A2A4A;letter-spacing:0.3px;">{{ Auth::user()->name }}</span>
                                <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#AAB8C8;margin-top:2px;">Administrator</span>
                            </div>
                            <svg style="color:#AAB8C8;" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="nav-dropdown-panel" style="min-width:210px;">
                            <div style="padding:14px 20px 12px;border-bottom:1px solid rgba(27,79,168,0.06);">
                                <div style="font-size:13px;color:#1A2A4A;font-weight:500;">{{ Auth::user()->name }}</div>
                                <div style="font-size:10px;color:#AAB8C8;margin-top:2px;letter-spacing:0.4px;">{{ Auth::user()->email }}</div>
                            </div>
                            <div style="padding:8px 0;">
                                <a href="{{ route('profile.edit') }}" class="nav-dropdown-item">
                                    <svg style="display:inline;vertical-align:middle;margin-right:8px;opacity:0.4;" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg>Profile
                                </a>
                                <div style="height:1px;background:rgba(27,79,168,0.06);margin:4px 0;"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-dropdown-item" style="background:none;border:none;cursor:pointer;width:100%;text-align:left;">
                                        <svg style="display:inline;vertical-align:middle;margin-right:8px;opacity:0.4;" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                        </svg>Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- HAMBURGER --}}
            <button @click="open = !open" class="sm:hidden flex flex-col gap-[5px] p-2" style="background:none;border:none;cursor:pointer;">
                <span class="hamburger-line" :style="open ? 'transform:translateY(6.5px) rotate(45deg);background:#1B4FA8' : ''"></span>
                <span class="hamburger-line" :style="open ? 'opacity:0' : ''"></span>
                <span class="hamburger-line" :style="open ? 'transform:translateY(-6.5px) rotate(-45deg);background:#1B4FA8' : ''"></span>
            </button>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden"
         style="border-top:1px solid rgba(27,79,168,0.07);background:rgba(255,255,255,0.97);backdrop-filter:blur(16px);">
        <div style="padding:12px 24px;display:flex;flex-direction:column;gap:4px;">
            <a href="{{ route('dashboard') }}"
               style="font-size:11px;letter-spacing:3px;text-transform:uppercase;
                      color:{{ request()->routeIs('dashboard') ? '#1B4FA8' : '#7A8A9A' }};
                      padding:12px 0;text-decoration:none;border-bottom:1px solid rgba(27,79,168,0.05);">
                Dashboard
            </a>
            <a href="{{ route('leads.index') }}"
               style="font-size:11px;letter-spacing:3px;text-transform:uppercase;
                      color:{{ request()->routeIs('leads.*') ? '#1B4FA8' : '#7A8A9A' }};
                      padding:12px 0;text-decoration:none;">
                Leads
            </a>
            <a href="{{ route('sales.index') }}"
            style="font-size:11px;letter-spacing:3px;text-transform:uppercase;
                    color:{{ request()->routeIs('sales.*') ? '#1B4FA8' : '#7A8A9A' }};
                    padding:12px 0;text-decoration:none;border-bottom:1px solid rgba(27,79,168,0.05);">
                Sales
            </a>
            <a href="{{ route('outstanding.index') }}"
            style="font-size:11px;letter-spacing:3px;text-transform:uppercase;
                    color:{{ request()->routeIs('outstanding.*') ? '#1B4FA8' : '#7A8A9A' }};
                    padding:12px 0;text-decoration:none;">
                Outstanding
            </a>
        </div>
        <div style="padding:16px 24px;border-top:1px solid rgba(27,79,168,0.07);">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div class="nav-avatar" style="width:38px;height:38px;">
                    <span style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:#1B4FA8;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div>
                    <div style="font-size:13px;color:#1A2A4A;font-weight:500;">{{ Auth::user()->name }}</div>
                    <div style="font-size:10px;color:#AAB8C8;letter-spacing:0.4px;">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" style="display:block;font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#7A8A9A;padding:10px 0;text-decoration:none;border-bottom:1px solid rgba(27,79,168,0.05);">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%;text-align:left;background:none;border:none;font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#DC2626;padding:10px 0;cursor:pointer;font-family:'DM Sans',sans-serif;">Log Out</button>
            </form>
        </div>
    </div>
</nav>