@extends('layouts.public')
@section('title', 'Pendaftaran Ditutup — PPDB SMK')

@section('content')
<div class="container" style="max-width:600px;padding-top:2rem;padding-bottom:3rem">

    {{-- Ikon utama --}}
    <div style="text-align:center;margin-bottom:2rem">
        <div style="width:90px;height:90px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem">
            <i class="fas fa-calendar-times" style="font-size:2.25rem;color:#dc2626"></i>
        </div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#0f2744;margin-bottom:.5rem">
            Pendaftaran Sedang Ditutup
        </h1>
        <p style="color:#64748b;font-size:.9rem;line-height:1.6;max-width:420px;margin:0 auto">
            Saat ini tidak ada periode PPDB yang sedang berjalan.
            Silakan cek kembali sesuai jadwal pendaftaran berikutnya.
        </p>
    </div>

    {{-- Periode mendatang --}}
    @if($periodeMendatang)
    <div style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:14px;padding:1.25rem;margin-bottom:1.25rem">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.85rem">
            <div style="width:38px;height:38px;background:#dbeafe;border-radius:9px;display:grid;place-items:center;flex-shrink:0">
                <i class="fas fa-clock" style="color:#1d4ed8"></i>
            </div>
            <div>
                <div style="font-weight:800;color:#1e3a5f;font-size:.95rem">Jadwal Pendaftaran Berikutnya</div>
                <div style="font-size:.75rem;color:#3b82f6">Segera dibuka — tandai tanggalnya!</div>
            </div>
        </div>

        <div style="background:#fff;border-radius:10px;padding:1rem;border:1px solid #bfdbfe">
            <div style="font-weight:700;color:#1e293b;font-size:1rem;margin-bottom:.65rem">
                {{ $periodeMendatang->nama_periode }}
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;font-size:.82rem">
                <div>
                    <div style="color:#64748b;margin-bottom:.15rem">Tanggal Buka</div>
                    <div style="font-weight:700;color:#1d4ed8">
                        {{ $periodeMendatang->tanggal_buka->translatedFormat('d F Y') }}
                    </div>
                </div>
                <div>
                    <div style="color:#64748b;margin-bottom:.15rem">Tanggal Tutup</div>
                    <div style="font-weight:700;color:#1d4ed8">
                        {{ $periodeMendatang->tanggal_tutup->translatedFormat('d F Y') }}
                    </div>
                </div>
                @if($periodeMendatang->tanggal_pengumuman)
                <div>
                    <div style="color:#64748b;margin-bottom:.15rem">Pengumuman</div>
                    <div style="font-weight:700;color:#0f2744">
                        {{ $periodeMendatang->tanggal_pengumuman->translatedFormat('d F Y') }}
                    </div>
                </div>
                @endif
                <div>
                    <div style="color:#64748b;margin-bottom:.15rem">Biaya Pendaftaran</div>
                    <div style="font-weight:700;color:#0f2744">
                        {{ $periodeMendatang->biaya_pendaftaran > 0 ? $periodeMendatang->biaya_format : 'Gratis' }}
                    </div>
                </div>
            </div>

            @if($periodeMendatang->keterangan)
            <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid #e2e8f0;font-size:.8rem;color:#64748b">
                <i class="fas fa-info-circle" style="margin-right:.3rem"></i>
                {{ $periodeMendatang->keterangan }}
            </div>
            @endif
        </div>

        {{-- Hitung mundur --}}
        @php $hariLagi = now()->diffInDays($periodeMendatang->tanggal_buka, false); @endphp
        <div style="text-align:center;margin-top:.85rem;font-size:.82rem;color:#1d4ed8;font-weight:600">
            <i class="fas fa-hourglass-start" style="margin-right:.35rem"></i>
            Dibuka dalam <strong>{{ $hariLagi }} hari</strong> lagi
        </div>
    </div>

    {{-- Periode yang baru saja tutup --}}
    @elseif($periodeLewat)
    <div style="background:#fef3c7;border:1.5px solid #fcd34d;border-radius:14px;padding:1.25rem;margin-bottom:1.25rem">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem">
            <div style="width:38px;height:38px;background:#fde68a;border-radius:9px;display:grid;place-items:center;flex-shrink:0">
                <i class="fas fa-info-circle" style="color:#92400e"></i>
            </div>
            <div style="font-weight:800;color:#78350f;font-size:.9rem">
                Periode Terakhir Telah Berakhir
            </div>
        </div>
        <div style="font-size:.85rem;color:#92400e;line-height:1.6">
            <strong>{{ $periodeLewat->nama_periode }}</strong> telah ditutup pada
            <strong>{{ $periodeLewat->tanggal_tutup->translatedFormat('d F Y') }}</strong>.
            Pantau website ini untuk informasi jadwal PPDB selanjutnya.
        </div>
    </div>

    @else
    {{-- Tidak ada info periode sama sekali --}}
    <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:1.25rem;margin-bottom:1.25rem;text-align:center">
        <i class="fas fa-calendar" style="font-size:1.75rem;color:#cbd5e1;margin-bottom:.75rem;display:block"></i>
        <div style="font-size:.875rem;color:#64748b;line-height:1.6">
            Jadwal PPDB belum diumumkan.<br>
            Pantau terus website ini atau hubungi pihak sekolah untuk informasi terbaru.
        </div>
    </div>
    @endif

    {{-- Info hubungi sekolah --}}
    <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:1.1rem 1.25rem;margin-bottom:1.5rem">
        <div style="font-weight:700;color:#0f2744;font-size:.875rem;margin-bottom:.65rem">
            <i class="fas fa-phone-alt" style="color:#1a4a8a;margin-right:.4rem"></i>
            Butuh informasi lebih lanjut?
        </div>
        <div style="font-size:.82rem;color:#64748b;line-height:1.7">
            Silakan hubungi panitia PPDB melalui:<br>
            <span style="color:#1e293b;font-weight:600">📞 Telepon sekolah +62 856-6468-9864</span> atau kunjungi langsung pada jam kerja.<br>
            <span style="color:#1e293b;font-weight:600">🌐 Website ini</span> akan diperbarui secara berkala.
        </div>
    </div>

    {{-- Tombol navigasi --}}
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <a href="{{ route('ppdb.cek-status') }}"
           style="flex:1;min-width:140px;background:#0f2744;color:#fff;padding:.8rem 1.25rem;border-radius:10px;font-weight:700;text-decoration:none;text-align:center;font-size:.875rem;display:flex;align-items:center;justify-content:center;gap:.45rem">
            <i class="fas fa-search"></i> Cek Status Pendaftaran
        </a>
        <a href="{{ route('home') }}"
           style="flex:1;min-width:140px;background:#fff;color:#0f2744;border:1.5px solid #e2e8f0;padding:.8rem 1.25rem;border-radius:10px;font-weight:700;text-decoration:none;text-align:center;font-size:.875rem;display:flex;align-items:center;justify-content:center;gap:.45rem">
            <i class="fas fa-home"></i> Kembali ke Beranda
        </a>
    </div>

</div>
@endsection
