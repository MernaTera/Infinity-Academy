<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinity Academy — Sign In</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,300&family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --orange:      #F5911E;
            --orange-light:#FFAB4A;
            --blue:        #1B4FA8;
            --blue-light:  #2D6FDB;
            --blue-dim:    rgba(27,79,168,0.10);
            --text:        #16233F;
            --muted:       #5A6A85;
            --faint:       #8A9AB5;
            --error:       #DC2626;
            --glass:       rgba(255,255,255,0.001);
            --glass-border:rgba(255,255,255,0.15);
        }

        html, body {
            height: 100%;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            overflow: hidden;
            background:
                radial-gradient(820px 480px at 10% -5%, rgba(245,145,30,0.30), transparent 60%),
                radial-gradient(900px 560px at 100% 0%, rgba(45,111,219,0.34), transparent 55%),
                radial-gradient(720px 560px at 60% 118%, rgba(127,119,221,0.24), transparent 60%),
                linear-gradient(135deg, #eaf0fb 0%, #f4eefb 48%, #fdeee0 100%);
        }

        canvas { position: fixed; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }

        .blob { position: fixed; border-radius: 50%; filter: blur(40px); z-index: 0; opacity: 0.55; pointer-events: none; }
        .blob--1 { width: 360px; height: 360px; left: -80px; top: 40%; background: radial-gradient(circle, rgba(45,111,219,0.5), transparent 70%); }
        .blob--2 { width: 320px; height: 320px; right: 4%; bottom: -80px; background: radial-gradient(circle, rgba(245,145,30,0.45), transparent 70%); }

        .scene { position: relative; z-index: 2; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 20px; }

        .card {
            width: 100%; max-width: 440px;
            background: var(--glass);
            backdrop-filter: blur(24px) saturate(170%); -webkit-backdrop-filter: blur(24px) saturate(170%);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(23,45,90,0.18), inset 0 1px 0 rgba(255,255,255,0.7);
            animation: cardIn 0.9s cubic-bezier(0.16,1,0.3,1) both;
            position: relative;
        }
        @keyframes cardIn { from { opacity: 0; transform: translateY(28px) scale(0.97); } to { opacity: 1; transform: none; } }
        /* .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--orange), var(--blue-light), transparent); } */

        .card-header { padding: 34px 40px 8px; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .logo-img { width: 150px; height: auto; margin-bottom: 16px; filter: drop-shadow(0 10px 24px rgba(27,79,168,0.22)); }
        .system-label { font-family: 'Bebas Neue', sans-serif; font-size: 10px; letter-spacing: 4px; color: var(--orange); text-transform: uppercase; margin-bottom: 4px; }
        .card-title { font-family: 'Cormorant Garamond', serif; font-size: 30px; font-weight: 300; color: var(--text); line-height: 1.1; }
        .card-title em { font-style: italic; color: var(--blue); }
        .card-body { padding: 24px 40px 34px; }

        .alert-session { display: flex; align-items: center; gap: 10px; padding: 11px 14px; background: rgba(245,145,30,0.10); border: 1px solid rgba(245,145,30,0.3); border-radius: 10px; margin-bottom: 20px; }
        .alert-session svg { flex-shrink: 0; color: var(--orange); }
        .alert-session p { font-size: 12px; color: #92400e; letter-spacing: 0.3px; line-height: 1.5; }
        .alert-success { padding: 11px 14px; background: rgba(34,197,94,0.10); border: 1px solid rgba(34,197,94,0.25); border-radius: 10px; margin-bottom: 20px; font-size: 12px; color: #15803D; letter-spacing: 0.3px; }

        .field { margin-bottom: 18px; animation: fieldIn 0.6s cubic-bezier(0.16,1,0.3,1) both; }
        .field:nth-child(1) { animation-delay: 0.15s; }
        .field:nth-child(2) { animation-delay: 0.25s; }
        @keyframes fieldIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: none; } }
        .field label { display: block; font-size: 9px; font-weight: 500; letter-spacing: 3px; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-wrap .icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--faint); transition: color 0.3s; pointer-events: none; }
        .input-wrap input { width: 100%; padding: 14px 44px 14px 42px; background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.7); border-radius: 13px; color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 300; outline: none; transition: border-color 0.3s, box-shadow 0.3s, background 0.3s; -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
        .input-wrap input::placeholder { color: var(--faint); }
        .input-wrap input:focus { border-color: var(--blue); box-shadow: 0 0 0 4px var(--blue-dim); background: rgba(255,255,255,0.78); }
        .input-wrap:focus-within .icon { color: var(--blue); }

        .input-wrap input.is-error { border-color: var(--error); background: rgba(239,68,68,0.05); }
        .input-wrap input.is-error:focus { box-shadow: 0 0 0 4px rgba(239,68,68,0.12); }
        .input-wrap.has-error .icon { color: var(--error); }

        .pw-toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 32px; height: 32px; display: grid; place-items: center; background: transparent; border: none; cursor: pointer; color: var(--faint); border-radius: 8px; transition: color 0.2s, background 0.2s; }
        .pw-toggle:hover { color: var(--blue); background: rgba(27,79,168,0.08); }
        .pw-toggle .eye-off { display: none; }
        .pw-toggle.showing .eye-open { display: none; }
        #password::-ms-reveal,
        #password::-ms-clear { display: none; }
        .pw-toggle.showing .eye-off { display: block; }

        .field-error { display: flex; align-items: center; gap: 6px; margin-top: 7px; font-size: 11px; color: var(--error); letter-spacing: 0.2px; animation: errorIn 0.3s ease both; }
        @keyframes errorIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
        .field-error svg { flex-shrink: 0; }

        .field-meta { display: flex; justify-content: flex-end; margin-top: 8px; }
        .field-meta a { font-size: 11px; color: var(--muted); text-decoration: none; letter-spacing: 0.3px; transition: color 0.2s; }
        .field-meta a:hover { color: var(--blue); }

        .remember-row { display: flex; align-items: center; gap: 10px; margin-bottom: 26px; animation: fieldIn 0.6s 0.35s cubic-bezier(0.16,1,0.3,1) both; }
        .custom-check { width: 18px; height: 18px; background: rgba(255,255,255,0.6); border: 1px solid rgba(27,79,168,0.25); border-radius: 6px; cursor: pointer; position: relative; flex-shrink: 0; transition: border-color 0.2s, background 0.2s; }
        .custom-check.checked { background: var(--blue-dim); border-color: var(--blue); }
        .custom-check.checked::after { content: ''; position: absolute; top: 3px; left: 6px; width: 5px; height: 9px; border: 1.5px solid var(--blue); border-top: none; border-left: none; transform: rotate(45deg); }
        .remember-row span { font-size: 12px; color: var(--muted); cursor: pointer; user-select: none; }

        .btn-submit { width: 100%; padding: 15px; border: none; border-radius: 14px; cursor: pointer; color: #fff; font-family: 'Bebas Neue', sans-serif; font-size: 16px; letter-spacing: 4px; background: linear-gradient(120deg, var(--blue), var(--blue-light)); box-shadow: 0 12px 30px rgba(27,79,168,0.38); transition: transform 0.25s, box-shadow 0.25s; animation: fieldIn 0.6s 0.45s cubic-bezier(0.16,1,0.3,1) both; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 16px 38px rgba(27,79,168,0.48); }
        .btn-submit span { position: relative; z-index: 1; }

        .card-footer { padding: 14px 40px; border-top: 1px solid rgba(255,255,255,0.5); display: flex; justify-content: space-between; align-items: center; }
        .version-badge { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: rgba(27,79,168,0.35); }
        .status-dot { display: flex; align-items: center; gap: 6px; font-size: 9px; letter-spacing: 1px; color: var(--muted); }
        .status-dot::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: #22C55E; box-shadow: 0 0 6px #22C55E; animation: blink 2s ease-in-out infinite; flex-shrink: 0; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        @media (max-width: 500px) {
            .card { border-radius: 18px; }
            .card-header, .card-body { padding-left: 24px; padding-right: 24px; }
            .card-footer { padding-left: 24px; padding-right: 24px; }
            .logo-img { width: 120px; }
        }
    </style>
