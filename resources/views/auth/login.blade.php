<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinity System — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #C9A84C;
            --gold-light: #E8C97A;
            --gold-dim: rgba(201, 168, 76, 0.15);
            --bg: #080808;
            --surface: #0F0F0F;
            --surface2: #161616;
            --text: #F0EDE6;
            --muted: #6B6560;
            --border: rgba(201, 168, 76, 0.2);
        }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            overflow: hidden;
        }

        /* ── BACKGROUND ── */
        .scene {
            position: fixed; inset: 0;
            display: grid;
            place-items: center;
        }

        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(201,168,76,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,168,76,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridDrift 20s linear infinite;
        }

        @keyframes gridDrift {
            from { background-position: 0 0; }
            to   { background-position: 60px 60px; }
        }

        .bg-radial {
            position: absolute;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201,168,76,0.07) 0%, transparent 65%);
            animation: pulse 8s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50%       { transform: scale(1.1); opacity: 1; }
        }

        .bg-corner {
            position: absolute;
            border: 1px solid var(--border);
            border-radius: 50%;
            pointer-events: none;
        }
        .bg-corner:nth-child(1) { width: 500px; height: 500px; top: -150px; left: -150px; opacity: 0.3; }
        .bg-corner:nth-child(2) { width: 300px; height: 300px; bottom: -80px; right: -80px; opacity: 0.2; }

        /* ── CARD ── */
        .card {
            position: relative;
            width: 460px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
            animation: cardIn 0.9s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to   { opacity: 1; transform: none; }
        }

        /* top gold line */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        /* ── HEADER ── */
        .card-header {
            padding: 48px 48px 32px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .logo-mark {
            width: 48px; height: 48px;
            flex-shrink: 0;
            position: relative;
        }

        .logo-mark svg {
            width: 100%; height: 100%;
        }

        .header-text {}

        .system-label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 11px;
            letter-spacing: 4px;
            color: var(--gold);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 300;
            color: var(--text);
            line-height: 1.1;
        }

        .card-title em {
            font-style: italic;
            color: var(--gold-light);
        }

        /* ── BODY ── */
        .card-body {
            padding: 36px 48px 48px;
        }

        .field {
            margin-bottom: 22px;
            animation: fieldIn 0.6s cubic-bezier(0.16,1,0.3,1) both;
        }

        .field:nth-child(1) { animation-delay: 0.2s; }
        .field:nth-child(2) { animation-delay: 0.3s; }

        @keyframes fieldIn {
            from { opacity: 0; transform: translateX(-12px); }
            to   { opacity: 1; transform: none; }
        }

        .field label {
            display: block;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            transition: color 0.3s;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            background: var(--surface2);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 2px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 300;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-wrap input::placeholder { color: var(--muted); }

        .input-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-dim);
        }

        .input-wrap input:focus ~ .icon,
        .input-wrap:focus-within .icon {
            color: var(--gold);
        }

        .field-meta {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .field-meta a {
            font-size: 11px;
            color: var(--muted);
            text-decoration: none;
            letter-spacing: 0.5px;
            transition: color 0.2s;
        }

        .field-meta a:hover { color: var(--gold); }

        /* remember row */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
            animation: fieldIn 0.6s 0.4s cubic-bezier(0.16,1,0.3,1) both;
        }

        .custom-check {
            width: 16px; height: 16px;
            background: var(--surface2);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 2px;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: border-color 0.2s, background 0.2s;
        }

        .custom-check.checked {
            background: var(--gold-dim);
            border-color: var(--gold);
        }

        .custom-check.checked::after {
            content: '';
            position: absolute;
            top: 3px; left: 5px;
            width: 4px; height: 7px;
            border: 1.5px solid var(--gold);
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        .remember-row span {
            font-size: 12px;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }

        /* btn */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: transparent;
            border: 1px solid var(--gold);
            border-radius: 2px;
            color: var(--gold);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 15px;
            letter-spacing: 4px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: color 0.4s;
            animation: fieldIn 0.6s 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gold);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
        }

        .btn-submit:hover::before { transform: scaleX(1); }
        .btn-submit:hover { color: var(--bg); }

        .btn-submit span { position: relative; z-index: 1; }

        /* divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
            animation: fieldIn 0.6s 0.6s cubic-bezier(0.16,1,0.3,1) both;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.05);
        }

        .divider span {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* register link */
        .register-prompt {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            animation: fieldIn 0.6s 0.7s cubic-bezier(0.16,1,0.3,1) both;
        }

        .register-prompt a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .register-prompt a:hover { color: var(--gold-light); }

        /* ── FOOTER ── */
        .card-footer {
            padding: 16px 48px;
            border-top: 1px solid rgba(255,255,255,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .version-badge {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.15);
        }

        .status-dot {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.2);
        }

        .status-dot::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #4ADE80;
            box-shadow: 0 0 6px #4ADE80;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        /* error */
        .alert-error {
            padding: 12px 16px;
            background: rgba(239,68,68,0.07);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 2px;
            margin-bottom: 24px;
            font-size: 12px;
            color: #FCA5A5;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>

<div class="scene">
    <div class="bg-grid"></div>
    <div class="bg-radial"></div>
    <div class="bg-corner"></div>
    <div class="bg-corner"></div>

    <div class="card">

        <div class="card-header">
            <!-- Logo Mark -->
            <div class="logo-mark">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="24,4 44,14 44,34 24,44 4,34 4,14" stroke="#C9A84C" stroke-width="1" fill="rgba(201,168,76,0.05)"/>
                    <polygon points="24,10 38,18 38,30 24,38 10,30 10,18" stroke="#C9A84C" stroke-width="0.5" fill="none" opacity="0.5"/>
                    <line x1="24" y1="4" x2="24" y2="44" stroke="#C9A84C" stroke-width="0.5" opacity="0.3"/>
                    <line x1="4" y1="14" x2="44" y2="34" stroke="#C9A84C" stroke-width="0.5" opacity="0.3"/>
                    <line x1="44" y1="14" x2="4" y2="34" stroke="#C9A84C" stroke-width="0.5" opacity="0.3"/>
                    <circle cx="24" cy="24" r="4" fill="#C9A84C" opacity="0.9"/>
                    <circle cx="24" cy="24" r="7" stroke="#C9A84C" stroke-width="0.5" fill="none" opacity="0.4"/>
                </svg>
            </div>

            <div class="header-text">
                <div class="system-label">Infinity System</div>
                <h1 class="card-title">Welcome <em>back</em></h1>
            </div>
        </div>

        <div class="card-body">

            {{-- @if ($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
            @endif --}}

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="name@company.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="••••••••••"
                            required
                        >
                    </div>
                    <div class="field-meta">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>
                </div>

                <div class="remember-row" id="rememberRow">
                    <div class="custom-check" id="checkBox"></div>
                    <input type="checkbox" name="remember" id="remember" hidden>
                    <span onclick="toggleCheck()">Keep me signed in</span>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Access System</span>
                </button>

                <!-- @if (Route::has('register'))
                <div class="divider"><span>or</span></div>
                <p class="register-prompt">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create one →</a>
                </p>
                @endif -->

            </form>
        </div>

        <div class="card-footer">
            <span class="version-badge">v1.0.0</span>
            <span class="status-dot">All systems operational Developed by Merna Tera</span>
        </div>
    </div>
</div>

<script>
    function toggleCheck() {
        const box = document.getElementById('checkBox');
        const input = document.getElementById('remember');
        box.classList.toggle('checked');
        input.checked = box.classList.contains('checked');
    }
    document.getElementById('checkBox').addEventListener('click', toggleCheck);
</script>

</body>
</html>