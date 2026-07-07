@extends('layouts.admin')
@section('title', $pegawai ? 'Edit Pegawai' : 'Tambah Pegawai')
@section('page-title', $pegawai ? 'Edit Pegawai' : 'Tambah Pegawai')

@section('content')
<div style="margin-bottom:1rem">
    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:680px">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-{{ $pegawai ? 'user-edit' : 'user-plus' }}" style="color:#1a4a8a"></i>
            {{ $pegawai ? 'Edit Data Pegawai: '.$pegawai->nama : 'Tambah Pegawai Baru' }}
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form method="POST"
                  action="{{ $pegawai ? route('admin.pegawai.update', $pegawai->id_admin) : route('admin.pegawai.store') }}">
                @csrf
                @if($pegawai) @method('PUT') @endif

                {{-- NIP & Nama --}}
                <div class="grid-2" style="margin-bottom:.85rem">
                    <div class="form-group">
                        <label class="form-label">NIP <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nip"
                            value="{{ old('nip', $pegawai?->nip) }}"
                            class="form-control @error('nip') is-invalid @enderror"
                            placeholder="Contoh: 198501012010011001"
                            maxlength="20" required>
                        <div class="form-hint">Nomor Induk Pegawai (maks. 20 karakter)</div>
                        @error('nip')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:#dc2626">*</span></label>
                        <input type="text" name="nama"
                            value="{{ old('nama', $pegawai?->nama) }}"
                            class="form-control @error('nama') is-invalid @enderror"
                            placeholder="Nama lengkap pegawai" required>
                        @error('nama')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Jabatan & JK --}}
                <div class="grid-2" style="margin-bottom:.85rem">
                    <div class="form-group">
                        <label class="form-label">Jabatan <span style="color:#dc2626">*</span></label>
                        <input type="text" name="jabatan"
                            value="{{ old('jabatan', $pegawai?->jabatan) }}"
                            class="form-control @error('jabatan') is-invalid @enderror"
                            placeholder="Contoh: Staf TU, Kepala Sekolah" required>
                        @error('jabatan')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span style="color:#dc2626">*</span></label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">— Pilih —</option>
                            <option value="L" {{ old('jenis_kelamin', $pegawai?->jenis_kelamin)==='L'?'selected':'' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pegawai?->jenis_kelamin)==='P'?'selected':'' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- No HP & Email --}}
                <div class="grid-2" style="margin-bottom:.85rem">
                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp"
                            value="{{ old('no_hp', $pegawai?->no_hp) }}"
                            class="form-control" placeholder="08xxxxxxxxxx" maxlength="15">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                            value="{{ old('email', $pegawai?->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="email@sekolah.sch.id">
                        @error('email')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Role & Status --}}
                <div class="grid-2" style="margin-bottom:.85rem">
                    <div class="form-group">
                        <label class="form-label">Role / Hak Akses <span style="color:#dc2626">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="admin"      {{ old('role',$pegawai?->role)==='admin'?'selected':'' }}>Admin</option>
                            <option value="superadmin" {{ old('role',$pegawai?->role)==='superadmin'?'selected':'' }}>Super Admin</option>
                        </select>
                        <div class="form-hint">Operator: lihat saja · Admin: kelola data · Super Admin: kelola semua</div>
                        @error('role')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                    @if($pegawai && Auth::guard('admin')->user()->isSuperAdmin() && Auth::guard('admin')->id() !== $pegawai->id)
                    <div class="form-group">
                        <label class="form-label">Status Akun</label>
                        <div style="display:flex;align-items:center;gap:.75rem;margin-top:.35rem">
                            <label style="display:flex;align-items:center;gap:.45rem;cursor:pointer;font-size:.875rem">
                                <input type="checkbox" name="is_aktif" value="1"
                                    {{ old('is_aktif', $pegawai?->is_aktif) ? 'checked' : '' }}
                                    style="width:16px;height:16px;cursor:pointer">
                                Akun Aktif
                            </label>
                        </div>
                        <div class="form-hint">Nonaktifkan untuk memblokir login tanpa menghapus data.</div>
                    </div>
                    @endif
                </div>

                {{-- Password --}}
                <div class="grid-2" style="margin-bottom:1.25rem">
                    <div class="form-group">
                        <label class="form-label">
                            Password
                            @if($pegawai) <span style="font-weight:400;color:#64748b">(kosongkan jika tidak diubah)</span> @else <span style="color:#dc2626">*</span> @endif
                        </label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ $pegawai ? 'Isi untuk mengubah password' : 'Min. 8 karakter' }}"
                            {{ $pegawai ? '' : 'required' }}>
                        <div class="form-hint">Min. 8 karakter, harus mengandung huruf dan angka.</div>
                        @error('password')<div style="font-size:.72rem;color:#dc2626;margin-top:.2rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ $pegawai ? 'Simpan Perubahan' : 'Tambah Pegawai' }}
                    </button>
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:.85rem;}
.form-hint{font-size:.72rem;color:#64748b;margin-top:.25rem;}
@media(max-width:600px){.grid-2{grid-template-columns:1fr;}}
</style>
@endpush
@endsection
