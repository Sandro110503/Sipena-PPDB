@extends('layouts.admin')
@section('title','Detail Wali')
@section('page-title','Detail Wali/Orang Tua')
@section('content')
<div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start">
    <div class="card">
        <div class="card-header"><i class="fas fa-user" style="color:#1a4a8a;margin-right:.5rem"></i> Data Wali</div>
        <div class="card-body">
            <table style="width:100%;font-size:.875rem">
                <tr><td style="color:var(--muted);width:40%;padding:.35rem 0">Nama Lengkap</td><td><strong>{{ $wali->nama_lengkap }}</strong></td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Jenis Kelamin</td><td>{{ $wali->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Hubungan</td><td>{{ $wali->hubungan }}</td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">No HP</td><td>{{ $wali->nomor_hp }}</td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Email</td><td>{{ $wali->email ?? '-' }}</td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Pekerjaan</td><td>{{ $wali->pekerjaan }}</td></tr>
                <tr>
                    <td style="color:var(--muted);padding:.35rem 0">Alamat</td>
                    <td>
                        {{ $wali->alamat->nama_jalan ?? '' }},
                        {{ $wali->alamat->kelurahan ?? '' }},
                        {{ $wali->alamat->kecamatan ?? '' }},
                        {{ $wali->alamat->kabupaten_kota ?? '' }},
                        {{ $wali->alamat->provinsi ?? '' }}
                        {{ $wali->alamat->kode_pos ?? '' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><i class="fas fa-link" style="color:#1a4a8a;margin-right:.5rem"></i> Siswa Terkait</div>
        <div class="card-body">
            @forelse($wali->relasiSiswa as $rel)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border)">
                <div>
                    <strong>{{ $rel->siswa?->nama_lengkap }}</strong>

                    <div style="font-size:.75rem;color:var(--muted)">
                        {{ $rel->siswa?->nomor_pendaftaran }}
                    </div>

                    @php
                        $alamatSiswa = $rel->siswa?->alamatCalonSiswa?->first()?->alamat;
                        $alamatWali = $wali->alamat;
                    @endphp

                    @if(
                        $alamatSiswa &&
                        $alamatWali &&
                        $alamatSiswa->id_alamat != $alamatWali->id_alamat
                    )
                        <div style="margin-top:.35rem;font-size:.75rem;color:#555">
                            <strong>Alamat Siswa:</strong><br>
                            {{ $alamatSiswa->nama_jalan }},
                            {{ $alamatSiswa->kelurahan }},
                            {{ $alamatSiswa->kecamatan }},
                            {{ $alamatSiswa->kabupaten_kota }},
                            {{ $alamatSiswa->provinsi }}
                            {{ $alamatSiswa->kode_pos }}
                        </div>
                    @endif

                </div>
                <a href="{{ route('admin.siswa.show', $rel->siswa) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>
            </div>
            @empty
            <p style="color:var(--muted);font-size:.875rem">Tidak ada siswa terkait.</p>
            @endforelse
        </div>
    </div>
</div>
<div style="margin-top:1rem">
    <a href="{{ route('admin.wali.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
@endsection
