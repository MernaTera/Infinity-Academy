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
            --orange-dim:  rgba(245,145,30,0.1);
            --blue:        #1B4FA8;
            --blue-light:  #2D6FDB;
            --blue-dim:    rgba(27,79,168,0.08);
            --bg:          #F8F6F2;
            --text:        #1A2A4A;
            --muted:       #7A8A9A;
            --border:      rgba(27,79,168,0.15);
            --surface:     rgba(255,255,255,0.6);
            --input-bg:    rgba(255,255,255,0.8);
            --error:       #DC2626;
            --error-bg:    rgba(239,68,68,0.06);
            --error-border:rgba(239,68,68,0.25);
        }

        html, body { height: 100%; background: var(--bg); color: var(--text); font-family: 'DM Sans', sans-serif; font-weight: 300; overflow: hidden; }
        canvas { position: fixed; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .grid-overlay { position: fixed; inset: 0; background-image: linear-gradient(rgba(27,79,168,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(27,79,168,0.06) 1px, transparent 1px); background-size: 70px 70px; z-index: 1; animation: gridMove 25s linear infinite; }
        @keyframes gridMove { from { background-position: 0 0; } to { background-position: 70px 70px; } }
        .scene { position: relative; z-index: 2; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 20px; }
        .card { width: 100%; max-width: 460px; background: var(--surface); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.75); border-radius: 8px; overflow: hidden; box-shadow: 0 8px 40px rgba(27,79,168,0.1), 0 2px 8px rgba(27,79,168,0.05), inset 0 1px 0 rgba(255,255,255,0.9); animation: cardIn 0.9s cubic-bezier(0.16,1,0.3,1) both; position: relative; }
        @keyframes cardIn { from { opacity: 0; transform: translateY(28px) scale(0.97); } to { opacity: 1; transform: none; } }
        .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, var(--orange), var(--blue-light), transparent); }
        .card-header { padding: 10px 15px 10px; border-bottom: 1px solid rgba(27,79,168,0.06); display: flex; align-items: center; gap: 16px; overflow: hidden; }
        .logo-img { width: 160px; height: auto; mix-blend-mode: multiply; flex-shrink: 0; }
        .header-divider { width: 1px; height: 40px; background: linear-gradient(to bottom, transparent, rgba(27,79,168,0.2), transparent); flex-shrink: 0; }
        .system-label { font-family: 'Bebas Neue', sans-serif; font-size: 10px; letter-spacing: 4px; color: var(--orange); text-transform: uppercase; margin-bottom: 4px; }
        .card-title { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 300; color: var(--text); line-height: 1.1; }
        .card-title em { font-style: italic; color: var(--blue); }
        .card-body { padding: 32px 40px 36px; }

        /* ── ALERTS ── */
        .alert-session { display: flex; align-items: center; gap: 10px; padding: 11px 14px; background: rgba(245,145,30,0.07); border: 1px solid rgba(245,145,30,0.3); border-radius: 4px; margin-bottom: 20px; }
        .alert-session svg { flex-shrink: 0; color: var(--orange); }
        .alert-session p { font-size: 12px; color: #92400e; letter-spacing: 0.3px; line-height: 1.5; }
        .alert-success { padding: 11px 14px; background: rgba(34,197,94,0.06); border: 1px solid rgba(34,197,94,0.2); border-radius: 4px; margin-bottom: 20px; font-size: 12px; color: #15803D; letter-spacing: 0.3px; }

        /* ── FIELD ── */
        .field { margin-bottom: 20px; animation: fieldIn 0.6s cubic-bezier(0.16,1,0.3,1) both; }
        .field:nth-child(1) { animation-delay: 0.2s; }
        .field:nth-child(2) { animation-delay: 0.3s; }
        @keyframes fieldIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: none; } }
        .field label { display: block; font-size: 9px; font-weight: 500; letter-spacing: 3px; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-wrap .icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); transition: color 0.3s; pointer-events: none; }
        .input-wrap input { width: 100%; padding: 13px 14px 13px 42px; background: var(--input-bg); border: 1px solid rgba(27,79,168,0.12); border-radius: 4px; color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 300; outline: none; transition: border-color 0.3s, box-shadow 0.3s; }
        .input-wrap input::placeholder { color: #B0BCCC; }
        .input-wrap input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-dim); }
        .input-wrap:focus-within .icon { color: var(--blue); }

        /* error state */
        .input-wrap input.is-error { border-color: var(--error); background: rgba(239,68,68,0.02); }
        .input-wrap input.is-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.10); }
        .input-wrap.has-error .icon { color: var(--error); }

        /* ── FIELD ERROR MSG ── */
        .field-error { display: flex; align-items: center; gap: 6px; margin-top: 7px; font-size: 11px; color: var(--error); letter-spacing: 0.2px; animation: errorIn 0.3s ease both; }
        @keyframes errorIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
        .field-error svg { flex-shrink: 0; }

        .field-meta { display: flex; justify-content: flex-end; margin-top: 6px; }
        .field-meta a { font-size: 11px; color: var(--muted); text-decoration: none; letter-spacing: 0.3px; transition: color 0.2s; }
        .field-meta a:hover { color: var(--blue); }

        /* ── REMEMBER ── */
        .remember-row { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; animation: fieldIn 0.6s 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .custom-check { width: 16px; height: 16px; background: var(--input-bg); border: 1px solid rgba(27,79,168,0.2); border-radius: 3px; cursor: pointer; position: relative; flex-shrink: 0; transition: border-color 0.2s, background 0.2s; }
        .custom-check.checked { background: var(--blue-dim); border-color: var(--blue); }
        .custom-check.checked::after { content: ''; position: absolute; top: 2px; left: 4px; width: 5px; height: 8px; border: 1.5px solid var(--blue); border-top: none; border-left: none; transform: rotate(45deg); }
        .remember-row span { font-size: 12px; color: var(--muted); cursor: pointer; user-select: none; }

        /* ── SUBMIT ── */
        .btn-submit { width: 100%; padding: 14px; background: transparent; border: 1.5px solid var(--blue); border-radius: 4px; color: var(--blue); font-family: 'Bebas Neue', sans-serif; font-size: 14px; letter-spacing: 4px; cursor: pointer; position: relative; overflow: hidden; transition: color 0.4s, border-color 0.4s; animation: fieldIn 0.6s 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .btn-submit::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, var(--blue), var(--blue-light)); transform: scaleX(0); transform-origin: left; transition: transform 0.4s cubic-bezier(0.16,1,0.3,1); }
        .btn-submit:hover::before { transform: scaleX(1); }
        .btn-submit:hover { color: #fff; border-color: var(--blue-light); }
        .btn-submit span { position: relative; z-index: 1; }

        /* ── FOOTER ── */
        .card-footer { padding: 14px 40px; border-top: 1px solid rgba(27,79,168,0.06); display: flex; justify-content: space-between; align-items: center; }
        .version-badge { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: rgba(27,79,168,0.25); }
        .status-dot { display: flex; align-items: center; gap: 6px; font-size: 9px; letter-spacing: 1px; color: var(--muted); }
        .status-dot::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: #22C55E; box-shadow: 0 0 6px #22C55E; animation: blink 2s ease-in-out infinite; flex-shrink: 0; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        .corner { position: fixed; width: 56px; height: 56px; pointer-events: none; z-index: 2; opacity: 0; animation: fadeIn 1.2s 1s ease forwards; }
        @keyframes fadeIn { to { opacity: 1; } }
        .corner--tl { top: 28px; left: 28px;   border-top: 1px solid var(--border); border-left:  1px solid var(--border); }
        .corner--tr { top: 28px; right: 28px;   border-top: 1px solid var(--border); border-right: 1px solid var(--border); }
        .corner--bl { bottom: 28px; left: 28px;  border-bottom: 1px solid var(--border); border-left:  1px solid var(--border); }
        .corner--br { bottom: 28px; right: 28px; border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); }

        @media (max-width: 500px) {
            .card { border-radius: 6px; }
            .card-header, .card-body { padding-left: 24px; padding-right: 24px; }
            .card-footer { padding-left: 24px; padding-right: 24px; }
            .logo-img { width: 80px; }
            .corner { display: none; }
        }
    </style>
