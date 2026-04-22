<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | Infinity Academy</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,300;1,600&family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --orange:      #F5911E;
            --orange-dim:  rgba(245,145,30,0.12);
            --blue:        #1B4FA8;
            --blue-light:  #2D6FDB;
            --blue-dim:    rgba(27,79,168,0.08);
            --bg:          #F8F6F2;
            --text:        #1A2A4A;
            --muted:       #7A8A9A;
            --border:      rgba(27,79,168,0.12);
            --surface:     rgba(255,255,255,0.55);
        }

        html, body {
            height: 100%; width: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            overflow: hidden;
        }

        /* ── CANVAS ── */
        canvas {
            position: fixed; inset: 0;
            width: 100%; height: 100%;
            pointer-events: none; z-index: 0;
        }

        /* ── GRID ── */
        .grid {
            position: fixed; inset: 0; z-index: 1;
            background-image:
                linear-gradient(rgba(27,79,168,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(27,79,168,0.05) 1px, transparent 1px);
            background-size: 70px 70px;
            animation: gridDrift 30s linear infinite;
        }
        @keyframes gridDrift {
            from { background-position: 0 0; }
            to   { background-position: 70px 70px; }
        }

        /* ── SCENE ── */
        .scene {
            position: relative; z-index: 2;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            overflow: hidden;
        }

        /* ── GIANT 403 ── */
        .giant-number {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(180px, 28vw, 380px);
            line-height: 0.85;
            letter-spacing: -4px;
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(27,79,168,0.18);
            position: relative;
            user-select: none;
            animation: numberIn 1.2s cubic-bezier(0.16,1,0.3,1) both;
        }

        /* fill with gradient on hover / animated */
        .giant-number::after {
            content: '403';
            position: absolute; inset: 0;
            font-family: 'Bebas Neue', sans-serif;
            font-size: inherit;
            line-height: inherit;
            letter-spacing: inherit;
            background: linear-gradient(135deg,
                rgba(245,145,30,0.08) 0%,
                rgba(27,79,168,0.06) 50%,
                rgba(245,145,30,0.04) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            -webkit-text-fill-color: transparent;
            animation: shimmer 4s ease-in-out infinite alternate;
        }

        @keyframes shimmer {
            from { filter: brightness(1); }
            to   { filter: brightness(1.5); }
        }

        @keyframes numberIn {
            from { opacity: 0; transform: scale(1.08) translateY(-30px); }
            to   { opacity: 1; transform: none; }
        }

        /* ── DIVIDER LINE ── */
        .divider {
            width: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--orange), var(--blue-light), transparent);
            margin: 24px 0 32px;
            animation: lineExpand 1s 0.6s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes lineExpand {
            from { width: 0; opacity: 0; }
            to   { width: min(520px, 80vw); opacity: 1; }
        }

        /* ── CONTENT BLOCK ── */
        .content {
            text-align: center;
            animation: contentIn 0.8s 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes contentIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: none; }
        }

        .label-top {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 11px;
            letter-spacing: 6px;
            color: var(--orange);
            text-transform: uppercase;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .label-top::before,
        .label-top::after {
            content: '';
            width: 24px; height: 1px;
            background: var(--orange);
            opacity: 0.5;
        }

        .headline {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(28px, 4vw, 48px);
            font-weight: 300;
            color: var(--text);
            line-height: 1.1;
            margin-bottom: 16px;
        }
        .headline em {
            font-style: italic;
            color: var(--blue);
        }

        .subtext {
            font-size: 13px;
            color: var(--muted);
            letter-spacing: 0.3px;
            line-height: 1.7;
            max-width: 380px;
            margin: 0 auto 36px;
        }

        /* ── ACTION ROW ── */
        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: transparent;
            border: 1.5px solid var(--blue);
            border-radius: 4px;
            color: var(--blue);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: 4px;
            text-decoration: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: color 0.4s, border-color 0.4s;
        }
        .btn-primary::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, var(--blue), var(--blue-light));
            transform: scaleX(0); transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
        }
        .btn-primary:hover::before { transform: scaleX(1); }
        .btn-primary:hover { color: #fff; border-color: var(--blue-light); }
        .btn-primary span { position: relative; z-index: 1; }
        .btn-primary svg { position: relative; z-index: 1; transition: transform 0.3s; }
        .btn-primary:hover svg { transform: translateX(-3px); }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 24px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 400;
            letter-spacing: 0.5px;
            text-decoration: none;
            border-radius: 4px;
            transition: color 0.3s;
        }
        .btn-ghost:hover { color: var(--text); }

        /* ── META BAR ── */
        .meta-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 14px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 3;
            border-top: 1px solid rgba(27,79,168,0.06);
            animation: fadeUp 0.8s 1.2s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: none; }
        }

        .meta-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .meta-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 11px;
            letter-spacing: 3px;
            color: rgba(27,79,168,0.3);
        }
        .meta-sep { color: rgba(27,79,168,0.15); font-size: 10px; }
        .meta-right {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(27,79,168,0.2);
        }

        /* ── CORNERS ── */
        .corner {
            position: fixed; width: 40px; height: 40px;
            pointer-events: none; z-index: 2;
            opacity: 0;
            animation: fadeIn 1s 1.4s ease forwards;
        }
        @keyframes fadeIn { to { opacity: 1; } }
        .corner--tl { top: 24px; left: 24px;   border-top: 1px solid var(--border); border-left:  1px solid var(--border); }
        .corner--tr { top: 24px; right: 24px;   border-top: 1px solid var(--border); border-right: 1px solid var(--border); }
        .corner--bl { bottom: 54px; left: 24px;  border-bottom: 1px solid var(--border); border-left:  1px solid var(--border); }
        .corner--br { bottom: 54px; right: 24px; border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); }

        /* ── SHIELD ICON ── */
        .shield-wrap {
            position: relative;
            display: inline-flex;
            margin-bottom: 8px;
            animation: shieldIn 1s 0.4s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes shieldIn {
            from { opacity: 0; transform: scale(0.7) rotate(-8deg); }
            to   { opacity: 1; transform: none; }
        }
        .shield-wrap svg {
            filter: drop-shadow(0 4px 16px rgba(245,145,30,0.2));
        }
        .shield-pulse {
            position: absolute; inset: -12px;
            border-radius: 50%;
            border: 1px solid rgba(245,145,30,0.2);
            animation: pulse 2.5s ease-in-out infinite;
        }
        .shield-pulse:nth-child(2) { inset: -24px; animation-delay: 0.8s; border-color: rgba(27,79,168,0.12); }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0; transform: scale(1.15); }
        }

        @media (max-width: 600px) {
            .giant-number { font-size: 40vw; }
            .meta-bar { padding: 12px 20px; }
            .corner { display: none; }
            .actions { flex-direction: column; }
        }
    </style>
