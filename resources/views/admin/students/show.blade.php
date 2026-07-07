@extends('layouts.admin')
@section('title','Detail Siswa')
@section('page-title','Detail Calon Siswa')

@section('content')
<div style="margin-bottom:1rem">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="grid-2" style="margin-bottom:1.25rem">
    {{-- Profil --}}
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-user" style="color:#1a4a8a;margin-right:.5rem"></i> Data Pribadi</span>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem">
                @if($calonSiswa->foto)
                <img src="{{ Storage::url($calonSiswa->foto) }}"
                     style="width:68px;height:68px;border-radius:12px;object-fit:cover" alt="Foto">
                @else
                <div style="width:68px;height:68px;border-radius:12px;background:#1a4a8a;color:#fff;display:grid;place-items:center;font-weight:800;font-size:1.6rem;flex-shrink:0">
                    {{ strtoupper(substr($calonSiswa->nama_depan,0,1)) }}
                </div>
                @endif
                <div>
                    <h2 style="font-size:1.05rem;font-weight:700">{{ $calonSiswa->nama_lengkap }}</h2>
                    <code style="font-size:.72rem;background:#f1f5f9;padding:.18rem .5rem;border-radius:4px">
                        {{ $calonSiswa->nomor_pendaftaran }}
                    </code>
                </div>
            </div>
            @php
                // Decode periode dari nomor_pendaftaran {KK}{MM}{YYYY}{NNN}
                // contoh 01062026001 → "Juni 2026"
                $_nomor = $calonSiswa->nomor_pendaftaran ?? '';
                $_periode = strlen($_nomor) === 11
                    ? \Carbon\Carbon::createFromFormat('mY', substr($_nomor, 2, 6))->locale('id')->isoFormat('MMMM YYYY')
                    : '-';
            @endphp
            @php $rows = [
                'NISN'              => $calonSiswa->nisn,
                'Jenis Kelamin'     => $calonSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'Tempat, Tgl Lahir' => "{$calonSiswa->tempat_lahir}, ".$calonSiswa->tanggal_lahir->format('d M Y'),
                'Asal Sekolah'      => $calonSiswa->asal_sekolah,
                'Tahun Lulus'       => $calonSiswa->tahun_lulus,
                'Email'             => $calonSiswa->email,
                'No. HP'            => $calonSiswa->nomor_hp,
                'Periode Pendaftaran'=> $_periode,
                'Tanggal Daftar'    => $calonSiswa->tanggal_daftar?->format('d M Y'),
            ]; @endphp
            @foreach($rows as $label => $val)
            <div style="display:flex;justify-content:space-between;padding:.48rem 0;border-bottom:1px solid #f1f5f9;font-size:.855rem;gap:.75rem">
                <span class="text-muted">{{ $label }}</span>
                <span style="font-weight:500;text-align:right">{{ $val ?? '-' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Status & Jurusan --}}
    <div style="display:flex;flex-direction:column;gap:1.1rem">
        {{-- Update Status --}}
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-tasks" style="color:#1a4a8a;margin-right:.5rem"></i> Status Penerimaan</span>
            </div>
            <div class="card-body">
                <div style="text-align:center;margin-bottom:1rem">
                    <span class="badge badge-{{ strtolower($calonSiswa->status_penerimaan) }}"
                          style="font-size:.875rem;padding:.5rem 1.25rem">
                        {{ $calonSiswa->status_penerimaan }}
                    </span>
                    @if($calonSiswa->tanggal_diterima)
                    <div style="font-size:.72rem;color:#64748b;margin-top:.35rem">
                        Diterima: {{ $calonSiswa->tanggal_diterima->format('d M Y') }}
                    </div>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.siswa.update-status', $calonSiswa->id_siswa) }}">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Ubah Status</label>
                        <select name="status_penerimaan" class="form-control">
                            @foreach(['Menunggu','Diterima','Ditolak','Cadangan'] as $st)
                            <option value="{{ $st }}" {{ $calonSiswa->status_penerimaan===$st?'selected':'' }}>
                                {{ $st }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        <i class="fas fa-save"></i> Simpan Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Pilihan Jurusan --}}
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-school" style="color:#1a4a8a;margin-right:.5rem"></i> Pilihan Jurusan</span>
            </div>
            <div class="card-body">
                @foreach($calonSiswa->pendaftaranJurusan->sortBy('urutan_pilihan') as $pj)
                @php
                    $jc = match($pj->jurusan->kode_jurusan){
                        'AKL'  => ['bg'=>'#ede9fe','color'=>'#5b21b6'],
                        'TJKT' => ['bg'=>'#dbeafe','color'=>'#1e40af'],
                        'MPLB' => ['bg'=>'#fce7f3','color'=>'#9d174d'],
                        default=> ['bg'=>'#f1f5f9','color'=>'#475569'],
                    };
                @endphp
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem .75rem;background:#f8fafc;border-radius:9px;border:1px solid #e2e8f0;margin-bottom:.5rem">
                    <div style="display:flex;align-items:center;gap:.6rem">
                        <div style="width:32px;height:32px;border-radius:7px;background:{{ $jc['bg'] }};color:{{ $jc['color'] }};display:grid;place-items:center;font-weight:800;font-size:.62rem;flex-shrink:0">
                            {{ $pj->urutan_pilihan }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.84rem">{{ $pj->jurusan->nama_jurusan }}</div>
                            <div style="font-size:.72rem;color:#64748b">{{ $pj->jurusan->kode_jurusan }}</div>
                        </div>
                    </div>
                    <span class="badge {{ $pj->status==='Diterima'?'badge-diterima':($pj->status==='Ditolak'?'badge-ditolak':'badge-menunggu') }}">
                        {{ $pj->status }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Pembayaran --}}
