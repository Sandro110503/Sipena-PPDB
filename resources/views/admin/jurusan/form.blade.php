@extends('layouts.admin')
@section('title', $jurusan ? 'Edit Jurusan' : 'Tambah Jurusan')
@section('page-title', $jurusan ? 'Edit Jurusan' : 'Tambah Jurusan')

@section('content')
<div style="margin-bottom:1rem">
    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:560px">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-school" style="color:#1a4a8a"></i>
            {{ $jurusan ? 'Edit Jurusan: '.$jurusan->nama_jurusan : 'Tambah Jurusan Baru' }}
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form method="POST"
                  action="{{ $jurusan ? route('admin.jurusan.update', $jurusan->id_jurusan) : route('admin.jurusan.store') }}">
                @csrf
                @if($jurusan) @method('PUT') @endif

                {{-- Kode Jurusan (2 digit angka) --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">
                        Kode Jurusan <span style="color:#dc2626">*</span>
                        <span style="font-size:.72rem;font-weight:400;color:#64748b">(2 digit angka)</span>
                    </label>
                    <input type="text" name="kode_jurusan"
                        value="{{ old('kode_jurusan', $jurusan?->kode_jurusan ?? $nextKode) }}"
                        class="form-control @error('kode_jurusan') is-invalid @enderror"
                        placeholder="Contoh: 01, 02, 03"
                        maxlength="2" inputmode="numeric" pattern="\d{2}"
                        style="width:80px;font-family:monospace;font-size:1rem;letter-spacing:.1em"
                        required>
                    <div style="font-size:.72rem;color:#64748b;margin-top:.25rem">
                        Kode ini menjadi <strong>awalan nomor pendaftaran</strong> siswa.
                        Contoh kode <code>01</code> → nomor <code>01062026001</code>.
                        Hanya 2 digit angka (01–99).
                    </div>
                    @error('kode_jurusan')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Singkatan (untuk badge / preview) --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">
                        Singkatan <span style="color:#dc2626">*</span>
                        <span style="font-size:.72rem;font-weight:400;color:#64748b">(ditampilkan di badge)</span>
                    </label>
                    <input type="text" name="singkatan"
                        value="{{ old('singkatan', $jurusan?->singkatan) }}"
                        class="form-control @error('singkatan') is-invalid @enderror"
                        placeholder="Contoh: AKL, TJKT, MPLB"
                        maxlength="10" style="text-transform:uppercase" required>
                    <div style="font-size:.72rem;color:#64748b;margin-top:.25rem">
                        Label singkat yang tampil di kartu dan tabel, misal <code>AKL</code>.
                    </div>
                    @error('singkatan')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Nama Jurusan --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">Nama Jurusan <span style="color:#dc2626">*</span></label>
                    <input type="text" name="nama_jurusan"
                        value="{{ old('nama_jurusan', $jurusan?->nama_jurusan) }}"
                        class="form-control @error('nama_jurusan') is-invalid @enderror"
                        placeholder="Nama lengkap jurusan" required>
                    @error('nama_jurusan')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Kapasitas --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">Kapasitas Siswa <span style="color:#dc2626">*</span></label>
                    <input type="number" name="kapasitas"
                        value="{{ old('kapasitas', $jurusan?->kapasitas ?? 36) }}"
                        class="form-control @error('kapasitas') is-invalid @enderror"
                        min="1" max="200" required>
                    <div style="font-size:.72rem;color:#64748b;margin-top:.25rem">
                        Jumlah maksimal siswa yang dapat diterima di jurusan ini.
                    </div>
                    @error('kapasitas')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Deskripsi --}}
                <div class="form-group" style="margin-bottom:.85rem">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        placeholder="Deskripsi singkat tentang kompetensi jurusan ini...">{{ old('deskripsi', $jurusan?->deskripsi) }}</textarea>
                    @error('deskripsi')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Keterangan Lainnya --}}
                <div class="form-group" style="margin-bottom:1.25rem">
                    <label class="form-label">Keterangan Lainnya</label>
                    <input type="text" name="keterangan_lainnya"
                        value="{{ old('keterangan_lainnya', $jurusan?->keterangan_lainnya) }}"
                        class="form-control @error('keterangan_lainnya') is-invalid @enderror"
                        placeholder="Informasi tambahan jika ada" maxlength="255">
                    @error('keterangan_lainnya')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                </div>

                {{-- Preview --}}
                <div style="background:#f8fafc;border-radius:10px;padding:.85rem 1rem;margin-bottom:1.25rem;border:1px solid #e2e8f0">
                    <div style="font-size:.72rem;font-weight:700;color:#64748b;margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.5px">Preview</div>
                    <div style="display:flex;align-items:center;gap:.75rem">
                        {{-- Badge pakai singkatan --}}
                        <div id="prevBadge" style="width:44px;height:44px;border-radius:10px;background:#dbeafe;color:#1e40af;display:grid;place-items:center;font-weight:800;font-size:.72rem;flex-shrink:0">
                            {{ $jurusan?->singkatan ?? '—' }}
                        </div>
                        <div>
                            <div id="prevNama" style="font-weight:700;font-size:.875rem;color:#0f2744">
                                {{ $jurusan?->nama_jurusan ?? 'Nama jurusan...' }}
                            </div>
                            <div id="prevKap" style="font-size:.72rem;color:#64748b">
                                Kapasitas: {{ $jurusan?->kapasitas ?? 36 }} siswa
                            </div>
                            {{-- Contoh nomor pendaftaran (semua angka) --}}
                            <div id="prevNomor" style="font-size:.72rem;color:#1e40af;margin-top:.25rem;font-family:monospace;background:#eff6ff;display:inline-block;padding:.15rem .4rem;border-radius:4px">
                                {{ str_pad($jurusan?->kode_jurusan ?? $nextKode ?? '??', 2, '0', STR_PAD_LEFT) . now()->format('mY') . '001' }}
                            </div>
                            <div style="font-size:.65rem;color:#94a3b8;margin-top:.1rem">contoh nomor pendaftaran</div>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ $jurusan ? 'Simpan Perubahan' : 'Tambah Jurusan' }}
                    </button>
                    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const now    = new Date();
const bulan  = String(now.getMonth() + 1).padStart(2, '0');
const tahun  = now.getFullYear();

const elKode    = document.querySelector('[name=kode_jurusan]');
const elSingkat = document.querySelector('[name=singkatan]');
const elNama    = document.querySelector('[name=nama_jurusan]');
const elKap     = document.querySelector('[name=kapasitas]');

// Hanya izinkan angka pada field kode
elKode.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 2);
    const padded = this.value.padStart(2, '0');
    document.getElementById('prevNomor').textContent =
        (this.value.length === 2 ? padded : (this.value || '??')) + bulan + tahun + '001';
});

// Singkatan → auto-uppercase + perbarui badge
elSingkat.addEventListener('input', function () {
    this.value = this.value.toUpperCase();
    document.getElementById('prevBadge').textContent = this.value || '—';
});

elNama.addEventListener('input', function () {
    document.getElementById('prevNama').textContent = this.value || 'Nama jurusan...';
});

elKap.addEventListener('input', function () {
    document.getElementById('prevKap').textContent = 'Kapasitas: ' + (this.value || '0') + ' siswa';
});
</script>
@endpush
@endsection
