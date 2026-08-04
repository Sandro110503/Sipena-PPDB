@extends('emails.layout')
@section('title', 'Bukti Pembayaran Baru')

@section('content')
<div style="width:44px;height:44px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
    <span style="color:#92400e;font-size:18px;">💳</span>
</div>

<h1 style="font-size:16px;font-weight:800;color:#0f2744;margin:0 0 8px;">Bukti Pembayaran Baru Diunggah</h1>
<p style="font-size:13.5px;color:#475569;line-height:1.6;margin:0 0 20px;">
    Seorang calon siswa baru saja mengunggah bukti pembayaran dan sedang menunggu verifikasi Anda.
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:2px;margin-bottom:22px;">
    @php
        $rows = [
            'Nama Siswa'        => $pembayaran->siswa->nama_lengkap ?? '-',
            'No. Pendaftaran'   => $pembayaran->siswa->nomor_pendaftaran ?? '-',
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
    <a href="{{ route('admin.pembayaran.show', $pembayaran) }}"
       style="display:inline-block;background:#0f2744;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 26px;border-radius:9px;">
        Verifikasi Sekarang
    </a>
</div>
@endsection
