<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — AttendanceIQ Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --ink: #0d1117; --paper: #f5f2eb; --accent: #e85d26;
            --sidebar-w: 240px; --sidebar-bg: #0d1117;
            --topbar-h: 60px; --white: #fff;
            --border: rgba(0,0,0,0.08); --muted: #6b7280;
            --success: #16a34a; --danger: #dc2626; --warning: #d97706;
            --info: #2563eb; --card-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
        html { font-size: 15px; }
        body { font-family: 'DM Sans', sans-serif; background: #f0ede6; color: var(--ink); -webkit-font-smoothing: antialiased; }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w); background: var(--sidebar-bg);
            display: flex; flex-direction: column; z-index: 300;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }
        .sidebar-logo {
            height: var(--topbar-h); display: flex; align-items: center;
            gap: 10px; padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            text-decoration: none;
        }
        .logo-sq {
            width: 28px; height: 28px; background: var(--accent);
            border-radius: 6px; position: relative; flex-shrink: 0;
        }
        .logo-sq::after {
            content: ''; position: absolute; inset: 5px;
            border: 2px solid rgba(255,255,255,.8); border-radius: 3px;
        }
        .logo-text {
            font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1rem;
            color: #fff; letter-spacing: -.01em;
        }
        .logo-badge {
            font-size: .55rem; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; background: var(--accent);
            color: #fff; padding: 2px 6px; border-radius: 4px;
        }

        .sidebar-nav { flex: 1; padding: 14px 10px; overflow-y: auto; }

        .nav-group-label {
            font-size: .65rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,.25);
            padding: 8px 10px 6px; margin-top: 10px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            text-decoration: none; color: rgba(255,255,255,.55);
            font-size: .85rem; font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .nav-link i { width: 18px; text-align: center; font-size: .85rem; }
        .nav-link:hover { background: rgba(255,255,255,.07); color: #fff; }
        .nav-link.active { background: rgba(232,93,38,.18); color: var(--accent); }
        .nav-link.active i { color: var(--accent); }

        .nav-badge {
            margin-left: auto; background: var(--accent); color: #fff;
            font-size: .6rem; font-weight: 700; padding: 2px 7px;
            border-radius: 100px; min-width: 20px; text-align: center;
        }

        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            background: rgba(255,255,255,.04);
        }
        .su-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--accent); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem; flex-shrink: 0;
        }
        .su-info strong { font-size: .82rem; color: #fff; display: block; }
        .su-info span { font-size: .7rem; color: rgba(255,255,255,.4); }
        .su-logout {
            margin-left: auto; color: rgba(255,255,255,.3);
            text-decoration: none; transition: color .2s;
            padding: 4px;
        }
        .su-logout:hover { color: #f87171; }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--topbar-h); z-index: 200;
            background: var(--white); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 16px;
            padding: 0 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .topbar-toggle {
            display: none; background: transparent; border: none;
            cursor: pointer; padding: 6px; color: var(--ink);
            -webkit-tap-highlight-color: transparent;
        }
        .topbar-toggle i { font-size: 1.1rem; }

        .topbar-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: .82rem; color: var(--muted);
        }
        .topbar-breadcrumb strong { color: var(--ink); font-weight: 600; }

        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 14px; }

        .topbar-clock {
            font-family: 'Syne', sans-serif; font-size: .9rem;
            font-weight: 700; color: var(--ink);
            letter-spacing: .02em;
        }

        .topbar-date { font-size: .75rem; color: var(--muted); }

        .notif-btn {
            position: relative; background: transparent; border: none;
            cursor: pointer; color: var(--muted); padding: 6px;
            border-radius: 8px; transition: background .15s, color .15s;
        }
        .notif-btn:hover { background: #f3f4f6; color: var(--ink); }
        .notif-badge {
            position: absolute; top: 3px; right: 3px;
            width: 8px; height: 8px; background: var(--accent);
            border-radius: 50%; border: 2px solid #fff;
        }

        .topbar-logout {
            display: inline-flex; align-items: center; gap: 6px;
            background: none; border: 1.5px solid var(--border);
            color: var(--muted); padding: 7px 14px; border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: .8rem;
            font-weight: 600; cursor: pointer; transition: all .2s;
            white-space: nowrap;
        }
        .topbar-logout:hover { border-color: var(--danger); color: var(--danger); background: #fef2f2; }

        /* ── MAIN ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }
        .main-content { padding: 28px 28px; }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-wrap: wrap; gap: 14px; margin-bottom: 26px;
        }
        .page-header h1 {
            font-family: 'Syne', sans-serif; font-size: 1.45rem;
            font-weight: 800; letter-spacing: -.02em;
        }
        .page-header p { font-size: .85rem; color: var(--muted); margin-top: 3px; }
        .page-header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        /* ── CARDS ── */
        .card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
        }
        .card-title {
            font-family: 'Syne', sans-serif; font-size: .95rem; font-weight: 700;
        }
        .card-body { padding: 20px; }

        /* ── STAT CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px; margin-bottom: 26px;
        }
        .stat-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 14px; padding: 20px;
            box-shadow: var(--card-shadow);
            display: flex; flex-direction: column; gap: 10px;
        }
        .stat-top { display: flex; align-items: center; justify-content: space-between; }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .stat-delta {
            font-size: .72rem; font-weight: 600; padding: 3px 8px;
            border-radius: 100px;
        }
        .delta-up   { background: #dcfce7; color: #16a34a; }
        .delta-down { background: #fee2e2; color: #dc2626; }
        .delta-warn { background: #fef3c7; color: #d97706; }
        .stat-num {
            font-family: 'Syne', sans-serif; font-size: 1.9rem;
            font-weight: 800; line-height: 1; color: var(--ink);
        }
        .stat-label { font-size: .78rem; color: var(--muted); }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            font-family: 'DM Sans', sans-serif; font-size: .84rem; font-weight: 600;
            padding: 9px 18px; border-radius: 8px; border: none;
            cursor: pointer; text-decoration: none; white-space: nowrap;
            transition: filter .15s, transform .12s, opacity .15s;
            -webkit-tap-highlight-color: transparent;
        }
        .btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
        .btn-primary   { background: var(--ink); color: #fff; }
        .btn-accent    { background: var(--accent); color: #fff; box-shadow: 0 3px 12px rgba(232,93,38,.3); }
        .btn-success   { background: #16a34a; color: #fff; }
        .btn-danger    { background: var(--danger); color: #fff; }
        .btn-warning   { background: var(--warning); color: #fff; }
        .btn-outline   { background: #fff; color: var(--ink); border: 1.5px solid var(--border); }
        .btn-outline:hover { border-color: rgba(0,0,0,.2); }
        .btn-sm { font-size: .76rem; padding: 6px 12px; border-radius: 6px; }
        .btn-icon { padding: 7px 10px; }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .84rem; }
        thead tr { background: #f8f7f4; }
        th {
            text-align: left; font-size: .72rem; font-weight: 700;
            letter-spacing: .07em; text-transform: uppercase;
            color: var(--muted); padding: 11px 14px;
            border-bottom: 1px solid var(--border); white-space: nowrap;
        }
        td { padding: 12px 14px; border-bottom: 1px solid #f1ede6; vertical-align: middle; }
        tbody tr:hover { background: #faf9f6; }
        tbody tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: .7rem; font-weight: 700; padding: 3px 10px;
            border-radius: 100px; white-space: nowrap;
        }
        .badge-present  { background: #dcfce7; color: #166534; }
        .badge-late     { background: #fef3c7; color: #92400e; }
        .badge-absent   { background: #fee2e2; color: #991b1b; }
        .badge-half     { background: #e0e7ff; color: #3730a3; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-admin    { background: rgba(232,93,38,.12); color: var(--accent); }
        .badge-employee { background: #e0e7ff; color: #3730a3; }
        .badge-ot       { background: #ede9fe; color: #5b21b6; }

        /* ── FORMS ── */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 7px; }
        .form-group.full { grid-column: 1/-1; }
        .form-label { font-size: .78rem; font-weight: 700; color: var(--ink); }
        .form-control {
            width: 100%; font-family: 'DM Sans', sans-serif;
            font-size: max(.88rem, 16px); /* prevent iOS zoom */
            padding: 10px 13px; border: 1.5px solid #e5e2da;
            border-radius: 9px; background: #fff; color: var(--ink);
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--ink);
            box-shadow: 0 0 0 3px rgba(13,17,23,.06);
        }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { font-size: .75rem; color: var(--danger); }
        .form-hint { font-size: .73rem; color: var(--muted); }
        select.form-control { cursor: pointer; }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 16px; border-radius: 10px; font-size: .84rem;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 20px;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

        /* ── PAGINATION ── */
        .pagination { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; padding: 16px 20px; }
        .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; padding: 0 10px;
            border: 1.5px solid var(--border); border-radius: 7px;
            font-size: .82rem; font-weight: 500; color: var(--muted);
            text-decoration: none; transition: all .15s;
        }
        .page-link:hover { border-color: var(--ink); color: var(--ink); }
        .page-link.active { background: var(--ink); color: #fff; border-color: var(--ink); }
        .page-link.disabled { opacity: .4; pointer-events: none; }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex; gap: 10px; flex-wrap: wrap;
            align-items: flex-end; margin-bottom: 20px;
        }
        .filter-bar .form-group { min-width: 160px; flex: 1; }
        .filter-bar label { font-size: .72rem; font-weight: 700; color: var(--muted); margin-bottom: 5px; display: block; }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center; padding: 60px 20px;
            color: var(--muted);
        }
        .empty-state i { font-size: 2.5rem; margin-bottom: 14px; opacity: .4; display: block; }
        .empty-state h3 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
        .empty-state p { font-size: .84rem; }

        /* ── OVERLAY ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 299;
        }
        .sidebar-overlay.show { display: block; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 220px; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open { transform: translateX(0); }

            .topbar { left: 0; }
            .topbar-toggle { display: flex; }

            .main-wrap { margin-left: 0; }
            .main-content { padding: 20px 16px; }

            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: 1; }

            .topbar-clock, .topbar-date { display: none; }

            .topbar-logout span { display: none; }
            .topbar-logout { padding: 7px 10px; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .page-header { flex-direction: column; }
            .main-content { padding: 16px 12px; }
            th, td { padding: 10px 10px; }
        }

        /* ── MISC ── */
        .text-muted { color: var(--muted); }
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .text-warning { color: var(--warning); }
        .fw-bold { font-weight: 700; }
        .mt-1 { margin-top: 6px; }
        .mt-2 { margin-top: 12px; }
        .mt-3 { margin-top: 20px; }
        .mb-1 { margin-bottom: 6px; }
        .mb-2 { margin-bottom: 12px; }
        .mb-3 { margin-bottom: 20px; }
        .gap-2 { gap: 10px; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .flex-wrap { flex-wrap: wrap; }
        .w-100 { width: 100%; }

        @yield('styles')
    </style>
    @yield('extra_styles')
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
        <div class="logo-sq"></div>
        <span class="logo-text">AttendanceIQ</span>
        <span class="logo-badge">Admin</span>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-group-label">Main</div>
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        <div class="nav-group-label">Workforce</div>
        <a href="{{ route('admin.employees.index') }}"
           class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Employees
        </a>

        <div class="nav-group-label">Attendance</div>
        <a href="{{ route('admin.attendance.index') }}"
           class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> Attendance
        </a>
        <a href="{{ route('admin.leaves.index') }}"
           class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-xmark"></i> Leaves
            @php $pendingCount = \App\Models\Leave::where('status','Pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>

        <div class="nav-group-label">Finance</div>
        <a href="{{ route('admin.payroll.index') }}"
           class="nav-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}">
            <i class="fa-solid fa-peso-sign"></i> Payroll
        </a>

        <div class="nav-group-label">Account</div>
        <a href="{{ route('admin.profile') }}"
           class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i> My Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="su-avatar">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</div>
            <div class="su-info">
                <strong>{{ auth()->user()->username }}</strong>
                <span>{{ auth()->user()->role }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="su-logout" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- TOPBAR -->
<header class="topbar">
    <button class="topbar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="topbar-breadcrumb">
        <span>Admin</span>
        <i class="fa-solid fa-chevron-right" style="font-size:.6rem;"></i>
        <strong>@yield('breadcrumb', 'Dashboard')</strong>
    </div>

    <div class="topbar-right">
        <div>
            <div class="topbar-clock" id="tClock"></div>
            <div class="topbar-date" id="tDate" style="text-align:right;"></div>
        </div>
        <button class="notif-btn" title="Notifications">
            <i class="fa-regular fa-bell" style="font-size:1rem;"></i>
            @if(isset($pendingCount) && $pendingCount > 0)
            <span class="notif-badge"></span>
            @endif
        </button>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="topbar-logout" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</header>

<!-- MAIN -->
<div class="main-wrap">
    <main class="main-content">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
/* Clock */
function tick(){
    const n=new Date(), p=v=>v.toString().padStart(2,'0');
    const el=document.getElementById('tClock');
    const de=document.getElementById('tDate');
    if(el) el.textContent=`${p(n.getHours())}:${p(n.getMinutes())}:${p(n.getSeconds())}`;
    if(de) de.textContent=n.toLocaleDateString('en-PH',{weekday:'short',month:'short',day:'numeric'});
}
tick(); setInterval(tick,1000);

/* Sidebar toggle */
function toggleSidebar(){
    const s=document.getElementById('sidebar');
    const o=document.getElementById('overlay');
    s.classList.toggle('open');
    o.classList.toggle('show');
}
function closeSidebar(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
}
window.addEventListener('resize',()=>{ if(window.innerWidth>768)closeSidebar(); });

/* Confirm deletes */
document.addEventListener('click', e => {
    const btn = e.target.closest('[data-confirm]');
    if(!btn) return;
    if(!confirm(btn.dataset.confirm || 'Are you sure?')){
        e.preventDefault();
    }
});

/* Auto-dismiss alerts */
setTimeout(()=>{
    document.querySelectorAll('.alert').forEach(el=>{
        el.style.transition='opacity .5s';
        el.style.opacity='0';
        setTimeout(()=>el.remove(),500);
    });
}, 5000);
</script>

@yield('scripts')
</body>
</html>