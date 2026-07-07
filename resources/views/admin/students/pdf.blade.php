<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; }
    .header { text-align: center; padding: 12px 0 10px; border-bottom: 3px solid #0f2744; margin-bottom: 12px; }
    .header h1 { font-size: 15px; font-weight: bold; color: #0f2744; }
    .header p { font-size: 9px; color: #64748b; margin-top: 2px; }
    .meta { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 9px; color: #64748b; }
    table { width: 100%; border-collapse: collapse; font-size: 9px; }
    th { background: #0f2744; color: #fff; padding: 6px 7px; text-align: left; font-weight: bold; }
    td { padding: 5px 7px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 8px; font-weight: bold; }
    .menunggu  { background: #fef3c7; color: #92400e; }
    .diterima  { background: #dcfce7; color: #166534; }
    .ditolak   { background: #fee2e2; color: #991b1b; }
    .cadangan  { background: #dbeafe; color: #1e40af; }
    .footer { margin-top: 14px; text-align: right; font-size: 8px; color: #94a3b8; }
    .summary { display: flex; gap: 10px; margin-bottom: 12px; }
    .sum-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; text-align: center; }
    .sum-val { font-size: 18px; font-weight: bold; color: #0f2744; }
    .sum-lbl { font-size: 8px; color: #64748b; }
</style>
</head>
<body>
<div class="header">
    <h1>LAPORAN DATA CALON SISWA PPDB SMK</h1>
    <p>Tahun Pelajaran {{ date('Y') }}/{{ date('Y')+1 }}
        @if($jurusanFilter) &bull; Jurusan: {{ $jurusanFilter->nama_jurusan }} @endif
    </p>
    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

<div class="summary">
    <div class="sum-box"><div class="sum-val">{{ $siswa->count() }}</div><div class="sum-lbl">Total Pendaftar</div></div>
    <div class="sum-box"><div class="sum-val">{{ $siswa->where('status_penerimaan','Proses')->count() }}</div><div class="sum-lbl">Proses</div></div>
    <div class="sum-box"><div class="sum-val">{{ $siswa->where('status_penerimaan','Diterima')->count() }}</div><div class="sum-lbl">Sukses</div></div>
    <div class="sum-box"><div class="sum-val">{{ $siswa->where('status_penerimaan','Ditolak')->count() }}</div><div class="sum-lbl">Ditolak</div></div>
    <div class="sum-box"><div class="sum-val">{{ $siswa->where('status_penerimaan','Cadangan')->count() }}</div><div class="sum-lbl">Cadangan</div></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:24px">No</th>
            <th>No. Pendaftaran</th>
            <th>Nama Lengkap</th>
            <th>NISN</th>
            <th>L/P</th>
            <th>Asal Sekolah</th>
            <th>Jurusan</th>
            <th>Alamat Siswa</th>
            <th>Alamat Orang Tua</th>
            <th>Status</th>
            <th>Tgl Daftar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($siswa as $i => $s)
        @php
            $p1 = $s->pendaftaranJurusan->where('urutan_pilihan',1)->first();
            $st = strtolower($s->status_penerimaan);

            $alamatSiswaRecord = $s->alamatCalonSiswa->first();
            $wali              = $s->relasiSiswa->first()?->wali;

            $alamatSiswaTxt = \App\Helpers\AlamatHelper::formatAlamatSiswaUntukExport($alamatSiswaRecord, $wali);
            $alamatOrtuTxt  = \App\Helpers\AlamatHelper::formatLengkap($wali?->alamat);
        @endphp
        <tr>
            <td style="text-align:center">{{ $i+1 }}</td>
            <td><strong>{{ $s->nomor_pendaftaran }}</strong></td>
            <td>{{ $s->nama_lengkap }}</td>
            <td>{{ $s->nisn }}</td>
            <td style="text-align:center">{{ $s->jenis_kelamin }}</td>
            <td>{{ $s->asal_sekolah }}</td>
            <td>{{ $p1?->jurusan?->singkatan ?? '-' }}</td>
            <td>{{ $alamatSiswaTxt }}</td>
            <td>{{ $alamatOrtuTxt }}</td>
            <td><span class="badge {{ $st }}">{{ $s->status_penerimaan }}</span></td>
            <td>{{ $s->tanggal_daftar?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">
    PPDB SMK &mdash; Sistem Penerimaan Peserta Didik Baru &bull; {{ now()->format('d/m/Y') }}
</div>
</body>
</html>