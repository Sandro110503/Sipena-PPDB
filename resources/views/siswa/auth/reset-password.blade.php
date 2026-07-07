<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SIPENA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; background: #f4f7fb; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .wrap { width: 100%; max-width: 440px; }
        .logo { text-align: center; margin-bottom: 1.75rem; }
        .logo-icon { width: 64px; height: 64px; background: linear-gradient(135deg,#0f2744,#1a4a8a); border-radius: 16px; display: grid; place-items: center; font-size: 1.75rem; color: #e8a020; margin: 0 auto .75rem; box-shadow: 0 8px 24px rgba(15,39,68,.25); }
        .logo h1 { font-size: 1.3rem; font-weight: 800; color: #0f2744; }
        .logo p  { font-size: .8rem; color: #64748b; margin-top: .2rem; }
        .card { background: #fff; border-radius: 18px; padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0; }
        .card h2 { font-size: 1.05rem; font-weight: 800; color: #0f2744; margin-bottom: .25rem; }
        .card p  { font-size: .82rem; color: #64748b; margin-bottom: 1.5rem; }
        .step-hint { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: .75rem 1rem; font-size: .8rem; color: #1e40af; margin-bottom: 1.25rem; display: flex; gap: .6rem; align-items: flex-start; }
        .divider-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin: 1.25rem 0 .75rem; display: flex; align-items: center; gap: .5rem; }
        .divider-label::before, .divider-label::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
        .form-group { margin-bottom: .9rem; }
        .form-label { display: block; font-size: .78rem; font-weight: 700; color: #1e293b; margin-bottom: .35rem; }
        .input-wrap { position: relative; }
        .input-wrap i.icon { position: absolute; left: .85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .85rem; pointer-events: none; }
        .form-control { width: 100%; padding: .65rem .85rem .65rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: .9rem; color: #1e293b; background: #f8fafc; transition: border-color .2s; }
        .form-control:focus { outline: none; border-color: #1a4a8a; background: #fff; }
        .form-control.is-invalid { border-color: #ef4444; }
        .invalid-feedback { font-size: .72rem; color: #ef4444; margin-top: .25rem; }
        .toggle-pw { position: absolute; right: .75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: .25rem; font-size: .85rem; }
        .btn { width: 100%; padding: .75rem; background: #0f2744; color: #fff; border: none; border-radius: 10px; font-family: inherit; font-size: .95rem; font-weight: 700; cursor: pointer; transition: background .2s; display: flex; align-items: center; justify-content: center; gap: .5rem; }
        .btn:hover { background: #1a4a8a; }
        .err { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 10px; padding: .7rem 1rem; font-size: .82rem; margin-bottom: 1rem; display: flex; align-items: flex-start; gap: .5rem; }
        .suc { background: #dcfce7; color: #166534; border: 1px solid #86efac; border-radius: 10px; padding: .7rem 1rem; font-size: .82rem; margin-bottom: 1rem; display: flex; align-items: flex-start; gap: .5rem; }
        .link-back { display: block; text-align: center; font-size: .82rem; color: #1a4a8a; font-weight: 600; text-decoration: none; margin-top: 1.1rem; }
        .link-back:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="logo">
        <div class="logo-icon"><i class="fas fa-key"></i></div>
        <h1>Reset Password</h1>
        <p>Atur ulang password akun portal siswa Anda</p>
    </div>

    <div class="card">
        <h2>Verifikasi Identitas</h2>
        <p>Masukkan data berikut untuk memastikan Anda adalah pemilik akun.</p>

        @if(session('success'))
        <div class="suc"><i class="fas fa-check-circle" style="flex-shrink:0;margin-top:1px"></i> {{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="err"><i class="fas fa-exclamation-circle" style="flex-shrink:0;margin-top:1px"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
        @endif

        <div class="step-hint">
            <i class="fas fa-shield-alt" style="margin-top:1px;flex-shrink:0"></i>
            <div>Isi <strong>NISN</strong>, <strong>Nomor Pendaftaran</strong>, dan <strong>Tanggal Lahir</strong> yang sesuai dengan data pendaftaran Anda.</div>
        </div>

        <form method="POST" action="{{ route('siswa.reset-password.proses') }}">
            @csrf

            {{-- Verifikasi Identitas --}}
            <div class="form-group">
                <label class="form-label">NISN</label>
                <div class="input-wrap">
                    <i class="fas fa-id-badge icon"></i>
                    <input type="text" name="nisn" value="{{ old('nisn') }}"
                        class="form-control @error('nisn') is-invalid @enderror"
                        placeholder="10 digit NISN" maxlength="10" inputmode="numeric" required>
                </div>
                @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Pendaftaran</label>
                <div class="input-wrap">
                    <i class="fas fa-hashtag icon"></i>
                    <input type="text" name="nomor_pendaftaran" value="{{ old('nomor_pendaftaran') }}"
                        class="form-control @error('nomor_pendaftaran') is-invalid @enderror"
                        placeholder="Contoh: 01062026001" required>
                </div>
                @error('nomor_pendaftaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <div class="input-wrap">
                    <i class="fas fa-calendar icon"></i>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                        class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                </div>
                @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="divider-label">Password Baru</div>

            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <div class="input-wrap">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password_baru" id="pw1"
                        class="form-control @error('password_baru') is-invalid @enderror"
                        placeholder="Minimal 8 karakter" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw1',this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group" style="margin-bottom:1.25rem">
                <label class="form-label">Konfirmasi Password Baru</label>
                <div class="input-wrap">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password_baru_confirmation" id="pw2"
                        class="form-control"
                        placeholder="Ulangi password baru" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw2',this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-key"></i> Reset Password
            </button>
        </form>
    </div>

    <a href="{{ route('siswa.login') }}" class="link-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Login
    </a>
</div>

<script>
function togglePw(id, btn) {
    const el = document.getElementById(id);
    const isHidden = el.type === 'password';
    el.type = isHidden ? 'text' : 'password';
    btn.querySelector('i').className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
}
</script>
</body>
</html>
