@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard PPDB')

@section('content')
{{-- STAT CARDS --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;color:#1e40af"><i class="fas fa-users"></i></div>
        <div><div class="stat-value">{{ number_format($stats['total_pendaftar']) }}</div><div class="stat-label">Total Pendaftar</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#92400e"><i class="fas fa-clock"></i></div>
        <div><div class="stat-value">{{ number_format($stats['menunggu']) }}</div><div class="stat-label">Menunggu Proses</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;color:#166534"><i class="fas fa-check-circle"></i></div>
        <div><div class="stat-value">{{ number_format($stats['diterima']) }}</div><div class="stat-label">Sukses</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;color:#991b1b"><i class="fas fa-times-circle"></i></div>
        <div><div class="stat-value">{{ number_format($stats['ditolak']) }}</div><div class="stat-label">Gagal</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede9fe;color:#5b21b6"><i class="fas fa-money-bill-wave"></i></div>
        <div><div class="stat-value" style="font-size:1.25rem">Rp {{ number_format($stats['total_pembayaran'],0,',','.') }}</div><div class="stat-label">Total Pembayaran</div></div>
    </div>
</div>

{{-- JURUSAN & CHART --}}
<div class="grid-2" style="margin-bottom:1.5rem">
    {{-- Jurusan Progress --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-school" style="color:#1a4a8a;margin-right:.5rem"></i> Kuota Per Jurusan</span>
        </div>
        <div class="card-body">
            @foreach($jurusan as $j)
            @php $persen = $j->kapasitas > 0 ? min(100, round($j->diterima / $j->kapasitas * 100)) : 0; @endphp
            <div style="margin-bottom:1.25rem">
                <div class="flex-between" style="margin-bottom:.4rem">
                    <strong style="font-size:.875rem">{{ $j->nama_jurusan }}</strong>
                    <span class="text-muted">{{ $j->diterima }} / {{ $j->kapasitas }}</span>
                </div>
                <div style="background:#e2e8f0;border-radius:999px;height:8px;overflow:hidden">
                    <div style="height:100%;width:{{ $persen }}%;background:{{ $persen>=100?'#dc2626':($persen>=75?'#d97706':'#1a4a8a') }};border-radius:999px;transition:width .5s"></div>
                </div>
                <div style="font-size:.7rem;color:#64748b;margin-top:.25rem">{{ $persen }}% terisi &bull; {{ $j->pilihan1 ?? 0 }} pilihan </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Grafik Pendaftaran per Bulan --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-line" style="color:#1a4a8a;margin-right:.5rem"></i> Pendaftaran per Bulan</span>
        </div>
        <div class="card-body">
            <canvas id="chartBulan" height="200"></canvas>
        </div>
    </div>
</div>

{{-- CHART TAMBAHAN: PERIODE & STATUS --}}
<div class="grid-2" style="margin-bottom:1.5rem">
    {{-- Pendaftaran per Periode PPDB --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-calendar-alt" style="color:#1a4a8a;margin-right:.5rem"></i> Pendaftaran per Periode PPDB</span>
        </div>
        <div class="card-body">
            <canvas id="chartPeriode" height="200"></canvas>
        </div>
    </div>

    {{-- Distribusi Status Pendaftaran --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-pie" style="color:#1a4a8a;margin-right:.5rem"></i> Distribusi Status Pendaftaran</span>
        </div>
        <div class="card-body">
            <canvas id="chartStatus" height="200"></canvas>
        </div>
    </div>
</div>

{{-- CHART PEMBAYARAN & DOKUMEN --}}
<div class="grid-3" style="margin-bottom:1.5rem">
    {{-- Pembayaran per Metode --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-wallet" style="color:#1a4a8a;margin-right:.5rem"></i> Pembayaran Terverifikasi per Metode</span>
        </div>
        <div class="card-body">
            @if($pembayaranPerMetode->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:2.5rem 0;font-size:.82rem">Belum ada pembayaran terverifikasi.</div>
            @else
            <canvas id="chartMetodeBayar" height="200"></canvas>
            @endif
        </div>
    </div>

    {{-- Status Verifikasi Pembayaran --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-tasks" style="color:#1a4a8a;margin-right:.5rem"></i> Status Verifikasi Pembayaran</span>
        </div>
        <div class="card-body">
            @if($pembayaranPerStatus->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:2.5rem 0;font-size:.82rem">Belum ada data pembayaran masuk.</div>
            @else
            <canvas id="chartStatusBayar" height="200"></canvas>
            @endif
        </div>
    </div>

    {{-- Status Verifikasi Dokumen --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-folder-open" style="color:#1a4a8a;margin-right:.5rem"></i> Status Verifikasi Dokumen</span>
        </div>
        <div class="card-body">
            @if($dokumenPerStatus->isEmpty())
            <div style="text-align:center;color:#94a3b8;padding:2.5rem 0;font-size:.82rem">Belum ada dokumen yang diunggah.</div>
            @else
            <canvas id="chartStatusDokumen" height="200"></canvas>
            @endif
        </div>
    </div>
</div>

{{-- TABEL TERBARU --}}
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-list" style="color:#1a4a8a;margin-right:.5rem"></i> 10 Pendaftar Terbaru</span>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No. Pendaftaran</th>
                    <th>Nama</th>
                    <th>NISN</th>
                    <th>Pilihan Jurusan</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftarTerbaru as $s)
                @php $p1 = $s->pendaftaranJurusan->where('urutan_pilihan',1)->first(); @endphp
                <tr>
                    <td><code style="font-size:.75rem">{{ $s->nomor_pendaftaran }}</code></td>
                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                    <td>{{ $s->nisn }}</td>
                    <td>
                        @if($p1)
                        <span class="badge badge-{{ strtolower($p1->jurusan->singkatan) }}">{{ $p1->jurusan->singkatan }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower($s->status_penerimaan) }}">{{ $s->status_penerimaan }}</span>
                    </td>
                    <td class="text-muted">{{ $s->tanggal_daftar?->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.siswa.show', $s->id_siswa) }}" class="btn btn-outline btn-sm btn-icon"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#64748b;padding:2rem">Belum ada data pendaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Pendaftaran per Bulan
new Chart(document.getElementById('chartBulan'), {
    type: 'bar',
    data: {
        labels: @json($perBulan->pluck('bulan')),
        datasets: [{
            label: 'Jumlah Pendaftar',
            data: @json($perBulan->pluck('jumlah')),
            backgroundColor: '#1a4a8a',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Chart Pendaftaran per Periode PPDB
new Chart(document.getElementById('chartPeriode'), {
    type: 'bar',
    data: {
        labels: @json($perPeriode->pluck('nama_periode')),
        datasets: [{
            label: 'Jumlah Pendaftar',
            data: @json($perPeriode->pluck('jumlah')),
            backgroundColor: '#e8a020',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Chart Distribusi Status Pendaftaran
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Menunggu', 'Diterima', 'Ditolak', 'Cadangan'],
        datasets: [{
            data: [
                {{ $stats['menunggu'] }},
                {{ $stats['diterima'] }},
                {{ $stats['ditolak'] }},
                {{ $stats['cadangan'] }}
            ],
            backgroundColor: ['#d97706', '#16a34a', '#dc2626', '#0369a1'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
        }
    }
});

@if($pembayaranPerMetode->isNotEmpty())
// Chart Pembayaran per Metode
new Chart(document.getElementById('chartMetodeBayar'), {
    type: 'bar',
    data: {
        labels: @json($pembayaranPerMetode->pluck('metode')),
        datasets: [{
            label: 'Jumlah Transaksi',
            data: @json($pembayaranPerMetode->pluck('jumlah')),
            backgroundColor: '#166534',
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    afterLabel: function(ctx) {
                        const total = @json($pembayaranPerMetode->pluck('total'));
                        return 'Total: Rp ' + Number(total[ctx.dataIndex]).toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
@endif

@if($pembayaranPerStatus->isNotEmpty())
// Chart Status Verifikasi Pembayaran
new Chart(document.getElementById('chartStatusBayar'), {
    type: 'doughnut',
    data: {
        labels: @json($pembayaranPerStatus->keys()),
        datasets: [{
            data: @json($pembayaranPerStatus->values()),
            backgroundColor: ['#16a34a', '#d97706', '#dc2626', '#0369a1'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
        }
    }
});
@endif

@if($dokumenPerStatus->isNotEmpty())
// Chart Status Verifikasi Dokumen
new Chart(document.getElementById('chartStatusDokumen'), {
    type: 'doughnut',
    data: {
        labels: @json($dokumenPerStatus->keys()),
        datasets: [{
            data: @json($dokumenPerStatus->values()),
            backgroundColor: ['#16a34a', '#d97706', '#dc2626', '#0369a1'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
        }
    }
});
@endif
</script>
@endpush
