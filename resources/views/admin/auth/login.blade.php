<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SMK YADIKA 8 JATIMULYA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Poppins,sans-serif;
    background:#eef3fa;
}

.login-wrapper{
    display:flex;
    min-height:100vh;
}

/*======================
LEFT
======================*/

.left{
    flex:1;
    position:relative;
    overflow:hidden;

    display:flex;
    justify-content:center;
    align-items:center;
}

.left::before{
    content:"";
    position:absolute;
    inset:0;

    background:url('{{ asset("images/sekolah.jpeg") }}')
    center/cover no-repeat;

    filter:blur(7px);
    transform:scale(1.08);
}

.left::after{
    content:"";
    position:absolute;
    inset:0;

    background:linear-gradient(
        135deg,
        rgba(12,39,72,.85),
        rgba(21,64,120,.82)
    );
}

.hero{
    position:relative;
    z-index:2;

    text-align:center;
    color:white;

    width:100%;
    max-width:560px;

    padding:40px;
}

.hero-logo{
    width:120px;
    margin-bottom:25px;

    filter:drop-shadow(0 12px 25px rgba(0,0,0,.35));
}

.hero h1{
    font-size:62px;
    font-weight:800;
    margin-bottom:12px;
}

.hero p{
    line-height:1.8;
    font-size:20px;
    margin-bottom:30px;
}

.pills{
    display:flex;
    justify-content:center;
    gap:12px;
}

.pill{
    padding:10px 22px;

    border-radius:40px;

    background:rgba(255,255,255,.12);

    border:1px solid rgba(255,255,255,.25);

    backdrop-filter:blur(8px);

    font-weight:600;
}

/*======================
RIGHT
======================*/

.right{

    width:470px;

    background:#f8fafc;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:40px;
}

.login-box{

    width:100%;

    background:white;

    border-radius:22px;

    padding:35px;

    box-shadow:0 20px 60px rgba(0,0,0,.08);
}

.nip-badge{

    display:flex;

    align-items:center;

    gap:10px;

    padding:15px;

    background:#eaf2ff;

    color:#1d4ed8;

    border-radius:12px;

    margin-bottom:25px;

    font-weight:600;
}

.form-group{
    margin-bottom:20px;
}

.form-label{

    display:block;

    margin-bottom:8px;

    font-weight:600;
}

.input-wrap{

    position:relative;
}

.input-wrap i{

    position:absolute;

    top:50%;

    left:15px;

    transform:translateY(-50%);

    color:#94a3b8;
}

.form-control{

    width:100%;

    padding:14px 15px 14px 45px;

    border-radius:12px;

    border:1px solid #dbe2ea;

    outline:none;

    transition:.3s;
}

.form-control:focus{

    border-color:#2563eb;
}

.remember{

    display:flex;

    gap:8px;

    margin:18px 0;

    color:#64748b;
}

.btn{

    width:100%;

    border:none;

    border-radius:12px;

    padding:15px;

    background:#0f2c59;

    color:white;

    font-weight:600;

    cursor:pointer;

    transition:.3s;
}

.btn:hover{

    background:#1d4ed8;
}

.err-msg{

    background:#fee2e2;

    color:#991b1b;

    border-radius:10px;

    padding:15px;

    margin-bottom:20px;
}

@media(max-width:900px){

    .left{

        display:none;
    }

    .right{

        width:100%;
    }

}
    </style>
</head>
<body>
<div class="login-wrapper">

    <!-- LEFT -->
    <div class="left">

        <div class="hero">

            <img src="{{ asset('images/logo.png') }}"
                class="hero-logo"
                alt="Logo">

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

    <!-- RIGHT -->
    <div class="right">

        <div class="login-box">

            <div class="nip-badge">
                <i class="fas fa-id-badge"></i>
                Login menggunakan NIP dan Password
            </div>

            @if($errors->any())
            <div class="err-msg">
                <i class="fas fa-exclamation-circle"></i>

                <div>
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="form-group">

                    <label class="form-label">
                        NIP
                    </label>

                    <div class="input-wrap">

                        <i class="fas fa-id-card"></i>

                        <input
                            type="text"
                            class="form-control"
                            name="nip"
                            value="{{ old('nip') }}"
                            placeholder="Masukkan NIP">

                    </div>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Password
                    </label>

                    <div class="input-wrap">

                        <i class="fas fa-lock"></i>

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            placeholder="••••••••">

                    </div>

                </div>

                <label class="remember">
                    <input type="checkbox" name="remember">
                    Ingat saya
                </label>

                <button class="btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Dashboard
                </button>

            </form>

        </div>

    </div>

</div>
</body>
</html>
