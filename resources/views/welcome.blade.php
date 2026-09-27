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
            --orange-light:#FFAB4A;
            --blue:        #1B4FA8;
            --blue-light:  #2D6FDB;
            --text:        #16233F;
            --muted:       #5A6A85;
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
                radial-gradient(720px 560px at 55% 118%, rgba(127,119,221,0.24), transparent 60%),
                linear-gradient(135deg, #eaf0fb 0%, #f4eefb 48%, #fdeee0 100%);
        }

        canvas { position: fixed; inset: 0; pointer-events: none; z-index: 0; }

        .blob { position: fixed; border-radius: 50%; filter: blur(45px); z-index: 0; opacity: 0.55; pointer-events: none; }
        .blob--1 { width: 420px; height: 420px; left: -100px; top: 30%; background: radial-gradient(circle, rgba(45,111,219,0.5), transparent 70%); }
        .blob--2 { width: 380px; height: 380px; right: 2%; bottom: -100px; background: radial-gradient(circle, rgba(245,145,30,0.45), transparent 70%); }

        .glass-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 52px 64px 44px;
            background: rgba(255,255,255,0.10);
            backdrop-filter: blur(26px) saturate(170%);
            -webkit-backdrop-filter: blur(26px) saturate(170%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 26px;
            box-shadow: 0 24px 70px rgba(23,45,90,0.18), inset 0 1px 0 rgba(255,255,255,0.7);
            max-width: 720px;
            width: 100%;
            position: relative;
        }
        .glass-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--orange), var(--blue-light), transparent); }

        .scene {
            position: relative;
            z-index: 2;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .top-label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: 6px;
            color: var(--blue);
            text-transform: uppercase;
            margin-bottom: 34px;
            text-align: center;
            opacity: 0;
            animation: fadeDown 0.8s 0.2s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }

        .logo-wrap {
            position: relative;
            margin-bottom: 28px;
            opacity: 0;
            animation: scaleIn 1s 0.4s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        .logo-wrap img {
            width: 360px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 14px 30px rgba(27,79,168,0.25));
        }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.7) rotate(-10deg); } to { opacity: 1; transform: scale(1) rotate(0deg); } }

        .brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(36px, 5vw, 62px);
            letter-spacing: 12px;
            text-transform: uppercase;
            line-height: 1;
            text-align: center;
            background: linear-gradient(135deg, var(--blue), var(--blue-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 22px;
            opacity: 0;
            animation: fadeUp 0.9s 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }

        .divider-line {
            width: 120px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--orange), var(--blue-light), transparent);
            margin-bottom: 40px;
            opacity: 0;
            animation: fadeUp 0.9s 0.9s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        .btn-enter {
            position: relative;
            display: inline-flex; align-items: center; gap: 12px;
            padding: 15px 46px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.5);
            color: #fff;
            background: linear-gradient(120deg, var(--blue), var(--blue-light));
            font-family: 'Bebas Neue', sans-serif;
            font-size: 16px; letter-spacing: 5px;
            text-decoration: none; cursor: pointer;
            box-shadow: 0 14px 34px rgba(27,79,168,0.4);
            transition: transform 0.3s, box-shadow 0.3s;
            opacity: 0;
            animation: fadeUp 0.9s 1.1s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        .btn-enter:hover { transform: translateY(-2px); box-shadow: 0 18px 42px rgba(27,79,168,0.5); }
        .btn-enter svg { width: 16px; height: 16px; transition: transform 0.3s; }
        .btn-enter:hover svg { transform: translateX(4px); }

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
        @keyframes fadeIn { to { opacity: 1; } }
        .pulse-dot { width: 6px; height: 6px; border-radius: 50%; background: #22C55E; box-shadow: 0 0 8px #22C55E; animation: blink 2.5s ease-in-out infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .status-text { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: var(--muted); }

        @media (max-width: 768px) {
            .scene { padding: 20px; }
            .glass-card { padding: 32px 24px; max-width: 100%; }
            .logo-wrap img { width: 220px; }
            .brand-name { font-size: clamp(28px, 6vw, 42px); letter-spacing: 6px; }
            .top-label { font-size: 11px; letter-spacing: 3px; margin-bottom: 24px; }
            .divider-line { margin-bottom: 30px; }
            .btn-enter { padding: 13px 32px; font-size: 14px; letter-spacing: 3px; }
            .bottom-bar { flex-direction: column; gap: 4px; text-align: center; }
            .status-text { font-size: 9px; letter-spacing: 2px; }
        }
    </style>
</head>
<body>

<canvas id="c"></canvas>
<div class="blob blob--1"></div>
<div class="blob blob--2"></div>

<div class="scene">
    <div class="glass-card">
        <div class="top-label">Infinity Academy Management System Platfrom</div>

        <div class="logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="Infinity Logo">
        </div>

        <h1 class="brand-name">Academy System</h1>

        <div class="divider-line"></div>

        <a href="{{ route('login') }}" class="btn-enter">
            <span>Enter System</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M13 6l6 6-6 6"/>
            </svg>
        </a>
    </div>
</div>

<div class="bottom-bar">
    <div class="pulse-dot"></div>
    <span class="status-text">All systems operational</span>
    <span class="status-text">Developed by Merna Tera</span>
</div>

<script>
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
        this.isOrange = Math.random() > 0.5;
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
            ctx.fillStyle = p.isOrange
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
                    ctx.strokeStyle = p.isOrange
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
