<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="theme-color" content="#0f2744">
    <title>@yield('title', 'PPDB SMK YADIKA 8 JATIMULYA')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy:#0f2744; --blue:#1a4a8a; --accent:#e8a020;
            --light:#f4f7fb; --white:#fff; --text:#1e293b;
            --muted:#64748b; --border:#e2e8f0;
            --nav-h: 60px;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--light);color:var(--text);-webkit-tap-highlight-color:transparent;}

        /* ===================== NAVBAR ===================== */
        nav{
            background:var(--navy);height:var(--nav-h);
            display:flex;align-items:center;justify-content:space-between;
            padding:0 1.25rem;position:sticky;top:0;z-index:200;
            box-shadow:0 2px 12px rgba(0,0,0,.3);
        }
        .nav-brand{display:flex;align-items:center;gap:.65rem;text-decoration:none;color:#fff;}
        .nav-brand-text strong{display:block;font-weight:800;font-size:.95rem;line-height:1.2;}
        .nav-brand-text small{font-size:.6rem;color:rgba(255,255,255,.5);font-weight:400;}

        /* Desktop links */
        .nav-links{display:flex;align-items:center;gap:.2rem;}
        .nav-links a{color:rgba(255,255,255,.75);text-decoration:none;padding:.45rem .8rem;border-radius:8px;font-size:.85rem;font-weight:500;transition:.2s;white-space:nowrap;}
        .nav-links a:hover,.nav-links a.active{background:rgba(255,255,255,.1);color:#fff;}
        .nav-links .btn-nav{background:var(--accent);color:var(--navy)!important;font-weight:700;padding:.45rem .9rem;}
        .nav-links .btn-nav:hover{filter:brightness(.9);}

        /* Hamburger */
        .nav-toggle{display:none;background:none;border:none;cursor:pointer;padding:.4rem;border-radius:8px;transition:.2s;color:#fff;font-size:1.2rem;line-height:1;}
        .nav-toggle:hover{background:rgba(255,255,255,.1);}

        /* Mobile drawer */
        .nav-drawer{
            display:none;position:fixed;top:var(--nav-h);left:0;right:0;bottom:0;z-index:190;
            background:rgba(0,0,0,.5);opacity:0;transition:opacity .25s;
        }
        .nav-drawer.open{opacity:1;}
        .nav-drawer-inner{
            position:absolute;top:0;right:0;width:260px;height:100%;
            background:var(--navy);padding:1rem 0;
            transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);
            overflow-y:auto;
        }
        .nav-drawer.open .nav-drawer-inner{transform:translateX(0);}
        .nav-drawer a{
            display:flex;align-items:center;gap:.75rem;
            padding:.85rem 1.5rem;color:rgba(255,255,255,.8);
            text-decoration:none;font-size:.9rem;font-weight:500;
            border-bottom:1px solid rgba(255,255,255,.06);transition:.2s;
        }
        .nav-drawer a:hover,.nav-drawer a.active{background:rgba(255,255,255,.08);color:#fff;}
        .nav-drawer .drawer-daftar{
            margin:1rem 1.25rem 0;background:var(--accent);color:var(--navy)!important;
            border-radius:10px;font-weight:700;justify-content:center;border:none;
        }

        .nav-brand-icon img{
            width:50px;
            height:50px;
            object-fit:contain;
        }
        /* ===================== LAYOUT ===================== */
        main{min-height:calc(100vh - var(--nav-h) - 60px);}
        footer{background:var(--navy);color:rgba(255,255,255,.6);text-align:center;padding:1.25rem;font-size:.78rem;}
        footer strong{color:var(--accent);}

        /* ===================== UTILITIES ===================== */
        .container{max-width:900px;margin:0 auto;padding:1.5rem 1rem;}
        .card{background:var(--white);border-radius:14px;border:1px solid var(--border);overflow:hidden;}
        .card-header{padding:.9rem 1.1rem;background:var(--light);border-bottom:1px solid var(--border);font-weight:700;display:flex;align-items:center;gap:.55rem;font-size:.9rem;}
        .card-header i{color:var(--blue);}
        .card-body{padding:1.1rem;}

        /* Forms */
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.85rem;}
        .form-group{margin-bottom:0;}
        .form-label{display:block;font-size:.78rem;font-weight:700;color:var(--text);margin-bottom:.35rem;}
        .req{color:#dc2626;}
        .form-control{width:100%;padding:.65rem .9rem;border:1.5px solid var(--border);border-radius:9px;font-family:inherit;font-size:.9rem;color:var(--text);background:var(--white);transition:border-color .2s;-webkit-appearance:none;}
        .form-control:focus{outline:none;border-color:var(--blue);}
        /* Touch target minimum 44px */
        input.form-control,select.form-control{min-height:44px;}
        select.form-control{cursor:pointer;}
        .form-hint{font-size:.72rem;color:var(--muted);margin-top:.25rem;}
        .invalid-feedback{font-size:.72rem;color:#dc2626;margin-top:.25rem;}
        .is-invalid{border-color:#dc2626!important;}

        .btn-submit{background:var(--navy);color:#fff;border:none;padding:.85rem 1.75rem;border-radius:10px;font-family:inherit;font-size:.95rem;font-weight:700;cursor:pointer;transition:background .2s;display:inline-flex;align-items:center;gap:.5rem;min-height:48px;touch-action:manipulation;}
        .btn-submit:hover{background:var(--blue);}
        .btn-submit:active{transform:scale(.98);}

        .alert{padding:.85rem 1rem;border-radius:10px;margin-bottom:1.1rem;display:flex;align-items:flex-start;gap:.6rem;font-size:.85rem;}
        .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
        .col-span-2{grid-column:span 2;}

        /* ===================== RESPONSIVE ===================== */
        @media(max-width:768px){
            .nav-links{display:none;}
            .nav-toggle{display:flex;align-items:center;justify-content:center;}
            .nav-drawer{display:block;}
            .form-grid{grid-template-columns:1fr;}
            .col-span-2{grid-column:span 1;}
            .container{padding:1rem .85rem;}
        }
        @media(max-width:480px){
            .card-body{padding:.85rem;}
            .btn-submit{width:100%;justify-content:center;}
        }
    </style>
    @stack('styles')
</head>
<body>
<nav>
    <a class="nav-brand" href="{{ route('home') }}">
        <div class="nav-brand-icon">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMK YADIKA 8">
        </div>
        <div class="nav-brand-text">
            <strong>PPDB SMK YADIKA 8 JATIMULYA</strong>
            <small>SIPENA</small>
        </div>
    </a>

    {{-- Desktop --}}
    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home')?'active':'' }}">Beranda</a>
        <a href="{{ route('ppdb.cek-status') }}" class="{{ request()->routeIs('ppdb.cek-status')?'active':'' }}">Cek Status</a>
        <a href="{{ route('siswa.login') }}" class="{{ request()->routeIs('siswa.*')?'active':'' }}">Portal Siswa</a>
        <a href="{{ route('ppdb.index') }}" class="btn-nav"><i class="fas fa-edit"></i> Daftar</a>
    </div>

    {{-- Hamburger --}}
    <button class="nav-toggle" id="navToggle" aria-label="Menu" aria-expanded="false">
        <i class="fas fa-bars" id="navIcon"></i>
    </button>
</nav>

{{-- Mobile Drawer --}}
<div class="nav-drawer" id="navDrawer" role="dialog" aria-modal="true">
    <div class="nav-drawer-inner">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home')?'active':'' }}">
            <i class="fas fa-home" style="width:18px"></i> Beranda
        </a>
        <a href="{{ route('ppdb.cek-status') }}" class="{{ request()->routeIs('ppdb.cek-status')?'active':'' }}">
            <i class="fas fa-search" style="width:18px"></i> Cek Status
        </a>
        <a href="{{ route('siswa.login') }}" class="{{ request()->routeIs('siswa.*')?'active':'' }}">
            <i class="fas fa-user" style="width:18px"></i> Portal Siswa
        </a>
        <a href="{{ route('ppdb.index') }}" class="drawer-daftar">
            <i class="fas fa-edit"></i> Daftar Sekarang
        </a>
    </div>
</div>

<main>
    @if(session('success'))
    <div style="padding:.85rem 1rem 0">
        <div class="alert" style="background:#dcfce7;color:#166534;border:1px solid #86efac">
            <i class="fas fa-check-circle" style="flex-shrink:0;margin-top:.1rem"></i> {{ session('success') }}
        </div>
    </div>
    @endif
    @yield('content')
</main>

<footer>
    &copy; {{ date('Y') }} <strong>SIPENA</strong> — PPDB SMK YADIKA 8 JATIMULYA.
</footer>

<script>
const toggle = document.getElementById('navToggle');
const drawer = document.getElementById('navDrawer');
const icon   = document.getElementById('navIcon');
let open = false;

function openDrawer(){
    open = true;
    drawer.classList.add('open');
    icon.className = 'fas fa-times';
    toggle.setAttribute('aria-expanded','true');
    document.body.style.overflow = 'hidden';
}
function closeDrawer(){
    open = false;
    drawer.classList.remove('open');
    icon.className = 'fas fa-bars';
    toggle.setAttribute('aria-expanded','false');
    document.body.style.overflow = '';
}

toggle.addEventListener('click', () => open ? closeDrawer() : openDrawer());
drawer.addEventListener('click', e => { if(e.target === drawer) closeDrawer(); });
document.addEventListener('keydown', e => { if(e.key==='Escape' && open) closeDrawer(); });
// Tutup drawer saat navigasi (SPA-like)
drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));
</script>
@stack('scripts')
</body>
</html>
