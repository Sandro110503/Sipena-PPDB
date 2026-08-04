@extends('siswa.layout')
@section('title','Berkas Persyaratan')

@push('styles')
<style>
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
{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.6rem;margin-bottom:1.1rem;font-size:.82rem">
    <a href="{{ route('siswa.dashboard') }}" style="color:#64748b;text-decoration:none;display:flex;align-items:center;gap:.3rem">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
    <span style="color:#e2e8f0">/</span>
    <span style="color:#0f2744;font-weight:600">Berkas Persyaratan</span>
</div>

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0f2744,#1a4a8a);color:#fff;border-radius:14px;padding:1.35rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
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
            <form method="POST" action="{{ route('siswa.berkas.hapus', $kode) }}" onsubmit="return confirm('Hapus {{ $d['label'] }}?')">
                @csrf @method('DELETE')
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
</div>
@endforeach

<div style="margin-top:1.25rem">
    <a href="{{ route('siswa.dashboard') }}" style="color:#64748b;text-decoration:none;font-size:.82rem;display:inline-flex;align-items:center;gap:.4rem">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>
@endsection
