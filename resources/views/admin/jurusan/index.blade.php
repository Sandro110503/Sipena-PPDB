@extends('layouts.admin')
@section('title','Manajemen Jurusan')
@section('page-title','Manajemen Jurusan')

@section('content')
<div class="flex-between" style="margin-bottom:1.1rem">
    <p class="text-muted">Kelola program keahlian / jurusan yang tersedia di sekolah.</p>
    <a href="{{ route('admin.jurusan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Jurusan
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;margin-bottom:1.5rem">
    @foreach($jurusan as $j)
    @php
        $persen   = $j->kapasitas > 0 ? min(100, round($j->diterima / $j->kapasitas * 100)) : 0;
        $barColor = $persen >= 100 ? '#dc2626' : ($persen >= 75 ? '#d97706' : '#1a4a8a');

        // Warna badge berputar berdasarkan kode angka agar tiap jurusan beda warna
        $palette = [
            ['bg'=>'#dbeafe','color'=>'#1e40af'],
            ['bg'=>'#ede9fe','color'=>'#5b21b6'],
            ['bg'=>'#fce7f3','color'=>'#9d174d'],
            ['bg'=>'#dcfce7','color'=>'#166534'],
            ['bg'=>'#fff7ed','color'=>'#92400e'],
            ['bg'=>'#fef9c3','color'=>'#713f12'],
        ];
        $badgeStyle = $palette[((int)$j->kode_jurusan - 1) % count($palette)];

        // Contoh nomor pendaftaran (KK MM YYYY 001)
        $contohNomor = str_pad($j->kode_jurusan, 2, '0', STR_PAD_LEFT) . now()->format('mY') . '001';
    @endphp
    <div class="card">
        <div class="card-body">
            {{-- Header jurusan --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:1rem">
                <div style="display:flex;align-items:center;gap:.75rem">
                    {{-- Badge pakai SINGKATAN --}}
                    <div style="width:44px;height:44px;border-radius:10px;background:{{ $badgeStyle['bg'] }};color:{{ $badgeStyle['color'] }};display:grid;place-items:center;font-weight:800;font-size:.75rem;flex-shrink:0"
                         title="Kode pendaftaran: {{ $j->kode_jurusan }}">
                        {{ $j->singkatan }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.9rem;color:#0f2744;line-height:1.3">{{ $j->nama_jurusan }}</div>
                        <div class="text-muted">{{ $j->deskripsi ? Str::limit($j->deskripsi, 50) : 'Tidak ada deskripsi' }}</div>
                        {{-- Contoh nomor pendaftaran --}}
                        <div style="font-size:.65rem;color:#1e40af;font-family:monospace;margin-top:.2rem;background:#eff6ff;display:inline-block;padding:.1rem .3rem;border-radius:3px"
                             title="Format nomor pendaftaran jurusan ini">
                            {{ $contohNomor }}…
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:.35rem;flex-shrink:0">
                    <a href="{{ route('admin.jurusan.edit', $j->id_jurusan) }}"
                       class="btn btn-outline btn-sm btn-icon" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST"
                        action="{{ route('admin.jurusan.destroy', $j->id_jurusan) }}"
                        class="form-delete">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;margin-bottom:.85rem;text-align:center">
                <div style="background:#f8fafc;border-radius:8px;padding:.5rem .25rem">
                    <div style="font-size:1.2rem;font-weight:800;color:#0f2744">{{ $j->total_pendaftar }}</div>
                    <div style="font-size:.65rem;color:#64748b">Pendaftar</div>
                </div>
                <div style="background:#f8fafc;border-radius:8px;padding:.5rem .25rem">
                    <div style="font-size:1.2rem;font-weight:800;color:#166534">{{ $j->diterima }}</div>
                    <div style="font-size:.65rem;color:#64748b">Diterima</div>
                </div>
                <div style="background:#f8fafc;border-radius:8px;padding:.5rem .25rem">
                    <div style="font-size:1.2rem;font-weight:800;color:#0f2744">{{ $j->kapasitas }}</div>
                    <div style="font-size:.65rem;color:#64748b">Kapasitas</div>
                </div>
            </div>

            {{-- Progress --}}
            <div style="display:flex;justify-content:space-between;font-size:.72rem;margin-bottom:.3rem">
                <span style="color:#64748b">Pengisian kuota</span>
                <span style="font-weight:700;color:{{ $barColor }}">{{ $persen }}%</span>
            </div>
            <div style="background:#e2e8f0;border-radius:999px;height:7px;overflow:hidden">
                <div style="height:100%;width:{{ $persen }}%;background:{{ $barColor }};border-radius:999px;transition:width .5s"></div>
            </div>
            <div style="font-size:.68rem;color:#94a3b8;margin-top:.25rem">
                {{ $j->diterima }}/{{ $j->kapasitas }} kursi terisi
                @if($persen >= 100) <span style="color:#dc2626;font-weight:700">— PENUH</span> @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabel ringkasan --}}
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-table" style="color:#1a4a8a;margin-right:.4rem"></i> Ringkasan Semua Jurusan</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">Kode</th>
                    <th style="width:60px">Badge</th>
                    <th>Nama Jurusan</th>
                    <th style="text-align:center">Kapasitas</th>
                    <th style="text-align:center">Total Pendaftar</th>
                    <th style="text-align:center">Pilihan 1</th>
                    <th style="text-align:center">Diterima</th>
                    <th style="text-align:center">Sisa Kuota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurusan as $j)
                @php
                    $palette  = [
                        ['bg'=>'#dbeafe','color'=>'#1e40af'],
                        ['bg'=>'#ede9fe','color'=>'#5b21b6'],
                        ['bg'=>'#fce7f3','color'=>'#9d174d'],
                        ['bg'=>'#dcfce7','color'=>'#166534'],
                        ['bg'=>'#fff7ed','color'=>'#92400e'],
                        ['bg'=>'#fef9c3','color'=>'#713f12'],
                    ];
                    $bs = $palette[((int)$j->kode_jurusan - 1) % count($palette)];
                @endphp
                <tr>
                    {{-- Kode angka (awalan nomor pendaftaran) --}}
                    <td>
                        <code style="font-size:.85rem;font-weight:700;background:#f1f5f9;padding:.15rem .4rem;border-radius:4px">
                            {{ str_pad($j->kode_jurusan, 2, '0', STR_PAD_LEFT) }}
                        </code>
                    </td>
                    {{-- Badge singkatan --}}
                    <td>
                        <span style="display:inline-flex;align-items:center;justify-content:center;
                                     min-width:36px;height:26px;padding:0 .4rem;border-radius:6px;
                                     background:{{ $bs['bg'] }};color:{{ $bs['color'] }};
                                     font-weight:800;font-size:.7rem">
                            {{ $j->singkatan }}
                        </span>
                    </td>
                    <td style="font-weight:600">{{ $j->nama_jurusan }}</td>
                    <td style="text-align:center">{{ $j->kapasitas }}</td>
                    <td style="text-align:center">{{ $j->total_pendaftar }}</td>
                    <td style="text-align:center">{{ $j->pilihan1 }}</td>
                    <td style="text-align:center">
                        <span style="color:#166534;font-weight:700">{{ $j->diterima }}</span>
                    </td>
                    <td style="text-align:center">
                        @php $sisa = $j->kapasitas - $j->diterima; @endphp
                        <span style="color:{{ $sisa<=0?'#dc2626':($sisa<=5?'#d97706':'#166534') }};font-weight:700">
                            {{ max(0,$sisa) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem">
                            <a href="{{ route('admin.jurusan.edit', $j->id_jurusan) }}"
                               class="btn btn-outline btn-sm btn-icon"><i class="fas fa-edit"></i></a>
                            <form method="POST"
                                action="{{ route('admin.jurusan.destroy', $j->id_jurusan) }}"
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
                @endforeach
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
