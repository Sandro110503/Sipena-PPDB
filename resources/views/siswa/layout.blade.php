<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f2744">
    <title>@yield('title','Portal Siswa') — SIPENA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{--navy:#0f2744;--blue:#1a4a8a;--accent:#e8a020;--light:#f4f7fb;--white:#fff;--text:#1e293b;--muted:#64748b;--border:#e2e8f0;--nav-h:58px;}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--light);color:var(--text);-webkit-tap-highlight-color:transparent;}

        /* NAV */
        nav{background:var(--navy);height:var(--nav-h);padding:0 1.1rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:200;box-shadow:0 2px 12px rgba(0,0,0,.25);}
        .nav-brand{display:flex;align-items:center;gap:.6rem;color:#fff;text-decoration:none;}
        .nav-brand-icon{width:32px;height:32px;background:var(--accent);border-radius:8px;display:grid;place-items:center;color:var(--navy);font-size:.9rem;font-weight:900;flex-shrink:0;}
        .nav-brand strong{font-weight:800;font-size:.9rem;display:block;line-height:1.2;}
        .nav-brand span{font-size:.62rem;color:rgba(255,255,255,.5);}
        .nav-right{display:flex;align-items:center;gap:.6rem;}
        .nav-user{font-size:.78rem;color:rgba(255,255,255,.75);text-align:right;display:none;}
        .nav-user strong{display:block;color:#fff;font-size:.82rem;}
        .btn-logout{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;padding:.38rem .8rem;border-radius:8px;font-family:inherit;font-size:.75rem;font-weight:600;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:.38rem;text-decoration:none;min-height:36px;touch-action:manipulation;}
        .btn-logout:hover{background:rgba(255,255,255,.18);}

        /* LAYOUT */
        .container{max-width:860px;margin:0 auto;padding:1.25rem 1rem;}
        main{min-height:calc(100vh - var(--nav-h) - 52px);}
        footer{text-align:center;padding:1.1rem;font-size:.72rem;color:#94a3b8;border-top:1px solid var(--border);}

        /* ALERTS */
        .alert{padding:.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:.85rem;display:flex;align-items:flex-start;gap:.6rem;}
        .alert-success{background:#dcfce7;color:#166534;border:1px solid #86efac;}
        .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}

        /* CARD */
        .card{background:var(--white);border-radius:14px;border:1px solid var(--border);overflow:hidden;margin-bottom:1rem;}
        .card:last-child{margin-bottom:0;}
        .card-header{padding:.8rem 1.1rem;background:var(--light);border-bottom:1px solid var(--border);font-weight:700;font-size:.85rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;flex-wrap:wrap;}
        .card-header i{color:var(--blue);}
        .card-body{padding:1rem;}

        /* BADGE */
        .badge{display:inline-flex;align-items:center;padding:.22rem .6rem;border-radius:999px;font-size:.68rem;font-weight:700;}
        .badge-menunggu{background:#fef3c7;color:#92400e;}
        .badge-diterima{background:#dcfce7;color:#166534;}
        .badge-ditolak{background:#fee2e2;color:#991b1b;}
        .badge-cadangan{background:#dbeafe;color:#1e40af;}

        /* GRID */
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}

        /* RESPONSIVE */
        @media(min-width:600px){
            .nav-user{display:block;}
            .nav-links-desk{display:flex!important;align-items:center;gap:.2rem;}
            .container{padding:1.5rem 1.25rem;}
        }
        @media(max-width:600px){
            .grid-2{grid-template-columns:1fr;}
            .card-body{padding:.85rem;}
        }
    </style>
    @stack('styles')
</head>
<body>
<nav>
    <a class="nav-brand" href="{{ route('siswa.dashboard') }}">
        <div class="nav-brand-icon">S</div>
        <div>
            <strong>SIPENA</strong>
            <span>Portal Siswa</span>
        </div>
    </a>
    <div class="nav-right">
        <div style="display:none" class="nav-links-desk">
            <a href="{{ route('siswa.dashboard') }}"
               style="color:rgba(255,255,255,.75);text-decoration:none;padding:.35rem .75rem;border-radius:7px;font-size:.78rem;font-weight:500;{{ request()->routeIs('siswa.dashboard')?'background:rgba(255,255,255,.1);color:#fff':'' }}">
               <i class="fas fa-home" style="margin-right:.25rem"></i>Dashboard
            </a>
            <a href="{{ route('siswa.pengaturan') }}"
               style="color:rgba(255,255,255,.75);text-decoration:none;padding:.35rem .75rem;border-radius:7px;font-size:.78rem;font-weight:500;{{ request()->routeIs('siswa.pengaturan')?'background:rgba(255,255,255,.1);color:#fff':'' }}">
               <i class="fas fa-cog" style="margin-right:.25rem"></i>Pengaturan
            </a>
            @if(Auth::guard('siswa')->user()->status_penerimaan === 'Diterima')
            <a href="{{ route('siswa.pembayaran') }}"
               style="color:rgba(255,255,255,.75);text-decoration:none;padding:.35rem .75rem;border-radius:7px;font-size:.78rem;font-weight:500;{{ request()->routeIs('siswa.pembayaran')?'background:rgba(255,255,255,.1);color:#fff':'' }}">
               <i class="fas fa-credit-card" style="margin-right:.25rem"></i>Pembayaran
            </a>
            @endif
        </div>
        <div class="nav-user">
            <strong>{{ Auth::guard('siswa')->user()->nama_lengkap }}</strong>
            {{ Auth::guard('siswa')->user()->nomor_pendaftaran }}
        </div>
        <form method="POST" action="{{ route('siswa.logout') }}" style="margin:0" id="formLogoutSiswa">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></button>
        </form>
    </div>
</nav>

<main>
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle" style="flex-shrink:0;margin-top:.1rem"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle" style="flex-shrink:0;margin-top:.1rem"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</main>

<footer>&copy; {{ date('Y') }} SIPENA — Portal Siswa</footer>
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfirmasi logout siswa
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formLogoutSiswa');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Keluar dari akun?',
            text: 'Anda akan diarahkan ke halaman login.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
</body>
</html>