<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use App\Services\PengaturanBackup;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackupOtomatis extends Command
{
    protected $signature = 'backup:otomatis';

    protected $description = 'Membuat backup database otomatis jika sudah waktunya sesuai pengaturan (mingguan/bulanan)';

    public function handle(BackupService $backupService): int
    {
        $pengaturan = PengaturanBackup::ambil();
        $today      = Carbon::today();

        if ($pengaturan->jenis === 'nonaktif') {
            $this->info('Backup otomatis nonaktif, tidak ada yang dilakukan.');
            return self::SUCCESS;
        }

        // Sudah dijalankan hari ini? Jangan dobel.
        if ($pengaturan->sudahJalanHariIni($today)) {
            $this->info('Backup otomatis sudah dijalankan hari ini.');
            return self::SUCCESS;
        }

        $harusJalan = false;

        if ($pengaturan->jenis === 'mingguan') {
            $harusJalan = $today->dayOfWeek === $pengaturan->hari;
        } elseif ($pengaturan->jenis === 'bulanan') {
            // Tanggal dibatasi 1-28 saat disimpan sehingga selalu valid di semua bulan
            $harusJalan = $today->day === $pengaturan->tanggal;
        }

        if (!$harusJalan) {
            $this->info('Belum waktunya backup otomatis.');
            return self::SUCCESS;
        }

        $hasil = $backupService->buatBackup('terjadwal');

        $pengaturan->tandaiSudahJalanHariIni($today);

        $this->info("Backup otomatis berhasil: {$hasil['filename']} ({$hasil['ukuran']})");

        return self::SUCCESS;
    }
}
