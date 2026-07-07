<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct()
    {
        // Pengecekan dilakukan di setiap method
    }

    private function checkAccess()
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses fitur backup.');
        }
    }

    // Halaman daftar backup
    public function index()
    {
        $this->checkAccess();

        $files = [];
        if (Storage::disk('local')->exists('backups')) {
            $allFiles = Storage::disk('local')->files('backups');
            foreach ($allFiles as $file) {
                if (str_ends_with($file, '.sql')) {
                    $files[] = [
                        'nama'    => basename($file),
                        'path'    => $file,
                        'ukuran'  => $this->formatBytes(Storage::disk('local')->size($file)),
                        'tanggal' => date(
                            'd M Y, H:i:s',
                            Storage::disk('local')->lastModified($file)
                        ),
                        'ts'      => Storage::disk('local')->lastModified($file),
                    ];
                }
            }
            usort($files, fn($a, $b) => $b['ts'] - $a['ts']);
        }

        $dbName = config('database.connections.mysql.database');

        return view('admin.backup.index', compact('files', 'dbName'));
    }

    // Proses backup database
    public function backup(Request $request)
    {
        $this->checkAccess();

        try {
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port', 3306);
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $filename = 'backup_ppdb_' . date('Y-m-d_H-i-s') . '.sql';

            // Generate SQL dump
            $sql = $this->generateSqlDump($dbHost, $dbPort, $dbName, $dbUser, $dbPass);

            // Simpan menggunakan Storage facade (disk yang sama dengan index())
            Storage::disk('local')->put('backups/' . $filename, $sql);

            $ukuran = $this->formatBytes(Storage::disk('local')->size('backups/' . $filename));

            ActivityLog::catat('Backup', 'backup', "Membuat backup database: {$filename} ({$ukuran}).");

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup berhasil dibuat: {$filename} ({$ukuran})");

        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    // Download file backup
    public function download($filename)
    {
        $this->checkAccess();

        if (!preg_match('/^backup_ppdb_[\d_\-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = 'backups/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        return Storage::disk('local')->download($path, $filename, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // Hapus file backup
    public function hapus($filename)
    {
        $this->checkAccess();

        if (!preg_match('/^backup_ppdb_[\d_\-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = 'backups/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            ActivityLog::catat('Backup', 'hapus', "Menghapus file backup: {$filename}.");
            return redirect()->route('admin.backup.index')
                ->with('success', "File backup {$filename} berhasil dihapus.");
        }

        return redirect()->route('admin.backup.index')
            ->with('error', 'File tidak ditemukan.');
    }

    // Generate SQL dump menggunakan PDO (tanpa mysqldump binary)
    private function generateSqlDump($host, $port, $dbName, $user, $pass): string
    {
        $pdo = DB::connection()->getPdo();

        $sql  = "-- ============================================\n";
        $sql .= "-- PPDB SMK - Backup Database\n";
        $sql .= "-- Database  : {$dbName}\n";
        $sql .= "-- Dibuat    : " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Dibuat oleh: " . Auth::guard('admin')->user()->nama . "\n";
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

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}