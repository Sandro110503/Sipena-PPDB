@extends('layouts.admin')
@section('title', $metode ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran')
@section('page-title', $metode ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran')
@section('content')
<div class="card" style="max-width:520px">
    <div class="card-header"><i class="fas fa-credit-card" style="color:#1a4a8a;margin-right:.5rem"></i> {{ $metode ? 'Edit' : 'Tambah' }} Metode Pembayaran</div>
    <div class="card-body">
        <form method="POST" action="{{ $metode ? route('admin.metode-pembayaran.update', $metode) : route('admin.metode-pembayaran.store') }}">
            @csrf @if($metode) @method('PUT') @endif
            <div class="form-group">
                <label class="form-label">Kode Metode <span style="color:red">*</span></label>
                <input type="text" name="kode_metode_bayar" class="form-control @error('kode_metode_bayar') is-invalid @enderror"
                    value="{{ old('kode_metode_bayar', $metode?->kode_metode_bayar) }}"
                    placeholder="cth: TRANSFER_BCA" {{ $metode ? 'readonly' : '' }}>
                @error('kode_metode_bayar')<div style="color:red;font-size:.75rem;margin-top:.25rem">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi <span style="color:red">*</span></label>
                <input type="text" name="deskripsi_metode_bayar" class="form-control @error('deskripsi_metode_bayar') is-invalid @enderror"
                    value="{{ old('deskripsi_metode_bayar', $metode?->deskripsi_metode_bayar) }}"
                    placeholder="Transfer Bank BCA">
                @error('deskripsi_metode_bayar')<div style="color:red;font-size:.75rem;margin-top:.25rem">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;gap:.75rem;margin-top:1.25rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('admin.metode-pembayaran.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