</head>
<body>

<canvas id="c"></canvas>
<div class="blob blob--1"></div>
<div class="blob blob--2"></div>

<div class="scene">
    <div class="card">

        <div class="card-header">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity Logo" class="logo-img">
            <div class="header-text">
                <div class="system-label">Infinity Academy</div>
                <h1 class="card-title">Welcome <em>back</em></h1>
            </div>
        </div>

        <div class="card-body">

            @if (session('session_expired'))
                <div class="alert-session">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                    <p>Your session has expired. Please sign in again.</p>
                </div>
            @endif

            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">Email Address</label>
                    <div class="input-wrap {{ $errors->has('email') ? 'has-error' : '' }}">
                        <svg class="icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <input
                            id="email" type="email" name="email"
                            placeholder="name@infinity.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            class="{{ $errors->has('email') ? 'is-error' : '' }}"
                            required autofocus>
                    </div>
                    @error('email')
                        <div class="field-error">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap {{ $errors->has('password') ? 'has-error' : '' }}">
                        <svg class="icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            id="password" type="password" name="password"
                            placeholder="••••••••••"
                            autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'is-error' : '' }}"
                            required>
                        <button type="button" class="pw-toggle" id="pwToggle" onclick="togglePassword()" aria-label="Show password">
                            <svg class="eye-open" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-off" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <!-- @if (Route::has('password.request'))
                        <div class="field-meta">
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                    @endif -->
                </div>

                <div class="remember-row">
                    <div class="custom-check" id="checkBox"></div>
                    <input type="checkbox" name="remember" id="remember" hidden>
                    <span onclick="toggleCheck()">Keep me signed in</span>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Access System</span>
                </button>

            </form>
        </div>

        <div class="card-footer">
            <span class="version-badge">v1.0.0 · Developed by Merna Tera</span>
            <span class="status-dot">All systems operational</span>
        </div>

    </div>
