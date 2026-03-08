<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — AttendanceIQ</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .logo-text { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1rem; color: #fff; }

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
            transition: background .15s, color .15s; margin-bottom: 2px;
        }
        .nav-link i { width: 18px; text-align: center; font-size: .85rem; }
        .nav-link:hover { background: rgba(255,255,255,.07); color: #fff; }
        .nav-link.active { background: rgba(232,93,38,.18); color: var(--accent); }
        .nav-link.active i { color: var(--accent); }

        .sidebar-footer { padding: 14px 10px; border-top: 1px solid rgba(255,255,255,.06); }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,.04);
        }
        .su-avatar {
            width: 34px; height: 34px; border-radius: 50%; background: var(--accent);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8rem; flex-shrink: 0;
        }
        .su-info { flex: 1; min-width: 0; }
        .su-info strong { font-size: .82rem; color: #fff; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .su-info span { font-size: .7rem; color: rgba(255,255,255,.4); }
        .su-logout {
            margin-left: auto; color: rgba(255,255,255,.3); background: none; border: none;
            text-decoration: none; padding: 4px; cursor: pointer; transition: color .2s;
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
            font-weight: 700; color: var(--ink); letter-spacing: .02em;
        }
        .topbar-date { font-size: .75rem; color: var(--muted); text-align: right; }

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
        .content { padding: 28px; max-width: 1200px; }

        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .page-header h1 { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800; }
        .page-header p { color: var(--muted); font-size: .88rem; margin-top: 4px; }
        .page-header-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

        .card { background: var(--white); border-radius: 12px; box-shadow: var(--card-shadow); overflow: hidden; }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px;
        }
        .card-title { font-weight: 700; font-size: .92rem; }
        .card-body { padding: 20px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--white); border-radius: 12px; padding: 20px;
            box-shadow: var(--card-shadow);
        }
        .stat-num { font-size: 1.8rem; font-weight: 800; font-family: 'Syne', sans-serif; }
        .stat-label { font-size: .78rem; color: var(--muted); margin-top: 4px; }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 8px; font-size: .82rem;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            font-family: inherit; transition: all .2s; white-space: nowrap;
        }
        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { background: #d14d1a; }
        .btn-primary { background: var(--info); color: #fff; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border); color: var(--ink); }
        .btn-outline:hover { border-color: var(--ink); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { background: #15803d; }
        .btn-lg { padding: 14px 28px; font-size: 1rem; border-radius: 10px; }
        .btn-sm { padding: 6px 12px; font-size: .78rem; }

        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        th { background: #faf9f6; font-weight: 600; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); white-space: nowrap; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        tbody tr:hover { background: #faf9f6; }
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 100px;
            font-size: .72rem; font-weight: 600; white-space: nowrap;
        }

        /* ── FORMS ── */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 7px; }
        .form-group.full { grid-column: 1/-1; }
        .form-label { font-size: .78rem; font-weight: 700; color: var(--ink); }
        .form-control {
            width: 100%; font-family: 'DM Sans', sans-serif;
            font-size: max(.88rem, 16px);
            padding: 10px 13px; border: 1.5px solid #e5e2da;
            border-radius: 9px; background: #fff; color: var(--ink);
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--ink);
            box-shadow: 0 0 0 3px rgba(13,17,23,.06);
        }

        .alert {
            padding: 14px 18px; border-radius: 10px; margin-bottom: 20px;
            display: flex; gap: 10px; align-items: center; font-size: .88rem;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

        .empty-state { text-align: center; padding: 50px 20px; color: var(--muted); }
        .empty-state i { font-size: 2.5rem; margin-bottom: 12px; opacity: .4; display: block; }

        /* ── OVERLAY (mobile) ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 299;
            -webkit-backdrop-filter: blur(2px);
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }

        /* Sidebar close button (mobile only) */
        .sidebar-close-btn {
            display: none; position: absolute; top: 14px; right: 10px;
            background: transparent; border: none; color: rgba(255,255,255,.4);
            font-size: 1.2rem; cursor: pointer; padding: 6px;
            border-radius: 6px; transition: color .2s, background .2s;
            z-index: 10;
        }
        .sidebar-close-btn:hover { color: #fff; background: rgba(255,255,255,.08); }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 220px; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            .sidebar.open { transform: translateX(0); }

            .topbar { left: 0; }
            .topbar-toggle { display: flex; }

            .main-wrap { margin-left: 0; }
            .content { padding: 20px 16px; }

            .form-grid { grid-template-columns: 1fr !important; }
            .form-group.full { grid-column: 1; }

            .topbar-clock, .topbar-date { display: none; }

            .topbar-logout span { display: none; }
            .topbar-logout { padding: 7px 10px; }

            .page-header { flex-direction: column; gap: 12px; }
            .page-header-actions { width: 100%; }
            .page-header-actions .btn { flex: 1; justify-content: center; }

            .card-header { flex-direction: column; align-items: flex-start; gap: 8px; }

            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }

            .table-wrap { -webkit-overflow-scrolling: touch; }
            table { min-width: 500px; }

            .sidebar-close-btn { display: flex; }

            .filter-bar { flex-direction: column !important; gap: 8px !important; }
            .filter-bar .form-control,
            .filter-bar .btn,
            .filter-bar select { width: 100% !important; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .page-header { flex-direction: column; }
            .content { padding: 14px 10px; }
            th, td { padding: 10px 8px; font-size: .78rem; }

            .page-header h1 { font-size: 1.15rem; }
            .btn { font-size: .78rem; padding: 8px 14px; }
            .card { border-radius: 10px; }
            .card-body { padding: 14px; }
        }

        @media (max-width: 360px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close-btn" onclick="closeSidebar()" aria-label="Close sidebar">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <a href="{{ route('employee.dashboard') }}" class="sidebar-logo">
        <div class="logo-sq"></div>
        <span class="logo-text">AttendanceIQ</span>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-group-label">Main</div>
        <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="{{ route('employee.attendance') }}" class="nav-link {{ request()->routeIs('employee.attendance*') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> Attendance
        </a>
        <a href="{{ route('employee.leaves') }}" class="nav-link {{ request()->routeIs('employee.leaves*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-xmark"></i> Leaves
        </a>

        <div class="nav-group-label">Account</div>
        <a href="{{ route('employee.profile') }}" class="nav-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i> My Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="su-avatar">{{ strtoupper(substr(auth()->user()->employee->name ?? 'U', 0, 2)) }}</div>
            <div class="su-info">
                <strong>{{ auth()->user()->employee->name ?? auth()->user()->username }}</strong>
                <span>Employee</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="su-logout" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button>
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
        <span>Employee</span>
        <i class="fa-solid fa-chevron-right" style="font-size:.6rem;"></i>
        <strong>@yield('breadcrumb', 'Dashboard')</strong>
    </div>

    <div class="topbar-right">
        <div>
            <div class="topbar-clock" id="tClock"></div>
            <div class="topbar-date" id="tDate"></div>
        </div>
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
    <div class="content">
        @yield('content')
    </div>
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
    document.body.style.overflow = s.classList.contains('open') ? 'hidden' : '';
}
function closeSidebar(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow = '';
}
window.addEventListener('resize',()=>{ if(window.innerWidth>768)closeSidebar(); });
/* Close sidebar when nav link clicked on mobile */
document.querySelectorAll('.sidebar-nav .nav-link').forEach(link=>{
    link.addEventListener('click',()=>{ if(window.innerWidth<=768) closeSidebar(); });
});

/* Auto-dismiss alerts */
setTimeout(()=>{
    document.querySelectorAll('.alert').forEach(el=>{
        el.style.transition='opacity .5s';
        el.style.opacity='0';
        setTimeout(()=>el.remove(),500);
    });
}, 5000);

/* SweetAlert2 flash modals */
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: {!! json_encode(session('success')) !!},
    confirmButtonColor: '#0d1117',
    background: '#fff',
    customClass: { popup: 'swal-modern' },
    timer: 3000,
    timerProgressBar: true,
    showConfirmButton: true
});
@endif
@if(session('tap_success'))
Swal.fire({
    icon: 'success',
    title: 'Time Recorded!',
    text: {!! json_encode(session('tap_success')) !!},
    confirmButtonColor: '#0d1117',
    background: '#fff',
    customClass: { popup: 'swal-modern' },
    timer: 3000,
    timerProgressBar: true,
    showConfirmButton: true
});
@endif
@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: {!! json_encode(session('error')) !!},
    confirmButtonColor: '#dc2626',
    background: '#fff',
    customClass: { popup: 'swal-modern' }
});
@endif
</script>

@yield('scripts')
</body>
</html>
