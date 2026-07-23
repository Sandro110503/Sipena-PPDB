<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Pengaturan jadwal backup otomatis.
 *
 * Sengaja TIDAK memakai tabel database — disimpan sebagai file JSON di
 * storage/app/backups/pengaturan_backup.json supaya tidak perlu migrasi
 * atau perubahan skema database sama sekali.
 */
class PengaturanBackup
{
    private const FILE = 'backups/pengaturan_backup.json';

    public string $jenis = 'nonaktif';   // nonaktif | mingguan | bulanan
    public int $hari = 1;                // 0=Minggu ... 6=Sabtu (untuk mingguan)
    public int $tanggal = 1;             // 1-28 (untuk bulanan)
    public string $jam = '01:00';
    public ?string $terakhir_jalan = null; // format Y-m-d, cegah backup dobel di hari yang sama

    public const HARI = [
        0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
        4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu',
    ];

    public static function ambil(): self
    {
        $instance = new self();

        if (Storage::disk('local')->exists(self::FILE)) {
            $data = json_decode(Storage::disk('local')->get(self::FILE), true) ?: [];

            $instance->jenis          = $data['jenis'] ?? 'nonaktif';
            $instance->hari           = (int) ($data['hari'] ?? 1);
            $instance->tanggal        = (int) ($data['tanggal'] ?? 1);
            $instance->jam            = $data['jam'] ?? '01:00';
            $instance->terakhir_jalan = $data['terakhir_jalan'] ?? null;
        }

        return $instance;
    }

    /**
     * Simpan perubahan (partial) lalu tulis ulang file JSON.
     */
    public function simpan(array $data = []): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        Storage::disk('local')->put(self::FILE, json_encode([
            'jenis'          => $this->jenis,
            'hari'           => $this->hari,
            'tanggal'        => $this->tanggal,
            'jam'            => $this->jam,
            'terakhir_jalan' => $this->terakhir_jalan,
        ], JSON_PRETTY_PRINT));
    }

    public function tandaiSudahJalanHariIni(Carbon $tanggal): void
    {
        $this->simpan(['terakhir_jalan' => $tanggal->toDateString()]);
    }

    public function sudahJalanHariIni(Carbon $today): bool
    {
        return $this->terakhir_jalan && Carbon::parse($this->terakhir_jalan)->isSameDay($today);
    }

    public function labelJenis(): string
    {
        return match ($this->jenis) {
            'mingguan' => 'Mingguan (setiap ' . (self::HARI[$this->hari] ?? '-') . ')',
            'bulanan'  => 'Bulanan (setiap tanggal ' . $this->tanggal . ')',
            default    => 'Nonaktif',
        };
    }
}