</head>
<body>

<canvas id="c"></canvas>
<div class="grid-overlay"></div>
<div class="corner corner--tl"></div>
<div class="corner corner--tr"></div>
<div class="corner corner--bl"></div>
<div class="corner corner--br"></div>

<div class="scene">
    <div class="card">

        <div class="card-header">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity Logo" class="logo-img">
            <div class="header-divider"></div>
            <div class="header-text">
                <div class="system-label">Infinity Academy</div>
                <h1 class="card-title">Welcome <em>back</em></h1>
            </div>
        </div>

        <div class="card-body">

            {{-- Session expired --}}
            @if (session('session_expired'))
                <div class="alert-session">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                    <p>Your session has expired. Please sign in again.</p>
                </div>
            @endif

            {{-- Password reset success --}}
            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
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

                {{-- Password --}}
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
                    </div>
                    @error('password')
                        <div class="field-error">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    @if (Route::has('password.request'))
                        <div class="field-meta">
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                    @endif
                </div>

                {{-- Remember --}}
                <div class="remember-row">
                    <div class="custom-check" id="checkBox"></div>
                    <input type="checkbox" name="remember" id="remember" hidden>
                    <span onclick="toggleCheck()">Keep me signed in</span>
                </div>

                {{-- Submit --}}
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