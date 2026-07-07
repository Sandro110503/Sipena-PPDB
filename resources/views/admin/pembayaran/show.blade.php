@extends('layouts.admin')
@section('title','Detail Pembayaran')
@section('page-title','Detail Pembayaran')
@section('content')

<div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start">
    {{-- Info Pembayaran --}}
    <div class="card">
        <div class="card-header"><i class="fas fa-receipt" style="color:#1a4a8a;margin-right:.5rem"></i> Informasi Pembayaran</div>
        <div class="card-body">
            <table style="width:100%;font-size:.875rem">
                <tr><td style="color:var(--muted);width:40%;padding:.35rem 0">ID Pembayaran</td><td><code>{{ $pembayaran->id_pembayaran }}</code></td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Tanggal Bayar</td><td>{{ $pembayaran->tanggal_bayar?->format('d F Y') }}</td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Metode</td><td>{{ $pembayaran->metodePembayaran?->deskripsi_metode_bayar ?? $pembayaran->kode_metode_bayar }}</td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Jumlah</td><td><strong style="font-size:1.1rem">Rp {{ number_format($pembayaran->jumlah_bayar,0,',','.') }}</strong></td></tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Status</td>
                    <td>
                        @php $sc = match($pembayaran->status_pembayaran){'Terverifikasi'=>'diterima','Ditolak'=>'ditolak',default=>'menunggu'}; @endphp
                        <span class="badge badge-{{ $sc }}">{{ $pembayaran->status_pembayaran }}</span>
                    </td>
                </tr>
                <tr><td style="color:var(--muted);padding:.35rem 0">Keterangan</td><td>{{ $pembayaran->keterangan ?? '-' }}</td></tr>
            </table>

            @if($pembayaran->bukti_bayar)
            <div style="margin-top:1rem">
                <div style="font-size:.78rem;font-weight:700;margin-bottom:.5rem">Bukti Pembayaran</div>
                <img src="{{ Storage::url($pembayaran->bukti_bayar) }}" alt="Bukti" style="max-width:100%;border-radius:8px;border:1px solid var(--border)" onerror="this.style.display='none'">
                <a href="{{ Storage::url($pembayaran->bukti_bayar) }}" target="_blank" class="btn btn-outline btn-sm" style="margin-top:.5rem"><i class="fas fa-external-link-alt"></i> Buka</a>
            </div>
            @endif
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem">
        {{-- Info Siswa --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-user-graduate" style="color:#1a4a8a;margin-right:.5rem"></i> Data Siswa</div>
            <div class="card-body" style="font-size:.875rem">
                <strong>{{ $pembayaran->siswa?->nama_lengkap }}</strong>
                <div style="color:var(--muted);margin-top:.25rem">{{ $pembayaran->siswa?->nomor_pendaftaran }}</div>
                <div>{{ $pembayaran->siswa?->nisn }}</div>
                <a href="{{ route('admin.siswa.show', $pembayaran->siswa) }}" class="btn btn-outline btn-sm" style="margin-top:.75rem"><i class="fas fa-eye"></i> Lihat Profil Siswa</a>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-edit" style="color:#1a4a8a;margin-right:.5rem"></i> Perbarui Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pembayaran.verifikasi', $pembayaran) }}">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="status_pembayaran" class="form-control">
                            @foreach(['Menunggu Verifikasi','Terverifikasi','Ditolak'] as $s)
                            <option value="{{ $s }}" {{ $pembayaran->status_pembayaran==$s?'selected':'' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan opsional...">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:1rem">
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
@endsection
