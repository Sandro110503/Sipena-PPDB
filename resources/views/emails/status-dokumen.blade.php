@extends('emails.layout')
@section('title', 'Status Dokumen Diperbarui')

@section('content')
@php
    $labelJenis    = \App\Services\BerkasPersyaratanService::jenisDokumen()[$dokumen->jenis_dokumen]['label'] ?? $dokumen->jenis_dokumen;
    $terverifikasi = $dokumen->status_verifikasi === 'Terverifikasi';
    $ditolak       = $dokumen->status_verifikasi === 'Ditolak';
    $warna         = $terverifikasi ? '#166534' : ($ditolak ? '#991b1b' : '#92400e');
    $bg            = $terverifikasi ? '#dcfce7' : ($ditolak ? '#fee2e2' : '#fef3c7');
    $ikon          = $terverifikasi ? '✓' : ($ditolak ? '✕' : 'ℹ');
@endphp

<div style="width:44px;height:44px;background:{{ $bg }};border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
    <span style="color:{{ $warna }};font-size:18px;font-weight:800;">{{ $ikon }}</span>
</div>

<h1 style="font-size:16px;font-weight:800;color:#0f2744;margin:0 0 8px;">
    @if($terverifikasi) {{ $labelJenis }} Anda Telah Terverifikasi
    @elseif($ditolak) {{ $labelJenis }} Anda Ditolak
    @else Status {{ $labelJenis }} Diperbarui
    @endif
</h1>

<p style="font-size:13.5px;color:#475569;line-height:1.6;margin:0 0 20px;">
    Halo {{ $dokumen->siswa->nama_lengkap ?? '' }}, dokumen <strong>{{ $labelJenis }}</strong> yang Anda unggah
    ({{ $dokumen->siswa->nomor_pendaftaran ?? '-' }}) telah diperiksa oleh panitia PPDB dan berstatus
    <strong style="color:{{ $warna }}">{{ $dokumen->status_verifikasi }}</strong>.
    @if($ditolak && $dokumen->keterangan)
        <br><br>Catatan dari panitia: <em>{{ $dokumen->keterangan }}</em>
    @endif
</p>

<div style="text-align:center;">
    <a href="{{ route('siswa.berkas') }}"
       style="display:inline-block;background:#0f2744;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 26px;border-radius:9px;">
        @if($ditolak) Unggah Ulang Sekarang @else Lihat Berkas Saya @endif
    </a>
</div>
@endsection
