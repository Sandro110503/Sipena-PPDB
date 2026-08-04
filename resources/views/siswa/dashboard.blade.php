@extends('siswa.layout')
@section('title','Dashboard Siswa')

@push('styles')
<style>
.page-title{margin-bottom:1.1rem;}
.page-title h1{font-size:1.2rem;font-weight:800;color:#0f2744;}
.page-title p{color:#64748b;font-size:.82rem;margin-top:.2rem;}

/* Status banner */
.status-banner{border:2px solid;border-radius:14px;padding:1rem 1.1rem;margin-bottom:1rem;display:flex;align-items:center;gap:.85rem;flex-wrap:wrap;}
.status-banner-icon{font-size:1.75rem;flex-shrink:0;}
.status-banner-text{flex:1;min-width:140px;}
.status-banner-text strong{display:block;font-size:.95rem;font-weight:800;line-height:1.3;}
.status-banner-text span{font-size:.78rem;opacity:.85;margin-top:.15rem;display:block;}

/* Pembayaran banner */
.bayar-banner{border-radius:12px;padding:.9rem 1.1rem;margin-bottom:1.1rem;display:flex;align-items:center;gap:.85rem;flex-wrap:wrap;border:1.5px solid;}
.bayar-banner-icon{width:42px;height:42px;border-radius:10px;display:grid;place-items:center;font-size:1.1rem;flex-shrink:0;}

/* Tombol bayar */
.btn-bayar{display:inline-flex;align-items:center;gap:.45rem;padding:.65rem 1.2rem;border-radius:10px;font-weight:700;text-decoration:none;font-size:.84rem;flex-shrink:0;min-height:42px;touch-action:manipulation;border:none;cursor:pointer;font-family:inherit;}

/* Info rows */
.info-row{display:flex;justify-content:space-between;align-items:flex-start;padding:.5rem 0;border-bottom:1px solid #f1f5f9;font-size:.84rem;gap:.75rem;}
.info-row:last-child{border-bottom:none;}
.info-row .lbl{color:#64748b;flex-shrink:0;}
.info-row .val{font-weight:600;text-align:right;word-break:break-word;}

/* Jurusan */
.jurusan-item{display:flex;align-items:center;justify-content:space-between;padding:.65rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;}
.jurusan-num{width:32px;height:32px;border-radius:8px;display:grid;place-items:center;font-weight:800;font-size:.65rem;flex-shrink:0;}

/* Tabel pembayaran */
.pay-table{width:100%;border-collapse:collapse;font-size:.8rem;}
.pay-table th{padding:.55rem .75rem;background:#f8fafc;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#64748b;border-bottom:1px solid #e2e8f0;white-space:nowrap;}
.pay-table td{padding:.6rem .75rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;}
.pay-table tr:last-child td{border-bottom:none;}
.st-badge{font-size:.65rem;font-weight:700;padding:.22rem .65rem;border-radius:999px;white-space:nowrap;}
.empty-state{text-align:center;padding:2rem 1rem;color:#94a3b8;font-size:.85rem;}
.empty-state i{font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3;}

/* Berkas persyaratan */
.berkas-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:1.25rem;margin-bottom:1rem;transition:.2s}
.berkas-card.ada{border-color:#86efac;background:#f0fdf4}
.berkas-icon{width:46px;height:46px;border-radius:11px;display:grid;place-items:center;font-size:1.2rem;flex-shrink:0;background:#eff6ff;color:#1a4a8a}
.berkas-card.ada .berkas-icon{background:#dcfce7;color:#166534}
.upload-area{display:block;box-sizing:border-box;width:100%;border:2px dashed #e2e8f0;border-radius:12px;padding:1.25rem;text-align:center;cursor:pointer;transition:.2s;background:#f8fafc;position:relative;overflow:hidden}
.upload-area:hover{border-color:#1a4a8a;background:#eff6ff}
.upload-area input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer}
.upload-area i{font-size:1.4rem;color:#94a3b8;display:block;margin-bottom:.35rem}
.upload-area span{font-size:.76rem;color:#64748b;display:block}
.btn-hapus-berkas{background:#fff;border:1px solid #fecaca;color:#dc2626;padding:.4rem .8rem;border-radius:8px;font-size:.72rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:.35rem}
.btn-hapus-berkas:hover{background:#fef2f2}
.btn-lihat-berkas{background:#0f2744;color:#fff;padding:.4rem .8rem;border-radius:8px;font-size:.72rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem}
</style>
@endpush

@section('content')
<div class="page-title">
    <h1>Selamat datang, {{ $siswa->nama_depan }}!</h1>
    <p>Pantau status pendaftaran dan lakukan pembayaran daftar ulang di sini.</p>
</div>

{{-- ===== STATUS PENERIMAAN ===== --}}
@php $cfg = match($siswa->status_penerimaan){
    'Diterima'=>['bg'=>'#dcfce7','border'=>'#86efac','color'=>'#166534','icon'=>'fas fa-check-circle','title'=>'Selamat! Anda Diterima'],
    'Ditolak' =>['bg'=>'#fee2e2','border'=>'#fca5a5','color'=>'#991b1b','icon'=>'fas fa-times-circle','title'=>'Maaf, Tidak Diterima'],
    'Cadangan'=>['bg'=>'#dbeafe','border'=>'#93c5fd','color'=>'#1e40af','icon'=>'fas fa-clock','title'=>'Status: Cadangan'],
    default   =>['bg'=>'#fef3c7','border'=>'#fcd34d','color'=>'#92400e','icon'=>'fas fa-hourglass-half','title'=>'Pendaftaran Sedang Diproses'],
}; @endphp

<div class="status-banner" style="background:{{ $cfg['bg'] }};border-color:{{ $cfg['border'] }}">
    <i class="{{ $cfg['icon'] }} status-banner-icon" style="color:{{ $cfg['color'] }}"></i>
    <div class="status-banner-text" style="color:{{ $cfg['color'] }}">
        <strong>{{ $cfg['title'] }}</strong>
        <span>
            @if($siswa->status_penerimaan === 'Menunggu')
                Pendaftaran Anda sedang diverifikasi.
            @elseif($siswa->status_penerimaan === 'Diterima')
                {{ $siswa->tanggal_diterima ? 'Diterima pada '.$siswa->tanggal_diterima->format('d M Y') : 'Silakan lanjutkan proses pembayaran.' }}
            @elseif($siswa->status_penerimaan === 'Ditolak')
                Terima kasih sudah mendaftar di PPDB SMK kami.
            @else
                Anda masuk daftar cadangan. Pantau terus perkembangan status.
            @endif
        </span>
    </div>
</div>

{{-- ===== STATUS PEMBAYARAN ===== --}}
@if($pembayaranTerverifikasi)
{{-- LUNAS --}}
<div class="bayar-banner" style="background:#dcfce7;border-color:#86efac">
    <div class="bayar-banner-icon" style="background:#bbf7d0;color:#166534">
        <i class="fas fa-check-double"></i>
    </div>
    <div style="flex:1">
        <div style="font-weight:700;color:#166534;font-size:.88rem">Pembayaran Terverifikasi</div>
        <div style="font-size:.75rem;color:#166534;opacity:.85;margin-top:.15rem">
            Rp {{ number_format($pembayaranTerverifikasi->jumlah_bayar,0,',','.') }}
            &bull; {{ $pembayaranTerverifikasi->tanggal_bayar->format('d M Y') }}
            &bull; {{ $pembayaranTerverifikasi->metodePembayaran->deskripsi_metode_bayar }}
        </div>
    </div>
    <span style="background:#166534;color:#fff;padding:.4rem .9rem;border-radius:8px;font-size:.75rem;font-weight:700;flex-shrink:0">
        <i class="fas fa-lock"></i> LUNAS
    </span>
</div>

@elseif($pembayaranMenunggu)
{{-- MENUNGGU VERIFIKASI --}}
<div class="bayar-banner" style="background:#fef3c7;border-color:#fcd34d">
    <div class="bayar-banner-icon" style="background:#fde68a;color:#92400e">
        <i class="fas fa-clock"></i>
    </div>
    <div style="flex:1">
        <div style="font-weight:700;color:#92400e;font-size:.88rem">Menunggu Verifikasi Pembayaran</div>
        <div style="font-size:.75rem;color:#92400e;opacity:.85;margin-top:.15rem">
            Bukti pembayaran Rp {{ number_format($pembayaranMenunggu->jumlah_bayar,0,',','.') }}
            sudah diunggah pada {{ $pembayaranMenunggu->created_at->format('d M Y, H:i') }}.
            Harap tunggu konfirmasi administrasi.
        </div>
    </div>
    <a href="{{ route('siswa.pembayaran') }}"
       style="background:#92400e;color:#fff;padding:.4rem .9rem;border-radius:8px;font-size:.75rem;font-weight:700;text-decoration:none;flex-shrink:0">
        <i class="fas fa-eye"></i> Detail
    </a>
</div>

@elseif($pembayaranDitolak)
{{-- DITOLAK — perlu upload ulang --}}
<div class="bayar-banner" style="background:#fee2e2;border-color:#fca5a5">
    <div class="bayar-banner-icon" style="background:#fecaca;color:#991b1b">
        <i class="fas fa-times-circle"></i>
    </div>
    <div style="flex:1">
        <div style="font-weight:700;color:#991b1b;font-size:.88rem">Pembayaran Ditolak</div>
        <div style="font-size:.75rem;color:#991b1b;opacity:.85;margin-top:.15rem">
            @if($pembayaranDitolak->keterangan)
                Alasan: {{ $pembayaranDitolak->keterangan }}
            @else
                Bukti pembayaran tidak valid. Silakan upload ulang.
            @endif
        </div>
    </div>
    <a href="{{ route('siswa.pembayaran') }}" class="btn-bayar"
       style="background:#991b1b;color:#fff">
        <i class="fas fa-redo"></i> Upload Ulang
    </a>
</div>

@else
{{-- BELUM BAYAR — tampilkan tombol selalu, tidak peduli status penerimaan --}}
<div class="bayar-banner" style="background:#eff6ff;border-color:#93c5fd">
    <div class="bayar-banner-icon" style="background:#dbeafe;color:#1a4a8a">
        <i class="fas fa-credit-card"></i>
    </div>
    <div style="flex:1">
        <div style="font-weight:700;color:#1e40af;font-size:.88rem">Pembayaran Pendaftaran</div>
        <div style="font-size:.75rem;color:#1e40af;opacity:.85;margin-top:.15rem">
            Silakan lakukan pembayaran dan upload bukti transfer untuk melengkapi pendaftaran.
        </div>
    </div>
    <a href="{{ route('siswa.pembayaran') }}" class="btn-bayar"
       style="background:#1a4a8a;color:#fff">
        <i class="fas fa-upload"></i> Bayar Sekarang
    </a>
</div>
@endif

{{-- ===== GRID INFO ===== --}}
<div class="grid-2" style="margin-bottom:1rem">
    {{-- Data Pendaftaran --}}
    <div class="card">
        <div class="card-header"><i class="fas fa-user"></i> Data Pendaftaran</div>
        <div class="card-body">
            @php $info = [
                'No. Pendaftaran' => $siswa->nomor_pendaftaran,
                'NISN'            => $siswa->nisn,
                'Nama Lengkap'    => $siswa->nama_lengkap,
                'Asal Sekolah'    => $siswa->asal_sekolah,
                'Tgl. Daftar'     => $siswa->tanggal_daftar?->format('d M Y'),
            ]; @endphp
            @foreach($info as $l => $v)
            <div class="info-row">
                <span class="lbl">{{ $l }}</span>
                <span class="val">{{ $v ?? '-' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Jurusan --}}
    <div class="card">
        <div class="card-header"><i class="fas fa-school"></i> Jurusan yang Dipilih</div>
        <div class="card-body">
            @forelse($siswa->pendaftaranJurusan->sortBy('urutan_pilihan') as $pj)
            @php
                $jc = match($pj->jurusan->kode_jurusan){
                    'AKL'  =>['bg'=>'#ede9fe','c'=>'#5b21b6'],
                    'TJKT' =>['bg'=>'#dbeafe','c'=>'#1e40af'],
                    'MPLB' =>['bg'=>'#fce7f3','c'=>'#9d174d'],
                    default=>['bg'=>'#f1f5f9','c'=>'#475569'],
                };
                $sc = match($pj->status){
                    'Diterima'=>'background:#dcfce7;color:#166534',
                    'Ditolak' =>'background:#fee2e2;color:#991b1b',
                    default   =>'background:#f1f5f9;color:#64748b',
                };
            @endphp
            <div class="jurusan-item">
                <div class="jurusan-num" style="background:{{ $jc['bg'] }};color:{{ $jc['c'] }}">
                    <i class="fas fa-school" style="font-size:.65rem"></i>
                </div>
                <div style="flex:1;min-width:0;margin:0 .65rem">
                    <div style="font-weight:700;font-size:.85rem;color:#0f2744">{{ $pj->jurusan->nama_jurusan }}</div>
                    <div style="font-size:.72rem;color:#64748b">{{ $pj->jurusan->kode_jurusan }}</div>
                </div>
                <span class="st-badge" style="{{ $sc }}">{{ $pj->status }}</span>
            </div>
            @empty
            <div style="text-align:center;color:#94a3b8;padding:1rem;font-size:.82rem">
                Belum ada pilihan jurusan.
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ===== RIWAYAT PEMBAYARAN ===== --}}
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-money-bill-wave"></i> Riwayat Pembayaran</span>
        @if($bisaUploadBayar)
        <a href="{{ route('siswa.pembayaran') }}"
           style="background:#0f2744;color:#fff;padding:.32rem .8rem;border-radius:8px;font-size:.75rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem">
            <i class="fas fa-plus"></i> Upload Bukti
        </a>
        @endif
    </div>

    @if($siswa->pembayaran->isEmpty())
    <div class="empty-state">
        <i class="fas fa-receipt"></i>
        Belum ada riwayat pembayaran.
        <div style="margin-top:.75rem">
            <a href="{{ route('siswa.pembayaran') }}"
               style="color:#1a4a8a;font-weight:600;text-decoration:none;font-size:.82rem">
                Upload bukti pembayaran sekarang →
            </a>
        </div>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="pay-table">
            <thead>
                <tr>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa->pembayaran->sortByDesc('tanggal_bayar') as $b)
                @php $sc = match($b->status_pembayaran){
                    'Terverifikasi'      =>'background:#dcfce7;color:#166534',
                    'Ditolak'            =>'background:#fee2e2;color:#991b1b',
                    default              =>'background:#fef3c7;color:#92400e',
                }; @endphp
                <tr>
                    <td>{{ $b->metodePembayaran->deskripsi_metode_bayar }}</td>
                    <td style="font-weight:700;white-space:nowrap">
                        Rp {{ number_format($b->jumlah_bayar,0,',','.') }}
                    </td>
                    <td style="color:#64748b;white-space:nowrap">
                        {{ $b->tanggal_bayar->format('d/m/Y') }}
                    </td>
                    <td>
                        <span class="st-badge" style="{{ $sc }}">
                            {{ $b->status_pembayaran }}
                        </span>
                    </td>
                    <td>
                        @if($b->bukti_bayar)
                        <a href="{{ Storage::url($b->bukti_bayar) }}" target="_blank"
                           style="color:#1a4a8a;font-size:.78rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        @else
                        <span style="color:#94a3b8">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ===== UNGGAH BERKAS PERSYARATAN ===== --}}
<div style="background:linear-gradient(135deg,#0f2744,#1a4a8a);color:#fff;border-radius:14px;padding:1.35rem 1.5rem;margin:1rem 0 1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <div style="width:50px;height:50px;background:rgba(255,255,255,.15);border-radius:12px;display:grid;place-items:center;font-size:1.3rem;flex-shrink:0">
        <i class="fas fa-folder-open"></i>
    </div>
    <div style="flex:1">
        <div style="font-size:.95rem;font-weight:800">Unggah Berkas Persyaratan</div>
        <div style="font-size:.78rem;opacity:.8;margin-top:.2rem">{{ $siswa->nama_lengkap }} — {{ $siswa->nomor_pendaftaran }}</div>
    </div>
    <div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:9px;padding:.55rem .9rem;font-size:.8rem;text-align:center;flex-shrink:0">
        <div style="opacity:.65;font-size:.65rem;margin-bottom:.1rem">KELENGKAPAN</div>
        <div style="font-weight:800">{{ $ringkasan['lengkap'] }} / {{ $ringkasan['total'] }}</div>
    </div>
</div>

@if($ringkasan['lengkap'] === $ringkasan['total'])
<div style="background:#dcfce7;border:1.5px solid #86efac;border-radius:12px;padding:.9rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem">
    <i class="fas fa-check-circle" style="color:#166534;font-size:1.1rem"></i>
    <span style="color:#166534;font-size:.85rem;font-weight:600">Semua berkas persyaratan sudah lengkap.</span>
</div>
@else
<div style="background:#fef3c7;border:1.5px solid #fde68a;border-radius:12px;padding:.9rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.6rem">
    <i class="fas fa-exclamation-circle" style="color:#92400e;font-size:1.1rem"></i>
    <span style="color:#92400e;font-size:.85rem;font-weight:600">Masih ada {{ $ringkasan['total'] - $ringkasan['lengkap'] }} berkas yang belum diunggah.</span>
</div>
@endif

{{-- Daftar Berkas --}}
@foreach($status as $kode => $d)
@php
    $badgeWarna = match($d['status']) {
        'Terverifikasi' => ['bg' => '#dcfce7', 'fg' => '#166534', 'label' => 'Terverifikasi'],
        'Ditolak'       => ['bg' => '#fee2e2', 'fg' => '#991b1b', 'label' => 'Ditolak'],
        'Menunggu Verifikasi' => ['bg' => '#fef3c7', 'fg' => '#92400e', 'label' => 'Menunggu Verifikasi'],
        default         => ['bg' => '#f1f5f9', 'fg' => '#94a3b8', 'label' => 'Belum Diunggah'],
    };
@endphp
<div class="berkas-card {{ $d['ada'] ? 'ada' : '' }}">
    <div style="display:flex;align-items:center;gap:.9rem;flex-wrap:wrap">
        <div class="berkas-icon"><i class="fas {{ $d['icon'] }}"></i></div>
        <div style="flex:1;min-width:180px">
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                <div style="font-weight:700;color:#0f2744;font-size:.9rem">{{ $d['label'] }}</div>
                <span style="background:{{ $badgeWarna['bg'] }};color:{{ $badgeWarna['fg'] }};font-size:.68rem;font-weight:700;padding:.15rem .55rem;border-radius:999px">{{ $badgeWarna['label'] }}</span>
            </div>
            @if($d['ada'])
                <div style="font-size:.74rem;color:#64748b;margin-top:.25rem">
                    {{ $d['ekstensi'] }}, {{ $d['ukuran_kb'] }} KB — diunggah {{ \Carbon\Carbon::parse($d['tanggal_unggah'])->translatedFormat('d F Y') }}
                </div>
                @if($d['status'] === 'Terverifikasi' && $d['verifikator'])
                    <div style="font-size:.72rem;color:#166534;margin-top:.15rem"><i class="fas fa-check-circle"></i> Diverifikasi oleh {{ $d['verifikator'] }}</div>
                @endif
                @if($d['status'] === 'Ditolak' && $d['keterangan'])
                    <div style="font-size:.75rem;color:#991b1b;margin-top:.35rem;background:#fef2f2;border-radius:8px;padding:.5rem .7rem">
                        <i class="fas fa-info-circle"></i> Catatan panitia: {{ $d['keterangan'] }}
                    </div>
                @endif
            @else
                <div style="font-size:.74rem;color:#94a3b8;margin-top:.15rem">Belum diunggah</div>
            @endif
        </div>

        @if($d['ada'])
        <div style="display:flex;gap:.4rem;flex-shrink:0">
            <a href="{{ $d['url'] }}" target="_blank" class="btn-lihat-berkas"><i class="fas fa-eye"></i> Lihat</a>
            @if($d['status'] !== 'Terverifikasi')
            <form method="POST"
                action="{{ route('siswa.berkas.hapus', $kode) }}"
                class="form-delete">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn-hapus-berkas"><i class="fas fa-trash"></i> Hapus</button>
            </form>
            @endif
        </div>
        @endif
    </div>

    @if($d['status'] !== 'Terverifikasi')
    <form method="POST" action="{{ route('siswa.berkas.upload', $kode) }}" enctype="multipart/form-data" style="margin-top:.9rem">
        @csrf
        <label class="upload-area">
            <input type="file" name="berkas" accept=".{{ str_replace(',', ',.', $d['mimes']) }}" onchange="this.form.submit()">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>{{ $d['ada'] ? 'Ketuk untuk unggah ulang' : 'Ketuk untuk pilih file' }} — {{ strtoupper(str_replace(',', ' / ', $d['mimes'])) }}, maks {{ round($d['max_kb']/1024,1) }} MB</span>
        </label>
    </form>
    @endif
<script>
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
</div>
@endforeach
@endsection
