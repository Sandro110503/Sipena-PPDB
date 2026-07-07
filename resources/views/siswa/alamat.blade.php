@extends('siswa.layout')
@section('title','Alamat Saya')

@push('styles')
<style>
.page-title { margin-bottom: 1.1rem; }
.page-title h1 { font-size: 1.2rem; font-weight: 800; color: #0f2744; }
.page-title p  { color: #64748b; font-size: .82rem; margin-top: .2rem; }
.form-group { margin-bottom: .9rem; }
.form-label { display: block; font-size: .78rem; font-weight: 700; color: #1e293b; margin-bottom: .35rem; }
.form-control { width: 100%; padding: .62rem .85rem; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: .875rem; color: #1e293b; background: #f8fafc; transition: border-color .2s, background .2s; }
.form-control:focus { outline: none; border-color: #1a4a8a; background: #fff; }
.form-control.is-invalid { border-color: #ef4444; }
.invalid-feedback { font-size: .72rem; color: #ef4444; margin-top: .25rem; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem 1rem; }
.btn-save { background: #0f2744; color: #fff; border: none; border-radius: 10px; padding: .7rem 1.5rem; font-family: inherit; font-size: .875rem; font-weight: 700; cursor: pointer; transition: background .2s; display: inline-flex; align-items: center; gap: .5rem; }
.btn-save:hover { background: #1a4a8a; }
.btn-outline { background: transparent; color: #1a4a8a; border: 1.5px solid #1a4a8a; border-radius: 10px; padding: .65rem 1.2rem; font-family: inherit; font-size: .875rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; text-decoration: none; }
.btn-outline:hover { background: #eff6ff; }
@media(max-width:600px) { .form-grid-2 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-title">
    <h1>Alamat Saya</h1>
    <p>Perbarui alamat tempat tinggal Anda saat ini.</p>
</div>

@php
    $alamat = $siswa->alamatCalonSiswa->first()?->alamat;
@endphp

<div class="card">
    <div class="card-header"><i class="fas fa-map-marker-alt"></i> Alamat Tempat Tinggal</div>
    <div class="card-body">
        <form method="POST" action="{{ route('siswa.alamat.update') }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Jenis Tempat Tinggal <span style="color:#dc2626">*</span></label>
                <select name="jenis_tempat_tinggal"
                    class="form-control @error('jenis_tempat_tinggal') is-invalid @enderror" required>
                    <option value="">— Pilih —</option>
                    @foreach(['Rumah Orang Tua/Wali','Sewa'] as $opt)
                    <option value="{{ $opt }}"
                        {{ old('jenis_tempat_tinggal', $alamat?->jenis_tempat_tinggal) === $opt ? 'selected' : '' }}>
                        {{ $opt }}
                    </option>
                    @endforeach
                </select>
                @error('jenis_tempat_tinggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Jalan / Alamat Lengkap <span style="color:#dc2626">*</span></label>
                <input type="text" name="nama_jalan"
                    value="{{ old('nama_jalan', $alamat?->nama_jalan) }}"
                    class="form-control @error('nama_jalan') is-invalid @enderror"
                    placeholder="Contoh: Jl. Merdeka No. 12 RT 03/RW 05" required>
                @error('nama_jalan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Kelurahan / Desa</label>
                    <input type="text" name="kelurahan"
                        value="{{ old('kelurahan', $alamat?->kelurahan) }}"
                        class="form-control" placeholder="Kelurahan / desa">
                </div>
                <div class="form-group">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" name="kecamatan"
                        value="{{ old('kecamatan', $alamat?->kecamatan) }}"
                        class="form-control" placeholder="Kecamatan">
                </div>
                <div class="form-group">
                    <label class="form-label">Kabupaten / Kota <span style="color:#dc2626">*</span></label>
                    <input type="text" name="kabupaten_kota"
                        value="{{ old('kabupaten_kota', $alamat?->kabupaten_kota) }}"
                        class="form-control @error('kabupaten_kota') is-invalid @enderror"
                        placeholder="Kabupaten / kota" required>
                    @error('kabupaten_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Provinsi <span style="color:#dc2626">*</span></label>
                    <input type="text" name="provinsi"
                        value="{{ old('provinsi', $alamat?->provinsi) }}"
                        class="form-control @error('provinsi') is-invalid @enderror"
                        placeholder="Provinsi" required>
                    @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" name="kode_pos"
                        value="{{ old('kode_pos', $alamat?->kode_pos) }}"
                        class="form-control" placeholder="Kode pos" maxlength="10" inputmode="numeric">
                </div>
            </div>

            <div style="display:flex;gap:.65rem;flex-wrap:wrap;margin-top:.5rem">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Simpan Alamat
                </button>
                <a href="{{ route('siswa.dashboard') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
