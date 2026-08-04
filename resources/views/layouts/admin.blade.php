<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f2744">
    <title>@yield('title', 'Admin PPDB') — SMK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{
            --navy:#0f2744;--blue:#1a4a8a;--accent:#e8a020;
            --light:#f4f7fb;--white:#fff;--text:#1e293b;
            --muted:#64748b;--border:#e2e8f0;
            --success:#16a34a;--danger:#dc2626;--warning:#d97706;
            --sidebar-w:255px;--topbar-h:58px;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        html,body{overflow-x:hidden;max-width:100%;}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--light);color:var(--text);-webkit-tap-highlight-color:transparent;}

        /* ====== SIDEBAR ====== */
        .sidebar{width:var(--sidebar-w);background:var(--navy);color:#fff;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:300;transition:transform .3s cubic-bezier(.4,0,.2,1);box-shadow:4px 0 20px rgba(0,0,0,.15);}
        .sidebar-brand{padding:1rem 1.1rem .9rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between;}
        .sidebar-brand-logo{
            display:flex;
            align-items:center;
            gap:.75rem;
        }

        .sidebar-brand-icon{
            width:52px;
            height:52px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }

        .sidebar-brand-icon img{
            width:100%;
            height:100%;
            object-fit:contain;
        }

        .sidebar-brand h2{
            font-size:1.15rem;
            font-weight:800;
            color:#fff;
            line-height:1.1;
            margin:0;
        }

        .sidebar-brand p{
            font-size:.72rem;
            color:rgba(255,255,255,.6);
            margin-top:2px;
        }
        .sidebar-brand h2{font-size:1rem;font-weight:800;color:var(--accent);}
        .sidebar-brand p{font-size:.62rem;color:rgba(255,255,255,.4);margin-top:1px;}
        .sidebar-close{display:none;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);font-size:1.1rem;padding:.25rem;border-radius:6px;}
        .sidebar-close:hover{color:#fff;background:rgba(255,255,255,.08);}
        .sidebar-nav{flex:1;padding:.65rem 0;overflow-y:auto;position:relative;}
        .sidebar-nav::-webkit-scrollbar{width:5px;}
        .sidebar-nav::-webkit-scrollbar-track{background:transparent;}
        .sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.18);border-radius:3px;}
        .sidebar-nav::-webkit-scrollbar-thumb:hover{background:rgba(255,255,255,.32);}
        .nav-label{font-size:.6rem;font-weight:700;letter-spacing:1.5px;color:rgba(255,255,255,.28);padding:.5rem 1.1rem .15rem;text-transform:uppercase;}
        .nav-item a{display:flex;align-items:center;gap:.65rem;padding:.5rem 1.1rem;color:rgba(255,255,255,.65);text-decoration:none;font-size:.82rem;font-weight:500;transition:.18s;border-left:3px solid transparent;}
        .sidebar-footer{padding:.85rem 1.1rem;border-top:1px solid rgba(255,255,255,.08);}
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:299;opacity:0;transition:opacity .3s;}

        /* ====== TOPBAR ====== */
        .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;transition:margin .3s;}
        .topbar{background:var(--white);border-bottom:1px solid var(--border);height:var(--topbar-h);padding:0 1.25rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
        .topbar-left{display:flex;align-items:center;gap:.85rem;}
        .topbar-toggle{display:none;background:none;border:none;cursor:pointer;color:var(--text);font-size:1.1rem;padding:.35rem;border-radius:8px;transition:.2s;}
        .topbar-toggle:hover{background:var(--light);}
        .topbar-title{font-size:1rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;}
        .topbar-user{display:flex;align-items:center;gap:.65rem;font-size:.82rem;color:var(--muted);}
        .topbar-name{display:none;}
        .avatar{width:34px;height:34px;border-radius:50%;background:var(--blue);color:#fff;display:grid;place-items:center;font-weight:700;font-size:.82rem;flex-shrink:0;cursor:pointer;}

        /* ====== LOGOUT BUTTON ====== */
        /* Logout pakai button type=submit di dalam form POST — CSRF otomatis */
        .btn-logout-sidebar{
            display:flex;align-items:center;justify-content:center;gap:.5rem;
            width:100%;padding:.6rem;background:rgba(255,255,255,.07);
            color:rgba(255,255,255,.75);border:1px solid rgba(255,255,255,.15);
            border-radius:9px;font-family:inherit;font-size:.82rem;font-weight:600;
            cursor:pointer;transition:.2s;
        }
        .btn-logout-sidebar:hover{background:rgba(220,38,38,.2);color:#fca5a5;border-color:rgba(220,38,38,.4);}

        /* ====== CONTENT ====== */
        .content{flex:1;padding:1.25rem;}

        /* ====== CARDS ====== */
        .card{background:var(--white);border-radius:12px;border:1px solid var(--border);overflow:hidden;}
        .card-header{padding:.85rem 1.1rem;border-bottom:1px solid var(--border);font-weight:700;font-size:.875rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;}
        .card-body{padding:1.1rem;}

        /* ====== STATS ====== */
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:.85rem;margin-bottom:1.25rem;}
        .stat-card{background:var(--white);border-radius:12px;padding:1rem;border:1px solid var(--border);display:flex;align-items:center;gap:.85rem;}
        .stat-icon{width:44px;height:44px;border-radius:10px;display:grid;place-items:center;font-size:1.15rem;flex-shrink:0;}
        .stat-value{font-size:1.6rem;font-weight:800;line-height:1;}
        .stat-label{font-size:.7rem;color:var(--muted);margin-top:2px;}

        /* ====== TABLE ====== */
        .table-wrapper{overflow-x:auto;-webkit-overflow-scrolling:touch;}
        table{width:100%;border-collapse:collapse;font-size:.85rem;}
        th{background:var(--light);padding:.65rem .85rem;text-align:left;font-weight:700;font-size:.7rem;letter-spacing:.4px;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
        td{padding:.65rem .85rem;border-bottom:1px solid var(--border);vertical-align:middle;}
        tr:last-child td{border-bottom:none;}
        tr:hover td{background:var(--light);}

        /* ====== BADGE ====== */
        .badge{display:inline-flex;align-items:center;padding:.22rem .6rem;border-radius:999px;font-size:.68rem;font-weight:700;letter-spacing:.2px;}
        .badge-menunggu{background:#fef3c7;color:#92400e;}
        .badge-diterima{background:#dcfce7;color:#166534;}
        .badge-ditolak{background:#fee2e2;color:#991b1b;}
        .badge-cadangan{background:#e0f2fe;color:#0369a1;}
        .badge-akl{background:#ede9fe;color:#5b21b6;}
        .badge-tjkt{background:#dbeafe;color:#1e40af;}
        .badge-mplb{background:#fce7f3;color:#9d174d;}

        /* ====== BUTTONS ====== */
        .btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem .9rem;border-radius:8px;font-size:.84rem;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.2s;touch-action:manipulation;min-height:36px;}
        .btn:active{transform:scale(.97);}
        .btn-primary{background:var(--blue);color:#fff;}
        .btn-primary:hover{background:var(--navy);}
        .btn-success{background:var(--success);color:#fff;}
        .btn-danger{background:var(--danger);color:#fff;}
        .btn-warning{background:var(--warning);color:#fff;}
        .btn-outline{background:transparent;color:var(--text);border:1.5px solid var(--border);}
        .btn-outline:hover{border-color:var(--blue);color:var(--blue);}
        .btn-sm{padding:.32rem .65rem;font-size:.76rem;min-height:30px;}
        .btn-icon{width:32px;height:32px;padding:0;justify-content:center;border-radius:8px;}

        /* ====== FORM ====== */
        .form-group{margin-bottom:.85rem;}
        .form-label{display:block;font-size:.78rem;font-weight:700;color:var(--text);margin-bottom:.32rem;}
        .form-control{width:100%;padding:.55rem .8rem;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:.875rem;color:var(--text);background:var(--white);transition:border-color .2s;-webkit-appearance:none;min-height:40px;}
        .form-control:focus{outline:none;border-color:var(--blue);}
        select.form-control{cursor:pointer;}
        .form-hint{font-size:.72rem;color:var(--muted);margin-top:.25rem;}
        .is-invalid{border-color:#dc2626!important;}

        /* ====== ALERT ====== */
        .alert{padding:.8rem 1rem;border-radius:10px;margin-bottom:.85rem;font-size:.85rem;display:flex;align-items:flex-start;gap:.6rem;}
        .alert-success{background:#dcfce7;color:#166534;border:1px solid #86efac;}
        .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
        .alert-warning{background:#fef3c7;color:#92400e;border:1px solid #fcd34d;}

        /* ====== MISC ====== */
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1.1rem;}
        .grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.1rem;}
        .flex-between{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.65rem;}
        .text-muted{color:var(--muted);font-size:.78rem;}
        .d-flex{display:flex;}
        .gap-2{gap:.5rem;}

        /* ====== RESPONSIVE ====== */
        @media(max-width:900px){
            .sidebar{transform:translateX(-100%);}
            .sidebar.open{transform:translateX(0);}
            .sidebar-close{display:block;}
            .sidebar-overlay{display:block;}
            .sidebar-overlay.open{opacity:1;pointer-events:all;}
            .main{margin-left:0;}
            .topbar-toggle{display:flex;align-items:center;justify-content:center;}
            .topbar-name{display:block;}
        }
        @media(max-width:640px){
            .stat-grid{grid-template-columns:1fr 1fr;}
            .grid-2{grid-template-columns:1fr;}
            .grid-3{grid-template-columns:1fr;}
            .content{padding:.9rem;}
            /* Paksa grid 2 kolom inline (style="grid-template-columns:1fr 1fr")
               ikut collapse jadi 1 kolom di layar kecil, karena inline style
               punya prioritas lebih tinggi dari class/media query biasa */
            [style*="grid-template-columns:1fr 1fr;"],
            [style*="grid-template-columns: 1fr 1fr;"]{
                grid-template-columns:1fr!important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-logo">

            <div class="sidebar-brand-icon">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK">
            </div>

            <div>
                <h2>PPDB SMK</h2>
                <p>Panel Administrasi</p>
            </div>

        </div>

        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard')?'active':'' }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </div>

        <div class="nav-label">Manajemen</div>
        <div class="nav-item">
            <a href="{{ route('admin.siswa.index') }}" class="{{ request()->routeIs('admin.siswa.*')?'active':'' }}">
                <i class="fas fa-users"></i> Data Siswa
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.jurusan.index') }}" class="{{ request()->routeIs('admin.jurusan.*')?'active':'' }}">
                <i class="fas fa-school"></i> Jurusan
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.periode.index') }}" class="{{ request()->routeIs('admin.periode.*')?'active':'' }}">
                <i class="fas fa-calendar-alt"></i> Periode PPDB
            </a>
        </div>
        @if(Auth::guard('admin')->user()?->isSuperAdmin())
        <div class="nav-item">
            <a href="{{ route('admin.pegawai.index') }}" class="{{ request()->routeIs('admin.pegawai.*')?'active':'' }}">
                <i class="fas fa-user-tie"></i> Pegawai
            </a>
        </div>
        @endif

        <div class="nav-item">
            <a href="{{ route('admin.pembayaran.index') }}" class="{{ request()->routeIs('admin.pembayaran.*')?'active':'' }}">
                <i class="fas fa-receipt"></i> Pembayaran
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.metode-pembayaran.index') }}" class="{{ request()->routeIs('admin.metode-pembayaran.*')?'active':'' }}">
                <i class="fas fa-credit-card"></i> Metode Bayar
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.wali.index') }}" class="{{ request()->routeIs('admin.wali.*')?'active':'' }}">
                <i class="fas fa-users"></i> Wali/Orang Tua
            </a>
        </div>
        <div class="nav-label">Referensi</div>
        <div class="nav-item">
            <a href="{{ route('admin.ref-tipe-relasi.index') }}" class="{{ request()->routeIs('admin.ref-tipe-relasi.*')?'active':'' }}">
                <i class="fas fa-link"></i> Tipe Relasi
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.ref-jenis-alamat.index') }}" class="{{ request()->routeIs('admin.ref-jenis-alamat.*')?'active':'' }}">
                <i class="fas fa-map-marker-alt"></i> Jenis Alamat
            </a>
        </div>

        <div class="nav-label">Sistem</div>
        <div class="nav-item">
            <a href="{{ route('admin.activity-log.index') }}" class="{{ request()->routeIs('admin.activity-log.*')?'active':'' }}">
                <i class="fas fa-history"></i> Log Aktivitas
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.backup.index') }}" class="{{ request()->routeIs('admin.backup.*')?'active':'' }}">
                <i class="fas fa-database"></i> Backup Database
            </a>
        </div>
        <div class="nav-label">Lainnya</div>
        <div class="nav-item">
            <a href="{{ route('home') }}" target="_blank">
                <i class="fas fa-globe"></i> Halaman Publik
            </a>
        </div>
        <div class="nav-label">Akun</div>
        <div class="nav-item">
            <a href="{{ route('admin.profil.index') }}"
            class="{{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i> Profil Saya
            </a>
        </div>
    </nav>
</aside>

<!-- MAIN -->
<div class="main" id="mainContent">
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <span class="topbar-title">@yield('page-title','Dashboard')</span>
        </div>
        <div class="topbar-user">
            <span class="topbar-name">{{ Auth::guard('admin')->user()->nama }}</span>

            <div style="position:relative">
                <div id="avaBtn" onclick="toggleAvaMenu()"
                    class="avatar" title="Profil & Pengaturan"
                    style="overflow:hidden;cursor:pointer">
                    @if(Auth::guard('admin')->user()->foto)
                        <img src="{{ asset('storage/'.Auth::guard('admin')->user()->foto) }}"
                            style="width:100%;height:100%;object-fit:cover">
                    @else
                        {{ strtoupper(substr(Auth::guard('admin')->user()->nama,0,1)) }}
                    @endif
                </div>

                <div id="avaMenu" style="display:none;position:absolute;right:0;top:calc(100% + .5rem);
                    background:#fff;border:1px solid var(--border);border-radius:10px;
                    box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:190px;z-index:500;overflow:hidden">
                    <div style="padding:.75rem 1rem .6rem;border-bottom:1px solid var(--light)">
                        <div style="font-weight:700;font-size:.8rem;color:var(--navy)">
                            {{ Auth::guard('admin')->user()->nama }}
                        </div>
                        <div style="font-size:.7rem;color:var(--muted)">
                            {{ Auth::guard('admin')->user()->jabatan }}
                        </div>
                    </div>
                    <a href="{{ route('admin.profil.index') }}"
                    style="display:flex;align-items:center;gap:.55rem;padding:.6rem 1rem;
                            font-size:.81rem;color:var(--text);text-decoration:none"
                    onmouseover="this.style.background='var(--light)'"
                    onmouseout="this.style.background=''">
                        <i class="fas fa-user-cog" style="width:14px;color:var(--blue)"></i>
                        Profil Saya
                    </a>
                    <div style="border-top:1px solid var(--light)">
                        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0" id="formLogoutAva">
                            @csrf
                            <button type="submit"
                                    style="width:100%;display:flex;align-items:center;gap:.55rem;
                                        padding:.6rem 1rem;font-size:.81rem;color:var(--danger);
                                        background:none;border:none;cursor:pointer;font-family:inherit"
                                    onmouseover="this.style.background='#fef2f2'"
                                    onmouseout="this.style.background=''">
                                <i class="fas fa-sign-out-alt" style="width:14px"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="content">
        @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle" style="flex-shrink:0;margin-top:.1rem"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle" style="flex-shrink:0;margin-top:.1rem"></i> {{ session('error') }}</div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle" style="flex-shrink:0;margin-top:.1rem"></i> {{ session('warning') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<script>
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');
const toggle   = document.getElementById('sidebarToggle');
const closeBtn = document.getElementById('sidebarClose');

function openSidebar(){
    sidebar.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar(){
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
}

toggle?.addEventListener('click', () =>
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
);
closeBtn?.addEventListener('click', closeSidebar);
overlay?.addEventListener('click', closeSidebar);
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeSidebar(); });

function toggleAvaMenu() {
    const m = document.getElementById('avaMenu');
    m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const btn  = document.getElementById('avaBtn');
    const menu = document.getElementById('avaMenu');
    if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});

</script>
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfirmasi logout — muncul di semua tombol keluar (sidebar & dropdown profil)
document.addEventListener('DOMContentLoaded', function () {
    ['formLogoutSidebar', 'formLogoutAva'].forEach(function (id) {
        const form = document.getElementById(id);
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
});
</script>
</body>
</html>