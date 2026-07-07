@extends('layouts.admin')
@section('title','Tipe Relasi')
@section('page-title','Tipe Relasi')
@section('content')

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-list" style="color:#1a4a8a;margin-right:.5rem"></i> Daftar Tipe Relasi</span>
        <a href="{{ route('admin.ref-tipe-relasi.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>#</th><th>Kode</th><th>Deskripsi</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($tipe as $i => $row)
            <tr>
                <td class="text-muted">{{ $tipe->firstItem() + $i }}</td>
                <td><code>{{ $row->kode_tipe_relasi }}</code></td>
                <td>{{ $row->deskripsi_tipe_relasi }}</td>
                <td class="text-muted">{{ $row->created_at?->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('admin.ref-tipe-relasi.edit', $row) }}" class="btn btn-outline btn-sm"><i class="fas fa-pen"></i></a>
                    <form method="POST"
                        action="{{ route('admin.ref-tipe-relasi.destroy', $row) }}"
                        class="form-delete" style="display:inline>
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($tipe->hasPages())
    <div style="padding:.85rem 1.1rem">{{ $tipe->links() }}</div>
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
