@extends('layouts.admin')
@section('title', 'Periode PPDB')
@section('page-title', 'Manajemen Periode PPDB')

@section('content')

{{-- Header --}}
<div class="flex-between" style="margin-bottom:1.25rem">
    <div>
        <p style="color:#64748b;font-size:.875rem;margin:0">
            Atur jadwal pembukaan dan penutupan pendaftaran peserta didik baru.
        </p>
    </div>
    <a href="{{ route('admin.periode.create') }}" class="btn btn-primary">
        <i class="fas fa-plus" style="margin-right:.4rem"></i> Tambah Periode
    </a>
</div>

{{-- Info periode aktif --}}
@php $aktif = $periodes->firstWhere('is_aktif', true); @endphp
@if($aktif)
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem">
    <div style="background:#dbeafe;width:40px;height:40px;border-radius:8px;display:grid;place-items:center;flex-shrink:0">
        <i class="fas fa-calendar-check" style="color:#1d4ed8"></i>
    </div>
    <div>
        <div style="font-weight:700;color:#1e3a5f">{{ $aktif->nama_periode }}</div>
        <div style="font-size:.8rem;color:#3b82f6">
            Pendaftaran: {{ $aktif->tanggal_buka->format('d M Y') }} –
            {{ $aktif->tanggal_tutup->format('d M Y') }}
            &nbsp;|&nbsp; Status:
            <span style="font-weight:600">{{ $aktif->status }}</span>
        </div>
    </div>
</div>
@else
<div style="background:#fefce8;border:1px solid #fde68a;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem">
    <i class="fas fa-exclamation-triangle" style="color:#d97706"></i>
    <span style="color:#92400e;font-size:.875rem">Belum ada periode PPDB yang aktif. Pendaftaran saat ini <strong>tertutup</strong>.</span>
</div>
@endif

{{-- Tabel --}}
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-list-alt" style="color:#1a4a8a;margin-right:.5rem"></i> Daftar Periode</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama Periode</th>
                    <th>Tahun Ajaran</th>
                    <th>Gelombang</th>
                    <th>Pendaftaran</th>
                    <th>Pengumuman</th>
                    <th>Biaya</th>
                    <th>Status</th>
                    <th style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodes as $i => $p)
                <tr style="{{ $p->is_aktif ? 'background:#f0fdf4' : '' }}">
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $p->nama_periode }}</strong>
                        @if($p->keterangan)
                        <div style="font-size:.75rem;color:#64748b;margin-top:.15rem">{{ Str::limit($p->keterangan, 60) }}</div>
                        @endif
                    </td>
                    <td>{{ $p->tahun_ajaran }}/{{ $p->tahun_ajaran + 1 }}</td>
                    <td style="text-align:center">{{ $p->gelombang }}</td>
                    <td style="font-size:.8rem">
                        {{ $p->tanggal_buka->format('d M Y') }}<br>
                        <span style="color:#64748b">s.d. {{ $p->tanggal_tutup->format('d M Y') }}</span>
                    </td>
                    <td style="font-size:.8rem">
                        {{ $p->tanggal_pengumuman?->format('d M Y') ?? '—' }}
                    </td>
                    <td style="font-size:.85rem">{{ $p->biaya_format }}</td>
                    <td>
                        @php
                            $badgeStyle = match($p->badge_color) {
                                'success'   => 'background:#dcfce7;color:#166534',
                                'warning'   => 'background:#fef3c7;color:#92400e',
                                'danger'    => 'background:#fee2e2;color:#991b1b',
                                default     => 'background:#f1f5f9;color:#475569',
                            };
                        @endphp
                        <span style="display:inline-block;padding:.25rem .65rem;border-radius:999px;font-size:.7rem;font-weight:600;{{ $badgeStyle }}">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.4rem;align-items:center">
                            {{-- Toggle aktif --}}
                            <form method="POST" action="{{ route('admin.periode.toggle-aktif', $p->id_periode) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="btn btn-sm {{ $p->is_aktif ? 'btn-success' : 'btn-outline' }}"
                                    title="{{ $p->is_aktif ? 'Nonaktifkan' : 'Jadikan Aktif' }}"
                                    style="padding:.3rem .55rem">
                                    <i class="fas {{ $p->is_aktif ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                            </form>

                            {{-- Edit --}}
                            <a href="{{ route('admin.periode.edit', $p->id_periode) }}"
                               class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Hapus --}}
                            <form method="POST"
                                action="{{ route('admin.periode.destroy', $p->id_periode) }}"
                                class="form-delete">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;color:#64748b;padding:2.5rem">
                        <i class="fas fa-calendar-times" style="font-size:2rem;margin-bottom:.75rem;display:block;color:#cbd5e1"></i>
                        Belum ada periode PPDB. <a href="{{ route('admin.periode.create') }}">Tambah sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
