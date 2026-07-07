@extends('layouts.admin')
@section('title', $jenis ? 'Edit Jenis Alamat' : 'Tambah Jenis Alamat')
@section('page-title', $jenis ? 'Edit Jenis Alamat' : 'Tambah Jenis Alamat')
@section('content')
<div class="card" style="max-width:520px">
    <div class="card-header"><i class="fas fa-map-marker-alt" style="color:#1a4a8a;margin-right:.5rem"></i> {{ $jenis ? 'Edit' : 'Tambah' }} Jenis Alamat</div>
    <div class="card-body">
        <form method="POST" action="{{ $jenis ? route('admin.ref-jenis-alamat.update', $jenis) : route('admin.ref-jenis-alamat.store') }}">
            @csrf @if($jenis) @method('PUT') @endif
            <div class="form-group">
                <label class="form-label">Kode <span style="color:red">*</span></label>
                <input type="text" name="kode_jenis_alamat" class="form-control @error('kode_jenis_alamat') is-invalid @enderror"
                    value="{{ old('kode_jenis_alamat', $jenis?->kode_jenis_alamat) }}"
                    placeholder="cth: RUMAH" {{ $jenis ? 'readonly' : '' }}>
                @error('kode_jenis_alamat')<div style="color:red;font-size:.75rem;margin-top:.25rem">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi <span style="color:red">*</span></label>
                <input type="text" name="deskripsi_jenis_alamat" class="form-control @error('deskripsi_jenis_alamat') is-invalid @enderror"
                    value="{{ old('deskripsi_jenis_alamat', $jenis?->deskripsi_jenis_alamat) }}"
                    placeholder="Rumah Tinggal">
                @error('deskripsi_jenis_alamat')<div style="color:red;font-size:.75rem;margin-top:.25rem">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;gap:.75rem;margin-top:1.25rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.ref-jenis-alamat.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
