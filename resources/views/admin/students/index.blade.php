@extends('layouts.admin')
@section('title', 'Data Siswa')
@section('page-title', 'Data Calon Siswa')

@section('content')
{{-- FILTER & EXPORT --}}
<div class="card" style="margin-bottom:1.25rem">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.siswa.index') }}">
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:1;min-width:200px">
                    <label class="form-label">Cari Nama / NISN / No. Pendaftaran</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
                <div style="min-width:160px">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach(['Menunggu','Diterima','Ditolak','Cadangan'] as $st)
                        <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:200px">
                    <label class="form-label">Jurusan Pilihan</label>
                    <select name="jurusan" class="form-control">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusan as $j)
                        <option value="{{ $j->id_jurusan }}" {{ request('jurusan')==$j->id_jurusan?'selected':'' }}>{{ $j->nama_jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:200px">
                    <label class="form-label">Periode</label>
                    <select name="periode" class="form-control">
                        <option value="">Semua Periode</option>
                        @foreach($periode as $p)
                        <option value="{{ $p->id_periode }}" {{ request('periode')==$p->id_periode?'selected':'' }}>{{ $p->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:180px">
                    <label class="form-label">Bulan Daftar</label>
                    <select name="bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        @foreach($bulanOptions as $ym)
                        <option value="{{ $ym }}" {{ request('bulan')===$ym?'selected':'' }}>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $ym)->translatedFormat('F Y') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline"><i class="fas fa-times"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-users" style="color:#1a4a8a;margin-right:.5rem"></i> Daftar Calon Siswa
            <span style="background:#e2e8f0;border-radius:999px;padding:.1rem .6rem;font-size:.75rem;margin-left:.5rem">{{ $siswa->total() }}</span>
        </span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.siswa.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('admin.siswa.export-pdf', request()->query()) }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Lengkap</th>
                    <th>NISN</th>
                    <th>Jurusan</th>
                    <th>Periode Daftar</th>
                    <th>Tgl. Daftar</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $i => $s)
                @php
                    $p1 = $s->pendaftaranJurusan->where('urutan_pilihan',1)->first();
                @endphp
                <tr onclick="window.location='{{ route('admin.siswa.show', $s->id_siswa) }}'" style="cursor:pointer" class="row-clickable">
                    <td class="text-muted">{{ $siswa->firstItem() + $i }}</td>
                    <td><code style="font-size:.75rem;background:#f1f5f9;padding:.2rem .4rem;border-radius:4px">{{ $s->nomor_pendaftaran }}</code></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.6rem">
                            @if($s->foto)
                            <img src="{{ Storage::url($s->foto) }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover" alt="">
                            @else
                            <div style="width:32px;height:32px;border-radius:50%;background:#1a4a8a;color:#fff;display:grid;place-items:center;font-weight:700;font-size:.75rem;flex-shrink:0">
                                {{ strtoupper(substr($s->nama_depan,0,1)) }}
                            </div>
                            @endif
                            <div>
                                <div style="font-weight:600;font-size:.875rem">{{ $s->nama_lengkap }}</div>
                                <div class="text-muted">{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $s->nisn }}</td>
                    <td>
                        @if($p1)
                        <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.8rem">
                            <span style="font-weight:700">{{ $p1->jurusan->singkatan }}</span>
                            <span class="text-muted" style="font-size:.7rem">{{ $p1->jurusan->nama_jurusan }}</span>
                        </span>
                        @else <span class="text-muted">-</span> @endif
                    </td>
                    <td style="white-space:nowrap">
                        <span style="font-size:.8rem;color:#1e40af;font-weight:600">
                            {{ $s->periode->nama_periode ?? '-' }}
                        </span>
                    </td>
                    <td class="text-muted" style="white-space:nowrap">{{ $s->tanggal_daftar?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($s->status_penerimaan) }}">
                            {{ $s->status_penerimaan }}
                        </span>
                    </td>

                    <td>
                        @php
                            $pembayaran = $s->pembayaran->first();
                        @endphp

                        @if($pembayaran)

                            @if($pembayaran->status_pembayaran == 'Terverifikasi')
                                <span style="display:inline-block;
                                            background:#22c55e;
                                            color:#fff;
                                            padding:4px 10px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:600;">
                                    Terverifikasi
                                </span>

                            @elseif($pembayaran->status_pembayaran == 'Menunggu Verifikasi')
                                <span style="display:inline-block;
                                            background:#facc15;
                                            color:#000;
                                            padding:4px 10px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:600;">
                                    Menunggu Verifikasi
                                </span>

                            @elseif($pembayaran->status_pembayaran == 'Ditolak')
                                <span style="display:inline-block;
                                            background:#ef4444;
                                            color:#fff;
                                            padding:4px 10px;
                                            border-radius:20px;
                                            font-size:12px;
                                            font-weight:600;">
                                    Ditolak
                                </span>

                            @endif

                        @else

                            <span style="display:inline-block;
                                        background:#9ca3af;
                                        color:#fff;
                                        padding:4px 10px;
                                        border-radius:20px;
                                        font-size:12px;
                                        font-weight:600;">
                                Belum Bayar
                            </span>

                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:#64748b;padding:3rem">
                    <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
                    Tidak ada data yang ditemukan.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:.75rem 1rem;border-top:1px solid var(--border)">
        {{ $siswa->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection