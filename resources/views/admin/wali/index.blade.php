@extends('layouts.admin')
@section('title','Data Wali/Orang Tua')
@section('page-title','Data Wali/Orang Tua')
@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-users" style="color:#1a4a8a;margin-right:.5rem"></i> Daftar Wali / Orang Tua</span>
        <form method="GET" style="display:flex;gap:.5rem">
            <input type="text" name="search" class="form-control" style="width:200px" placeholder="Nama / No HP..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
            @if(request('search'))<a href="{{ route('admin.wali.index') }}" class="btn btn-outline btn-sm">Reset</a>@endif
        </form>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama</th><th>Jenis Kelamin</th><th>Hubungan</th><th>No HP</th><th>Pekerjaan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($wali as $i => $w)
            <tr>
                <td class="text-muted">{{ $wali->firstItem() + $i }}</td>
                <td><strong>{{ $w->nama_lengkap }}</strong></td>
                <td>{{ $w->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>{{ $w->hubungan }}</td>
                <td>{{ $w->nomor_hp }}</td>
                <td>{{ $w->pekerjaan }}</td>
                <td><a href="{{ route('admin.wali.show', $w) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">Belum ada data wali</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:.75rem 1rem;border-top:1px solid var(--border)">
        {{ $wali->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
