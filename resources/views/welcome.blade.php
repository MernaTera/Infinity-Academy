<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinity System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,300&family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #C9A84C;
            --gold-light: #E8C97A;
            --gold-dim: rgba(201,168,76,0.12);
            --bg: #060606;
            --text: #F0EDE6;
            --muted: #5A5550;
            --border: rgba(201,168,76,0.18);
        }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-weight: 300;
            overflow: hidden;
        }

        /* ─── CANVAS BG ─── */
        canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        /* ─── GRID OVERLAY ─── */
        .grid-overlay {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(201,168,76,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,168,76,0.035) 1px, transparent 1px);
            background-size: 70px 70px;
            z-index: 1;
            animation: gridMove 25s linear infinite;
        }
        @keyframes gridMove {
            from { background-position: 0 0; }
            to   { background-position: 70px 70px; }
        }

        /* ─── MAIN LAYOUT ─── */
        .scene {
            position: relative;
            z-index: 2;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 40px;
        }

        /* ─── TOP LABEL ─── */
        .top-label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 11px;
            letter-spacing: 6px;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 48px;
            opacity: 0;
            animation: fadeDown 0.8s 0.2s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: none; }
        }

        /* ─── LOGO MARK ─── */
        .logo-wrap {
            position: relative;
            margin-bottom: 32px;
            opacity: 0;
            animation: scaleIn 1s 0.4s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.7) rotate(-10deg); }
            to   { opacity: 1; transform: scale(1) rotate(0deg); }
        }

        .logo-wrap svg {
            width: 110px;
            height: 110px;
            filter: drop-shadow(0 0 30px rgba(201,168,76,0.35));
        }

        .logo-ring {
            animation: spin 20s linear infinite;
            transform-origin: center;
            transform-box: fill-box;
        }

        .logo-ring-2 {
            animation: spin 14s linear infinite reverse;
            transform-origin: center;
            transform-box: fill-box;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ─── TITLE ─── */
        .brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(52px, 8vw, 96px);
            letter-spacing: 12px;
            text-transform: uppercase;
            line-height: 1;
            text-align: center;
            background: linear-gradient(135deg, #C9A84C 0%, #E8C97A 40%, #C9A84C 70%, #A07830 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            opacity: 0;
            animation: fadeUp 0.9s 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        .brand-sub {
            font-family: 'Cormorant Garamond', serif;
            font-size: 40px;
            font-style: italic;
            color: var(--muted);
            letter-spacing: 3px;
            text-align: center;
            margin-bottom: 56px;
            opacity: 0;
            animation: fadeUp 0.9s 0.8s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: none; }
        }

        /* ─── DIVIDER ─── */
        .divider-line {
            width: 120px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin-bottom: 56px;
            opacity: 0;
            animation: fadeUp 0.9s 0.9s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        /* ─── ENTER BUTTON ─── */
        .btn-enter {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 18px 48px;
            background: transparent;
            border: 1px solid var(--gold);
            border-radius: 2px;
            color: var(--gold);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 16px;
            letter-spacing: 5px;
            text-decoration: none;
            cursor: pointer;
            overflow: hidden;
            transition: color 0.45s;
            opacity: 0;
            animation: fadeUp 0.9s 1.1s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        .btn-enter::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gold);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.45s cubic-bezier(0.16,1,0.3,1);
        }

        .btn-enter:hover::before { transform: scaleX(1); }
        .btn-enter:hover { color: #060606; }

        .btn-enter span,
        .btn-enter svg {
            position: relative;
            z-index: 1;
        }

        .btn-enter svg {
            width: 16px; height: 16px;
            transition: transform 0.3s;
        }

        .btn-enter:hover svg { transform: translateX(4px); }

        /* ─── CORNER DECORATIONS ─── */
        .corner {
            position: fixed;
            width: 60px; height: 60px;
            pointer-events: none;
            z-index: 2;
            opacity: 0;
            animation: fadeIn 1.2s 1.3s ease forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .corner--tl { top: 32px; left: 32px; border-top: 1px solid var(--border); border-left: 1px solid var(--border); }
        .corner--tr { top: 32px; right: 32px; border-top: 1px solid var(--border); border-right: 1px solid var(--border); }
        .corner--bl { bottom: 32px; left: 32px; border-bottom: 1px solid var(--border); border-left: 1px solid var(--border); }
        .corner--br { bottom: 32px; right: 32px; border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); }

        /* ─── BOTTOM STATUS ─── */
        .bottom-bar {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 3;
            opacity: 0;
            animation: fadeIn 1s 1.5s ease forwards;
        }

        .pulse-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #4ADE80;
            box-shadow: 0 0 8px #4ADE80;
            animation: blink 2.5s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        .status-text {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--muted);
        }
    </style>
</head>
<body>

<canvas id="c"></canvas>
<div class="grid-overlay"></div>

<!-- Corner accents -->
<div class="corner corner--tl"></div>
<div class="corner corner--tr"></div>
<div class="corner corner--bl"></div>
<div class="corner corner--br"></div>

<div class="scene">
    <div class="top-label">Infinity Academy Management System Platfrom</div>

    <!-- Animated Logo -->
    <div class="logo-wrap">
        <svg viewBox="0 0 110 110" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- outer hex -->
            <polygon points="55,6 100,30 100,80 55,104 10,80 10,30"
                     stroke="#C9A84C" stroke-width="1" fill="rgba(201,168,76,0.04)"/>
            <!-- rotating ring 1 -->
            <g class="logo-ring">
                <polygon points="55,16 90,35 90,75 55,94 20,75 20,35"
                         stroke="#C9A84C" stroke-width="0.6" fill="none" opacity="0.4"
                         stroke-dasharray="4 6"/>
            </g>
            <!-- rotating ring 2 -->
            <g class="logo-ring-2">
                <circle cx="55" cy="55" r="24" stroke="#C9A84C" stroke-width="0.6"
                        fill="none" opacity="0.3" stroke-dasharray="3 5"/>
            </g>
            <!-- axis lines -->
            <line x1="55" y1="6"  x2="55" y2="104" stroke="#C9A84C" stroke-width="0.4" opacity="0.2"/>
            <line x1="10" y1="30" x2="100" y2="80" stroke="#C9A84C" stroke-width="0.4" opacity="0.2"/>
            <line x1="100" y1="30" x2="10" y2="80" stroke="#C9A84C" stroke-width="0.4" opacity="0.2"/>
            <!-- center -->
            <circle cx="55" cy="55" r="7" fill="#C9A84C" opacity="0.9"/>
            <circle cx="55" cy="55" r="3" fill="#060606"/>
            <!-- nodes -->
            <circle cx="55" cy="6"   r="2" fill="#C9A84C" opacity="0.6"/>
            <circle cx="100" cy="30" r="2" fill="#C9A84C" opacity="0.6"/>
            <circle cx="100" cy="80" r="2" fill="#C9A84C" opacity="0.6"/>
            <circle cx="55" cy="104" r="2" fill="#C9A84C" opacity="0.6"/>
            <circle cx="10" cy="80"  r="2" fill="#C9A84C" opacity="0.6"/>
            <circle cx="10" cy="30"  r="2" fill="#C9A84C" opacity="0.6"/>
        </svg>
    </div>

    <h1 class="brand-name">Infinity Academy</h1>
    <p class="brand-sub">System</p>

    <div class="divider-line"></div>

    <a href="{{ route('login') }}" class="btn-enter">
        <span>Enter System</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
    </a>
</div>

<!-- Status bar -->
<div class="bottom-bar">
    <div class="pulse-dot"></div>
    <span class="status-text">All systems operational</span>
    <span class="status-text">Developed by Merna Tera</span>
</div>

<script>
    // Particle field
    const canvas = document.getElementById('c');
    const ctx = canvas.getContext('2d');
    let W, H, particles = [];

    function resize() {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }

    function Particle() {
        this.x = Math.random() * W;
        this.y = Math.random() * H;
        this.vx = (Math.random() - 0.5) * 0.3;
        this.vy = (Math.random() - 0.5) * 0.3;
        this.r  = Math.random() * 1.2 + 0.3;
        this.a  = Math.random() * 0.5 + 0.1;
    }

    function init() {
        resize();
        particles = Array.from({ length: 80 }, () => new Particle());
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0) p.x = W; if (p.x > W) p.x = 0;
            if (p.y < 0) p.y = H; if (p.y > H) p.y = 0;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(201,168,76,${p.a})`;
            ctx.fill();

            // connect nearby
            for (let j = i + 1; j < particles.length; j++) {
                const q = particles[j];
                const dx = p.x - q.x, dy = p.y - q.y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 120) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = `rgba(201,168,76,${0.06 * (1 - dist/120)})`;
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', resize);
    init();
    draw();
</script>

</body>
</html>