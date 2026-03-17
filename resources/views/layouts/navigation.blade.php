<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
     :class="scrolled ? 'border-b border-[rgba(201,168,76,0.15)] bg-[rgba(6,6,6,0.95)] backdrop-blur-md' : 'border-b border-[rgba(201,168,76,0.08)] bg-[#060606]'"
     class="sticky top-0 z-50 transition-all duration-500"
     style="font-family: 'DM Sans', sans-serif;">

    {{-- Google Fonts (only loads once if not already in layout) --}}
    @once
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital@1&display=swap" rel="stylesheet">
    @endonce

    <style>
        .nav-link-item {
            position: relative;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #5A5550;
            text-decoration: none;
            font-weight: 400;
            padding: 4px 0;
            transition: color 0.3s;
        }
        .nav-link-item::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 1px;
            background: #C9A84C;
            transition: width 0.35s cubic-bezier(0.16,1,0.3,1);
        }
        .nav-link-item:hover,
        .nav-link-item.active { color: #C9A84C; }
        .nav-link-item:hover::after,
        .nav-link-item.active::after { width: 100%; }

        .nav-dropdown-panel {
            background: #0F0F0F;
            border: 1px solid rgba(201,168,76,0.15);
            border-radius: 2px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            overflow: hidden;
        }
        .nav-dropdown-item {
            display: block;
            padding: 10px 20px;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #5A5550;
            text-decoration: none;
            transition: color 0.2s, background 0.2s;
            white-space: nowrap;
        }
        .nav-dropdown-item:hover {
            color: #C9A84C;
            background: rgba(201,168,76,0.06);
        }

        .hamburger-line {
            display: block;
            width: 22px; height: 1px;
            background: #5A5550;
            transition: all 0.3s;
            transform-origin: center;
        }
    </style>

    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-[60px]">

            {{-- ── LOGO ── --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" style="text-decoration:none;">
                {{-- Mini hex logo --}}
                <svg width="32" height="32" viewBox="0 0 110 110" fill="none"
                     style="filter: drop-shadow(0 0 8px rgba(201,168,76,0.3)); transition: filter 0.3s;"
                     class="group-hover:[filter:drop-shadow(0_0_14px_rgba(201,168,76,0.55))]">
                    <polygon points="55,6 100,30 100,80 55,104 10,80 10,30"
                             stroke="#C9A84C" stroke-width="1.5" fill="rgba(201,168,76,0.05)"/>
                    <line x1="55" y1="6"  x2="55" y2="104" stroke="#C9A84C" stroke-width="0.5" opacity="0.25"/>
                    <line x1="10" y1="30" x2="100" y2="80" stroke="#C9A84C" stroke-width="0.5" opacity="0.25"/>
                    <line x1="100" y1="30" x2="10" y2="80" stroke="#C9A84C" stroke-width="0.5" opacity="0.25"/>
                    <circle cx="55" cy="55" r="7" fill="#C9A84C" opacity="0.9"/>
                    <circle cx="55" cy="55" r="3" fill="#060606"/>
                </svg>

                <div class="flex flex-col leading-none">
                    <span style="font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:4px; color:#C9A84C; line-height:1;">
                        INFINITY Academy
                    </span>
                    <span style="font-family:'Cormorant Garamond',serif; font-size:10px; font-style:italic; letter-spacing:3px; color: #8A7A6A; line-height:1.4;">
                        System
                    </span>
                </div>
            </a>

            {{-- ── DESKTOP NAV ── --}}
            <div class="hidden sm:flex items-center gap-8">
                <a href="{{ route('dashboard') }}"
                   class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>

                {{-- Add more nav links here as needed --}}
                {{-- Example:
                <a href="{{ route('customers.index') }}"
                   class="nav-link-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    Customers
                </a>
                --}}
            </div>

            {{-- ── USER DROPDOWN ── --}}
            <div class="hidden sm:flex items-center gap-6">

                {{-- Notification bell (optional) --}}
                <button style="color:#3A3530; transition:color 0.2s;" onmouseover="this.style.color='#C9A84C'" onmouseout="this.style.color='#3A3530'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 group" style="background:none;border:none;cursor:pointer;outline:none;">
                            {{-- Avatar circle --}}
                            <div style="width:32px;height:32px;border-radius:50%;background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;transition:border-color 0.3s;"
                                 class="group-hover:border-[#C9A84C]">
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:13px;color:#C9A84C;letter-spacing:1px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>

                            <div class="flex flex-col items-start leading-none">
                                <span style="font-size:11px;font-weight:500;color:#C9A9A0;letter-spacing:0.5px;">
                                    {{ Auth::user()->name }}
                                </span>
                                <span style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:#3A3530;margin-top:2px;">
                                    Administrator
                                </span>
                            </div>

                            <svg style="color:#3A3530;transition:transform 0.3s;" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="nav-dropdown-panel" style="min-width:200px;">
                            {{-- User info header --}}
                            <div style="padding:14px 20px 12px;border-bottom:1px solid rgba(255,255,255,0.04);">
                                <div style="font-size:12px;color:#C9A9A0;font-weight:500;">{{ Auth::user()->name }}</div>
                                <div style="font-size:10px;color:#3A3530;margin-top:2px;letter-spacing:0.5px;">{{ Auth::user()->email }}</div>
                            </div>

                            <div style="padding:8px 0;">
                                <a href="{{ route('profile.edit') }}" class="nav-dropdown-item">
                                    <span style="margin-right:10px;opacity:0.5;">
                                        <svg style="display:inline;vertical-align:middle;" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    Profile
                                </a>

                                <div style="height:1px;background:rgba(255,255,255,0.04);margin:6px 0;"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-dropdown-item w-full text-left"
                                            style="background:none;border:none;cursor:pointer;width:100%;">
                                        <span style="margin-right:10px;opacity:0.5;">
                                            <svg style="display:inline;vertical-align:middle;" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                                <polyline points="16 17 21 12 16 7"/>
                                                <line x1="21" y1="12" x2="9" y2="12"/>
                                            </svg>
                                        </span>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- ── HAMBURGER (mobile) ── --}}
            <button @click="open = !open" class="sm:hidden flex flex-col gap-[5px] p-2" style="background:none;border:none;cursor:pointer;">
                <span class="hamburger-line" :style="open ? 'transform:translateY(6px) rotate(45deg);background:#C9A84C' : ''"></span>
                <span class="hamburger-line" :style="open ? 'opacity:0' : ''"></span>
                <span class="hamburger-line" :style="open ? 'transform:translateY(-6px) rotate(-45deg);background:#C9A84C' : ''"></span>
            </button>

        </div>
    </div>

    {{-- ── MOBILE MENU ── --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden"
         style="border-top:1px solid rgba(201,168,76,0.08);background:#060606;">

        <div style="padding:16px 24px;display:flex;flex-direction:column;gap:4px;">
            <a href="{{ route('dashboard') }}"
               style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:{{ request()->routeIs('dashboard') ? '#C9A84C' : '#5A5550' }};padding:10px 0;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.03);">
                Dashboard
            </a>
        </div>

        {{-- Mobile user section --}}
        <div style="padding:16px 24px;border-top:1px solid rgba(201,168,76,0.08);">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.3);display:flex;align-items:center;justify-content:center;">
                    <span style="font-family:'Bebas Neue',sans-serif;font-size:15px;color:#C9A84C;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <div style="font-size:13px;color:#C9A9A0;font-weight:500;">{{ Auth::user()->name }}</div>
                    <div style="font-size:10px;color:#3A3530;letter-spacing:0.5px;">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}"
               style="display:block;font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#5A5550;padding:10px 0;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.03);">
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%;text-align:left;background:none;border:none;font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#5A5550;padding:10px 0;cursor:pointer;font-family:'DM Sans',sans-serif;">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</nav>