</div>

<script>
    function toggleCheck() {
        const box   = document.getElementById('checkBox');
        const input = document.getElementById('remember');
        box.classList.toggle('checked');
        input.checked = box.classList.contains('checked');
    }
    document.getElementById('checkBox').addEventListener('click', toggleCheck);

    function togglePassword() {
        const input = document.getElementById('password');
        const btn   = document.getElementById('pwToggle');
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        btn.classList.toggle('showing', show);
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    }

    const canvas = document.getElementById('c');
    const ctx    = canvas.getContext('2d');
    let W, H, particles = [];
    function resize() { W = canvas.width = innerWidth; H = canvas.height = innerHeight; }
    function Particle() {
        this.x = Math.random() * W; this.y = Math.random() * H;
        this.vx = (Math.random() - 0.5) * 0.35; this.vy = (Math.random() - 0.5) * 0.35;
        this.r = Math.random() * 1.8 + 0.5; this.isOrange = Math.random() > 0.5;
        this.a = Math.random() * 0.45 + 0.15;
    }
    function init() { resize(); particles = Array.from({length: 110}, () => new Particle()); }
    function draw() {
        ctx.clearRect(0, 0, W, H);
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0) p.x = W; if (p.x > W) p.x = 0;
            if (p.y < 0) p.y = H; if (p.y > H) p.y = 0;
            ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = p.isOrange ? `rgba(245,145,30,${p.a})` : `rgba(27,79,168,${p.a})`;
            ctx.fill();
            for (let j = i + 1; j < particles.length; j++) {
                const q = particles[j];
                const dx = p.x - q.x, dy = p.y - q.y;
                const d = Math.sqrt(dx*dx + dy*dy);
                if (d < 120) {
                    ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = p.isOrange ? `rgba(245,145,30,${0.12*(1-d/120)})` : `rgba(27,79,168,${0.12*(1-d/120)})`;
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(draw);
    }
    window.addEventListener('resize', resize);
    init(); draw();
</script>
</body>
</html>
