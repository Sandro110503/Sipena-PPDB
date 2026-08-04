<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }

    .kwitansi { border: 1.5px solid #0f2744; border-radius: 4px; padding: 0; }

    .header { text-align: center; padding: 16px 20px 14px; border-bottom: 3px solid #0f2744; background: #f8fafc; }
    .header h1 { font-size: 15px; font-weight: bold; color: #0f2744; letter-spacing: .5px; }
    .header p { font-size: 9px; color: #64748b; margin-top: 2px; }

    .title-row { text-align: center; padding: 14px 0 4px; }
    .title-row .label { font-size: 9px; color: #64748b; letter-spacing: 1px; text-transform: uppercase; }
    .title-row .kode { font-size: 13px; font-weight: bold; color: #0f2744; margin-top: 2px; }

    .status-badge { display: inline-block; margin: 8px auto 0; padding: 4px 16px; border-radius: 999px;
        background: #dcfce7; color: #166534; font-size: 10px; font-weight: bold; }

    .content { padding: 14px 24px 6px; }
    table.detail { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.detail td { padding: 6px 4px; font-size: 10.5px; border-bottom: 1px dashed #e2e8f0; }
    table.detail td.label { color: #64748b; width: 42%; }
    table.detail td.value { font-weight: bold; color: #0f2744; text-align: right; }

    .jumlah-box { margin: 16px 24px; background: #0f2744; border-radius: 6px; padding: 12px 16px; text-align: center; }
    .jumlah-box .lbl { font-size: 8.5px; color: #cbd5e1; letter-spacing: 1px; text-transform: uppercase; }
    .jumlah-box .val { font-size: 19px; font-weight: bold; color: #ffffff; margin-top: 3px; }

    .ttd { padding: 6px 24px 20px; display: flex; justify-content: flex-end; }
    .ttd .box { text-align: center; font-size: 10px; color: #1e293b; width: 160px; }
    .ttd .box .garis { margin-top: 40px; border-top: 1px solid #94a3b8; padding-top: 4px; font-weight: bold; }

    .footer-note { padding: 10px 24px 18px; font-size: 8.5px; color: #94a3b8; text-align: center; line-height: 1.5; }
</style>
</head>
<body>
<div class="kwitansi">

    <div class="header">
        <h1>KWITANSI PEMBAYARAN</h1>
        <p>SIPENA — Sistem Informasi PPDB SMK Yadika 8 Jatimulya</p>
    </div>

    <div class="title-row">
        <div class="label">Nomor Kwitansi</div>
        <div class="kode">KW/{{ str_pad($pembayaran->id_pembayaran, 5, '0', STR_PAD_LEFT) }}/{{ $pembayaran->tanggal_bayar?->format('Y') }}</div>
        <div class="status-badge">✓ Terverifikasi</div>
    </div>

    <div class="content">
        <table class="detail">
            <tr><td class="label">Nama Siswa</td><td class="value">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</td></tr>
            <tr><td class="label">No. Pendaftaran</td><td class="value">{{ $pembayaran->siswa->nomor_pendaftaran ?? '-' }}</td></tr>
            @if($pembayaran->siswa?->pendaftaranJurusan?->first())
            <tr><td class="label">Jurusan Pilihan</td><td class="value">{{ $pembayaran->siswa->pendaftaranJurusan->first()->jurusan->nama_jurusan ?? '-' }}</td></tr>
            @endif
            <tr><td class="label">Metode Pembayaran</td><td class="value">{{ $pembayaran->metodePembayaran->deskripsi_metode_bayar ?? $pembayaran->kode_metode_bayar }}</td></tr>
            <tr><td class="label">Tanggal Pembayaran</td><td class="value">{{ $pembayaran->tanggal_bayar?->format('d F Y') }}</td></tr>
            <tr><td class="label">Diverifikasi Oleh</td><td class="value">{{ $pembayaran->verifikator->nama ?? 'Panitia PPDB' }}</td></tr>
            <tr><td class="label">Tanggal Verifikasi</td><td class="value">{{ $pembayaran->updated_at?->format('d F Y, H:i') }} WIB</td></tr>
        </table>
    </div>

    <div class="jumlah-box">
        <div class="lbl">Jumlah Dibayar</div>
        <div class="val">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</div>
    </div>

    <div class="ttd">
        <div class="box">
            <div>Jatimulya, {{ now()->format('d F Y') }}</div>
            <div class="garis">{{ $pembayaran->verifikator->nama ?? 'Panitia PPDB' }}</div>
        </div>
    </div>

    <div class="footer-note">
        Dokumen ini dibuat secara elektronik oleh sistem SIPENA dan sah tanpa tanda tangan basah.<br>
        Dicetak pada {{ now()->format('d F Y, H:i') }} WIB.
    </div>

</div>
</body>
</html>
