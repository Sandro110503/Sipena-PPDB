@extends('siswa.layout')
@section('title','Pengaturan Akun')

@push('styles')
<style>
/* ── Page Header ─────────────────────────────────────────── */
.page-title{margin-bottom:1.1rem;}
.page-title h1{font-size:1.2rem;font-weight:800;color:#0f2744;}
.page-title p{color:#64748b;font-size:.82rem;margin-top:.2rem;}

/* ── Tab Bar ─────────────────────────────────────────────── */
.tab-bar{
    display:flex;gap:.3rem;
    background:#f1f5f9;padding:.3rem;
    border-radius:12px;margin-bottom:1.25rem;
    overflow-x:auto;-webkit-overflow-scrolling:touch;
}
.tab-bar::-webkit-scrollbar{display:none;}
.tab-btn{
    flex:1;min-width:80px;padding:.55rem .4rem;
    border:none;background:transparent;border-radius:9px;
    font-family:inherit;font-size:.77rem;font-weight:600;
    color:#64748b;cursor:pointer;transition:.2s;
    display:flex;align-items:center;justify-content:center;gap:.35rem;
    white-space:nowrap;
}
.tab-btn.active{background:#fff;color:#0f2744;box-shadow:0 1px 4px rgba(0,0,0,.1);}

/* ── Form ────────────────────────────────────────────────── */
.form-group{margin-bottom:.9rem;}
.form-label{display:block;font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:.35rem;}
.req{color:#dc2626;}
.form-control{
    width:100%;padding:.62rem .85rem;
    border:1.5px solid #e2e8f0;border-radius:10px;
    font-family:inherit;font-size:.875rem;color:#1e293b;
    background:#f8fafc;transition:border-color .2s,background .2s;
}
.form-control:focus{outline:none;border-color:#1a4a8a;background:#fff;}
.form-control.is-invalid{border-color:#ef4444;}
.invalid-feedback{font-size:.72rem;color:#ef4444;margin-top:.25rem;}
.form-hint{font-size:.72rem;color:#64748b;margin-top:.25rem;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1rem;}
.readonly-field{background:#f1f5f9;color:#64748b;cursor:not-allowed;}

/* ── Buttons ─────────────────────────────────────────────── */
.btn-save{
    background:#0f2744;color:#fff;border:none;border-radius:10px;
    padding:.7rem 1.5rem;font-family:inherit;font-size:.875rem;font-weight:700;
    cursor:pointer;transition:background .2s;
    display:inline-flex;align-items:center;gap:.5rem;
}
.btn-save:hover{background:#1a4a8a;}
.btn-outline{
    background:transparent;color:#1a4a8a;
    border:1.5px solid #1a4a8a;border-radius:10px;
    padding:.65rem 1.2rem;font-family:inherit;font-size:.875rem;font-weight:700;
    cursor:pointer;transition:.2s;
    display:inline-flex;align-items:center;gap:.5rem;text-decoration:none;
}
.btn-outline:hover{background:#eff6ff;}

/* ── Avatar ──────────────────────────────────────────────── */
.avatar-wrap{
    display:flex;align-items:center;gap:1rem;
    margin-bottom:1.25rem;padding-bottom:1.25rem;
    border-bottom:1px solid #f1f5f9;
}
.avatar{width:76px;height:76px;border-radius:14px;object-fit:cover;flex-shrink:0;border:2px solid #e2e8f0;}
.avatar-placeholder{
    width:76px;height:76px;border-radius:14px;
    background:linear-gradient(135deg,#1a4a8a,#0f2744);color:#fff;
    display:grid;place-items:center;
    font-weight:800;font-size:2rem;flex-shrink:0;
}
.avatar-info{flex:1;}
.avatar-info p{font-size:.75rem;color:#64748b;margin-top:.3rem;}

/* ── Password ────────────────────────────────────────────── */
.pw-wrap{position:relative;}
.pw-wrap .form-control{padding-right:2.5rem;}
.pw-toggle{
    position:absolute;right:.75rem;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:#94a3b8;padding:.2rem;font-size:.85rem;
}
.pw-strength-bar{height:4px;border-radius:2px;background:#e2e8f0;margin-top:.4rem;overflow:hidden;}
.pw-strength-fill{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
.pw-strength-label{font-size:.7rem;color:#64748b;margin-top:.25rem;}

/* ── Toggle switch ───────────────────────────────────────── */
.toggle-row{
    display:flex;align-items:center;justify-content:space-between;
    padding:.75rem 0;border-bottom:1px solid #f1f5f9;
}
.toggle-row:last-child{border-bottom:none;}
.toggle-info{flex:1;margin-right:1rem;}
.toggle-info strong{font-size:.85rem;font-weight:700;color:#1e293b;display:block;}
.toggle-info span{font-size:.75rem;color:#64748b;}
.toggle-switch{position:relative;width:42px;height:24px;flex-shrink:0;}
.toggle-switch input{opacity:0;width:0;height:0;}
.toggle-slider{
    position:absolute;cursor:pointer;inset:0;
    background:#e2e8f0;border-radius:999px;transition:.2s;
}
.toggle-slider::before{
    content:'';position:absolute;
    width:18px;height:18px;border-radius:50%;
    left:3px;top:3px;background:#fff;
    box-shadow:0 1px 3px rgba(0,0,0,.2);transition:.2s;
}
.toggle-switch input:checked + .toggle-slider{background:#1a4a8a;}
.toggle-switch input:checked + .toggle-slider::before{transform:translateX(18px);}

/* ── Readonly data box ───────────────────────────────────── */
.data-box{
    background:#f8fafc;border-radius:10px;
    padding:.85rem 1rem;margin-bottom:1rem;border:1px solid #e2e8f0;
}
.data-box .sec-label{
    font-size:.7rem;font-weight:700;color:#64748b;
    text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem;
}
.data-box .data-grid{display:grid;grid-template-columns:1fr 1fr;gap:.5rem .75rem;}
.data-box .data-item span{color:#94a3b8;font-size:.7rem;display:block;}
.data-box .data-item strong{font-weight:600;color:#374151;font-size:.8rem;}

/* ── Info banner ─────────────────────────────────────────── */
.info-banner{
    background:#eff6ff;border:1px solid #bfdbfe;
    border-radius:10px;padding:.8rem 1rem;margin-bottom:1rem;
    font-size:.82rem;color:#1e40af;
    display:flex;align-items:flex-start;gap:.6rem;
}
.info-banner i{flex-shrink:0;margin-top:.1rem;}

/* ── Hapus foto ──────────────────────────────────────────── */
.btn-danger-sm{
    background:#fee2e2;color:#991b1b;border:1.5px solid #fca5a5;
    border-radius:8px;padding:.4rem .85rem;
    font-family:inherit;font-size:.77rem;font-weight:700;
    cursor:pointer;transition:.2s;
    display:inline-flex;align-items:center;gap:.35rem;
}
.btn-danger-sm:hover{background:#fca5a5;}

.forgot {
    text-align: right;
    margin-top: 0.5rem;
    margin-bottom: 1.5rem;
    padding-top: 0.25rem;
}
.forgot a {
    font-size: 0.85rem;
    color: #C9A227;
    text-decoration: none;
    font-weight: 500;
    transition: color .15s;
}
.forgot a:hover {
    color: #1B2A4A;
    text-decoration: underline;
}
.forgot a i {
    margin-right: 4px;
    font-size: 0.8rem;
}

/* ── Responsive ──────────────────────────────────────────── */
@media(max-width:600px){
    .form-grid-2{grid-template-columns:1fr;}
    .tab-btn i + span{display:none;}
    .data-box .data-grid{grid-template-columns:1fr;}
}
</style>
@endpush

@section('content')
<div class="page-title">
    <h1><i class="fas fa-cog" style="color:#1a4a8a;font-size:1rem"></i> Pengaturan Akun</h1>
    <p>Kelola data diri, alamat, keamanan, dan preferensi notifikasi Anda.</p>
</div>

{{-- ── Tab Bar ────────────────────────────────────────────── --}}
<div class="tab-bar" id="tabBar" role="tablist">
    <button class="tab-btn {{ !in_array(session('tab'),['alamat','password','notifikasi']) ? 'active' : '' }}"
            onclick="switchTab('profil')" id="tab-profil">
        <i class="fas fa-user"></i><span>Data Diri</span>
    </button>
    <button class="tab-btn {{ session('tab')==='alamat' ? 'active' : '' }}"
            onclick="switchTab('alamat')" id="tab-alamat">
        <i class="fas fa-map-marker-alt"></i><span>Alamat</span>
    </button>
    <button class="tab-btn {{ session('tab')==='password' ? 'active' : '' }}"
            onclick="switchTab('password')" id="tab-password">
        <i class="fas fa-lock"></i><span>Password</span>
    </button>
    <button class="tab-btn {{ session('tab')==='notifikasi' ? 'active' : '' }}"
            onclick="switchTab('notifikasi')" id="tab-notifikasi">
        <i class="fas fa-bell"></i><span>Notifikasi</span>
    </button>
</div>

{{-- ══════════════════════════════════════
     TAB 1: DATA DIRI
══════════════════════════════════════ --}}
<div id="pane-profil" class="{{ in_array(session('tab'),['alamat','password','notifikasi']) ? 'hidden' : '' }}">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-user"></i> Data Diri</span>
        </div>
        <div class="card-body">
            <div class="avatar-wrap">
                @if($siswa->foto)
                    <img src="{{ Storage::url($siswa->foto) }}" class="avatar" alt="Foto" id="avatar-img">
                @else
                    <div class="avatar-placeholder" id="avatar-placeholder">
                        {{ strtoupper(substr($siswa->nama_depan,0,1)) }}
                    </div>
                @endif
                <div class="avatar-info">
                    <label class="form-label" style="margin-bottom:.35rem">Foto Profil</label>

                    {{-- Form khusus upload foto --}}
                    <form method="POST" action="{{ route('siswa.pengaturan.profil') }}" enctype="multipart/form-data" id="form-foto">
                        @csrf @method('PUT')
                        <input type="file" name="foto" id="foto-input"
                            class="form-control @error('foto') is-invalid @enderror"
                            accept="image/jpg,image/jpeg,image/png"
                            style="padding:.4rem .75rem;font-size:.8rem"
                            onchange="previewFoto(this); this.form.submit();">
                        <p>JPG/PNG, maks. 2MB</p>
                        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </form>

                    @if($siswa->foto)
                    <form method="POST" action="{{ route('siswa.pengaturan.hapus-foto') }}" style="margin-top:.5rem"
                        onsubmit="return confirm('Hapus foto profil?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger-sm">
                            <i class="fas fa-trash"></i> Hapus Foto
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        
            <form method="POST" action="{{ route('siswa.pengaturan.profil') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Nama --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Depan <span class="req">*</span></label>
                        <input type="text" name="nama_depan"
                               value="{{ old('nama_depan',$siswa->nama_depan) }}"
                               class="form-control @error('nama_depan') is-invalid @enderror" required>
                        @error('nama_depan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Tengah</label>
                        <input type="text" name="nama_tengah"
                               value="{{ old('nama_tengah',$siswa->nama_tengah) }}"
                               class="form-control" placeholder="(opsional)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="nama_belakang"
                               value="{{ old('nama_belakang',$siswa->nama_belakang) }}"
                               class="form-control" placeholder="(opsional)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <input type="text"
                               value="{{ $siswa->jenis_kelamin==='L'?'Laki-laki':'Perempuan' }}"
                               class="form-control readonly-field" readonly>
                    </div>
                </div>

                {{-- Kontak --}}
                <div class="form-grid-2" style="margin-top:.1rem">
                    <div class="form-group">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email"
                               value="{{ old('email',$siswa->email) }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        <div class="form-hint">Digunakan untuk notifikasi penting.</div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP / WhatsApp <span class="req">*</span></label>
                        <input type="text" name="nomor_hp"
                               value="{{ old('nomor_hp',$siswa->nomor_hp) }}"
                               class="form-control @error('nomor_hp') is-invalid @enderror"
                               inputmode="tel" placeholder="08xx-xxxx-xxxx" required>
                        @error('nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span class="req">*</span></label>
                        <input type="text" name="tempat_lahir"
                               value="{{ old('tempat_lahir',$siswa->tempat_lahir) }}"
                               class="form-control @error('tempat_lahir') is-invalid @enderror" required>
                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span class="req">*</span></label>
                        <input type="date" name="tanggal_lahir"
                               value="{{ old('tanggal_lahir',$siswa->tanggal_lahir?->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Data akademik readonly --}}
                <div class="data-box">
                    <div class="sec-label">Data Akademik (tidak dapat diubah)</div>
                    <div class="data-grid">
                        @foreach([
                            'No. Pendaftaran' => $siswa->nomor_pendaftaran,
                            'NISN'            => $siswa->nisn,
                            'Asal Sekolah'    => $siswa->asal_sekolah,
                            'Tahun Lulus'     => $siswa->tahun_lulus,
                        ] as $lbl => $val)
                        <div class="data-item">
                            <span>{{ $lbl }}</span>
                            <strong>{{ $val ?? '-' }}</strong>
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

{{-- ══════════════════════════════════════
     TAB 2: ALAMAT
══════════════════════════════════════ --}}
<div id="pane-alamat" class="{{ session('tab')==='alamat' ? '' : 'hidden' }}">
    @php $alamat = $siswa->alamatCalonSiswa->first()?->alamat; @endphp
    <div class="card">
        <div class="card-header"><i class="fas fa-map-marker-alt"></i> Alamat Tempat Tinggal</div>
        <div class="card-body">
            <form method="POST" action="{{ route('siswa.pengaturan.alamat') }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Jenis Tempat Tinggal <span class="req">*</span></label>
                    <select name="jenis_tempat_tinggal"
                            class="form-control @error('jenis_tempat_tinggal') is-invalid @enderror" required>
                        <option value="">— Pilih —</option>
                        @foreach(['Rumah Orang Tua/Wali','Sewa/Kost'] as $opt)
                        <option value="{{ $opt }}"
                            {{ old('jenis_tempat_tinggal',$alamat?->jenis_tempat_tinggal)===$opt?'selected':'' }}>
                            {{ $opt }}
                        </option>
                        @endforeach
                    </select>
                    @error('jenis_tempat_tinggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Jalan / Alamat Lengkap <span class="req">*</span></label>
                    <input type="text" name="nama_jalan"
                           value="{{ old('nama_jalan',$alamat?->nama_jalan) }}"
                           class="form-control @error('nama_jalan') is-invalid @enderror"
                           placeholder="Contoh: Jl. Merdeka No. 12 RT 03/RW 05" required>
                    @error('nama_jalan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Kelurahan / Desa</label>
                        <input type="text" name="kelurahan"
                               value="{{ old('kelurahan',$alamat?->kelurahan) }}"
                               class="form-control" placeholder="Kelurahan / desa">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan"
                               value="{{ old('kecamatan',$alamat?->kecamatan) }}"
                               class="form-control" placeholder="Kecamatan">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kabupaten / Kota <span class="req">*</span></label>
                        <input type="text" name="kabupaten_kota"
                               value="{{ old('kabupaten_kota',$alamat?->kabupaten_kota) }}"
                               class="form-control @error('kabupaten_kota') is-invalid @enderror"
                               placeholder="Kab. / Kota" required>
                        @error('kabupaten_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Provinsi <span class="req">*</span></label>
                        <input type="text" name="provinsi"
                               value="{{ old('provinsi',$alamat?->provinsi) }}"
                               class="form-control @error('provinsi') is-invalid @enderror"
                               placeholder="Provinsi" required>
                        @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="kode_pos"
                               value="{{ old('kode_pos',$alamat?->kode_pos) }}"
                               class="form-control" placeholder="Kode pos"
                               maxlength="10" inputmode="numeric">
                    </div>
                </div>

                <div style="display:flex;gap:.65rem;flex-wrap:wrap;margin-top:.25rem">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Alamat
                    </button>
                    <a href="{{ route('siswa.dashboard') }}" class="btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     TAB 3: PASSWORD
══════════════════════════════════════ --}}
<div id="pane-password" class="{{ session('tab')==='password' ? '' : 'hidden' }}">
    <div class="card">
        <div class="card-header"><i class="fas fa-lock"></i> Ganti Password</div>
        <div class="card-body" style="max-width:440px">
            <div class="info-banner">
                <i class="fas fa-shield-alt"></i>
                Password minimal 8 karakter. Kombinasikan huruf besar, huruf kecil, dan angka agar lebih aman.
            </div>
            <form method="POST" action="{{ route('siswa.pengaturan.password') }}">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label">Password Saat Ini <span class="req">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_lama" id="pw0"
                            class="form-control @error('password_lama') is-invalid @enderror"
                            placeholder="Password yang sekarang" required autocomplete="current-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('pw0',this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="forgot">
                    <a href="{{ route('siswa.reset-password') }}">
                        <i class="fas fa-question-circle"></i> Lupa Password?
                    </a>
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru <span class="req">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_baru" id="pw1"
                               class="form-control @error('password_baru') is-invalid @enderror"
                               placeholder="Minimal 8 karakter" required
                               oninput="checkStrength(this.value)"
                               autocomplete="new-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('pw1',this)" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-fill"></div></div>
                    <div class="pw-strength-label" id="pw-label"></div>
                    @error('password_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom:1.25rem">
                    <label class="form-label">Konfirmasi Password Baru <span class="req">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_baru_confirmation" id="pw2"
                               class="form-control"
                               placeholder="Ulangi password baru" required autocomplete="new-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('pw2',this)" tabindex="-1">
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

{{-- ══════════════════════════════════════
     TAB 4: NOTIFIKASI
══════════════════════════════════════ --}}
<div id="pane-notifikasi" class="{{ session('tab')==='notifikasi' ? '' : 'hidden' }}">
    <div class="card">
        <div class="card-header"><i class="fas fa-bell"></i> Preferensi Notifikasi</div>
        <div class="card-body">
            <p style="font-size:.82rem;color:#64748b;margin-bottom:1rem">
                Notifikasi dikirim ke email <strong>{{ $siswa->email }}</strong>.
            </p>
            <form method="POST" action="{{ route('siswa.pengaturan.notifikasi') }}">
                @csrf @method('PATCH')

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>Status Pendaftaran</strong>
                        <span>Notifikasi saat status penerimaan berubah (diterima / ditolak / cadangan).</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_status" value="1"
                               {{ ($notifPrefs['notif_status']??true)?'checked':'' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>Konfirmasi Pembayaran</strong>
                        <span>Notifikasi saat bukti pembayaran diverifikasi atau ditolak.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_pembayaran" value="1"
                               {{ ($notifPrefs['notif_pembayaran']??true)?'checked':'' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>Pengingat Dokumen</strong>
                        <span>Notifikasi bila ada dokumen persyaratan yang belum lengkap.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_dokumen" value="1"
                               {{ ($notifPrefs['notif_dokumen']??false)?'checked':'' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>Pengumuman Sekolah</strong>
                        <span>Informasi umum dari panitia PPDB (jadwal, perubahan, dll).</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="notif_pengumuman" value="1"
                               {{ ($notifPrefs['notif_pengumuman']??true)?'checked':'' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="margin-top:1rem">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Preferensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Akun --}}
    <div class="card">
        <div class="card-header"><i class="fas fa-info-circle"></i> Info Akun</div>
        <div class="card-body">
            <div class="data-box" style="margin-bottom:0">
                <div class="sec-label">Ringkasan Akun</div>
                <div class="data-grid">
                    <div class="data-item">
                        <span>Terdaftar sejak</span>
                        <strong>{{ $siswa->tanggal_daftar?->format('d M Y') ?? '-' }}</strong>
                    </div>
                    <div class="data-item">
                        <span>Status Penerimaan</span>
                        <strong>{{ $siswa->status_penerimaan ?? 'Menunggu' }}</strong>
                    </div>
                    <div class="data-item">
                        <span>Email Aktif</span>
                        <strong style="word-break:break-all">{{ $siswa->email }}</strong>
                    </div>
                    <div class="data-item">
                        <span>Nomor HP</span>
                        <strong>{{ $siswa->nomor_hp ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>.hidden{display:none;}</style>
<script>
/* ── Tab ───────────────────────────────────────── */
function switchTab(tab) {
    ['profil','alamat','password','notifikasi'].forEach(t => {
        document.getElementById('pane-' + t).classList.toggle('hidden', t !== tab);
        document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    });
    const url = new URL(location.href);
    url.searchParams.set('tab', tab);
    history.replaceState(null, '', url.toString());
}

/* ── Password visibility ───────────────────────── */
function togglePw(id, btn) {
    const el = document.getElementById(id);
    const show = el.type === 'password';
    el.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}

/* ── Password strength ─────────────────────────── */
function checkStrength(v) {
    const fill  = document.getElementById('pw-fill');
    const label = document.getElementById('pw-label');
    let s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const map = [
        {w:'0%',  bg:'#e2e8f0',txt:''},
        {w:'25%', bg:'#ef4444',txt:'Lemah'},
        {w:'50%', bg:'#f97316',txt:'Cukup'},
        {w:'75%', bg:'#eab308',txt:'Baik'},
        {w:'100%',bg:'#22c55e',txt:'Kuat'},
    ];
    const c = map[s] || map[0];
    fill.style.width = c.w;
    fill.style.background = c.bg;
    label.textContent = c.txt;
    label.style.color = c.bg;
}

/* ── Foto preview ──────────────────────────────── */
function previewFoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        let img = document.getElementById('avatar-img');
        const ph = document.getElementById('avatar-placeholder');
        if (!img) {
            img = document.createElement('img');
            img.className = 'avatar';
            img.alt = 'Foto';
            img.id = 'avatar-img';
            if (ph) ph.replaceWith(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── Init dari URL ?tab= ───────────────────────── */
(function() {
    const t = new URLSearchParams(location.search).get('tab');
    if (t && ['profil','alamat','password','notifikasi'].includes(t)) switchTab(t);
})();
</script>
@endpush