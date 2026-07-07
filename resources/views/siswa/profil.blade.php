@extends('siswa.layout')
@section('title','Profil Saya')

@push('styles')
<style>
.page-title { margin-bottom: 1.1rem; }
.page-title h1 { font-size: 1.2rem; font-weight: 800; color: #0f2744; }
.page-title p  { color: #64748b; font-size: .82rem; margin-top: .2rem; }

/* Tab */
.tab-bar { display: flex; gap: .35rem; background: #f1f5f9; padding: .3rem; border-radius: 12px; margin-bottom: 1.25rem; }
.tab-btn { flex: 1; padding: .55rem .5rem; border: none; background: transparent; border-radius: 9px; font-family: inherit; font-size: .8rem; font-weight: 600; color: #64748b; cursor: pointer; transition: .2s; display: flex; align-items: center; justify-content: center; gap: .4rem; }
.tab-btn.active { background: #fff; color: #0f2744; box-shadow: 0 1px 4px rgba(0,0,0,.1); }

/* Form */
.form-group { margin-bottom: .9rem; }
.form-label { display: block; font-size: .78rem; font-weight: 700; color: #1e293b; margin-bottom: .35rem; }
.form-control { width: 100%; padding: .62rem .85rem; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: .875rem; color: #1e293b; background: #f8fafc; transition: border-color .2s, background .2s; }
.form-control:focus { outline: none; border-color: #1a4a8a; background: #fff; }
.form-control.is-invalid { border-color: #ef4444; }
.invalid-feedback { font-size: .72rem; color: #ef4444; margin-top: .25rem; }
.form-hint { font-size: .72rem; color: #64748b; margin-top: .25rem; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem 1rem; }
.btn-save { background: #0f2744; color: #fff; border: none; border-radius: 10px; padding: .7rem 1.5rem; font-family: inherit; font-size: .875rem; font-weight: 700; cursor: pointer; transition: background .2s; display: inline-flex; align-items: center; gap: .5rem; }
.btn-save:hover { background: #1a4a8a; }
.btn-outline { background: transparent; color: #1a4a8a; border: 1.5px solid #1a4a8a; border-radius: 10px; padding: .65rem 1.2rem; font-family: inherit; font-size: .875rem; font-weight: 700; cursor: pointer; transition: .2s; display: inline-flex; align-items: center; gap: .5rem; text-decoration: none; }
.btn-outline:hover { background: #eff6ff; }

/* Avatar upload */
.avatar-wrap { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; }
.avatar { width: 72px; height: 72px; border-radius: 14px; object-fit: cover; flex-shrink: 0; }
.avatar-placeholder { width: 72px; height: 72px; border-radius: 14px; background: #1a4a8a; color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 1.8rem; flex-shrink: 0; }
.avatar-info { flex: 1; }
.avatar-info p { font-size: .75rem; color: #64748b; margin-top: .3rem; }

/* Password toggle */
.pw-wrap { position: relative; }
.pw-wrap .form-control { padding-right: 2.5rem; }
.pw-toggle { position: absolute; right: .75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: .2rem; font-size: .85rem; }

/* Readonly badge */
.readonly-field { background: #f1f5f9; color: #64748b; cursor: not-allowed; }

@media(max-width:600px) {
    .form-grid-2 { grid-template-columns: 1fr; }
    .tab-btn span { display: none; }
}
</style>
@endpush

@section('content')
<div class="page-title">
    <h1>Profil Saya</h1>
    <p>Perbarui informasi pribadi dan keamanan akun Anda.</p>
</div>

{{-- Tab navigation --}}
<div class="tab-bar" id="tabBar">
    <button class="tab-btn {{ session('tab') !== 'password' ? 'active' : '' }}"
            onclick="switchTab('profil')" id="tab-profil">
        <i class="fas fa-user"></i> <span>Data Diri</span>
    </button>
    <button class="tab-btn {{ session('tab') === 'password' ? 'active' : '' }}"
            onclick="switchTab('password')" id="tab-password">
        <i class="fas fa-lock"></i> <span>Ganti Password</span>
    </button>
</div>

{{-- ===== TAB: DATA DIRI ===== --}}
<div id="pane-profil" class="{{ session('tab') === 'password' ? 'hidden' : '' }}">
    <div class="card">
        <div class="card-header"><i class="fas fa-user"></i> Data Diri</div>
        <div class="card-body">
            <form method="POST" action="{{ route('siswa.profil.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Avatar --}}
                <div class="avatar-wrap">
                    @if($siswa->foto)
                    <img src="{{ Storage::url($siswa->foto) }}" class="avatar" alt="Foto">
                    @else
                    <div class="avatar-placeholder">{{ strtoupper(substr($siswa->nama_depan,0,1)) }}</div>
                    @endif
                    <div class="avatar-info">
                        <label class="form-label" style="margin-bottom:.35rem">Foto Profil</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                            accept="image/jpg,image/jpeg,image/png"
                            style="padding:.4rem .75rem;font-size:.8rem">
                        <p>JPG/PNG, maks. 2MB</p>
                        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Nama --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Depan <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nama_depan" value="{{ old('nama_depan', $siswa->nama_depan) }}"
                            class="form-control @error('nama_depan') is-invalid @enderror" required>
                        @error('nama_depan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Tengah</label>
                        <input type="text" name="nama_tengah" value="{{ old('nama_tengah', $siswa->nama_tengah) }}"
                            class="form-control" placeholder="(opsional)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="nama_belakang" value="{{ old('nama_belakang', $siswa->nama_belakang) }}"
                            class="form-control" placeholder="(opsional)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <input type="text" value="{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}"
                            class="form-control readonly-field" readonly>
                        <div class="form-hint">Tidak dapat diubah.</div>
                    </div>
                </div>

                {{-- Kontak --}}
                <div class="form-grid-2" style="margin-top:.25rem">
                    <div class="form-group">
                        <label class="form-label">Email <span style="color:#dc2626">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $siswa->email) }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $siswa->nomor_hp) }}"
                            class="form-control @error('nomor_hp') is-invalid @enderror"
                            inputmode="tel" required>
                        @error('nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span style="color:#dc2626">*</span></label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                            class="form-control @error('tempat_lahir') is-invalid @enderror" required>
                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span style="color:#dc2626">*</span></label>
                        <input type="date" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d')) }}"
                            class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Data akademik (readonly) --}}
                <div style="background:#f8fafc;border-radius:10px;padding:.85rem 1rem;margin-top:.5rem;margin-bottom:1.1rem;border:1px solid #e2e8f0">
                    <div style="font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem">Data Akademik (tidak dapat diubah)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem .75rem">
                        @foreach(['No. Pendaftaran'=>$siswa->nomor_pendaftaran,'NISN'=>$siswa->nisn,'Asal Sekolah'=>$siswa->asal_sekolah,'Tahun Lulus'=>$siswa->tahun_lulus] as $lbl=>$val)
                        <div style="font-size:.8rem">
                            <span style="color:#94a3b8;font-size:.7rem;display:block">{{ $lbl }}</span>
                            <span style="font-weight:600;color:#374151">{{ $val ?? '-' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('siswa.dashboard') }}" class="btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== TAB: GANTI PASSWORD ===== --}}
<div id="pane-password" class="{{ session('tab') === 'password' ? '' : 'hidden' }}">
    <div class="card">
        <div class="card-header"><i class="fas fa-lock"></i> Ganti Password</div>
        <div class="card-body" style="max-width:420px">
            <form method="POST" action="{{ route('siswa.ganti-password') }}">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label">Password Saat Ini <span style="color:#dc2626">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_lama" id="pw0"
                            class="form-control @error('password_lama') is-invalid @enderror"
                            placeholder="Password yang sekarang" required>
                        <button type="button" class="pw-toggle" onclick="togglePw('pw0',this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru <span style="color:#dc2626">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_baru" id="pw1"
                            class="form-control @error('password_baru') is-invalid @enderror"
                            placeholder="Minimal 8 karakter" required>
                        <button type="button" class="pw-toggle" onclick="togglePw('pw1',this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom:1.25rem">
                    <label class="form-label">Konfirmasi Password Baru <span style="color:#dc2626">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_baru_confirmation" id="pw2"
                            class="form-control"
                            placeholder="Ulangi password baru" required>
                        <button type="button" class="pw-toggle" onclick="togglePw('pw2',this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-key"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>.hidden { display: none; }</style>
<script>
function switchTab(tab) {
    ['profil','password'].forEach(t => {
        document.getElementById('pane-' + t).classList.toggle('hidden', t !== tab);
        document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    });
}
function togglePw(id, btn) {
    const el = document.getElementById(id);
    const show = el.type === 'password';
    el.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}
</script>
@endpush
