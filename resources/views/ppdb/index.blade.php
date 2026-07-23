@extends('layouts.public')
@section('title', 'Formulir Pendaftaran PPDB SMK')

@push('styles')
<style>
    /* ── Semua input teks tampil UPPERCASE secara visual ── */
    input[type="text"],
    input[type="date"],
    textarea {
        text-transform: uppercase;
    }
    /* Email tetap lowercase secara visual */
    input[type="email"] {
        text-transform: lowercase;
    }

    /* ── Kotak "Petunjuk Pengisian" di setiap card ── */
    .petunjuk-box {
        background: #eaf3ff;
        border: 1px solid #bcd9ff;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        font-size: .82rem;
        color: #1e3a5f;
    }
    .petunjuk-box .petunjuk-title {
        font-weight: 800;
        color: #0f2744;
        margin-bottom: .5rem;
        font-size: .85rem;
    }
    .petunjuk-box ol {
        margin: 0;
        padding-left: 1.25rem;
    }
    .petunjuk-box ol li {
        margin-bottom: .35rem;
        line-height: 1.55;
    }
    .petunjuk-box ol li:last-child {
        margin-bottom: 0;
    }
    .petunjuk-box strong {
        color: #0f2744;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="text-align:center;margin-bottom:2rem;padding-top:.5rem">
        <h1 style="font-size:1.6rem;font-weight:800;color:#0f2744">Formulir Pendaftaran</h1>
        <p style="color:#64748b;margin-top:.35rem">Isi semua data dengan benar dan lengkap</p>
    </div>

    {{-- ===== BANNER INFO PERIODE AKTIF ===== --}}
    @if(isset($periode))
    <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:.9rem 1.1rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.85rem;flex-wrap:wrap">
        <div style="width:40px;height:40px;background:#dcfce7;border-radius:10px;display:grid;place-items:center;flex-shrink:0">
            <i class="fas fa-calendar-check" style="color:#16a34a;font-size:1.1rem"></i>
        </div>
        <div style="flex:1;min-width:180px">
            <div style="font-weight:700;color:#14532d;font-size:.9rem">{{ $periode->nama_periode }}</div>
            <div style="font-size:.78rem;color:#166534;margin-top:.2rem">
                Pendaftaran dibuka <strong>{{ $periode->tanggal_buka->translatedFormat('d F Y') }}</strong>
                s.d. <strong>{{ $periode->tanggal_tutup->translatedFormat('d F Y') }}</strong>
                @if($periode->biaya_pendaftaran > 0)
                &nbsp;·&nbsp; Biaya: <strong>{{ $periode->biaya_format }}</strong>
                @else
                &nbsp;·&nbsp; <strong>Gratis</strong>
                @endif
            </div>
        </div>
        @php
            $sisaHari = (int) now()->diffInDays($periode->tanggal_tutup, false);
        @endphp
        @if($sisaHari <= 7 && $sisaHari >= 0)
        <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:.4rem .8rem;font-size:.75rem;font-weight:700;color:#92400e;white-space:nowrap">
            <i class="fas fa-exclamation-triangle"></i>
            Tutup dalam {{ $sisaHari }} hari
        </div>
        @endif
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0;margin-top:.1rem"></i>
        <div>
            <strong>Terdapat {{ $errors->count() }} kesalahan:</strong>
            <ul style="margin-top:.35rem;padding-left:1.1rem">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <form id="form-ppdb" method="POST" action="{{ route('ppdb.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- DATA PRIBADI --}}
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-header"><i class="fas fa-user"></i> Data Pribadi Calon Siswa</div>
            <div class="card-body">
                <div class="petunjuk-box">
                    <div class="petunjuk-title">Petunjuk Pengisian:</div>
                    <ol>
                        <li>Isi data sesuai dokumen resmi (KTP/KK/Akta Kelahiran/Ijazah), jangan disingkat atau direkayasa.</li>
                        <li>Perhatikan huruf besar dan kecil pada Tempat Lahir dan Asal Sekolah, sistem akan otomatis mengubahnya menjadi huruf kapital saat disimpan.</li>
                        <li>NISN diisi 10 digit angka sesuai data Dapodik/Ijazah, tanpa spasi atau tanda baca.</li>
                        <li>Tahun Lulus diisi tahun kelulusan dari SMP/MTs asal.</li>
                        <li>Foto bersifat opsional. Jika diunggah, gunakan format JPG/PNG dengan ukuran maksimal 2MB.</li>
                        <li>Kolom bertanda <strong>*</strong> wajib diisi, kolom lainnya boleh dikosongkan.</li>
                    </ol>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Depan <span class="req">*</span></label>
                        <input type="text" name="nama_depan" value="{{ old('nama_depan') }}"
                            class="form-control @error('nama_depan') is-invalid @enderror"
                            placeholder="NAMA DEPAN" required>
                        @error('nama_depan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Tengah</label>
                        <input type="text" name="nama_tengah" value="{{ old('nama_tengah') }}"
                            class="form-control" placeholder="NAMA TENGAH (OPSIONAL)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="nama_belakang" value="{{ old('nama_belakang') }}"
                            class="form-control" placeholder="NAMA BELAKANG (OPSIONAL)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">— Pilih —</option>
                            <option value="L" {{ old('jenis_kelamin')==='L'?'selected':'' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin')==='P'?'selected':'' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span class="req">*</span></label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                            class="form-control @error('tempat_lahir') is-invalid @enderror"
                            placeholder="KOTA TEMPAT LAHIR" required>
                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span class="req">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="form-control @error('tanggal_lahir') is-invalid @enderror" required>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">NISN <span class="req">*</span></label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}"
                            class="form-control @error('nisn') is-invalid @enderror"
                            placeholder="10 DIGIT NISN" maxlength="10" required>
                        <div class="form-hint">Nomor Induk Siswa Nasional (10 digit angka)</div>
                        @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Asal Sekolah <span class="req">*</span></label>
                        <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}"
                            class="form-control @error('asal_sekolah') is-invalid @enderror"
                            placeholder="NAMA SMP/MTS ASAL" required>
                        @error('asal_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tahun Lulus <span class="req">*</span></label>
                        <input type="text" name="tahun_lulus" value="{{ old('tahun_lulus', date('Y')) }}"
                            class="form-control @error('tahun_lulus') is-invalid @enderror"
                            placeholder="{{ date('Y') }}" maxlength="4" required>
                        @error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Foto <span style="font-weight:400;color:#64748b">(opsional)</span></label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                            accept="image/*">
                        <div class="form-hint">Format JPG/PNG, maks. 2MB</div>
                        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- PILIHAN JURUSAN —  HANYA 1 --}}
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-header"><i class="fas fa-school"></i> Pilihan Jurusan</div>
            <div class="card-body">
                <div class="petunjuk-box">
                    <div class="petunjuk-title">Petunjuk Pengisian:</div>
                    <ol>
                        <li>Pilih <strong>hanya satu</strong> jurusan yang paling sesuai dengan minat dan kemampuan Anda.</li>
                        <li>Perhatikan sisa kuota pada setiap jurusan sebelum menentukan pilihan.</li>
                        <li>Pastikan pilihan sudah benar sebelum formulir dikirim, karena perubahan jurusan setelah pendaftaran harus melalui pihak sekolah.</li>
                        <li>Kolom bertanda <strong>*</strong> wajib diisi.</li>
                    </ol>
                </div>
                <div class="form-group">
                    <label class="form-label">Jurusan yang Dipilih <span class="req">*</span></label>
                    <select name="pilihan_1" class="form-control @error('pilihan_1') is-invalid @enderror" required>
                        <option value="">— Pilih Jurusan —</option>
                        @foreach($jurusan as $j)
                        <option value="{{ $j->id_jurusan }}" {{ old('pilihan_1')==$j->id_jurusan?'selected':'' }}>
                            [{{ $j->kode_jurusan }}] {{ $j->nama_jurusan }}
                        </option>
                        @endforeach
                    </select>
                    @error('pilihan_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-hint">Pilih satu jurusan yang paling sesuai dengan minat Anda.</div>
                </div>

                {{-- Info jurusan --}}
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.75rem;margin-top:1rem">
                    @foreach($jurusan as $j)
                    @php $jc = match($j->kode_jurusan){'AKL'=>['bg'=>'#ede9fe','c'=>'#5b21b6'],'TJKT'=>['bg'=>'#dbeafe','c'=>'#1e40af'],'MPLB'=>['bg'=>'#fce7f3','c'=>'#9d174d'],default=>['bg'=>'#f1f5f9','c'=>'#475569']}; @endphp
                    <div style="background:{{ $jc['bg'] }};border-radius:10px;padding:.75rem;border:1px solid {{ $jc['bg'] }}">
                        <div style="font-weight:800;font-size:.78rem;color:{{ $jc['c'] }};margin-bottom:.25rem">{{ $j->kode_jurusan }}</div>
                        <div style="font-size:.75rem;color:{{ $jc['c'] }};opacity:.85;line-height:1.4">{{ $j->nama_jurusan }}</div>
                        <div style="font-size:.68rem;color:{{ $jc['c'] }};opacity:.65;margin-top:.25rem">Kuota: {{ $j->kapasitas }} siswa</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ALAMAT --}}
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-header"><i class="fas fa-map-marker-alt"></i> Alamat Tempat Tinggal</div>
            <div class="card-body">
                <div class="petunjuk-box">
                    <div class="petunjuk-title">Petunjuk Pengisian:</div>
                    <ol>
                        <li>Pilih <strong>"Bersama Orang Tua / Wali"</strong> jika Anda tinggal serumah dengan orang tua/wali, alamat akan otomatis mengikuti data pada bagian "Data Orang Tua / Wali" di bawah.</li>
                        <li>Pilih <strong>"Kost / Kontrak / Sewa"</strong> jika Anda tinggal terpisah dari orang tua/wali (perantauan), lalu lengkapi alamat tempat tinggal Anda saat ini.</li>
                        <li>Isi alamat secara lengkap dan sesuai dengan kondisi sebenarnya, karena akan digunakan untuk keperluan surat-menyurat sekolah.</li>
                        <li>Kolom bertanda <strong>*</strong> wajib diisi sesuai opsi yang dipilih.</li>
                    </ol>
                </div>

                {{-- Toggle pilihan utama --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem">
                    <label id="lbl-ortu" style="display:flex;align-items:flex-start;gap:.75rem;padding:1rem 1.1rem;border:2px solid #1a4a8a;border-radius:12px;cursor:pointer;background:#eff6ff;transition:.2s">
                        <input type="radio" name="tinggal_bersama_ortu" value="1" id="r-ortu"
                            {{ old('tinggal_bersama_ortu', '1') === '1' ? 'checked' : '' }}
                            style="margin-top:.15rem;accent-color:#1a4a8a;flex-shrink:0">
                        <div>
                            <div style="font-weight:700;font-size:.875rem;color:#0f2744">Bersama Orang Tua / Wali</div>
                            <div style="font-size:.75rem;color:#475569;margin-top:.2rem">Saya tinggal di rumah orang tua atau wali</div>
                        </div>
                    </label>
                    <label id="lbl-sendiri" style="display:flex;align-items:flex-start;gap:.75rem;padding:1rem 1.1rem;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;background:#f8fafc;transition:.2s">
                        <input type="radio" name="tinggal_bersama_ortu" value="0" id="r-sendiri"
                            {{ old('tinggal_bersama_ortu') === '0' ? 'checked' : '' }}
                            style="margin-top:.15rem;accent-color:#1a4a8a;flex-shrink:0">
                        <div>
                            <div style="font-weight:700;font-size:.875rem;color:#0f2744">Kost / Kontrak / Sewa</div>
                            <div style="font-size:.75rem;color:#475569;margin-top:.2rem">Saya tinggal di tempat lain (perantauan)</div>
                        </div>
                    </label>
                </div>
                @error('tinggal_bersama_ortu')<div style="font-size:.75rem;color:#ef4444;margin-bottom:.75rem">{{ $message }}</div>@enderror

                {{-- Info jika tinggal bersama ortu --}}
                <div id="info-ortu" style="{{ old('tinggal_bersama_ortu', '1') === '1' ? '' : 'display:none' }}">
                    <div style="display:flex;align-items:flex-start;gap:.6rem;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.82rem;color:#166534">
                        <i class="fas fa-info-circle" style="margin-top:.15rem;flex-shrink:0"></i>
                        <div>Alamat Anda akan diisi otomatis dari data orang tua / wali yang diisi di bawah.
                        Tidak perlu mengisi alamat terpisah.</div>
                    </div>
                </div>

                {{-- Form alamat siswa — hanya muncul jika tinggal sendiri --}}
                <div id="form-alamat-sendiri" style="{{ old('tinggal_bersama_ortu') === '0' ? '' : 'display:none' }}">
                    <div style="margin-top:.75rem;margin-bottom:.75rem;font-size:.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">
                        Alamat Tempat Tinggal Siswa (Kost / Kontrak / Sewa)
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Jalan / Alamat Lengkap <span class="req">*</span></label>
                            <input type="text" name="nama_jalan" value="{{ old('nama_jalan') }}"
                                class="form-control @error('nama_jalan') is-invalid @enderror"
                                placeholder="JL. CONTOH NO. 12 RT 01/RW 03">
                            @error('nama_jalan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelurahan / Desa</label>
                            <input type="text" name="kelurahan" value="{{ old('kelurahan') }}"
                                class="form-control" placeholder="KELURAHAN / DESA">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan') }}"
                                class="form-control" placeholder="KECAMATAN">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kabupaten / Kota <span class="req">*</span></label>
                            <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota') }}"
                                class="form-control @error('kabupaten_kota') is-invalid @enderror"
                                placeholder="KABUPATEN / KOTA">
                            @error('kabupaten_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Provinsi <span class="req">*</span></label>
                            <input type="text" name="provinsi" value="{{ old('provinsi') }}"
                                class="form-control @error('provinsi') is-invalid @enderror"
                                placeholder="PROVINSI">
                            @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos') }}"
                                class="form-control" placeholder="KODE POS" maxlength="10">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATA WALI --}}
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-header"><i class="fas fa-user-friends"></i> Data Orang Tua / Wali</div>
            <div class="card-body">
                <div class="petunjuk-box">
                    <div class="petunjuk-title">Petunjuk Pengisian:</div>
                    <ol>
                        <li>Isi data orang tua/wali yang aktif dan dapat dihubungi sewaktu-waktu.</li>
                        <li>Nomor HP wajib aktif karena akan digunakan sekolah untuk menyampaikan informasi penting terkait pendaftaran.</li>
                        <li>Alamat orang tua/wali wajib diisi lengkap, karena dapat digunakan sebagai alamat siswa apabila tinggal bersama orang tua/wali.</li>
                        <li>Kolom bertanda <strong>*</strong> wajib diisi, kolom lainnya boleh dikosongkan.</li>
                    </ol>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Hubungan <span class="req">*</span></label>
                        <select name="wali_hubungan" class="form-control @error('wali_hubungan') is-invalid @enderror" required>
                            <option value="">— Pilih —</option>
                            <option value="AY" {{ old('wali_hubungan')==='AY'?'selected':'' }}>Ayah</option>
                            <option value="IB" {{ old('wali_hubungan')==='IB'?'selected':'' }}>Ibu</option>
                            <option value="WL" {{ old('wali_hubungan')==='WL'?'selected':'' }}>Wali</option>
                        </select>
                        @error('wali_hubungan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                        <select name="wali_jenis_kelamin" class="form-control @error('wali_jenis_kelamin') is-invalid @enderror" required>
                            <option value="">— Pilih —</option>
                            <option value="L" {{ old('wali_jenis_kelamin')==='L'?'selected':'' }}>Laki-laki</option>
                            <option value="P" {{ old('wali_jenis_kelamin')==='P'?'selected':'' }}>Perempuan</option>
                        </select>
                        @error('wali_jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Depan <span class="req">*</span></label>
                        <input type="text" name="wali_nama_depan" value="{{ old('wali_nama_depan') }}"
                            class="form-control @error('wali_nama_depan') is-invalid @enderror"
                            placeholder="NAMA DEPAN" required>
                        @error('wali_nama_depan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="wali_nama_belakang" value="{{ old('wali_nama_belakang') }}"
                            class="form-control" placeholder="NAMA BELAKANG">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP <span class="req">*</span></label>
                        <input type="text" name="wali_nomor_hp" value="{{ old('wali_nomor_hp') }}"
                            class="form-control @error('wali_nomor_hp') is-invalid @enderror"
                            placeholder="08XXXXXXXXXX" required>
                        @error('wali_nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" name="wali_pekerjaan" value="{{ old('wali_pekerjaan') }}"
                            class="form-control" placeholder="PEKERJAAN ORANG TUA / WALI">
                    </div>
                </div>

                {{-- Alamat orang tua / wali --}}
                <div style="margin-top:1.1rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                    <div style="font-size:.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.75rem">
                        Alamat Orang Tua / Wali
                        <span id="lbl-alamat-ortu-hint" style="font-size:.7rem;font-weight:400;color:#1e40af;text-transform:none;letter-spacing:0;margin-left:.5rem">
                            (akan dipakai sebagai alamat Anda jika tinggal bersama ortu)
                        </span>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Jalan / Alamat Lengkap <span class="req">*</span></label>
                            <input type="text" name="wali_nama_jalan" value="{{ old('wali_nama_jalan') }}"
                                class="form-control @error('wali_nama_jalan') is-invalid @enderror"
                                placeholder="JL. CONTOH NO. 12 RT 01/RW 03" required>
                            @error('wali_nama_jalan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelurahan / Desa</label>
                            <input type="text" name="wali_kelurahan" value="{{ old('wali_kelurahan') }}"
                                class="form-control" placeholder="KELURAHAN / DESA">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="wali_kecamatan" value="{{ old('wali_kecamatan') }}"
                                class="form-control" placeholder="KECAMATAN">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kabupaten / Kota <span class="req">*</span></label>
                            <input type="text" name="wali_kabupaten_kota" value="{{ old('wali_kabupaten_kota') }}"
                                class="form-control @error('wali_kabupaten_kota') is-invalid @enderror"
                                placeholder="KABUPATEN / KOTA" required>
                            @error('wali_kabupaten_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Provinsi <span class="req">*</span></label>
                            <input type="text" name="wali_provinsi" value="{{ old('wali_provinsi') }}"
                                class="form-control @error('wali_provinsi') is-invalid @enderror"
                                placeholder="PROVINSI" required>
                            @error('wali_provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="wali_kode_pos" value="{{ old('wali_kode_pos') }}"
                                class="form-control" placeholder="KODE POS" maxlength="10">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- AKUN --}}
        <div class="card" style="margin-bottom:1.5rem">
            <div class="card-header"><i class="fas fa-lock"></i> Akun & Kontak</div>
            <div class="card-body">
                <div class="petunjuk-box">
                    <div class="petunjuk-title">Petunjuk Pengisian:</div>
                    <ol>
                        <li>Gunakan email yang <strong>masih aktif</strong>, karena akan digunakan untuk login ke Portal Siswa dan menerima notifikasi status pendaftaran.</li>
                        <li>Password minimal 8 karakter, kombinasikan huruf dan angka agar lebih aman dan mudah diingat.</li>
                        <li>Pastikan Konfirmasi Password diisi sama persis dengan Password.</li>
                        <li>Nomor HP diisi dengan nomor yang dapat dihubungi sebagai kontak tambahan.</li>
                        <li>Kolom bertanda <strong>*</strong> wajib diisi.</li>
                    </ol>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="email@contoh.com" required>
                        <div class="form-hint">Digunakan untuk login portal siswa</div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP <span class="req">*</span></label>
                        <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"
                            class="form-control @error('nomor_hp') is-invalid @enderror"
                            placeholder="08XXXXXXXXXX" required>
                        @error('nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span class="req">*</span></label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min. 8 karakter" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password <span class="req">*</span></label>
                        <input type="password" name="password_confirmation"
                            class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align:center;padding-bottom:2rem">
            <p style="color:#64748b;font-size:.8rem;margin-bottom:1rem">
                Pastikan semua data yang diisi sudah benar sebelum mengirim formulir.
            </p>
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {

    /* ================================================================
     * 1. UPPERCASE — konversi value ke huruf besar saat submit
     *    - Semua input[type=text] → strtoupper
     *    - input[type=email]      → lowercase (konvensi)
     *    - input[type=password]   → dibiarkan (case-sensitive)
     * ================================================================ */
    document.getElementById('form-ppdb').addEventListener('submit', function () {
        // Teks biasa → UPPERCASE
        this.querySelectorAll('input[type="text"], textarea').forEach(function (el) {
            el.value = el.value.trim().toUpperCase();
        });
        // Email → lowercase
        this.querySelectorAll('input[type="email"]').forEach(function (el) {
            el.value = el.value.trim().toLowerCase();
        });
        // password & file → dibiarkan
    });

    /* ================================================================
     * 2. Feedback visual real-time saat user mengetik
     *    (agar terlihat langsung jadi huruf besar di layar)
     *    CSS text-transform sudah menangani tampilan,
     *    tapi ini memastikan value benar-benar berubah live.
     * ================================================================ */
    document.querySelectorAll('#form-ppdb input[type="text"], #form-ppdb textarea').forEach(function (el) {
        el.addEventListener('input', function () {
            const pos = this.selectionStart; // simpan posisi kursor
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos); // kembalikan posisi kursor
        });
    });

    /* ================================================================
     * 3. Toggle alamat (sama seperti sebelumnya)
     * ================================================================ */
    const rOrtu      = document.getElementById('r-ortu');
    const rSendiri   = document.getElementById('r-sendiri');
    const lblOrtu    = document.getElementById('lbl-ortu');
    const lblSendiri = document.getElementById('lbl-sendiri');
    const infoOrtu   = document.getElementById('info-ortu');
    const formSendiri = document.getElementById('form-alamat-sendiri');
    const fieldsSendiri = formSendiri.querySelectorAll('input');

    function applyState(bersama) {
        lblOrtu.style.borderColor    = bersama ? '#1a4a8a' : '#e2e8f0';
        lblOrtu.style.background     = bersama ? '#eff6ff' : '#f8fafc';
        lblSendiri.style.borderColor = bersama ? '#e2e8f0' : '#1a4a8a';
        lblSendiri.style.background  = bersama ? '#f8fafc' : '#eff6ff';

        infoOrtu.style.display    = bersama ? ''     : 'none';
        formSendiri.style.display = bersama ? 'none' : '';

        fieldsSendiri.forEach(function (el) {
            if (['nama_jalan', 'kabupaten_kota', 'provinsi'].includes(el.name)) {
                el.required = !bersama;
            }
        });
    }

    rOrtu.addEventListener('change',    function () { applyState(true);  });
    rSendiri.addEventListener('change', function () { applyState(false); });

    // Hint alamat wali
    const hintAlamatOrtu = document.getElementById('lbl-alamat-ortu-hint');
    function updateHint(bersama) {
        if (hintAlamatOrtu) {
            hintAlamatOrtu.textContent = bersama
                ? '(akan dipakai sebagai alamat Anda jika tinggal bersama ortu)'
                : '(dicatat sebagai alamat orang tua, terpisah dari alamat Anda)';
        }
    }
    rOrtu.addEventListener('change',    function () { updateHint(true);  });
    rSendiri.addEventListener('change', function () { updateHint(false); });

    // Init
    applyState(rOrtu.checked);
    updateHint(rOrtu.checked);

})();
</script>
@endpush