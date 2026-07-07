@extends('layouts.admin')
@section('title','Verifikasi Pembayaran')
@section('page-title','Verifikasi Pembayaran')
@section('content')

<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));margin-bottom:1.25rem">
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;color:#166534"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value" style="font-size:1.1rem">Rp {{ number_format($totalTerverifikasi,0,',','.') }}</div>
            <div class="stat-label">Total Terverifikasi</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-receipt" style="color:#1a4a8a;margin-right:.5rem"></i> Data Pembayaran</span>
        <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="width:180px" placeholder="Cari nama/nomor..." value="{{ request('search') }}">
            <select name="status" class="form-control" style="width:160px">
                <option value="">Semua Status</option>
                <option value="Menunggu Verifikasi" {{ request('status')=='Menunggu Verifikasi'?'selected':'' }}>Menunggu Verifikasi</option>
                <option value="Terverifikasi" {{ request('status')=='Terverifikasi'?'selected':'' }}>Terverifikasi</option>
                <option value="Ditolak" {{ request('status')=='Ditolak'?'selected':'' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Tanggal</th><th>Siswa</th><th>Metode</th>
                    <th>Jumlah</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pembayaran as $i => $p)
            <tr>
                <td class="text-muted">{{ $pembayaran->firstItem() + $i }}</td>
                <td>{{ $p->tanggal_bayar?->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $p->siswa?->nama_lengkap }}</strong>
                    <div style="font-size:.72rem;color:var(--muted)">{{ $p->siswa?->nomor_pendaftaran }}</div>
                </td>
                <td>{{ $p->metodePembayaran?->deskripsi_metode_bayar ?? $p->kode_metode_bayar }}</td>
                <td><strong>Rp {{ number_format($p->jumlah_bayar,0,',','.') }}</strong></td>
                <td>
                    @php
                        $sc = match($p->status_pembayaran) {
                            'Terverifikasi' => 'diterima',
                            'Ditolak' => 'ditolak',
                            default => 'menunggu'
                        };
                    @endphp
                    <span class="badge badge-{{ $sc }}">{{ $p->status_pembayaran }}</span>
                </td>
                <td>
                    <a href="{{ route('admin.pembayaran.show', $p) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">Belum ada data pembayaran</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($pembayaran->hasPages())
    <div style="padding:.85rem 1.1rem">{{ $pembayaran->links() }}</div>
    @endif
</div>
@endsection
