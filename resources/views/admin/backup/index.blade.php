@extends('layouts.admin')
@section('title','Backup Database')
@section('page-title','Backup Database')

@push('styles')
<style>
.backup-info{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.85rem;margin-bottom:1.25rem;}
.info-card{background:#fff;border-radius:12px;border:1px solid var(--border);padding:1rem 1.1rem;display:flex;align-items:center;gap:.85rem;}
.info-icon{width:42px;height:42px;border-radius:10px;display:grid;place-items:center;font-size:1.1rem;flex-shrink:0;}
.info-val{font-size:1.3rem;font-weight:800;line-height:1;}
.info-lbl{font-size:.7rem;color:var(--muted);margin-top:2px;}
.backup-card{background:#fff;border-radius:12px;border:1px solid var(--border);overflow:hidden;margin-bottom:1.25rem;}
.backup-card-head{padding:.9rem 1.1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.65rem;}
.backup-card-head h3{font-size:.9rem;font-weight:700;display:flex;align-items:center;gap:.5rem;}
.file-row{display:flex;align-items:center;justify-content:space-between;padding:.85rem 1.1rem;border-bottom:1px solid #f8fafc;flex-wrap:wrap;gap:.65rem;transition:.15s;}
.file-row:last-child{border-bottom:none;}
.file-row:hover{background:#fafafa;}
.file-icon{width:40px;height:40px;border-radius:9px;background:#dbeafe;display:grid;place-items:center;color:#1a4a8a;font-size:1.1rem;flex-shrink:0;}
.file-info{flex:1;min-width:180px;}
.file-info strong{display:block;font-size:.82rem;font-weight:700;color:#0f2744;word-break:break-all;}
.file-meta{font-size:.72rem;color:#64748b;margin-top:.2rem;display:flex;gap:.75rem;flex-wrap:wrap;}
.file-meta span{display:flex;align-items:center;gap:.3rem;}
.empty-backup{padding:3rem 1.5rem;text-align:center;color:#94a3b8;}
.empty-backup i{font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.3;}
.empty-backup p{font-size:.875rem;}
.how-to{background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:1.1rem 1.25rem;margin-bottom:1.25rem;}
.how-to h4{font-size:.85rem;font-weight:700;color:#166534;margin-bottom:.6rem;display:flex;align-items:center;gap:.4rem;}
.how-to ol{padding-left:1.25rem;font-size:.8rem;color:#166534;line-height:2;}
.warning-box{background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:.85rem 1rem;margin-bottom:1.25rem;font-size:.8rem;color:#92400e;display:flex;gap:.6rem;align-items:flex-start;}
.jenis-badge{font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:20px;display:inline-flex;align-items:center;gap:.25rem;}
.jenis-manual{background:#dbeafe;color:#1e40af;}
.jenis-terjadwal{background:#ede9fe;color:#5b21b6;}
.search-bar{display:flex;gap:.6rem;flex-wrap:wrap;padding:.9rem 1.1rem;border-bottom:1px solid var(--border);background:#f8fafc;}
.search-bar input,.search-bar select{border:1px solid var(--border);border-radius:8px;padding:.5rem .7rem;font-size:.8rem;flex:1;min-width:160px;}
.jadwal-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.9rem;align-items:end;}
.jadwal-form label{display:block;font-size:.75rem;font-weight:700;color:#334155;margin-bottom:.35rem;}
.jadwal-form select{width:100%;border:1px solid var(--border);border-radius:8px;padding:.55rem .7rem;font-size:.83rem;}
.jadwal-status{font-size:.72rem;padding:.3rem .65rem;border-radius:8px;background:#f0fdf4;color:#166534;border:1px solid #86efac;display:inline-block;margin-top:.5rem;}
</style>
@endpush

@section('content')

{{-- Info Stat --}}
<div class="backup-info">
    <div class="info-card">
        <div class="info-icon" style="background:#dbeafe;color:#1a4a8a"><i class="fas fa-database"></i></div>
        <div>
            <div class="info-val">{{ $dbName }}</div>
            <div class="info-lbl">Nama Database</div>
        </div>
    </div>
    <div class="info-card">
        <div class="info-icon" style="background:#dcfce7;color:#166534"><i class="fas fa-file-archive"></i></div>
        <div>
            <div class="info-val">{{ count($semuaFiles) }}</div>
            <div class="info-lbl">Total File Backup</div>
        </div>
    </div>
    <div class="info-card">
        <div class="info-icon" style="background:#fef3c7;color:#92400e"><i class="fas fa-clock"></i></div>
        <div>
            <div class="info-val">{{ count($semuaFiles) > 0 ? \Carbon\Carbon::createFromTimestamp($semuaFiles[0]['ts'])->diffForHumans() : '-' }}</div>
            <div class="info-lbl">Backup Terakhir</div>
        </div>
    </div>
    <div class="info-card">
        <div class="info-icon" style="background:#ede9fe;color:#5b21b6"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="info-val" style="font-size:1rem">{{ $pengaturan->jenis === 'nonaktif' ? 'Nonaktif' : ($pengaturan->jenis === 'mingguan' ? 'Mingguan' : 'Bulanan') }}</div>
            <div class="info-lbl">Backup Otomatis</div>
        </div>
    </div>
</div>

{{-- Peringatan --}}
<div class="warning-box">
    <i class="fas fa-exclamation-triangle" style="flex-shrink:0;margin-top:.1rem"></i>
    <div>
        <strong>Penting:</strong> Lakukan backup secara rutin terutama sebelum melakukan perubahan besar pada sistem.
        File backup tersimpan di server dan dapat didownload kapan saja. Hapus backup lama secara berkala untuk menghemat ruang penyimpanan.
    </div>
</div>

{{-- Tombol Backup --}}
<div class="backup-card">
    <div class="backup-card-head">
        <h3><i class="fas fa-plus-circle" style="color:#1a4a8a"></i> Buat Backup Baru</h3>
    </div>
    <div style="padding:1.1rem">
        <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">
            Klik tombol di bawah untuk membuat backup seluruh database <strong>{{ $dbName }}</strong> saat ini.
            File backup berformat <code>.sql</code> dan mencakup semua tabel beserta datanya.
        </p>
        <form method="POST" action="{{ route('admin.backup.proses') }}" id="formBackup">
            @csrf
            <button type="button" class="btn btn-primary" id="btnBackup" onclick="konfirmasiBackup()">
                <i class="fas fa-database"></i> Buat Backup Sekarang
            </button>
        </form>
    </div>
</div>

{{-- Cara Restore --}}
<div class="how-to">
    <h4><i class="fas fa-info-circle"></i> Cara Restore Database dari File Backup</h4>
    <ol>
        <li>Download file backup (.sql) yang ingin direstore</li>
        <li>Buka phpMyAdmin atau MySQL Workbench</li>
        <li>Pilih database <strong>{{ $dbName }}</strong></li>
        <li>Klik menu <strong>Import</strong>, lalu pilih file .sql yang sudah didownload</li>
        <li>Klik <strong>Go / Eksekusi</strong> dan tunggu hingga selesai</li>
    </ol>
</div>

{{-- Jadwal Backup Otomatis --}}
<div class="backup-card">
    <div class="backup-card-head">
        <h3><i class="fas fa-calendar-alt" style="color:#5b21b6"></i> Jadwal Backup Otomatis</h3>
    </div>
    <div style="padding:1.1rem">
        <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">
            Backup akan dibuat otomatis oleh sistem sesuai jadwal di bawah ini (tanpa perlu diklik manual).
            Saat ini: <strong>{{ $pengaturan->labelJenis() }}</strong>.
        </p>
        <form method="POST" action="{{ route('admin.backup.pengaturan') }}" id="formJadwal">
            @csrf
            <div class="jadwal-form">
                <div>
                    <label for="jenis">Frekuensi</label>
                    <select name="jenis" id="jenis" onchange="toggleJadwalFields()">
                        <option value="nonaktif" {{ $pengaturan->jenis === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="mingguan" {{ $pengaturan->jenis === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ $pengaturan->jenis === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div id="fieldHari" style="{{ $pengaturan->jenis !== 'mingguan' ? 'display:none' : '' }}">
                    <label for="hari">Setiap Hari</label>
                    <select name="hari" id="hari">
                        @foreach(\App\Services\PengaturanBackup::HARI as $val => $label)
                        <option value="{{ $val }}" {{ $pengaturan->hari === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="fieldTanggal" style="{{ $pengaturan->jenis !== 'bulanan' ? 'display:none' : '' }}">
                    <label for="tanggal">Setiap Tanggal</label>
                    <select name="tanggal" id="tanggal">
                        @for($t = 1; $t <= 28; $t++)
                        <option value="{{ $t }}" {{ $pengaturan->tanggal === $t ? 'selected' : '' }}>Tanggal {{ $t }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        <i class="fas fa-save"></i> Simpan Jadwal
                    </button>
                </div>
            </div>
        </form>
        <div class="jadwal-status">
            <i class="fas fa-clock"></i> Dijalankan otomatis setiap hari pukul 01:00
        </div>
    </div>
</div>

{{-- Daftar File Backup --}}
<div class="backup-card">
    <div class="backup-card-head">
        <h3><i class="fas fa-history" style="color:#1a4a8a"></i> Riwayat Backup</h3>
        <span style="font-size:.75rem;color:#64748b">{{ count($files) }} dari {{ count($semuaFiles) }} file</span>
    </div>

    <form method="GET" action="{{ route('admin.backup.index') }}" class="search-bar">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama file / tanggal, mis. 2026-07-23...">
        <select name="jenis">
            <option value="">Semua Jenis</option>
            <option value="manual" {{ $jenis === 'manual' ? 'selected' : '' }}>Manual</option>
            <option value="terjadwal" {{ $jenis === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
        @if($cari || $jenis)
        <a href="{{ route('admin.backup.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>

    @if(empty($files))
    <div class="empty-backup">
        <i class="fas fa-database"></i>
        @if($cari || $jenis)
        <p>Tidak ada file backup yang cocok dengan pencarian.</p>
        @else
        <p>Belum ada file backup.</p>
        <p style="margin-top:.35rem;font-size:.78rem">Klik "Buat Backup Sekarang" untuk membuat backup pertama.</p>
        @endif
    </div>
    @else
    @foreach($files as $file)
    <div class="file-row">
        <div class="file-icon"><i class="fas fa-file-code"></i></div>
        <div class="file-info">
            <strong>{{ $file['nama'] }}</strong>
            <div class="file-meta">
                <span class="jenis-badge {{ $file['jenis'] === 'terjadwal' ? 'jenis-terjadwal' : 'jenis-manual' }}">
                    <i class="fas {{ $file['jenis'] === 'terjadwal' ? 'fa-robot' : 'fa-hand-pointer' }}"></i>
                    {{ $file['jenis'] === 'terjadwal' ? 'Terjadwal' : 'Manual' }}
                </span>
                <span><i class="fas fa-hdd"></i> {{ $file['ukuran'] }}</span>
                <span><i class="fas fa-calendar"></i> {{ $file['tanggal'] }}</span>
            </div>
        </div>
        <div style="display:flex;gap:.45rem;flex-shrink:0">
            <a href="{{ route('admin.backup.download', $file['nama']) }}"
               class="btn btn-success btn-sm" title="Download">
                <i class="fas fa-download"></i> Download
            </a>
            <form method="POST"
                action="{{ route('admin.backup.hapus', $file['nama']) }}"
                class="form-delete">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
    @endif
</div>

@endsection

@push('scripts')
<script>
// Tampilkan/sembunyikan field hari (mingguan) atau tanggal (bulanan) sesuai pilihan frekuensi
function toggleJadwalFields() {
    const jenis = document.getElementById('jenis').value;
    document.getElementById('fieldHari').style.display = jenis === 'mingguan' ? '' : 'none';
    document.getElementById('fieldTanggal').style.display = jenis === 'bulanan' ? '' : 'none';
}

// Popup konfirmasi sebelum backup
function konfirmasiBackup() {
    Swal.fire({
        title: 'Buat backup sekarang?',
        text: 'Proses ini mungkin memakan waktu beberapa detik tergantung ukuran database.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Backup Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#1a4a8a',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('btnBackup');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang membuat backup...';

            // Popup loading selama proses submit form (redirect)
            Swal.fire({
                title: 'Sedang membuat backup...',
                text: 'Mohon tunggu, jangan tutup halaman ini.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            document.getElementById('formBackup').submit();
        }
    });
}

// Popup hasil backup (sukses / gagal) setelah redirect kembali dari server
@if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: @json(session('success')),
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#1a4a8a'
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Gagal!',
        text: @json(session('error')),
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc2626'
    });
@endif

document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush