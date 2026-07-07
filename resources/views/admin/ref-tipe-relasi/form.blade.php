@extends('layouts.admin')
@section('title', $tipe ? 'Edit Tipe Relasi' : 'Tambah Tipe Relasi')
@section('page-title', $tipe ? 'Edit Tipe Relasi' : 'Tambah Tipe Relasi')
@section('content')
<div class="card" style="max-width:520px">
    <div class="card-header"><i class="fas fa-list" style="color:#1a4a8a;margin-right:.5rem"></i> {{ $tipe ? 'Edit' : 'Tambah' }} Tipe Relasi</div>
    <div class="card-body">
        <form method="POST" action="{{ $tipe ? route('admin.ref-tipe-relasi.update', $tipe) : route('admin.ref-tipe-relasi.store') }}">
            @csrf @if($tipe) @method('PUT') @endif
            <div class="form-group">
                <label class="form-label">Kode <span style="color:red">*</span></label>
                <input type="text" name="kode_tipe_relasi" class="form-control @error('kode_tipe_relasi') is-invalid @enderror"
                    value="{{ old('kode_tipe_relasi', $tipe?->kode_tipe_relasi) }}"
                    placeholder="cth: AYAH" {{ $tipe ? 'readonly' : '' }}>
                @error('kode_tipe_relasi')<div style="color:red;font-size:.75rem;margin-top:.25rem">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi <span style="color:red">*</span></label>
                <input type="text" name="deskripsi_tipe_relasi" class="form-control @error('deskripsi_tipe_relasi') is-invalid @enderror"
                    value="{{ old('deskripsi_tipe_relasi', $tipe?->deskripsi_tipe_relasi) }}"
                    placeholder="Ayah Kandung">
                @error('deskripsi_tipe_relasi')<div style="color:red;font-size:.75rem;margin-top:.25rem">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;gap:.75rem;margin-top:1.25rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.ref-tipe-relasi.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
