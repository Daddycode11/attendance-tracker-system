<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttendanceIQ — Smart Workforce Tracking</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #0d1117;
            --paper: #f5f2eb;
            --accent: #e85d26;
            --muted: #6b7280;
            --border: rgba(0,0,0,0.08);
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper); color: var(--ink);
            overflow-x: hidden; -webkit-font-smoothing: antialiased;
        }

        /* ─── NAV ─── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 200;
            height: 64px; display: flex; align-items: center;
            justify-content: space-between; padding: 0 5%;
            background: rgba(245,242,235,0.92);
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.15rem;
            color: var(--ink); text-decoration: none;
            display: flex; align-items: center; gap: 8px; flex-shrink: 0;
        }
        .logo-box {
            width: 28px; height: 28px; background: var(--accent);
            border-radius: 6px; position: relative; flex-shrink: 0;
        }
        .logo-box::after {
            content: ''; position: absolute; inset: 5px;
            border: 2px solid rgba(255,255,255,.85); border-radius: 3px;
        }

        .nav-links { display: flex; align-items: center; gap: 4px; }

        .btn-ghost {
            font-family: 'DM Sans', sans-serif; font-size: .875rem; font-weight: 500;
            color: var(--ink); text-decoration: none;
            padding: 8px 14px; border-radius: 8px;
            border: 1.5px solid transparent; transition: border-color .2s;
            white-space: nowrap;
        }
        .btn-ghost:hover { border-color: rgba(0,0,0,.18); }

        .btn-nav-cta {
            font-family: 'DM Sans', sans-serif; font-size: .875rem; font-weight: 700;
            color: #fff; background: var(--ink); text-decoration: none;
            padding: 9px 20px; border-radius: 8px;
            transition: background .2s, transform .15s; white-space: nowrap;
        }
        .btn-nav-cta:hover { background: #1a2332; transform: translateY(-1px); }

        /* hamburger */
        .hamburger {
            display: none; flex-direction: column; gap: 5px;
            cursor: pointer; padding: 8px; background: transparent; border: none;
            -webkit-tap-highlight-color: transparent;
        }
        .hamburger span {
            display: block; width: 22px; height: 2px;
            background: var(--ink); border-radius: 2px;
            transition: transform .3s, opacity .3s;
        }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* mobile drawer */
        .mob-drawer {
            display: none; position: fixed;
            top: 64px; left: 0; right: 0; z-index: 199;
            background: var(--paper); border-bottom: 1px solid var(--border);
            padding: 14px 5% 20px;
            flex-direction: column; gap: 6px;
            box-shadow: 0 10px 30px rgba(0,0,0,.07);
        }
        .mob-drawer.open { display: flex; }
        .mob-drawer a {
            font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 500;
            color: var(--ink); text-decoration: none;
            padding: 13px 16px; border-radius: 10px;
            transition: background .15s;
        }
        .mob-drawer a:hover { background: rgba(0,0,0,.05); }
        .mob-drawer .mob-cta {
            background: var(--ink); color: #fff;
            text-align: center; margin-top: 4px;
        }
        .mob-drawer .mob-cta:hover { background: #1a2332; }

        /* ─── HERO ─── */
        .hero {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 0;
            align-items: center;
            min-height: 100svh;
            padding: 100px 5% 72px;
            position: relative; overflow: hidden;
        }
        .hero-left { position: relative; z-index: 1; }
        .hero-right { position: relative; z-index: 2; margin-left: 40px; }

        .hero-grid-bg {
            position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: radial-gradient(ellipse 85% 85% at 50% 50%, black 30%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 85% 85% at 50% 50%, black 30%, transparent 100%);
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; border: 1.5px solid var(--border);
            border-radius: 100px; padding: 6px 16px 6px 8px;
            font-size: .78rem; font-weight: 500; color: var(--muted);
            margin-bottom: 22px; width: fit-content;
            animation: fadeUp .5s ease both;
        }
        .badge-dot {
            width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
            animation: pulseDot 2s infinite;
        }
        @keyframes pulseDot {
            0%,100% { opacity:1; transform:scale(1); }
            50% { opacity:.5; transform:scale(1.4); }
        }

        .hero h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 800; line-height: 1.06;
            letter-spacing: -.03em; max-width: 640px;
            animation: fadeUp .5s .08s ease both;
        }
        .hero h1 em {
            font-style: normal; color: var(--accent);
            text-decoration: underline; text-decoration-style: wavy;
            text-decoration-color: var(--accent); text-underline-offset: 6px;
        }

        .hero-p {
            font-size: clamp(.9rem, 1.8vw, 1.05rem); color: var(--muted);
            max-width: 460px; line-height: 1.72; margin: 20px 0 34px;
            animation: fadeUp .5s .16s ease both;
        }

        .hero-btns {
            display: flex; gap: 12px; flex-wrap: wrap;
            animation: fadeUp .5s .24s ease both;
        }

        .btn-accent {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--accent); color: #fff;
            font-family: 'DM Sans', sans-serif; font-size: .95rem; font-weight: 700;
            padding: 13px 28px; border-radius: 10px; text-decoration: none;
            box-shadow: 0 4px 18px rgba(232,93,38,.35);
            transition: transform .15s, box-shadow .2s; white-space: nowrap;
        }
        .btn-accent:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(232,93,38,.45); }
        .btn-accent:active { transform: translateY(0); }

        .btn-outline {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: var(--ink);
            font-family: 'DM Sans', sans-serif; font-size: .95rem; font-weight: 600;
            padding: 13px 28px; border-radius: 10px; text-decoration: none;
            border: 1.5px solid var(--border);
            transition: border-color .2s, transform .15s; white-space: nowrap;
        }
        .btn-outline:hover { border-color: rgba(0,0,0,.22); transform: translateY(-1px); }

        /* floating card */
        .float-card {
            background: #fff; border: 1.5px solid var(--border);
            border-radius: 20px; padding: 24px 26px;
            box-shadow: 0 24px 64px rgba(0,0,0,.09);
            animation: slideIn .7s .3s ease both;
        }
        @keyframes slideIn {
            from { opacity:0; transform:translateX(28px); }
            to   { opacity:1; transform:translateX(0); }
        }

        .card-top {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px;
        }
        .card-top-info strong { font-size: .88rem; font-family: 'Syne', sans-serif; }
        .card-top-info p { font-size: .72rem; color: var(--muted); margin-top: 2px; }
        .cav {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg,#667eea,#764ba2);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .75rem; flex-shrink: 0;
        }

        .card-clock {
            font-family: 'Syne', sans-serif; font-size: 2.2rem;
            font-weight: 800; letter-spacing: -.02em; line-height: 1;
        }
        .card-date { font-size: .7rem; color: var(--muted); margin-top: 4px; margin-bottom: 16px; }
        .card-btns-row { display: flex; gap: 8px; margin-bottom: 12px; }
        .cbtn {
            flex: 1; padding: 10px 6px; border: none; border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: .78rem; font-weight: 700;
            cursor: pointer; transition: transform .12s;
        }
        .cbtn:hover { transform: scale(1.03); }
        .cbtn.in  { background: #dcfce7; color: #166534; }
        .cbtn.out { background: #fee2e2; color: #991b1b; }
        .card-status-row {
            display: flex; align-items: center; gap: 6px;
            font-size: .72rem; color: var(--muted);
        }
        .sdot { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; flex-shrink: 0; }

        /* ─── STATS ─── */
        .stats-bar {
            background: var(--ink);
            display: grid; grid-template-columns: repeat(4,1fr);
        }
        .stat-item {
            padding: 28px 22px;
            border-right: 1px solid rgba(255,255,255,.07);
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-family: 'Syne', sans-serif; font-size: 1.85rem; font-weight: 800;
            color: var(--accent); line-height: 1;
        }
        .stat-lbl { font-size: .76rem; color: rgba(255,255,255,.42); margin-top: 5px; }

        /* ─── FEATURES ─── */
        .sec-wrap { max-width: 1160px; margin: 0 auto; padding: 80px 5%; }

        .sec-tag {
            display: inline-block; font-size: .72rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
            color: var(--accent); margin-bottom: 14px;
        }
        .sec-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.65rem, 3.2vw, 2.55rem);
            font-weight: 800; letter-spacing: -.02em;
            max-width: 470px; margin-bottom: 46px; line-height: 1.15;
        }

        .feat-grid {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 18px;
        }
        .feat-card {
            background: #fff; border: 1.5px solid var(--border);
            border-radius: 16px; padding: 26px;
            transition: transform .2s, box-shadow .2s;
        }
        .feat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,.07); }
        .feat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 14px;
        }
        .feat-card h3 {
            font-family: 'Syne', sans-serif; font-size: .93rem; font-weight: 700; margin-bottom: 8px;
        }
        .feat-card p { font-size: .84rem; color: var(--muted); line-height: 1.65; }

        /* ─── SCHEDULE ─── */
        .sched-section {
            background: var(--ink); color: #fff;
            position: relative; overflow: hidden;
        }
        .sched-section::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 340px; height: 340px;
            background: var(--accent); opacity: .06; border-radius: 50%;
            pointer-events: none;
        }
        .sched-inner {
            max-width: 1160px; margin: 0 auto; padding: 80px 5%;
            display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center;
        }
        .sched-section .sec-tag { color: var(--accent); }
        .sched-section .sec-title { color: #fff; margin-bottom: 30px; }

        .sched-list { display: flex; flex-direction: column; }
        .sched-row {
            display: flex; align-items: center; gap: 14px;
            padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .sched-row:last-child { border-bottom: none; }
        .sched-t {
            font-family: 'Syne', sans-serif; font-size: .93rem; font-weight: 700;
            color: var(--accent); min-width: 78px;
        }
        .sched-lbl { font-size: .84rem; color: rgba(255,255,255,.58); flex: 1; }
        .sched-pill {
            padding: 3px 12px; border-radius: 100px;
            font-size: .67rem; font-weight: 700; letter-spacing: .04em; white-space: nowrap;
        }
        .pill-in  { background: rgba(34,197,94,.15);  color: #4ade80; }
        .pill-out { background: rgba(239,68,68,.15);  color: #f87171; }

        /* attendance visual */
        .att-visual {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 18px; padding: 24px;
        }
        .att-title {
            font-size: .7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: rgba(255,255,255,.3); margin-bottom: 18px;
        }
        .att-row {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .att-row:last-child { border-bottom: none; }
        .aav {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .68rem; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .att-name {
            font-size: .82rem; font-weight: 500; flex: 1;
            min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .att-times { display: flex; gap: 4px; flex-shrink: 0; }
        .att-pill {
            font-size: .62rem; padding: 2px 6px; border-radius: 100px;
            background: rgba(255,255,255,.07); color: rgba(255,255,255,.5);
        }
        .att-badge {
            font-size: .67rem; font-weight: 700;
            padding: 2px 9px; border-radius: 100px; white-space: nowrap; flex-shrink: 0;
        }

        /* ─── CTA ─── */
        .cta-sec { padding: 96px 5%; text-align: center; }
        .cta-sec h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(1.8rem, 4.5vw, 3.1rem);
            font-weight: 800; letter-spacing: -.02em; margin-bottom: 14px;
        }
        .cta-sec p { font-size: 1rem; color: var(--muted); margin-bottom: 30px; }

        /* ─── FOOTER ─── */
        footer {
            background: var(--ink); padding: 22px 5%;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
        }
        footer span, footer a {
            font-size: .76rem; color: rgba(255,255,255,.32);
        }
        footer a { text-decoration: none; transition: color .2s; }
        footer a:hover { color: #fff; }

        /* ─── ANIMATION ─── */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ═══════════════════════════
           TABLET  ≤ 1024px
        ═══════════════════════════ */
        @media (max-width: 1024px) {
            .hero { grid-template-columns: 1fr 260px; padding: 100px 5% 64px; }
            .hero-right { margin-left: 28px; }
            .feat-grid { grid-template-columns: repeat(2,1fr); }
            .sched-inner { gap: 36px; }
        }

        /* ═══════════════════════════
           MOBILE  ≤ 768px
        ═══════════════════════════ */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hamburger { display: flex; }

            /* Hero: single column */
            .hero {
                grid-template-columns: 1fr;
                grid-template-rows: auto;
                min-height: auto;
                padding: 92px 5% 52px;
                gap: 0;
            }
            .hero-left { order: 1; }
            .hero-right { order: 2; margin-left: 0; margin-top: 32px; }

            /* Stats: 2×2 */
            .stats-bar { grid-template-columns: repeat(2,1fr); }
            .stat-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,.07); padding: 20px 18px; }
            .stat-item:nth-child(odd)  { border-right: 1px solid rgba(255,255,255,.07); }
            .stat-item:nth-child(3), .stat-item:nth-child(4) { border-bottom: none; }

            /* Features: single column */
            .feat-grid { grid-template-columns: 1fr; }
            .sec-wrap { padding: 56px 5%; }

            /* Schedule: single column */
            .sched-inner { grid-template-columns: 1fr; gap: 32px; padding: 56px 5%; }

            .cta-sec { padding: 68px 5%; }

            footer { justify-content: center; text-align: center; }
        }

        /* ═══════════════════════════
           SMALL MOBILE  ≤ 420px
        ═══════════════════════════ */
        @media (max-width: 420px) {
            nav { padding: 0 4%; }
            .hero, .sec-wrap, .sched-inner, .cta-sec, footer { padding-left: 4%; padding-right: 4%; }

            .hero-btns { flex-direction: column; }
            .btn-accent, .btn-outline { width: 100%; justify-content: center; }

            .att-times { display: none; }

            .float-card { padding: 20px; }
            .card-clock { font-size: 1.9rem; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a class="nav-logo" href="#">
        <div class="logo-box"></div>
        AttendanceIQ
    </a>
    <div class="nav-links">
        <a href="#features" class="btn-ghost">Features</a>
        <a href="#schedule" class="btn-ghost">Schedule</a>
        <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
        <a href="{{ route('login') }}" class="btn-nav-cta">Get Started →</a>
    </div>
    <button class="hamburger" id="ham" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- Mobile Drawer -->
<div class="mob-drawer" id="drawer">
    <a href="#features" onclick="closeD()">Features</a>
    <a href="#schedule" onclick="closeD()">Schedule</a>
    <a href="{{ route('login') }}" onclick="closeD()">Sign In</a>
    <a href="{{ route('login') }}" class="mob-cta">Get Started →</a>
</div>

<!-- HERO -->
<section class="hero">
    <div class="hero-grid-bg"></div>

    <div class="hero-left">
        <div class="hero-badge">
            <div class="badge-dot"></div>
            Live attendance tracking, right now
        </div>

        <h1>Smart attendance<br>for <em>modern</em><br>workplaces.</h1>

        <p class="hero-p">Replace biometric machines with a fast, mobile-friendly Time In / Time Out system. Track late, overtime, absences, and payroll — all in one place.</p>

        <div class="hero-btns">
            <a href="{{ route('login') }}" class="btn-accent">⏱ Start Tracking</a>
            <a href="#features" class="btn-outline">See Features</a>
        </div>
    </div>

    <div class="hero-right">
        <div class="float-card">
            <div class="card-top">
                <div class="card-top-info">
                    <strong>Juan Dela Cruz</strong>
                    <p>Software Engineer</p>
                </div>
                <div class="cav">JD</div>
            </div>
            <div class="card-clock" id="clk">--:--:--</div>
            <div class="card-date" id="cdt">Loading…</div>
            <div class="card-btns-row">
                <button class="cbtn in">⬆ TIME IN</button>
                <button class="cbtn out">⬇ TIME OUT</button>
            </div>
            <div class="card-status-row">
                <div class="sdot"></div>
                Time In recorded at 8:02 AM
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-num">99%</div>
        <div class="stat-lbl">Uptime reliability</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">4×</div>
        <div class="stat-lbl">Faster than biometric</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">Auto</div>
        <div class="stat-lbl">Late & OT detection</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">0 ₱</div>
        <div class="stat-lbl">Hardware cost</div>
    </div>
</div>

<!-- FEATURES -->
<section id="features">
    <div class="sec-wrap">
        <span class="sec-tag">What's included</span>
        <h2 class="sec-title">Everything you need to manage attendance</h2>

        <div class="feat-grid">
            <div class="feat-card">
                <div class="feat-icon" style="background:#fff3ed;">⏰</div>
                <h3>Time In / Time Out</h3>
                <p>Large tap-friendly buttons. Smart logic auto-fills AM in, lunch out, PM in, and final out in the correct sequence.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#fef2f2;">🔴</div>
                <h3>Late Detection</h3>
                <p>Automatically flags employees arriving after 8:00 AM and calculates exact late minutes for payroll deduction.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#f0fdf4;">📈</div>
                <h3>Overtime Tracking</h3>
                <p>Any time out after 5:00 PM is recorded as overtime, automatically converted to hours and additional pay.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#eff6ff;">👥</div>
                <h3>Employee Management</h3>
                <p>Add, edit, and manage employees with department, position, and salary info all stored in one central place.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#fdf4ff;">💰</div>
                <h3>Payroll Computation</h3>
                <p>Monthly net salary auto-calculated: basic pay + overtime − late deductions − absent deductions.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon" style="background:#fefce8;">📋</div>
                <h3>Absent Auto-Detection</h3>
                <p>Daily cron job at 6 PM marks any employee with no attendance record as Absent — zero manual effort.</p>
            </div>
        </div>
    </div>
</section>

<!-- SCHEDULE -->
<section class="sched-section" id="schedule">
    <div class="sched-inner">
        <div>
            <span class="sec-tag">Daily workflow</span>
            <h2 class="sec-title">Built around your work schedule</h2>
            <div class="sched-list">
                <div class="sched-row">
                    <span class="sched-t">8:00 AM</span>
                    <span class="sched-lbl">Morning Time In</span>
                    <span class="sched-pill pill-in">TIME IN</span>
                </div>
                <div class="sched-row">
                    <span class="sched-t">12:00 PM</span>
                    <span class="sched-lbl">Lunch Break Out</span>
                    <span class="sched-pill pill-out">TIME OUT</span>
                </div>
                <div class="sched-row">
                    <span class="sched-t">1:00 PM</span>
                    <span class="sched-lbl">Afternoon Time In</span>
                    <span class="sched-pill pill-in">TIME IN</span>
                </div>
                <div class="sched-row">
                    <span class="sched-t">5:00 PM</span>
                    <span class="sched-lbl">End of Day Time Out</span>
                    <span class="sched-pill pill-out">TIME OUT</span>
                </div>
            </div>
        </div>

        <div class="att-visual">
            <div class="att-title">Today's Attendance — March 6, 2026</div>
            <div class="att-row">
                <div class="aav" style="background:linear-gradient(135deg,#667eea,#764ba2)">JD</div>
                <span class="att-name">Juan Dela Cruz</span>
                <div class="att-times">
                    <span class="att-pill">8:02</span><span class="att-pill">12:01</span>
                    <span class="att-pill">1:03</span><span class="att-pill">5:00</span>
                </div>
                <span class="att-badge" style="background:rgba(34,197,94,.15);color:#4ade80;">Present</span>
            </div>
            <div class="att-row">
                <div class="aav" style="background:linear-gradient(135deg,#f093fb,#f5576c)">MS</div>
                <span class="att-name">Maria Santos</span>
                <div class="att-times">
                    <span class="att-pill">8:35</span><span class="att-pill">12:00</span>
                    <span class="att-pill">1:01</span><span class="att-pill">5:31</span>
                </div>
                <span class="att-badge" style="background:rgba(251,191,36,.15);color:#fbbf24;">Late</span>
            </div>
            <div class="att-row">
                <div class="aav" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">CR</div>
                <span class="att-name">Carlo Reyes</span>
                <div class="att-times">
                    <span class="att-pill">7:58</span><span class="att-pill">12:02</span>
                    <span class="att-pill">1:00</span><span class="att-pill">6:15</span>
                </div>
                <span class="att-badge" style="background:rgba(99,102,241,.15);color:#a5b4fc;">OT 75m</span>
            </div>
            <div class="att-row">
                <div class="aav" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">AL</div>
                <span class="att-name">Ana Lim</span>
                <div class="att-times"><span class="att-pill" style="opacity:.3;">—</span></div>
                <span class="att-badge" style="background:rgba(239,68,68,.15);color:#f87171;">Absent</span>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-sec">
    <h2>Ready to simplify<br>your attendance?</h2>
    <p>Works on any mobile, tablet, or desktop. No hardware needed.</p>
    <a href="{{ route('login') }}" class="btn-accent" style="font-size:1rem;padding:14px 36px;display:inline-flex;margin:0 auto;">
        Sign in to get started →
    </a>
</section>

<!-- FOOTER -->
<footer>
    <span>© 2026 AttendanceIQ. Built with Laravel.</span>
    <a href="{{ route('login') }}">Admin Login →</a>
</footer>

<script>
/* Clock */
function tick(){
    const n=new Date();
    const p=v=>v.toString().padStart(2,'0');
    const t=`${p(n.getHours())}:${p(n.getMinutes())}:${p(n.getSeconds())}`;
    const d=n.toLocaleDateString('en-PH',{weekday:'short',month:'short',day:'numeric',year:'numeric'});
    const ce=document.getElementById('clk');
    const de=document.getElementById('cdt');
    if(ce)ce.textContent=t;
    if(de)de.textContent=d;
}
tick(); setInterval(tick,1000);

/* Hamburger */
const ham=document.getElementById('ham');
const drawer=document.getElementById('drawer');
ham.addEventListener('click',()=>{
    const o=drawer.classList.toggle('open');
    ham.classList.toggle('open',o);
    ham.setAttribute('aria-expanded',o);
});
function closeD(){
    drawer.classList.remove('open');
    ham.classList.remove('open');
    ham.setAttribute('aria-expanded','false');
}
document.addEventListener('click',e=>{
    if(!ham.contains(e.target)&&!drawer.contains(e.target))closeD();
});
window.addEventListener('resize',()=>{ if(window.innerWidth>768)closeD(); });

/* Scroll reveal */
const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{
        if(e.isIntersecting){
            e.target.style.opacity='1';
            e.target.style.transform='translateY(0)';
            obs.unobserve(e.target);
        }
    });
},{threshold:0.08});
document.querySelectorAll('.feat-card,.stat-item,.sched-row,.att-row').forEach(el=>{
    el.style.cssText+='opacity:0;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;';
    obs.observe(el);
});
</script>
</body>
</html>