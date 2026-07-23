@extends('layouts.admin')
@section('title','Log Aktivitas')
@section('page-title','Log Aktivitas')

@section('content')
<div class="flex-between" style="margin-bottom:1.1rem">
    <div>
        <p class="text-muted">Riwayat aktivitas seluruh pengguna admin di sistem PPDB.</p>
    </div>
    <div style="display:flex;gap:.5rem">
        <span class="badge" style="background:#dbeafe;color:#1e40af">
            <i class="fas fa-calendar-day" style="margin-right:.3rem"></i> {{ $hariIni }} aktivitas hari ini
        </span>
        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('modalBersihkan').style.display='flex'">
            <i class="fas fa-broom"></i> Bersihkan Log Lama
        </button>
    </div>
</div>

{{-- FILTER --}}
<div class="card" style="margin-bottom:1.1rem">
    <div class="card-body" style="padding:.85rem 1.1rem">
        <form method="GET" action="{{ route('admin.activity-log.index') }}">
            <div style="display:flex;gap:.65rem;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:1;min-width:180px">
                    <label class="form-label">Cari Deskripsi / Nama Admin</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
                <div style="min-width:150px">
                    <label class="form-label">Modul</label>
                    <select name="modul" class="form-control">
                        <option value="">Semua Modul</option>
                        @foreach($daftarModul as $m)
                        <option value="{{ $m }}" {{ request('modul')===$m?'selected':'' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:150px">
                    <label class="form-label">Jenis Aktivitas</label>
                    <select name="aktivitas" class="form-control">
                        <option value="">Semua Jenis</option>
                        @foreach($konfigAktivitas as $key => $cfg)
                        <option value="{{ $key }}" {{ request('aktivitas')===$key?'selected':'' }}>{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:160px">
                    <label class="form-label">Pegawai</label>
                    <select name="admin_id" class="form-control">
                        <option value="">Semua Pegawai</option>
                        @foreach($daftarAdmin as $a)
                        <option value="{{ $a->id }}" {{ (string) request('admin_id')===(string) $a->id?'selected':'' }}>{{ $a->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:140px">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control">
                </div>
                <div style="min-width:140px">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-history" style="color:#1a4a8a;margin-right:.4rem"></i> Riwayat Aktivitas
            <span style="background:#e2e8f0;border-radius:999px;padding:.1rem .55rem;font-size:.72rem;margin-left:.4rem">{{ $logs->total() }}</span>
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pegawai</th>
                    <th>Modul</th>
                    <th>Aktivitas</th>
                    <th>Deskripsi</th>
                    <th>IP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php $cfg = $log->konfig; @endphp
                <tr>
                    <td style="white-space:nowrap">
                        <div style="font-weight:600;font-size:.82rem">{{ $log->created_at->format('d M Y') }}</div>
                        <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.55rem">
                            <div style="width:30px;height:30px;border-radius:50%;background:#1a4a8a;color:#fff;display:grid;place-items:center;font-weight:700;font-size:.72rem;flex-shrink:0">
                                {{ strtoupper(substr($log->nama_admin ?? '?',0,1)) }}
                            </div>
                            <span style="font-size:.83rem;font-weight:600">{{ $log->nama_admin ?? 'Sistem' }}</span>
                        </div>
                    </td>
                    <td><span class="text-muted" style="font-size:.8rem">{{ $log->modul }}</span></td>
                    <td>
                        <span class="badge" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}">
                            <i class="fas fa-{{ $cfg['icon'] }}" style="margin-right:.3rem;font-size:.62rem"></i>{{ $cfg['label'] }}
                        </span>
                    </td>
                    <td style="max-width:340px"><span style="font-size:.83rem">{{ $log->deskripsi }}</span></td>
                    <td><code style="font-size:.72rem;background:#f1f5f9;padding:.15rem .4rem;border-radius:4px">{{ $log->ip_address ?? '-' }}</code></td>
                    <td>
                        <div style="display:flex;gap:.35rem">
                            <a href="{{ route('admin.activity-log.show', $log->id) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(Auth::guard('admin')->user()->isSuperAdmin())
                            <form method="POST"
                                action="{{ route('admin.activity-log.destroy', $log->id) }}" style="display:inline"
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
                        <i class="fas fa-history" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
                        Belum ada log aktivitas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:.75rem 1rem;border-top:1px solid var(--border)">
        {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- MODAL BERSIHKAN LOG LAMA --}}
<div id="modalBersihkan" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:500;align-items:center;justify-content:center;padding:1rem">
    <div class="card" style="max-width:420px;width:100%">
        <div class="card-header">
            <span><i class="fas fa-broom" style="margin-right:.4rem"></i> Bersihkan Log Lama</span>
        </div>
        <form method="POST" action="{{ route('admin.activity-log.bersihkan') }}">
            @csrf
            <div class="card-body">
                <p class="text-muted" style="margin-bottom:.85rem">
                    Hapus semua log aktivitas yang lebih lama dari jumlah hari berikut. Tindakan ini tidak bisa dibatalkan.
                </p>
                <div class="form-group">
                    <label class="form-label">Hapus log lebih lama dari (hari)</label>
                    <input type="number" name="lebih_dari_hari" value="90" min="7" max="730" class="form-control" required>
                </div>
            </div>
            <div class="card-header" style="border-top:1px solid var(--border);border-bottom:none;justify-content:flex-end;gap:.5rem">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalBersihkan').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin membersihkan log lama?')">
                    <i class="fas fa-broom"></i> Bersihkan
                </button>
            </div>
        </form>
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