<div class="card" style="margin-bottom:1.25rem">
    <div class="card-header">
        <span><i class="fas fa-money-bill-wave" style="color:#1a4a8a;margin-right:.5rem"></i> Pembayaran</span>
    </div>
    @if($calonSiswa->pembayaran->isEmpty())
    <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:.875rem">
        <i class="fas fa-receipt" style="font-size:1.75rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
        Belum ada data pembayaran.
    </div>
    @else
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calonSiswa->pembayaran->sortByDesc('tanggal_bayar') as $bayar)
                @php
                    $sc = match($bayar->status_pembayaran){
                        'Terverifikasi'      => 'badge-diterima',
                        'Ditolak'            => 'badge-ditolak',
                        default              => 'badge-menunggu',
                    };
                @endphp
                <tr>
                    <td>{{ $bayar->metodePembayaran->deskripsi_metode_bayar }}</td>
                    <td style="font-weight:700;white-space:nowrap">
                        Rp {{ number_format($bayar->jumlah_bayar,0,',','.') }}
                    </td>
                    <td class="text-muted">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                    <td class="text-muted">{{ $bayar->keterangan ?? '-' }}</td>
                    <td><span class="badge {{ $sc }}">{{ $bayar->status_pembayaran }}</span></td>
                    <td>
                        @if($bayar->bukti_bayar)
                        <a href="{{ Storage::url($bayar->bukti_bayar) }}" target="_blank"
                           class="btn btn-outline btn-sm btn-icon" title="Lihat bukti">
                            <i class="fas fa-eye"></i>
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.pembayaran.show', $bayar->id_pembayaran) }}"
                        class="btn btn-primary btn-sm">
                            <i class="fas fa-clipboard-check"></i> Verifikasi
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Alamat & Wali --}}
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-map-marker-alt" style="color:#1a4a8a;margin-right:.5rem"></i> Alamat</span>
        </div>
        <div class="card-body" style="font-size:.875rem">
            @forelse($calonSiswa->alamatCalonSiswa as $ac)
            <div style="padding:.75rem;background:#f8fafc;border-radius:9px;border:1px solid #e2e8f0;margin-bottom:.6rem">
                <span class="badge badge-menunggu" style="margin-bottom:.45rem;font-size:.65rem">
                    {{ $ac->jenisAlamat->deskripsi_jenis_alamat ?? '-' }}
                </span>
                <div style="font-weight:600">{{ $ac->alamat->jenis_tempat_tinggal }}</div>
                <div class="text-muted">{{ $ac->alamat->nama_jalan }}</div>
                @if($ac->alamat->kelurahan)
                <div class="text-muted">{{ $ac->alamat->kelurahan }}, {{ $ac->alamat->kecamatan }}</div>
                @endif
                <div class="text-muted">{{ $ac->alamat->kota ?? $ac->alamat->kabupaten_kota }}, {{ $ac->alamat->provinsi }} {{ $ac->alamat->kode_pos }}</div>
            </div>
            @empty
            <div style="color:#94a3b8;text-align:center;padding:1rem">Belum ada data alamat.</div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-user-friends" style="color:#1a4a8a;margin-right:.5rem"></i> Orang Tua / Wali</span>
        </div>
        <div class="card-body" style="font-size:.875rem">
            @forelse($calonSiswa->relasiSiswa as $rs)
            @if($rs->wali)
            @php
                $labelHub = match($rs->wali->hubungan){
                    'AY'=>'Ayah','IB'=>'Ibu','WL'=>'Wali',default=>$rs->wali->hubungan
                };
            @endphp
            <div style="padding:.75rem;background:#f8fafc;border-radius:9px;border:1px solid #e2e8f0;margin-bottom:.6rem">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.35rem">
                    <strong>{{ $rs->wali->nama_depan }} {{ $rs->wali->nama_belakang }}</strong>
                    <span class="badge badge-cadangan">{{ $labelHub }}</span>
                </div>
                @if($rs->wali->pekerjaan)
                <div class="text-muted"><i class="fas fa-briefcase" style="width:14px"></i> {{ $rs->wali->pekerjaan }}</div>
                @endif
                @if($rs->wali->nomor_hp)
                <div class="text-muted"><i class="fas fa-phone" style="width:14px"></i> {{ $rs->wali->nomor_hp }}</div>
                @endif
            </div>
            @endif
            @empty
            <div style="color:#94a3b8;text-align:center;padding:1rem">Belum ada data wali.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
