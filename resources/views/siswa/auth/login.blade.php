<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal Siswa — SIPENA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body{
            font-family:'Plus Jakarta Sans',sans-serif;
            min-height:100vh;
            background:#e2e8f0;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:1rem;
        }

        .container{
            width:100%;
            max-width:1200px;
            min-height:700px;
            display:flex;
            overflow:hidden;
            border-radius:24px;
            box-shadow:0 25px 60px rgba(15,39,68,.15);
            background:#fff;
        }

        /* PANEL KIRI */
        .left{
            flex:1;
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
        }

        /* FOTO SEKOLAH */
        .left::before{
            content:'';
            position:absolute;
            inset:0;

            background:
                url('{{ asset("images/sekolah.jpeg") }}')
                center center / cover no-repeat;

            filter:blur(8px);
            transform:scale(1.1);
        }

        /* OVERLAY BIRU */
        .left::after{
            content:'';
            position:absolute;
            inset:0;

            background:
                linear-gradient(
                    135deg,
                    rgba(15,39,68,.88),
                    rgba(26,74,138,.82)
                );
        }

        /* KONTEN HERO */
        .hero{
            position:relative;
            z-index:2;
            text-align:center;
            color:#fff;
            max-width:550px;
            padding:2rem;
        }

        .hero-logo{
            width:130px;
            height:130px;
            object-fit:contain;
            margin-bottom:1.5rem;
        }

        .hero h1{
            font-size:2.5rem;
            font-weight:800;
            margin-bottom:.5rem;
        }

        .hero p{
            font-size:1rem;
            line-height:1.8;
            color:rgba(255,255,255,.9);
        }

        .pills{
            display:flex;
            justify-content:center;
            flex-wrap:wrap;
            gap:.5rem;
            margin-top:1rem;
        }

        .pill{
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.2);
            color:#fff;
            padding:.4rem .9rem;
            border-radius:999px;
            font-size:.75rem;
            font-weight:700;
            backdrop-filter:blur(8px);
        }

        /* PANEL KANAN */
        .right{
            width:440px;
            background:#f4f7fb;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:2rem;
        }

        .card{
            width:100%;
            background:#fff;
            border-radius:18px;
            padding:2rem;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
        }

        .forgot{
            text-align:right;
            margin-top:.75rem;
        }

        .forgot a{
            color:#1a4a8a;
            text-decoration:none;
            font-size:.8rem;
            font-weight:600;
        }

        .divider{
            text-align:center;
            margin:1rem 0;
            color:#94a3b8;
        }

        .link-back{
            display:block;
            text-align:center;
            text-decoration:none;
            color:#1a4a8a;
            font-weight:600;
        }

        .home-link{
            text-align:center;
            margin-top:1rem;
        }

        .home-link a{
            text-decoration:none;
            color:#64748b;
        }

        .remember{
    display:flex;
    align-items:center;
    gap:.5rem;
    font-size:.8rem;
    color:#64748b;
    margin-bottom:.75rem;
}

.forgot{
    text-align:right;
    margin-top:.75rem;
}

.forgot a{
    color:#1a4a8a;
    text-decoration:none;
    font-size:.8rem;
    font-weight:600;
}

.divider{
    text-align:center;
    margin:1rem 0;
    color:#94a3b8;
}

.link-back{
    display:block;
    text-align:center;
    text-decoration:none;
    color:#1a4a8a;
    font-weight:600;
}

.home-link{
    text-align:center;
    margin-top:1rem;
}

.home-link a{
    text-decoration:none;
    color:#64748b;
}

.form-group{
    margin-bottom:1rem;
}

.form-label{
    display:block;
    font-size:.8rem;
    font-weight:700;
    color:#1e293b;
    margin-bottom:.4rem;
}

.input-wrap{
    position:relative;
}

.input-wrap i{
    position:absolute;
    left:.85rem;
    top:50%;
    transform:translateY(-50%);
    color:#94a3b8;
}

.form-control{
    width:100%;
    padding:.75rem .85rem .75rem 2.5rem;
    border:1.5px solid #e2e8f0;
    border-radius:10px;
    font-size:.9rem;
    background:#f8fafc;
    color:#1e293b;
    transition:.2s;
}

.form-control:focus{
    outline:none;
    border-color:#1a4a8a;
    background:#fff;
}

.btn{
    width:100%;
    padding:.8rem;
    background:#0f2744;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:.95rem;
    font-weight:700;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:.5rem;
    transition:.2s;
}

.btn:hover{
    background:#1a4a8a;
}

.info{
    background:#dbeafe;
    border:1px solid #93c5fd;
    color:#1e40af;
    border-radius:10px;
    padding:.75rem 1rem;
    margin-bottom:1rem;
    font-size:.82rem;
}

.err{
    background:#fee2e2;
    border:1px solid #fca5a5;
    color:#991b1b;
    border-radius:10px;
    padding:.75rem 1rem;
    margin-bottom:1rem;
    font-size:.82rem;
}

.card h2{
    font-size:2rem;
    color:#0f2744;
    font-weight:800;
    margin-bottom:.25rem;
}

.card > p{
    color:#64748b;
    margin-bottom:1.5rem;
    line-height:1.6;
}

        @media(max-width:768px){.left{display:none;}.right{width:100%;padding:2rem 1.25rem;}}
    </style>
</head>
<body>

<div class="container">

    <!-- PANEL KIRI -->
    <div class="left">
        <div class="hero">

            <img src="{{ asset('images/logo.png') }}"
                alt="Logo Yadika"
                class="hero-logo">

            <h1>SIPENA</h1>

            <p>
                Sistem Informasi Penerimaan Peserta Didik Baru
                <br>
                SMK YADIKA 8 JATIMULYA
            </p>

            <div class="pills">
                <span class="pill">AKL</span>
                <span class="pill">TJKT</span>
                <span class="pill">MPLB</span>
            </div>

        </div>
    </div>

    <!-- PANEL KANAN -->
    <div class="right">

        <div class="card">

            <h2>Login Siswa</h2>

            @if(session('error'))
                <div class="err">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="err">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="info">
                <i class="fas fa-info-circle"></i>
                Gunakan nomor pendaftaran atau NISN beserta password yang dibuat saat mendaftar.
            </div>

            <form method="POST" action="{{ route('siswa.login.post') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label class="form-label">Nomor Pendaftaran / NISN</label>
                    <div class="input-wrap">
                        <i class="fas fa-id-card"></i>
                        <input type="text"
                               name="login"
                               value="{{ old('login') }}"
                               class="form-control"
                               placeholder="Nomor Pendaftaran atau NISN"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="••••••••"
                               required>
                    </div>
                </div>

                <label class="remember">
                    <input type="checkbox" name="remember">
                    Ingat saya
                </label>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </form>

            <div class="forgot">
                <a href="{{ route('siswa.reset-password') }}">
                    Lupa Password?
                </a>
            </div>

            <div class="divider">atau</div>

            <a href="{{ route('ppdb.cek-status') }}" class="link-back">
                <i class="fas fa-search"></i>
                Cek Status Tanpa Login
            </a>

            <div class="home-link">
                <a href="{{ route('home') }}">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>

        </div>

    </div>

</div>

</body>
</html>
