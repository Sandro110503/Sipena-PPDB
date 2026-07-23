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
@endsection
