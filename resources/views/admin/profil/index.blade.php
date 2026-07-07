@extends('layouts.admin')
@section('title', 'Profil Saya')
@section('page-title', 'Profil & Pengaturan')

@push('styles')
<style>
/* ── Profile Hero ─────────────────────────────── */
.profil-hero {
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
    border-radius: 14px;
    padding: 1.75rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    color: #fff;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.profil-hero::after {
    content: '';
    position: absolute;
    right: -50px; top: -50px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(232,160,32,.1);
    pointer-events: none;
}
.profil-ava-wrap { position: relative; flex-shrink: 0; }
.profil-ava {
    width: 84px; height: 84px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.25);
    background: var(--blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: #fff;
    overflow: hidden; flex-shrink: 0;
}
.profil-ava img { width: 100%; height: 100%; object-fit: cover; }
.profil-ava-edit {
    position: absolute; bottom: 0; right: 0;
    width: 26px; height: 26px; border-radius: 50%;
    background: var(--accent); color: var(--navy);
    border: 2px solid #fff;
    display: grid; place-items: center;
    font-size: .65rem; cursor: pointer;
    transition: .2s;
}
.profil-ava-edit:hover { transform: scale(1.1); background: #f5c04a; }
.profil-hero-info h2 { font-size: 1.2rem; font-weight: 800; }
.profil-hero-info p  { font-size: .78rem; opacity: .65; margin: .15rem 0 0; }
.profil-hero-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    margin-top: .45rem;
    background: rgba(232,160,32,.2); border: 1px solid rgba(232,160,32,.35);
    color: #f5d080; font-size: .65rem; font-weight: 700;
    padding: .2rem .6rem; border-radius: 999px; letter-spacing: .5px;
}

/* ── Tab Nav ──────────────────────────────────── */
.tab-nav {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--border);
    margin-bottom: 1.5rem;
    overflow-x: auto;
}
.tab-btn {
    display: flex; align-items: center; gap: .4rem;
    padding: .65rem 1.1rem;
    font-family: inherit; font-size: .82rem; font-weight: 600;
    color: var(--muted); background: none; border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer; white-space: nowrap;
    margin-bottom: -2px; transition: .18s;
}
.tab-btn:hover  { color: var(--blue); }
.tab-btn.active { color: var(--blue); border-bottom-color: var(--blue); }

/* ── Tab Panels ───────────────────────────────── */
.tab-panel { display: none; }
.tab-panel.active { display: block; animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

/* ── Section card ─────────────────────────────── */
.section-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.1rem;
}
.section-head {
    padding: .8rem 1.1rem;
    background: var(--light);
    border-bottom: 1px solid var(--border);
    font-size: .78rem; font-weight: 700; color: var(--navy);
    display: flex; align-items: center; gap: .45rem;
}
.section-head i { color: var(--blue); }
.section-body { padding: 1.1rem; }

/* ── Readonly info row ────────────────────────── */
.info-row {
    display: flex; gap: 1rem; align-items: center;
    padding: .55rem 0;
    border-bottom: 1px solid var(--light);
    font-size: .83rem;
}
.info-row:last-child { border-bottom: none; }
.info-lbl { min-width: 120px; color: var(--muted); font-weight: 500; font-size: .78rem; }
.info-val { color: var(--text); font-weight: 600; }

/* ── Password strength ────────────────────────── */
.strength-bar-wrap {
    height: 5px; border-radius: 999px;
    background: var(--border); margin-top: .45rem; overflow: hidden;
}
.strength-bar {
    height: 100%; border-radius: 999px;
    width: 0; transition: width .35s, background .35s;
}
.strength-label { font-size: .7rem; margin-top: .25rem; }

