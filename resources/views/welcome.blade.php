<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infinity System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,300&family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --orange:      #F5911E;
            --orange-light: #FFAB4A;
            --blue:        #1B4FA8;
            --blue-light:  #2D6FDB;
            --bg:          #F8F6F2;
            --text:        #1A2A4A;
            --muted:       #7A8A9A;
            --border:      rgba(27,79,168,0.18);
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
                linear-gradient(rgba(27,79,168,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(27,79,168,0.06) 1px, transparent 1px);
            background-size: 70px 70px;
            z-index: 1;
            animation: gridMove 25s linear infinite;
        }
        @keyframes gridMove {
            from { background-position: 0 0; }
            to   { background-position: 70px 70px; }
        }

        /* ─── GLASS CARD ─── */
        .glass-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 48px 60px 40px;

            background: rgba(255,255,255,0.18); 

            backdrop-filter: blur(25px); 
            -webkit-backdrop-filter: blur(25px);

            border: 1px solid rgba(255,255,255,0.4); 

            border-radius: 8px;

            box-shadow:
                0 8px 40px rgba(27,79,168,0.06),
                inset 0 1px 0 rgba(255,255,255,0.6);

            max-width: 900px;
            width: 100%;
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
            font-size: 15px;
            letter-spacing: 6px;
            color: #2b5ba8;
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

        .logo-wrap img {
            width: 400px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 10px 25px rgba(27,79,168,0.25));
        }



        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.7) rotate(-10deg); }
            to   { opacity: 1; transform: scale(1) rotate(0deg); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ─── TITLE ─── */
        .brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(36px, 5vw, 64px);
            letter-spacing: 12px;
            text-transform: uppercase;
            line-height: 1;
            text-align: center;
            background: linear-gradient(135deg, #2b5ba8 0%);
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
            font-weight: 300;
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
            background: linear-gradient(90deg, transparent, var(--orange), var(--blue-light), transparent);            margin-bottom: 56px;
            opacity: 0;
            animation: fadeUp 0.9s 0.9s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        /* ─── ENTER BUTTON ─── */
        .btn-enter {
            position: relative;
            display: inline-flex; align-items: center; gap: 12px;
            padding: 14px 44px;
            background: transparent;
            border: 1.5px solid var(--blue);
            border-radius: 2px;
            color: var(--blue);
            font-family: 'Bebas Neue', sans-serif;
            font-size: 15px; letter-spacing: 5px;
            text-decoration: none; cursor: pointer;
            overflow: hidden;
            transition: color 0.45s, border-color 0.45s;
            opacity: 0;
            animation: fadeUp 0.9s 1.1s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        .btn-enter::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, var(--blue), var(--blue-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.45s cubic-bezier(0.16,1,0.3,1);
        }

        .btn-enter:hover::before { transform: scaleX(1); }
        .btn-enter:hover { color: #ffffff; }

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


    /* ─── MOBILE RESPONSIVE ─── */
    @media (max-width: 768px) {

        .scene {
            padding: 20px;
        }

        .glass-card {
            padding: 28px 20px;
            max-width: 100%;
        }

        .logo-wrap img {
            width: 220px; /* كان 400 */
        }

        .brand-name {
            font-size: clamp(28px, 6vw, 42px);
            letter-spacing: 6px;
        }

        .top-label {
            font-size: 11px;
            letter-spacing: 3px;
            margin-bottom: 24px;
            text-align: center;
        }

        .divider-line {
            margin-bottom: 30px;
        }

        .btn-enter {
            padding: 12px 28px;
            font-size: 13px;
            letter-spacing: 3px;
        }

        .bottom-bar {
            flex-direction: column;
            gap: 4px;
            text-align: center;
        }

        .status-text {
            font-size: 9px;
            letter-spacing: 2px;
        }

        /* corners hide عشان الزحمة */
        .corner {
            display: none;
        }
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
    <div class="glass-card">
        <div class="top-label">Infinity Academy Management System Platfrom</div>

        <!-- Animated Logo -->
        <div class="logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity Logo">
        </div>

        <h1 class="brand-name">Academy System</h1>
        <!-- <p class="brand-sub">System</p> -->

        <div class="divider-line"></div>

        <a href="{{ route('login') }}" class="btn-enter">
            <span>Enter System</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M13 6l6 6-6 6"/>
            </svg>
        </a>
    </div>
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

    function Particle() {
        this.x        = Math.random() * W;
        this.y        = Math.random() * H;
        this.vx       = (Math.random() - 0.5) * 0.35;
        this.vy       = (Math.random() - 0.5) * 0.35;
        this.r        = Math.random() * 1.8 + 0.5;
        this.isOrange = Math.random() > 0.5;   // ← اتحدد هنا مرة واحدة
        this.a        = Math.random() * 0.45 + 0.15;
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
            ctx.fillStyle = p.isOrange              // ← بيجيب القيمة اللي اتحسبت قبل
                ? `rgba(245,145,30,${p.a})`
                : `rgba(27,79,168,${p.a})`;
            ctx.fill();

            for (let j = i + 1; j < particles.length; j++) {
                const q  = particles[j];
                const dx = p.x - q.x, dy = p.y - q.y;
                const d  = Math.sqrt(dx*dx + dy*dy);
                if (d < 120) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = p.isOrange    // ← نفس لون الـ particle مش random جديد
                        ? `rgba(245,145,30,${0.12*(1-d/120)})`
                        : `rgba(27,79,168,${0.12*(1-d/120)})`;
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