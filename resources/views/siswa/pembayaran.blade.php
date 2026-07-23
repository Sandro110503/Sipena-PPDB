@extends('siswa.layout')
@section('title','Pembayaran Pendaftaran')

@push('styles')
<style>
.form-ctrl{width:100%;padding:.62rem .88rem;border:1.5px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:.88rem;color:#1e293b;background:#fff;transition:border-color .2s;-webkit-appearance:none;min-height:44px;}
.form-ctrl:focus{outline:none;border-color:#1a4a8a;}
select.form-ctrl{cursor:pointer;}
.form-ctrl.err{border-color:#dc2626;}
.err-msg{font-size:.72rem;color:#dc2626;margin-top:.25rem;}

.upload-area{border:2px dashed #e2e8f0;border-radius:12px;padding:1.75rem;text-align:center;cursor:pointer;transition:.2s;background:#f8fafc;position:relative;overflow:hidden;}
.upload-area:hover,.upload-area.drag{border-color:#1a4a8a;background:#eff6ff;}
.upload-area input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-area i{font-size:1.85rem;color:#94a3b8;display:block;margin-bottom:.5rem;}
.upload-area strong{display:block;font-size:.82rem;font-weight:700;color:#0f2744;}
.upload-area span{font-size:.72rem;color:#94a3b8;display:block;margin-top:.2rem;}

.btn-submit-bayar{background:#0f2744;color:#fff;border:none;padding:.8rem 1.75rem;border-radius:10px;font-family:inherit;font-size:.9rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;min-height:46px;touch-action:manipulation;transition:.2s;}
.btn-submit-bayar:hover{background:#1a4a8a;}
.btn-submit-bayar:disabled{opacity:.5;cursor:not-allowed;}

.rekening-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.9rem 1rem;position:relative;}
.rekening-norek{font-size:1rem;font-weight:800;color:#0f2744;font-family:monospace;letter-spacing:1px;}
.btn-copy{position:absolute;top:.75rem;right:.75rem;background:#fff;border:1px solid #e2e8f0;border-radius:7px;padding:.25rem .6rem;font-size:.7rem;cursor:pointer;color:#64748b;transition:.2s;display:flex;align-items:center;gap:.3rem;}
.btn-copy:hover{border-color:#1a4a8a;color:#1a4a8a;}
</style>
@endpush

@section('content')
{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.6rem;margin-bottom:1.1rem;font-size:.82rem">
    <a href="{{ route('siswa.dashboard') }}" style="color:#64748b;text-decoration:none;display:flex;align-items:center;gap:.3rem">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
    <span style="color:#e2e8f0">/</span>
    <span style="color:#0f2744;font-weight:600">Pembayaran</span>
</div>

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0f2744,#1a4a8a);color:#fff;border-radius:14px;padding:1.35rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <div style="width:50px;height:50px;background:rgba(255,255,255,.15);border-radius:12px;display:grid;place-items:center;font-size:1.3rem;flex-shrink:0">
        <i class="fas fa-credit-card"></i>
    </div>
    <div style="flex:1">
        <div style="font-size:.95rem;font-weight:800">Pembayaran Biaya Pendaftaran</div>
        <div style="font-size:.78rem;opacity:.8;margin-top:.2rem">
            {{ $siswa->nama_lengkap }} — {{ $siswa->nomor_pendaftaran }}
        </div>
    </div>
    @php $pj = $siswa->pendaftaranJurusan->first(); @endphp
    @if($pj)
    <div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:9px;padding:.55rem .9rem;font-size:.8rem;text-align:center;flex-shrink:0">
        <div style="opacity:.65;font-size:.65rem;margin-bottom:.1rem">JURUSAN</div>
        <div style="font-weight:800">{{ $pj->jurusan->kode_jurusan }}</div>
    </div>
    @endif
</div>

@if($pembayaranTerverifikasi)
{{-- SUDAH LUNAS --}}
<div style="background:#dcfce7;border:2px solid #86efac;border-radius:14px;padding:2rem;text-align:center">
    <i class="fas fa-check-circle" style="font-size:3rem;color:#166534;margin-bottom:.75rem;display:block"></i>
    <h2 style="font-size:1.1rem;font-weight:800;color:#166534;margin-bottom:.4rem">Pembayaran Sudah Terverifikasi!</h2>
    <p style="color:#166534;font-size:.875rem;opacity:.9;margin-bottom:1.25rem">
        Proses pembayaran daftar ulang Anda telah selesai dikonfirmasi oleh panitia.
    </p>
    <div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;display:inline-block;min-width:280px;text-align:left;margin-bottom:1.25rem">
        @foreach(['Metode'=>$pembayaranTerverifikasi->metodePembayaran->deskripsi_metode_bayar,'Jumlah'=>'Rp '.number_format($pembayaranTerverifikasi->jumlah_bayar,0,',','.'),'Tgl. Bayar'=>$pembayaranTerverifikasi->tanggal_bayar->format('d M Y'),'Status'=>'Terverifikasi ✓'] as $l=>$v)
        <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid #f0fdf4;font-size:.84rem;gap:1rem">
            <span style="color:#166534;opacity:.8">{{ $l }}</span>
            <span style="font-weight:700;color:#166534">{{ $v }}</span>
        </div>
        @endforeach
    </div>
    <div>
        <a href="{{ route('siswa.dashboard') }}" style="background:#166534;color:#fff;padding:.65rem 1.5rem;border-radius:10px;text-decoration:none;font-weight:700;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem">
            <i class="fas fa-home"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

@elseif($pembayaranMenunggu)
{{-- SEDANG MENUNGGU --}}
<div style="background:#fef3c7;border:2px solid #fcd34d;border-radius:14px;padding:2rem;text-align:center">
    <i class="fas fa-clock" style="font-size:3rem;color:#92400e;margin-bottom:.75rem;display:block;animation:pulse 2s infinite"></i>
    <h2 style="font-size:1.1rem;font-weight:800;color:#92400e;margin-bottom:.4rem">Menunggu Verifikasi Panitia</h2>
    <p style="color:#92400e;font-size:.875rem;opacity:.9;margin-bottom:1.25rem">
        Bukti pembayaran Anda sudah diterima dan sedang diverifikasi oleh panitia PPDB.
        Proses verifikasi biasanya memakan waktu 1×24 jam.
    </p>
    <div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;display:inline-block;min-width:280px;text-align:left;margin-bottom:1.25rem">
        @foreach(['Metode'=>$pembayaranMenunggu->metodePembayaran->deskripsi_metode_bayar,'Jumlah'=>'Rp '.number_format($pembayaranMenunggu->jumlah_bayar,0,',','.'),'Tgl. Bayar'=>$pembayaranMenunggu->tanggal_bayar->format('d M Y'),'Diunggah'=>$pembayaranMenunggu->created_at->format('d M Y, H:i')] as $l=>$v)
        <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid #fef9c3;font-size:.84rem;gap:1rem">
            <span style="color:#92400e;opacity:.8">{{ $l }}</span>
            <span style="font-weight:700;color:#92400e">{{ $v }}</span>
        </div>
        @endforeach
    </div>
    @if($pembayaranMenunggu->bukti_bayar)
    <div style="margin-bottom:1rem">
        <a href="{{ Storage::url($pembayaranMenunggu->bukti_bayar) }}" target="_blank"
           style="background:#92400e;color:#fff;padding:.55rem 1.25rem;border-radius:9px;text-decoration:none;font-size:.82rem;font-weight:700;display:inline-flex;align-items:center;gap:.4rem">
            <i class="fas fa-eye"></i> Lihat Bukti Pembayaran
        </a>
    </div>
    @endif
    <div>
        <a href="{{ route('siswa.dashboard') }}" style="color:#92400e;font-size:.82rem;text-decoration:none;font-weight:600">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

@else
{{-- ===== FORM UPLOAD BUKTI ===== --}}

{{-- Pesan jika ada yang ditolak --}}
@if($pembayaranDitolak)
<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:.9rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.65rem;font-size:.84rem;color:#991b1b">
    <i class="fas fa-exclamation-triangle" style="flex-shrink:0;margin-top:.1rem"></i>
    <div>
        <strong>Pembayaran sebelumnya ditolak.</strong>
        @if($pembayaranDitolak->keterangan)
            Alasan: {{ $pembayaranDitolak->keterangan }}.
        @endif
        Silakan periksa kembali dan upload bukti yang valid.
    </div>
</div>
@endif

{{-- Info Rekening --}}
<div class="card" style="margin-bottom:1.1rem">
    <div class="card-header"><i class="fas fa-university"></i> Rekening Tujuan Pembayaran</div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.85rem">
            @php $rekenings = [
                ['bank'=>'BRI','norek'=>'1234-5678-9012-3456','an'=>'SMK Yadika 8'],
                ['bank'=>'BNI','norek'=>'987654321','an'=>'SMK Yadika 8'],
                ['bank'=>'Mandiri','norek'=>'1400099999999','an'=>'SMK Yadika 8'],
            ]; @endphp
            @foreach($rekenings as $r)
            <div class="rekening-card">
                <div style="font-size:.68rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.35rem">{{ $r['bank'] }}</div>
                <div class="rekening-norek">{{ $r['norek'] }}</div>
                <div style="font-size:.75rem;color:#64748b;margin-top:.2rem">a.n. {{ $r['an'] }}</div>
                <button class="btn-copy" onclick="salin('{{ $r['norek'] }}',this)">
                    <i class="fas fa-copy"></i> Salin
                </button>
            </div>
            @endforeach
        </div>
        <div style="margin-top:.85rem;background:#fef3c7;border-radius:8px;padding:.65rem .9rem;font-size:.78rem;color:#92400e">
            <i class="fas fa-info-circle"></i>
            Cantumkan <strong>{{ $siswa->nomor_pendaftaran }}</strong> sebagai keterangan/berita transfer.
        </div>
    </div>
</div>

{{-- Form Upload --}}
<div class="card">
    <div class="card-header"><i class="fas fa-upload"></i> Upload Bukti Pembayaran</div>
    <div class="card-body">
        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1rem;margin-bottom:1rem;font-size:.82rem;color:#991b1b;display:flex;gap:.5rem;align-items:flex-start">
            <i class="fas fa-exclamation-circle" style="flex-shrink:0;margin-top:.1rem"></i>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
        @endif

        <form method="POST" action="{{ route('siswa.pembayaran.upload') }}"
              enctype="multipart/form-data" id="formBayar">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;margin-bottom:.85rem">
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:.32rem">
                        Metode Pembayaran <span style="color:#dc2626">*</span>
                    </label>
                    <select name="kode_metode_bayar" class="form-ctrl @error('kode_metode_bayar') err @enderror" required>
                        <option value="">— Pilih —</option>
                        @foreach($metode as $m)
                        <option value="{{ $m->kode_metode_bayar }}" {{ old('kode_metode_bayar')===$m->kode_metode_bayar?'selected':'' }}>
                            {{ $m->deskripsi_metode_bayar }}
                        </option>
                        @endforeach
                    </select>
                    @error('kode_metode_bayar')<div class="err-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:.32rem">
                        Jumlah Dibayar (Rp) <span style="color:#dc2626">*</span>
                    </label>

                    @php $biayaOtomatis = $periodePpdb?->biaya_pendaftaran ?? null; @endphp

                    @if($biayaOtomatis)
                        {{-- Tampil as read-only, nilai dikirim via hidden input --}}
                        <div class="form-ctrl" style="background:#f1f5f9;color:#0f2744;font-weight:700;cursor:default;display:flex;align-items:center;">
                            Rp {{ number_format($biayaOtomatis, 0, ',', '.') }}
                        </div>
                        <input type="hidden" name="jumlah_bayar" value="{{ (int) $biayaOtomatis }}">
                        <div style="font-size:.7rem;color:#64748b;margin-top:.3rem;">
                            <i class="fas fa-info-circle"></i> Sesuai biaya pendaftaran periode aktif
                        </div>
                    @else
                        <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar') }}"
                            class="form-ctrl @error('jumlah_bayar') err @enderror"
                            placeholder="Contoh: 500000" min="1000" required>
                    @endif

                    @error('jumlah_bayar')<div class="err-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:.32rem">
                        Tanggal Pembayaran <span style="color:#dc2626">*</span>
                    </label>
                    <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                        class="form-ctrl @error('tanggal_bayar') err @enderror"
                        max="{{ date('Y-m-d') }}" required>
                    @error('tanggal_bayar')<div class="err-msg">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:.32rem">
                        Keterangan <span style="font-weight:400;color:#64748b">(opsional)</span>
                    </label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        class="form-ctrl" placeholder="Contoh: Transfer via BRI Mobile">
                </div>
            </div>

            {{-- Upload Area --}}
            <div style="margin-bottom:1.1rem">
                <label style="display:block;font-size:.78rem;font-weight:700;color:#1e293b;margin-bottom:.32rem">
                    Bukti Pembayaran <span style="color:#dc2626">*</span>
                    <span style="font-weight:400;color:#64748b">— JPG, PNG, atau PDF (maks. 3MB)</span>
                </label>
                <div class="upload-area" id="dropZone"
                     ondragover="onDragOver(event)"
                     ondragleave="onDragLeave(event)"
                     ondrop="onDrop(event)"
                     onclick="document.getElementById('fileBukti').click()">
                    <input type="file" id="fileBukti" name="bukti_bayar"
                           accept=".jpg,.jpeg,.png,.pdf"
                           onchange="onFileChange(event)" required style="display:none">
                    <div id="placeholder">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <strong>Klik di sini atau seret file</strong>
                        <span>JPG, PNG, PDF — maksimal 3MB</span>
                    </div>
                    <div id="preview" style="display:none">
                        <i class="fas fa-file-check" style="color:#166534;font-size:1.85rem"></i>
                        <strong id="namaFile" style="color:#166534"></strong>
                        <span id="ukuranFile"></span>
                        <button type="button" onclick="hapusFile(event)"
                                style="background:#fee2e2;color:#991b1b;border:none;border-radius:7px;padding:.3rem .85rem;font-size:.72rem;font-weight:700;cursor:pointer;margin-top:.35rem">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                </div>
                @error('bukti_bayar')
                <div class="err-msg" style="margin-top:.35rem"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                <button type="submit" class="btn-submit-bayar" id="btnSubmit">
                    <i class="fas fa-paper-plane"></i> Kirim Bukti Pembayaran
                </button>
                <a href="{{ route('siswa.dashboard') }}"
                   style="background:#f1f5f9;color:#1e293b;padding:.8rem 1.25rem;border-radius:10px;text-decoration:none;font-size:.88rem;font-weight:600;display:inline-flex;align-items:center;gap:.4rem">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endif

<style>
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.6}}
@media(max-width:500px){[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important;}}
</style>
@endsection

@push('scripts')
<script>
function salin(teks, btn) {
    navigator.clipboard.writeText(teks).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Disalin!';
        btn.style.color = '#166534'; btn.style.borderColor = '#86efac';
        setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; btn.style.borderColor = ''; }, 2000);
    });
}

function tampilPreview(file) {
    document.getElementById('placeholder').style.display = 'none';
    document.getElementById('preview').style.display = 'block';
    document.getElementById('namaFile').textContent = file.name;
    document.getElementById('ukuranFile').textContent = (file.size/1024/1024).toFixed(2) + ' MB';
    document.getElementById('dropZone').style.borderColor = '#86efac';
    document.getElementById('dropZone').style.background = '#f0fdf4';
    document.getElementById('btnSubmit').disabled = false;
}

function onFileChange(e) {
    if (e.target.files[0]) tampilPreview(e.target.files[0]);
}

function hapusFile(e) {
    e.stopPropagation();
    document.getElementById('fileBukti').value = '';
    document.getElementById('preview').style.display = 'none';
    document.getElementById('placeholder').style.display = 'block';
    document.getElementById('dropZone').style.borderColor = '#e2e8f0';
    document.getElementById('dropZone').style.background = '#f8fafc';
    document.getElementById('btnSubmit').disabled = true;
}

function onDragOver(e) { e.preventDefault(); document.getElementById('dropZone').style.borderColor='#1a4a8a'; }
function onDragLeave(e) { document.getElementById('dropZone').style.borderColor='#e2e8f0'; }
function onDrop(e) {
    e.preventDefault();
    onDragLeave(e);
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const dt = new DataTransfer(); dt.items.add(file);
    document.getElementById('fileBukti').files = dt.files;
    tampilPreview(file);
}

// Disable tombol saat submit
document.getElementById('formBayar')?.addEventListener('submit', function() {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengunggah...';
});

// Mulai tombol submit nonaktif sampai file dipilih
document.getElementById('btnSubmit').disabled = true;
</script>
@endpush
