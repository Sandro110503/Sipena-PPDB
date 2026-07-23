<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    /**
     * Buat file backup database baru.
     *
     * @param  string $jenis 'manual' (dari klik admin) atau 'terjadwal' (dari scheduler)
     * @return array{filename:string,ukuran:string,path:string}
     */
    public function buatBackup(string $jenis = 'manual'): array
    {
        $dbName = config('database.connections.mysql.database');

        $prefix   = $jenis === 'terjadwal' ? 'backup_ppdb_terjadwal_' : 'backup_ppdb_manual_';
        $filename = $prefix . date('Y-m-d_H-i-s') . '.sql';

        $sql = $this->generateSqlDump($dbName, $jenis);

        Storage::disk('local')->put('backups/' . $filename, $sql);

        $ukuran = $this->formatBytes(Storage::disk('local')->size('backups/' . $filename));

        $pembuat = $jenis === 'terjadwal'
            ? 'Sistem (terjadwal otomatis)'
            : (Auth::guard('admin')->user()->nama ?? 'Sistem');

        ActivityLog::catat(
            'Backup',
            'backup',
            "Membuat backup database: {$filename} ({$ukuran}) — dibuat oleh {$pembuat}."
        );

        return [
            'filename' => $filename,
            'ukuran'   => $ukuran,
            'path'     => 'backups/' . $filename,
        ];
    }

    /**
     * Ambil daftar file backup, dengan opsi pencarian nama file dan filter jenis.
     *
     * @param  string|null $cari   Kata kunci nama file (mis. tanggal atau "manual"/"terjadwal")
     * @param  string|null $jenis  'manual' | 'terjadwal' | null (semua)
     */
    public function daftarBackup(?string $cari = null, ?string $jenis = null): array
    {
        $files = [];

        if (!Storage::disk('local')->exists('backups')) {
            return $files;
        }

        $allFiles = Storage::disk('local')->files('backups');

        foreach ($allFiles as $file) {
            if (!str_ends_with($file, '.sql')) {
                continue;
            }

            $nama = basename($file);

            // Tentukan jenis dari nama file
            $jenisFile = str_contains($nama, '_terjadwal_') ? 'terjadwal' : 'manual';

            if ($jenis && $jenis !== $jenisFile) {
                continue;
            }

            if ($cari && !str_contains(strtolower($nama), strtolower($cari))) {
                continue;
            }

            $files[] = [
                'nama'    => $nama,
                'path'    => $file,
                'jenis'   => $jenisFile,
                'ukuran'  => $this->formatBytes(Storage::disk('local')->size($file)),
                'tanggal' => date('d M Y, H:i:s', Storage::disk('local')->lastModified($file)),
                'ts'      => Storage::disk('local')->lastModified($file),
            ];
        }

        usort($files, fn($a, $b) => $b['ts'] - $a['ts']);

        return $files;
    }

    private function generateSqlDump(string $dbName, string $jenis): string
    {
        $pdo = DB::connection()->getPdo();

        $pembuat = $jenis === 'terjadwal'
            ? 'Sistem (terjadwal otomatis)'
            : (Auth::guard('admin')->user()->nama ?? 'Sistem');

        $sql  = "-- ============================================\n";
        $sql .= "-- PPDB SMK - Backup Database\n";
        $sql .= "-- Database  : {$dbName}\n";
        $sql .= "-- Dibuat    : " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Dibuat oleh: {$pembuat}\n";
        $sql .= "-- ============================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $sql .= "-- -------\n";
            $sql .= "-- Tabel: `{$table}`\n";
            $sql .= "-- -------\n\n";

            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createSql  = $createStmt['Create Table'] ?? $createStmt[array_key_last($createStmt)];
            $sql .= $createSql . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $columns = '`' . implode('`, `', array_keys($rows[0])) . '`';
                $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES\n";

                $valueRows = [];
                foreach ($rows as $row) {
                    $vals = array_map(function ($val) use ($pdo) {
                        if ($val === null) return 'NULL';
                        return $pdo->quote($val);
                    }, $row);
                    $valueRows[] = '(' . implode(', ', $vals) . ')';
                }

                $sql .= implode(",\n", $valueRows) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "-- ============================================\n";
        $sql .= "-- Selesai: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- ============================================\n";

        return $sql;
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
