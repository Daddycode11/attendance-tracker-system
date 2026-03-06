<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — AttendanceIQ</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #0d1117;
            --paper: #f5f2eb;
            --accent: #e85d26;
            --muted: #6b7280;
            --border: rgba(0,0,0,0.09);
            --error: #dc2626;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--ink);
            min-height: 100vh;
            display: flex;
        }

        /* LEFT PANEL */
        .left-panel {
            flex: 1;
            background: var(--ink);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 48px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            bottom: -100px; left: -100px;
            width: 500px; height: 500px;
            background: var(--accent);
            opacity: 0.06;
            border-radius: 50%;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 320px; height: 320px;
            background: #2563eb;
            opacity: 0.05;
            border-radius: 50%;
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.2rem;
            color: white; text-decoration: none;
            display: flex; align-items: center; gap: 8px;
            position: relative; z-index: 1;
        }

        .logo-icon {
            width: 30px; height: 30px;
            background: var(--accent); border-radius: 7px;
            position: relative;
        }

        .logo-icon::after {
            content: '';
            position: absolute; inset: 5px;
            border: 2px solid rgba(255,255,255,0.8);
            border-radius: 3px;
        }

        .left-content {
            position: relative; z-index: 1;
        }

        .left-content h2 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            color: white;
            line-height: 1.1;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }

        .left-content h2 span { color: var(--accent); }

        .left-content p {
            color: rgba(255,255,255,0.45);
            font-size: 0.95rem; line-height: 1.7;
            max-width: 380px;
        }

        /* Mini attendance preview */
        .preview-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px; padding: 22px;
            margin-top: 36px; max-width: 380px;
        }

        .preview-card-title {
            font-size: 0.72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
            margin-bottom: 16px;
        }

        .preview-stat-row {
            display: flex; gap: 12px; margin-bottom: 16px;
        }

        .preview-stat {
            flex: 1; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px; padding: 14px;
        }

        .preview-stat-num {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem; font-weight: 800;
            color: white;
        }

        .preview-stat-label {
            font-size: 0.7rem; color: rgba(255,255,255,0.35);
            margin-top: 2px;
        }

        .preview-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .preview-dot {
            width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
        }

        .preview-name {
            font-size: 0.8rem; color: rgba(255,255,255,0.6); flex: 1;
        }

        .preview-time {
            font-size: 0.75rem; font-family: 'Syne', sans-serif;
            font-weight: 600; color: rgba(255,255,255,0.4);
        }

        .preview-badge {
            font-size: 0.65rem; font-weight: 600;
            padding: 2px 8px; border-radius: 100px;
        }

        .left-footer {
            position: relative; z-index: 1;
            font-size: 0.75rem; color: rgba(255,255,255,0.2);
        }

        .left-footer a { color: rgba(255,255,255,0.3); text-decoration: none; }

        /* RIGHT PANEL */
        .right-panel {
            width: 480px;
            background: var(--paper);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
        }

        .back-link {
            position: absolute; top: 28px; left: 28px;
            display: flex; align-items: center; gap: 6px;
            font-size: 0.8rem; font-weight: 500; color: var(--muted);
            text-decoration: none; transition: color 0.2s;
        }

        .back-link:hover { color: var(--ink); }

        .back-link svg { width: 14px; height: 14px; }

        .right-panel h1 {
            font-family: 'Syne', sans-serif;
            font-size: 2rem; font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .right-panel .subtitle {
            font-size: 0.9rem; color: var(--muted);
            margin-bottom: 36px;
        }

        /* FORM */
        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 0.8rem; font-weight: 600;
            color: var(--ink); margin-bottom: 8px;
            letter-spacing: 0.01em;
        }

        .form-input {
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            padding: 13px 16px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: white;
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            border-color: var(--ink);
            box-shadow: 0 0 0 3px rgba(13,17,23,0.06);
        }

        .form-input.error { border-color: var(--error); }
        .form-input.error:focus { box-shadow: 0 0 0 3px rgba(220,38,38,0.1); }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer; color: var(--muted);
            display: flex; align-items: center;
            transition: color 0.2s;
        }

        .input-icon:hover { color: var(--ink); }
        .input-icon svg { width: 18px; height: 18px; }

        .form-error {
            font-size: 0.78rem; color: var(--error);
            margin-top: 6px; display: none;
        }

        .form-error.show { display: block; }

        /* ERROR ALERT */
        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca;
            color: var(--error); border-radius: 10px;
            padding: 12px 16px; font-size: 0.85rem;
            margin-bottom: 24px; display: none;
        }

        .alert-error.show { display: block; }

        /* ROLE SELECT */
        .role-tabs {
            display: flex; gap: 8px; margin-bottom: 24px;
        }

        .role-tab {
            flex: 1; padding: 10px;
            border: 1.5px solid var(--border);
            border-radius: 10px; background: white;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem; font-weight: 500;
            color: var(--muted); cursor: pointer;
            transition: all 0.2s; text-align: center;
        }

        .role-tab.active {
            border-color: var(--ink);
            background: var(--ink); color: white;
        }

        /* SUBMIT BUTTON */
        .submit-btn {
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem; font-weight: 700;
            color: white; border: none;
            padding: 14px; border-radius: 10px;
            background: var(--ink);
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 8px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .submit-btn:hover { background: #1a2332; transform: translateY(-1px); }
        .submit-btn:active { transform: translateY(0); }

        .submit-btn.loading { opacity: 0.7; cursor: not-allowed; }

        /* DIVIDER */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border);
        }

        .divider span { font-size: 0.75rem; color: var(--muted); }

        /* BACK TO LANDING */
        .back-to-landing {
            text-align: center; margin-top: 24px;
            font-size: 0.82rem; color: var(--muted);
        }

        .back-to-landing a { color: var(--ink); font-weight: 600; text-decoration: none; }
        .back-to-landing a:hover { text-decoration: underline; }

        /* MOBILE LAYOUT */
        @media (max-width: 768px) {
            body { flex-direction: column; }

            .left-panel {
                min-height: auto; padding: 28px 24px 32px;
            }

            .left-content h2 { font-size: 1.8rem; }
            .preview-card { display: none; }
            .left-footer { display: none; }

            .right-panel {
                width: 100%; padding: 40px 24px;
            }

            .back-link { top: 16px; left: 16px; }
        }

        /* ANIMATION */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .right-panel > * {
            animation: fadeUp 0.5s ease both;
        }

        .right-panel > *:nth-child(2) { animation-delay: 0.05s; }
        .right-panel > *:nth-child(3) { animation-delay: 0.1s; }
        .right-panel > *:nth-child(4) { animation-delay: 0.15s; }

        /* Live clock */
        .live-time {
            font-family: 'Syne', sans-serif;
            font-size: 0.9rem; font-weight: 600;
            color: rgba(255,255,255,0.3);
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <a class="logo" href="{{ route('welcome') }}">
        <div class="logo-icon"></div>
        AttendanceIQ
    </a>

    <div class="left-content">
        <h2>Welcome<br>back to<br><span>your team.</span></h2>
        <p>Manage attendance, track late and overtime, and run payroll — all from one mobile-friendly dashboard.</p>

        <div class="preview-card">
            <div class="preview-card-title">Today — March 6, 2026</div>
            <div class="preview-stat-row">
                <div class="preview-stat">
                    <div class="preview-stat-num">18</div>
                    <div class="preview-stat-label">Present</div>
                </div>
                <div class="preview-stat">
                    <div class="preview-stat-num" style="color:#f87171;">3</div>
                    <div class="preview-stat-label">Absent</div>
                </div>
                <div class="preview-stat">
                    <div class="preview-stat-num" style="color:#fbbf24;">5</div>
                    <div class="preview-stat-label">Late</div>
                </div>
            </div>

            <div class="preview-row">
                <div class="preview-dot" style="background:#4ade80;"></div>
                <span class="preview-name">Juan Dela Cruz</span>
                <span class="preview-time">8:02 AM</span>
                <span class="preview-badge" style="background:rgba(34,197,94,0.15);color:#4ade80;">IN</span>
            </div>
            <div class="preview-row">
                <div class="preview-dot" style="background:#fbbf24;"></div>
                <span class="preview-name">Maria Santos</span>
                <span class="preview-time">8:35 AM</span>
                <span class="preview-badge" style="background:rgba(251,191,36,0.15);color:#fbbf24;">LATE</span>
            </div>
            <div class="preview-row">
                <div class="preview-dot" style="background:#f87171;"></div>
                <span class="preview-name">Ana Lim</span>
                <span class="preview-time">—</span>
                <span class="preview-badge" style="background:rgba(239,68,68,0.15);color:#f87171;">ABSENT</span>
            </div>
        </div>
    </div>

    <div class="left-footer">
        © 2026 AttendanceIQ &nbsp;·&nbsp;
        <a href="#">Privacy</a> &nbsp;·&nbsp;
        <span class="live-time" id="leftClock"></span>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <a href="{{ route('welcome') }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to home
    </a>

    <h1>Sign in</h1>
    <p class="subtitle">Enter your credentials to access your dashboard.</p>

    <!-- ERROR ALERT (Laravel) -->
    @if($errors->any())
    <div class="alert-error show">
        {{ $errors->first() }}
    </div>
    @endif

    <!-- ROLE SELECTOR -->
    <div class="role-tabs">
        <div class="role-tab active" onclick="setRole(this, 'Admin')">🛡 Admin</div>
        <div class="role-tab" onclick="setRole(this, 'Employee')">👤 Employee</div>
    </div>

    <!-- FORM -->
    <form method="POST" action="{{ route('authenticate') }}" id="loginForm">
        @csrf
        <input type="hidden" name="role_hint" id="roleHint" value="Admin">

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input
                type="text"
                name="username"
                id="username"
                class="form-input @error('username') error @enderror"
                placeholder="Enter your username"
                value="{{ old('username') }}"
                autocomplete="username"
                required
            >
            @error('username')
                <div class="form-error show">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrapper">
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-input"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
                <span class="input-icon" onclick="togglePassword()" id="eyeIcon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </span>
            </div>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
            <span>Sign In</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    <div class="back-to-landing">
        <a href="{{ route('welcome') }}">← Return to landing page</a>
    </div>
</div>

<script>
    // Role tab toggle
    function setRole(el, role) {
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('roleHint').value = role;
    }

    // Show/hide password
    let showPass = false;
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        showPass = !showPass;
        input.type = showPass ? 'text' : 'password';
        icon.innerHTML = showPass
            ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
               </svg>`
            : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
               </svg>`;
    }

    // Submit loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
        btn.innerHTML = '<span>Signing in...</span>';
        btn.disabled = true;
    });

    // Live clock (left panel)
    function updateLeftClock() {
        const el = document.getElementById('leftClock');
        if (!el) return;
        const now = new Date();
        const h = now.getHours().toString().padStart(2, '0');
        const m = now.getMinutes().toString().padStart(2, '0');
        const s = now.getSeconds().toString().padStart(2, '0');
        el.textContent = `${h}:${m}:${s}`;
    }
    updateLeftClock();
    setInterval(updateLeftClock, 1000);
</script>

</body>
</html>