</head>
<body>

<canvas id="c"></canvas>
<div class="grid"></div>

<div class="corner corner--tl"></div>
<div class="corner corner--tr"></div>
<div class="corner corner--bl"></div>
<div class="corner corner--br"></div>

<div class="scene">

    <!-- Giant 403 -->
    <div class="giant-number">403</div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Shield + Content -->
    <div class="content">

        <div style="display:flex; justify-content:center; margin-bottom: 24px;">
            <div class="shield-wrap">
                <div class="shield-pulse"></div>
                <div class="shield-pulse"></div>
                <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="url(#sg)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <defs>
                        <linearGradient id="sg" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#F5911E"/>
                            <stop offset="100%" stop-color="#1B4FA8"/>
                        </linearGradient>
                    </defs>
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M9 12l2 2 4-4"/>
                </svg>
            </div>
        </div>

        <div class="label-top">Access Denied</div>

        <h1 class="headline">
            You are not <em>authorized</em><br>to view this page
        </h1>

        <p class="subtext">
            Your account does not have the required permissions to access this resource.
            If you believe this is a mistake, please contact your system administrator.
        </p>

        <div class="actions">
            <a href="{{ route('login') }}" class="btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Return to Login</span>
            </a>
            @auth
            <a href="javascript:history.back()" class="btn-ghost">
                Go back
            </a>
            @endauth
        </div>

    </div>

</div>

<!-- Meta Bar -->
<div class="meta-bar">
    <div class="meta-left">
        <span class="meta-logo">Infinity Academy</span>
        <span class="meta-sep">·</span>
        <span class="meta-logo">System v1.0</span>
    </div>
    <div class="meta-right">Error Code 403 · Forbidden</div>
</div>

<script>
    const canvas = document.getElementById('c');
    const ctx    = canvas.getContext('2d');
    let W, H, particles = [];

    function resize() { W = canvas.width = innerWidth; H = canvas.height = innerHeight; }

    function Particle() {
        this.x        = Math.random() * W;
        this.y        = Math.random() * H;
        this.vx       = (Math.random() - 0.5) * 0.25;
        this.vy       = (Math.random() - 0.5) * 0.25;
        this.r        = Math.random() * 1.6 + 0.4;
        this.isOrange = Math.random() > 0.6;
        this.a        = Math.random() * 0.3 + 0.08;
    }

    function init() { resize(); particles = Array.from({length: 90}, () => new Particle()); }

    function draw() {
        ctx.clearRect(0, 0, W, H);
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0) p.x = W; if (p.x > W) p.x = 0;
            if (p.y < 0) p.y = H; if (p.y > H) p.y = 0;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = p.isOrange ? `rgba(245,145,30,${p.a})` : `rgba(27,79,168,${p.a})`;
            ctx.fill();

            for (let j = i + 1; j < particles.length; j++) {
                const q  = particles[j];
                const dx = p.x - q.x, dy = p.y - q.y;
                const d  = Math.sqrt(dx*dx + dy*dy);
                if (d < 100) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = p.isOrange
                        ? `rgba(245,145,30,${0.08*(1-d/100)})`
                        : `rgba(27,79,168,${0.08*(1-d/100)})`;
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