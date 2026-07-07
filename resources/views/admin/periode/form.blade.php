@extends('layouts.admin')
@section('title', isset($periode) ? 'Edit Periode PPDB' : 'Tambah Periode PPDB')
@section('page-title', isset($periode) ? 'Edit Periode PPDB' : 'Tambah Periode PPDB')

@section('content')

<div style="max-width:720px">
    <div class="card">
        <div class="card-header">
            <span>
                <i class="fas fa-calendar-alt" style="color:#1a4a8a;margin-right:.5rem"></i>
                {{ $periode ? 'Edit: ' . $periode->nama_periode : 'Periode Baru' }}
            </span>
            <a href="{{ route('admin.periode.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left" style="margin-right:.35rem"></i> Kembali
            </a>
        </div>
        <div class="card-body" style="padding:1.5rem">

            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.85rem 1rem;margin-bottom:1.25rem;color:#991b1b;font-size:.875rem">
                <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
                <ul style="margin:.4rem 0 0 1.25rem;padding:0">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST"
                  action="{{ $periode ? route('admin.periode.update', $periode->id_periode) : route('admin.periode.store') }}">
                @csrf
                @if($periode) @method('PUT') @endif

                {{-- Nama Periode --}}
                <div class="form-group" style="margin-bottom:1.1rem">
                    <label class="form-label">Nama Periode <span style="color:#dc2626">*</span></label>
                    <input type="text" name="nama_periode" class="form-control"
                           value="{{ old('nama_periode', $periode?->nama_periode) }}"
                           placeholder="cth: PPDB 2025/2026 Gelombang 1"
                           required>
                </div>

                {{-- Tahun Ajaran & Gelombang --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                    <div class="form-group">
                        <label class="form-label">Tahun Ajaran <span style="color:#dc2626">*</span></label>
                        <input type="number" name="tahun_ajaran" class="form-control"
                               value="{{ old('tahun_ajaran', $periode?->tahun_ajaran ?? date('Y')) }}"
                               min="2020" max="2099" required>
                        <small class="text-muted">Tahun awal, misal 2025 untuk 2025/2026</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gelombang <span style="color:#dc2626">*</span></label>
                        <input type="number" name="gelombang" class="form-control"
                               value="{{ old('gelombang', $periode?->gelombang ?? 1) }}"
                               min="1" max="10" required>
                    </div>
                </div>

                {{-- Tanggal Buka & Tutup --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                    <div class="form-group">
                        <label class="form-label">Tanggal Buka <span style="color:#dc2626">*</span></label>
                        <input type="date" name="tanggal_buka" class="form-control"
                               value="{{ old('tanggal_buka', $periode?->tanggal_buka?->format('Y-m-d')) }}"
                               required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Tutup <span style="color:#dc2626">*</span></label>
                        <input type="date" name="tanggal_tutup" class="form-control"
                               value="{{ old('tanggal_tutup', $periode?->tanggal_tutup?->format('Y-m-d')) }}"
                               required>
                    </div>
                </div>

                {{-- Tanggal Pengumuman --}}
                <div class="form-group" style="margin-bottom:1.1rem">
                    <label class="form-label">Tanggal Pengumuman</label>
                    <input type="date" name="tanggal_pengumuman" class="form-control"
                           value="{{ old('tanggal_pengumuman', $periode?->tanggal_pengumuman?->format('Y-m-d')) }}">
                    <small class="text-muted">Opsional — tanggal pengumuman hasil seleksi.</small>
                </div>

                {{-- Biaya Pendaftaran --}}
                <div class="form-group" style="margin-bottom:1.1rem">
                    <label class="form-label">Biaya Pendaftaran (Rp) <span style="color:#dc2626">*</span></label>
                    <input type="number" name="biaya_pendaftaran" class="form-control"
                           value="{{ old('biaya_pendaftaran', $periode?->biaya_pendaftaran ?? 0) }}"
                           min="0" step="1000" required>
                    <small class="text-muted">Masukkan 0 jika gratis.</small>
                </div>

                {{-- Keterangan --}}
                <div class="form-group" style="margin-bottom:1.1rem">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"
                              placeholder="Informasi tambahan (persyaratan khusus, kuota, dsb.)">{{ old('keterangan', $periode?->keterangan) }}</textarea>
                </div>

                {{-- Toggle Aktif --}}
                <div class="form-group" style="margin-bottom:1.5rem">
                    <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;user-select:none">
                        <input type="hidden"  name="is_aktif" value="0">
                        <input type="checkbox" name="is_aktif" value="1"
                               {{ old('is_aktif', $periode?->is_aktif) ? 'checked' : '' }}
                               style="width:16px;height:16px;cursor:pointer">
                        <span style="font-weight:600">Jadikan periode aktif</span>
                    </label>
                    <small class="text-muted" style="margin-left:1.6rem">
                        Hanya satu periode yang dapat aktif sekaligus.
                        Mengaktifkan periode ini akan menonaktifkan periode lain secara otomatis.
                    </small>
                </div>

                <div style="display:flex;gap:.75rem">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" style="margin-right:.4rem"></i>
                        {{ $periode ? 'Simpan Perubahan' : 'Tambah Periode' }}
                    </button>
                    <a href="{{ route('admin.periode.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
