@extends('emails.layout')
@section('title', 'Status Pembayaran Diperbarui')

@section('content')
@php
    $terverifikasi = $pembayaran->status_pembayaran === 'Terverifikasi';
    $ditolak       = $pembayaran->status_pembayaran === 'Ditolak';
    $warna         = $terverifikasi ? '#166534' : ($ditolak ? '#991b1b' : '#92400e');
    $bg            = $terverifikasi ? '#dcfce7' : ($ditolak ? '#fee2e2' : '#fef3c7');
    $ikon          = $terverifikasi ? '✓' : ($ditolak ? '✕' : 'ℹ');
@endphp

<div style="width:44px;height:44px;background:{{ $bg }};border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
    <span style="color:{{ $warna }};font-size:18px;font-weight:800;">{{ $ikon }}</span>
</div>

<h1 style="font-size:16px;font-weight:800;color:#0f2744;margin:0 0 8px;">
    @if($terverifikasi) Pembayaran Anda Telah Terverifikasi
    @elseif($ditolak) Pembayaran Anda Ditolak
    @else Status Pembayaran Diperbarui
    @endif
</h1>

<p style="font-size:13.5px;color:#475569;line-height:1.6;margin:0 0 20px;">
    Halo {{ $pembayaran->siswa->nama_lengkap ?? '' }}, status pembayaran pendaftaran Anda
    ({{ $pembayaran->siswa->nomor_pendaftaran ?? '-' }}) telah diperbarui oleh panitia PPDB menjadi
    <strong style="color:{{ $warna }}">{{ $pembayaran->status_pembayaran }}</strong>.
    @if($ditolak && $pembayaran->keterangan)
        <br><br>Catatan dari panitia: <em>{{ $pembayaran->keterangan }}</em>
    @endif
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:2px;margin-bottom:22px;">
    @php
        $rows = [
            'Metode Pembayaran' => $pembayaran->metodePembayaran->deskripsi_metode_bayar ?? $pembayaran->kode_metode_bayar,
            'Jumlah'            => 'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.'),
            'Tanggal Bayar'     => optional($pembayaran->tanggal_bayar)->format('d F Y'),
        ];
    @endphp
    @foreach($rows as $label => $value)
    <tr>
        <td style="padding:9px 16px;font-size:12.5px;color:#64748b;">{{ $label }}</td>
        <td style="padding:9px 16px;font-size:12.5px;font-weight:700;color:#0f2744;text-align:right;">{{ $value }}</td>
    </tr>
    @endforeach
</table>

<div style="text-align:center;">
    <a href="{{ route('siswa.pembayaran') }}"
       style="display:inline-block;background:#0f2744;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 26px;border-radius:9px;">
        @if($terverifikasi) Lihat & Unduh Kwitansi @else Lihat Detail & Unggah Ulang @endif
    </a>
</div>
@endsection
