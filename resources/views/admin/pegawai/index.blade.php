@extends('layouts.admin')
@section('title','Manajemen Pegawai')
@section('page-title','Manajemen Pegawai')

@section('content')
<div class="flex-between" style="margin-bottom:1.1rem">
    <div>
        <p class="text-muted">Kelola akun pegawai yang dapat mengakses sistem PPDB.</p>
    </div>
    @if(Auth::guard('admin')->user()->isSuperAdmin())
    <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Pegawai
    </a>
    @endif
</div>

{{-- FILTER --}}
<div class="card" style="margin-bottom:1.1rem">
    <div class="card-body" style="padding:.85rem 1.1rem">
        <form method="GET" action="{{ route('admin.pegawai.index') }}">
            <div style="display:flex;gap:.65rem;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:1;min-width:180px">
                    <label class="form-label">Cari Nama / NIP / Jabatan</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
                <div style="min-width:140px">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="">Semua Role</option>
                        <option value="superadmin" {{ request('role')==='superadmin'?'selected':'' }}>Super Admin</option>
                        <option value="admin"      {{ request('role')==='admin'?'selected':'' }}>Admin</option>
                        <option value="operator"   {{ request('role')==='operator'?'selected':'' }}>Operator</option>
                    </select>
                </div>
                <div style="min-width:130px">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="1" {{ request('status')==='1'?'selected':'' }}>Aktif</option>
                        <option value="0" {{ request('status')==='0'?'selected':'' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-users" style="color:#1a4a8a;margin-right:.4rem"></i> Daftar Pegawai
            <span style="background:#e2e8f0;border-radius:999px;padding:.1rem .55rem;font-size:.72rem;margin-left:.4rem">{{ $pegawai->total() }}</span>
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>No. HP</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawai as $p)
                @php
                    $roleCfg = match($p->role) {
                        'superadmin' => ['bg'=>'#ede9fe','color'=>'#5b21b6'],
                        'operator'   => ['bg'=>'#dbeafe','color'=>'#1e40af'],
                        default      => ['bg'=>'#dcfce7','color'=>'#166534'],
                    };
                    $isSelf = Auth::guard('admin')->id() === $p->id;
                @endphp
                <tr>
                    <td>
                        <code style="font-size:.75rem;background:#f1f5f9;padding:.15rem .45rem;border-radius:4px">
                            {{ $p->nip }}
                        </code>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.6rem">
                            <div style="width:34px;height:34px;border-radius:50%;background:#1a4a8a;color:#fff;display:grid;place-items:center;font-weight:700;font-size:.8rem;flex-shrink:0">
                                {{ strtoupper(substr($p->nama,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.85rem">
                                    {{ $p->nama }}
                                    @if($isSelf)
                                    <span style="font-size:.65rem;background:#fef3c7;color:#92400e;padding:.1rem .45rem;border-radius:4px;font-weight:700;margin-left:.3rem">Anda</span>
                                    @endif
                                </div>
                                <div class="text-muted">{{ $p->email ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $p->jabatan ?? '-' }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>
                        <span class="badge" style="background:{{ $roleCfg['bg'] }};color:{{ $roleCfg['color'] }}">
                            {{ $p->role_label }}
                        </span>
                    </td>
                    <td>
                        @if($p->is_aktif)
                        <span class="badge badge-diterima"><i class="fas fa-circle" style="font-size:.45rem"></i> Aktif</span>
                        @else
                        <span class="badge badge-ditolak"><i class="fas fa-circle" style="font-size:.45rem"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem">
                            <a href="{{ route('admin.pegawai.edit', $p->id_admin) }}"
                               class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(Auth::guard('admin')->user()->isSuperAdmin() && !$isSelf)
                            <form method="POST" action="{{ route('admin.pegawai.toggle-aktif', $p->id_admin) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-icon"
                                    style="background:{{ $p->is_aktif?'#fef3c7':'#dcfce7' }};color:{{ $p->is_aktif?'#92400e':'#166534' }}"
                                    title="{{ $p->is_aktif?'Nonaktifkan':'Aktifkan' }}">
                                    <i class="fas fa-{{ $p->is_aktif?'ban':'check' }}"></i>
                                </button>
                            </form>
                            <form method="POST"
                                action="{{ route('admin.pegawai.destroy', $p->id_admin) }}"
                                class="form-delete">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem;color:#94a3b8">
                        <i class="fas fa-users" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
                        Tidak ada data pegawai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pegawai->hasPages())
    <div style="padding:.75rem 1rem;border-top:1px solid var(--border)">
        {{ $pegawai->links() }}
    </div>
    @endif
    <script>
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    </script>
</div>
@endsection
