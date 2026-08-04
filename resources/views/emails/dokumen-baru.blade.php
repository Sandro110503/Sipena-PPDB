@extends('emails.layout')
@section('title', 'Dokumen Baru Diunggah')

@section('content')
@php $labelJenis = \App\Services\BerkasPersyaratanService::jenisDokumen()[$dokumen->jenis_dokumen]['label'] ?? $dokumen->jenis_dokumen; @endphp

<div style="width:44px;height:44px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
    <span style="color:#1a4a8a;font-size:18px;">📄</span>
</div>

<h1 style="font-size:16px;font-weight:800;color:#0f2744;margin:0 0 8px;">Dokumen Baru Diunggah</h1>
<p style="font-size:13.5px;color:#475569;line-height:1.6;margin:0 0 20px;">
    Seorang calon siswa baru saja mengunggah dokumen persyaratan dan sedang menunggu verifikasi Anda.
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:2px;margin-bottom:22px;">
    @php
        $rows = [
            'Nama Siswa'      => $dokumen->siswa->nama_lengkap ?? '-',
            'No. Pendaftaran' => $dokumen->siswa->nomor_pendaftaran ?? '-',
            'Jenis Dokumen'   => $labelJenis,
            'Tanggal Unggah'  => optional($dokumen->tanggal_unggah)->format('d F Y'),
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
    <a href="{{ route('admin.siswa.show', $dokumen->id_siswa) }}"
       style="display:inline-block;background:#0f2744;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;padding:12px 26px;border-radius:9px;">
        Verifikasi Sekarang
    </a>
</div>
@endsection
