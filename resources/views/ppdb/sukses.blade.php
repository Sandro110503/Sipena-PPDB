@extends('layouts.public')
@section('title', 'Pendaftaran Berhasil!')

@section('content')
<div style="min-height:65vh;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem">
    <div style="text-align:center;max-width:520px;width:100%">
        {{-- Ikon sukses --}}
        <div style="width:96px;height:96px;background:linear-gradient(135deg,#dcfce7,#bbf7d0);border-radius:50%;display:grid;place-items:center;margin:0 auto 1.5rem;font-size:2.75rem;color:#16a34a;box-shadow:0 8px 24px rgba(22,163,74,.2)">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1 style="font-size:1.75rem;font-weight:800;color:#0f2744;margin-bottom:.5rem">Pendaftaran Berhasil!</h1>
        <p style="color:#64748b;line-height:1.7;margin-bottom:1.75rem;font-size:.9rem">
            Data pendaftaran Anda telah berhasil disimpan dalam sistem PPDB SMK.<br>
            Simpan nomor pendaftaran Anda untuk mengecek status penerimaan.
        </p>

        {{-- Box nomor pendaftaran --}}
        <div style="background:#f0f9ff;border:2px dashed #38bdf8;border-radius:12px;padding:1.25rem;margin-bottom:1.75rem">
            <div style="font-size:.75rem;font-weight:700;color:#0369a1;letter-spacing:.5px;text-transform:uppercase;margin-bottom:.35rem">
                <i class="fas fa-ticket-alt"></i> &nbsp;Nomor Pendaftaran Anda
            </div>
            <div style="font-size:1.4rem;font-weight:800;font-family:monospace;color:#0f2744;letter-spacing:1px">
                {{ session('nomor_pendaftaran', '—') }}
            </div>
            <div style="font-size:.72rem;color:#64748b;margin-top:.35rem">
                Gunakan nomor ini bersama tanggal lahir untuk cek status
            </div>
        </div>

        {{-- Langkah selanjutnya --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;margin-bottom:1.75rem;text-align:left">
            <div style="font-size:.8rem;font-weight:700;color:#64748b;margin-bottom:.85rem;text-transform:uppercase;letter-spacing:.5px">Langkah Selanjutnya</div>
            @foreach([
                ['fa-solid fa-1','Masuk ke Portal Siswa dengan Nomor pendaftaran atau NISN dan Password yang telah dibuat saat pendaftaran'],
                ['fa-solid fa-2','Lakukan pembayaran biaya pendaftaran sesuai instruksi yang tersedia di Portal Siswa'],
                ['fa-solid fa-3','Tunggu proses verifikasi bukti pembayaran oleh panitia PPDB'],
                ['fa-solid fa-4','Pantau status pendaftaran secara berkala'],
            ] as [$icon, $text])
            <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:.65rem;font-size:.875rem">
                <div style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;display:grid;place-items:center;font-size:.75rem;color:#1a4a8a;flex-shrink:0">
                    <i class="{{ $icon }}"></i>
                </div>
                <span style="color:#475569;line-height:1.5;padding-top:.2rem">{{ $text }}</span>
            </div>
            @endforeach
        </div>

        <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap">
            <a href="{{ route('siswa.login') }}" style="background:#0f2744;color:#fff;padding:.8rem 1.75rem;border-radius:10px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem">
                <i class="fas fa-sign-in-alt"></i> Masuk Portal Siswa
            </a>
            <a href="{{ route('ppdb.cek-status') }}" style="background:#f1f5f9;color:#0f2744;padding:.8rem 1.5rem;border-radius:10px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem">
                <i class="fas fa-search"></i> Cek Status
            </a>
            <a href="{{ route('home') }}" style="background:#f1f5f9;color:#0f2744;padding:.8rem 1.5rem;border-radius:10px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem">
                <i class="fas fa-home"></i> Beranda
            </a>
        </div>
    </div>
</div>
@endsection