/* ── Toggle switch ────────────────────────────── */
.toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .75rem 0; border-bottom: 1px solid var(--light);
}
.toggle-row:last-child { border-bottom: none; }
.toggle-info h4 { font-size: .83rem; font-weight: 600; color: var(--text); margin: 0 0 .1rem; }
.toggle-info p  { font-size: .72rem; color: var(--muted); margin: 0; }
.switch { position: relative; width: 42px; height: 23px; flex-shrink: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.switch-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #cbd5e1; border-radius: 999px; transition: .25s;
}
.switch-slider::before {
    content: ''; position: absolute;
    width: 17px; height: 17px; left: 3px; top: 3px;
    background: #fff; border-radius: 50%;
    transition: .25s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.switch input:checked + .switch-slider { background: var(--blue); }
.switch input:checked + .switch-slider::before { transform: translateX(19px); }

/* ── Foto preview ─────────────────────────────── */
.foto-preview-wrap {
    display: flex; align-items: center; gap: 1rem;
    padding: .85rem; border: 1.5px dashed var(--border);
    border-radius: 10px; margin-bottom: .85rem;
}
.foto-preview {
    width: 64px; height: 64px; border-radius: 50%;
    background: var(--blue); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; font-weight: 800; overflow: hidden; flex-shrink: 0;
}
.foto-preview img { width: 100%; height: 100%; object-fit: cover; }
</style>
@endpush

@section('content')
@php $tab = session('tab', 'profil'); @endphp

{{-- ── HERO PROFIL ─────────────────────────────────────────────────────── --}}
<div class="profil-hero">
    <div class="profil-ava-wrap">
        <div class="profil-ava" id="heroAva">
            @if($admin->foto)
                <img src="{{ asset('storage/'.$admin->foto) }}" alt="Foto {{ $admin->nama }}" id="heroAvaImg">
            @else
                <span id="heroAvaInisial">{{ $admin->inisial }}</span>
            @endif
        </div>
        <label class="profil-ava-edit" for="inputFotoHero" title="Ganti foto">
            <i class="fas fa-camera"></i>
        </label>
        <form method="POST" action="{{ route('admin.profil.upload-foto') }}"
              enctype="multipart/form-data" id="heroFotoForm">
            @csrf
            <input type="file" id="inputFotoHero" name="foto" accept="image/*"
                   style="display:none" onchange="this.form.submit()">
        </form>
    </div>

    <div class="profil-hero-info">
        <h2>{{ $admin->nama }}</h2>
        <p>{{ $admin->jabatan }}</p>
        <span class="profil-hero-badge">
            <i class="fas fa-shield-alt"></i> {{ $admin->role_label }}
        </span>
        @if($admin->email)
            <p style="margin-top:.4rem;font-size:.72rem;opacity:.6">
                <i class="fas fa-envelope" style="width:12px;margin-right:.3rem"></i>{{ $admin->email }}
            </p>
        @endif
    </div>

    @if($admin->foto)
    <form method="POST" action="{{ route('admin.profil.hapus-foto') }}"
          style="position:absolute;top:1rem;right:1rem"
          onsubmit="return confirm('Hapus foto profil?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm"
                style="background:rgba(0,0,0,.3);color:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.15);font-size:.7rem">
            <i class="fas fa-trash-alt"></i> Hapus Foto
        </button>
    </form>
    @endif
</div>

{{-- ── TAB NAV ──────────────────────────────────────────────────────────── --}}
<div class="tab-nav">
    <button class="tab-btn {{ $tab==='profil'?'active':'' }}"     onclick="gotoTab('profil',this)">
        <i class="fas fa-user"></i> Data Diri
    </button>
    <button class="tab-btn {{ $tab==='foto'?'active':'' }}"       onclick="gotoTab('foto',this)">
        <i class="fas fa-image"></i> Foto Profil
    </button>
    <button class="tab-btn {{ $tab==='password'?'active':'' }}"   onclick="gotoTab('password',this)">
        <i class="fas fa-lock"></i> Password
    </button>
    <button class="tab-btn {{ $tab==='notifikasi'?'active':'' }}" onclick="gotoTab('notifikasi',this)">
        <i class="fas fa-bell"></i> Notifikasi
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 1 · DATA DIRI                                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-panel {{ $tab==='profil'?'active':'' }}" id="panel-profil">
    <div class="section-card">
        <div class="section-head">
            <i class="fas fa-user-edit"></i> Edit Data Diri
        </div>
        <div class="section-body">
            @if($errors->any() && $tab==='profil')
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profil.update') }}">
                @csrf @method('PUT')

                <div class="grid-2" style="margin-bottom:.85rem">
                    <div class="form-group">
                        <label class="form-label">NIP</label>
                        <input type="text" class="form-control"
                               value="{{ $admin->nip }}" disabled
                               style="background:var(--light);color:var(--muted)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama"
                               value="{{ old('nama', $admin->nama) }}"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Nama lengkap" required>
                        @error('nama')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom:.85rem">
                    <div class="form-group">
                        <label class="form-label">Jabatan <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="jabatan"
                               value="{{ old('jabatan', $admin->jabatan) }}"
                               class="form-control @error('jabatan') is-invalid @enderror"
                               placeholder="Contoh: Staf TU, Kepala Admin" required>
                        @error('jabatan')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span style="color:var(--danger)">*</span></label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="L" {{ old('jenis_kelamin', $admin->jenis_kelamin)==='L'?'selected':'' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $admin->jenis_kelamin)==='P'?'selected':'' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom:1.1rem">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                               value="{{ old('email', $admin->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="email@sekolah.sch.id">
                        @error('email')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp"
                               value="{{ old('no_hp', $admin->no_hp) }}"
                               class="form-control @error('no_hp') is-invalid @enderror"
                               placeholder="08xxxxxxxxxx" maxlength="15">
                        @error('no_hp')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:flex;gap:.65rem;justify-content:flex-end">
                    <button type="reset" class="btn btn-outline btn-sm">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data Diri
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Info readonly --}}
    <div class="section-card">
        <div class="section-head"><i class="fas fa-info-circle"></i> Info Akun</div>
        <div class="section-body">
            <div class="info-row">
                <span class="info-lbl">Role</span>
                <span class="info-val">
                    <span class="badge {{ $admin->isSuperAdmin()?'badge-diterima':'badge-menunggu' }}">
                        {{ $admin->role_label }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-lbl">Status Akun</span>
                <span class="info-val" style="color:{{ $admin->is_aktif?'var(--success)':'var(--danger)' }}">
                    <i class="fas fa-circle" style="font-size:.5rem;margin-right:.3rem"></i>
                    {{ $admin->is_aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            @if($admin->created_at)
            <div class="info-row">
                <span class="info-lbl">Akun Dibuat</span>
                <span class="info-val">{{ $admin->created_at->translatedFormat('d F Y') }}</span>
            </div>
            @endif
            @if($admin->updated_at)
            <div class="info-row">
                <span class="info-lbl">Terakhir Diubah</span>
                <span class="info-val">{{ $admin->updated_at->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 2 · FOTO PROFIL                                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-panel {{ $tab==='foto'?'active':'' }}" id="panel-foto">
    <div class="section-card">
        <div class="section-head"><i class="fas fa-camera"></i> Ganti Foto Profil</div>
        <div class="section-body">
            @if($errors->any() && $tab==='foto')
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            {{-- Preview current foto --}}
            <div class="foto-preview-wrap">
                <div class="foto-preview" id="fotoPreviewBox">
                    @if($admin->foto)
                        <img src="{{ asset('storage/'.$admin->foto) }}" alt="Foto" id="fotoPreviewImg">
                    @else
                        <span id="fotoPreviewInisial">{{ $admin->inisial }}</span>
                    @endif
                </div>
                <div>
                    <div style="font-weight:700;font-size:.83rem;margin-bottom:.2rem">Foto Saat Ini</div>
                    @if($admin->foto)
                        <div style="font-size:.72rem;color:var(--muted)">Klik "Pilih Foto" untuk mengganti.</div>
                    @else
                        <div style="font-size:.72rem;color:var(--muted)">Belum ada foto. Unggah foto profil Anda.</div>
                    @endif
                </div>
            </div>

            {{-- Upload form --}}
            <form method="POST" action="{{ route('admin.profil.upload-foto') }}"
                  enctype="multipart/form-data" id="uploadFotoForm">
                @csrf
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">Pilih File Foto</label>
                    <input type="file" name="foto" id="inputFotoTab" accept="image/*"
                           class="form-control @error('foto') is-invalid @enderror"
                           onchange="previewFoto(this)">
                    <div class="form-hint">
                        Format: JPG, JPEG, PNG, WEBP &bull; Ukuran maks. 2 MB
                    </div>
                    @error('foto')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Foto
                    </button>
                    @if($admin->foto)
                        <form method="POST" action="{{ route('admin.profil.hapus-foto') }}"
                              style="margin:0" onsubmit="return confirm('Hapus foto profil?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Hapus Foto
                            </button>
                        </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 3 · PASSWORD                                                        --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-panel {{ $tab==='password'?'active':'' }}" id="panel-password">
    <div class="section-card" style="max-width:520px">
        <div class="section-head"><i class="fas fa-key"></i> Ganti Password</div>
        <div class="section-body">
            @if($errors->any() && $tab==='password')
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profil.ganti-password') }}">
                @csrf @method('PATCH')

                {{-- Password lama --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">Password Saat Ini <span style="color:var(--danger)">*</span></label>
                    <div style="position:relative">
                        <input type="password" name="password_lama" id="pwLama"
                               class="form-control @error('password_lama') is-invalid @enderror"
                               placeholder="Masukkan password saat ini" required
                               style="padding-right:2.5rem">
                        <button type="button" onclick="togglePw('pwLama',this)"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_lama')<div style="font-size:.72rem;color:var(--danger);margin-top:.25rem">{{ $message }}</div>@enderror
                </div>

                {{-- Password baru --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">Password Baru <span style="color:var(--danger)">*</span></label>
                    <div style="position:relative">
                        <input type="password" name="password_baru" id="pwBaru"
                               class="form-control @error('password_baru') is-invalid @enderror"
                               placeholder="Min. 8 karakter, huruf besar, kecil & angka"
                               required style="padding-right:2.5rem"
                               oninput="cekKekuatan(this.value);cekCocok()">
                        <button type="button" onclick="togglePw('pwBaru',this)"
                                style="position:absolute;right:.75rem;top:.7rem;background:none;border:none;cursor:pointer;color:var(--muted)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="strength-bar-wrap">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-label text-muted" id="strengthLabel"></div>
                    <div class="form-hint">Gunakan minimal 8 karakter dengan kombinasi huruf besar, kecil, dan angka.</div>
                    @error('password_baru')<div style="font-size:.72rem;color:var(--danger);margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Konfirmasi --}}
                <div class="form-group" style="margin-bottom:1.25rem">
                    <label class="form-label">Konfirmasi Password Baru <span style="color:var(--danger)">*</span></label>
                    <div style="position:relative">
                        <input type="password" name="password_baru_confirmation" id="pwKonfirm"
                               class="form-control" placeholder="Ulangi password baru"
                               required style="padding-right:2.5rem"
                               oninput="cekCocok()">
                        <button type="button" onclick="togglePw('pwKonfirm',this)"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="strength-label" id="cocokLabel" style="margin-top:.3rem"></div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-lock"></i> Simpan Password Baru
                </button>
            </form>
        </div>
    </div>

    <div class="section-card" style="max-width:520px">
        <div class="section-head"><i class="fas fa-shield-alt"></i> Tips Keamanan</div>
        <div class="section-body">
            <ul style="padding-left:1.2rem;font-size:.82rem;color:var(--muted);line-height:2">
                <li>Gunakan password unik yang tidak dipakai di layanan lain.</li>
                <li>Hindari nama, tanggal lahir, atau NIP sebagai password.</li>
                <li>Jangan bagikan password kepada siapapun.</li>
                <li>Selalu logout di komputer bersama / publik.</li>
                <li>Segera ganti password jika akun Anda dicurigai diretas.</li>
            </ul>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 4 · NOTIFIKASI                                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div class="tab-panel {{ $tab==='notifikasi'?'active':'' }}" id="panel-notifikasi">
    <div class="section-card">
        <div class="section-head"><i class="fas fa-bell"></i> Preferensi Notifikasi & Tampilan</div>
        <div class="section-body">
            <form method="POST" action="{{ route('admin.profil.notifikasi') }}">
                @csrf @method('PATCH')

                {{-- Notifikasi --}}
                <div style="font-size:.72rem;font-weight:700;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:.75rem">
                    Notifikasi Sistem
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <h4>Pendaftar Baru</h4>
                        <p>Tampilkan alert saat ada calon siswa baru mendaftar.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_pendaftar_baru" value="1"
                               {{ $admin->notif_pendaftar_baru ? 'checked' : '' }}>
                        <span class="switch-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <h4>Pembayaran Masuk</h4>
                        <p>Tampilkan alert saat ada bukti pembayaran diunggah siswa.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_pembayaran_baru" value="1"
                               {{ $admin->notif_pembayaran_baru ? 'checked' : '' }}>
                        <span class="switch-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <h4>Kirim ke Email</h4>
                        <p>Ringkasan harian dikirim ke
                            <strong>{{ $admin->email ?? '(email belum diisi)' }}</strong>.
                        </p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_email" value="1"
                               {{ $admin->notif_email ? 'checked' : '' }}
                               {{ !$admin->email ? 'disabled title=Isi email di tab Data Diri terlebih dahulu' : '' }}>
                        <span class="switch-slider" style="{{ !$admin->email ? 'opacity:.5;cursor:not-allowed' : '' }}"></span>
                    </label>
                </div>

                @if(!$admin->email)
                <div class="alert alert-warning" style="margin-top:.75rem;padding:.6rem .85rem;font-size:.75rem">
                    <i class="fas fa-exclamation-triangle" style="flex-shrink:0"></i>
                    Notifikasi email tidak tersedia. Isi email di tab <strong>Data Diri</strong> terlebih dahulu.
                </div>
                @endif

                {{-- Tampilan tabel --}}
                <div style="font-size:.72rem;font-weight:700;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin:1.25rem 0 .75rem">
                    Tampilan Tabel
                </div>
                <div class="form-group" style="max-width:200px">
                    <label class="form-label">Baris per Halaman</label>
                    <select name="tampilan_rows" class="form-control">
                        @foreach([10,25,50,100] as $n)
                        <option value="{{ $n }}" {{ ($admin->tampilan_rows??25)==$n ? 'selected':'' }}>
                            {{ $n }} baris
                        </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Default tampilan tabel di semua halaman.</div>
                </div>

                <div style="margin-top:1.1rem">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Preferensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ── Switching tab ───────────────────────────── */
function gotoTab(name, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    btn.classList.add('active');
}

/* ── Toggle show/hide password ───────────────── */
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const ico = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        ico.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

/* ── Password strength ───────────────────────── */
function cekKekuatan(val) {
    const bar  = document.getElementById('strengthBar');
    const lbl  = document.getElementById('strengthLabel');
    const lvls = [
        { pct: 20, bg: '#dc2626', txt: 'Sangat lemah' },
        { pct: 40, bg: '#f97316', txt: 'Lemah' },
        { pct: 60, bg: '#eab308', txt: 'Sedang' },
        { pct: 80, bg: '#22c55e', txt: 'Kuat' },
        { pct:100, bg: '#16a34a', txt: 'Sangat kuat' },
    ];
    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[a-z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;
    const l = lvls[score - 1] || { pct: 0, bg: '#e2e8f0', txt: '' };
    bar.style.width      = l.pct + '%';
    bar.style.background = l.bg;
    lbl.textContent      = l.txt;
    lbl.style.color      = l.bg;
}

/* ── Password match check ────────────────────── */
function cekCocok() {
    const pw1 = document.getElementById('pwBaru').value;
    const pw2 = document.getElementById('pwKonfirm').value;
    const lbl = document.getElementById('cocokLabel');
    if (!pw2) { lbl.textContent = ''; return; }
    if (pw1 === pw2) {
        lbl.textContent = '✓ Password cocok';
        lbl.style.color = '#16a34a';
    } else {
        lbl.textContent = '✗ Password tidak cocok';
        lbl.style.color = '#dc2626';
    }
}

/* ── Preview foto sebelum upload ─────────────── */
function previewFoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        /* preview di tab foto */
        const box = document.getElementById('fotoPreviewBox');
        box.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover">`;
        /* preview di hero avatar */
        const hero = document.getElementById('heroAva');
        hero.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
@endsection
