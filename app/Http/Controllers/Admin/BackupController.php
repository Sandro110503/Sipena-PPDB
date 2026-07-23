<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\BackupService;
use App\Services\PengaturanBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct(private BackupService $backupService)
    {
        // Pengecekan dilakukan di setiap method
    }

    private function checkAccess()
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses fitur backup.');
        }
    }

    // Halaman daftar backup (mendukung pencarian & filter jenis)
    public function index(Request $request)
    {
        $this->checkAccess();

        $cari  = $request->query('cari');
        $jenis = $request->query('jenis'); // manual | terjadwal | null

        $files = $this->backupService->daftarBackup($cari, $jenis);

        // Total & terakhir dihitung dari SEMUA file (bukan hasil filter),
        // supaya kartu info tetap merepresentasikan kondisi sebenarnya.
        $semuaFiles = $cari || $jenis ? $this->backupService->daftarBackup() : $files;

        $dbName     = config('database.connections.mysql.database');
        $pengaturan = PengaturanBackup::ambil();

        return view('admin.backup.index', compact('files', 'semuaFiles', 'dbName', 'pengaturan', 'cari', 'jenis'));
    }

    // Proses backup database manual
    public function backup(Request $request)
    {
        $this->checkAccess();

        try {
            $hasil = $this->backupService->buatBackup('manual');

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup berhasil dibuat: {$hasil['filename']} ({$hasil['ukuran']})");

        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    // Simpan pengaturan jadwal backup otomatis
    public function simpanPengaturan(Request $request)
    {
        $this->checkAccess();

        $data = $request->validate([
            'jenis'   => 'required|in:nonaktif,mingguan,bulanan',
            'hari'    => 'required_if:jenis,mingguan|nullable|integer|min:0|max:6',
            'tanggal' => 'required_if:jenis,bulanan|nullable|integer|min:1|max:28',
        ], [
            'hari.required_if'    => 'Pilih hari untuk backup mingguan.',
            'tanggal.required_if' => 'Pilih tanggal untuk backup bulanan.',
        ]);

        $pengaturan = PengaturanBackup::ambil();
        $pengaturan->simpan([
            'jenis'   => $data['jenis'],
            'hari'    => $data['hari'] ?? $pengaturan->hari,
            'tanggal' => $data['tanggal'] ?? $pengaturan->tanggal,
        ]);

        ActivityLog::catat('Backup', 'ubah', "Mengubah jadwal backup otomatis menjadi: {$pengaturan->labelJenis()}.");

        return redirect()->route('admin.backup.index')
            ->with('success', 'Pengaturan jadwal backup otomatis berhasil disimpan.');
    }

    // Download file backup
    public function download($filename)
    {
        $this->checkAccess();

        if (!preg_match('/^backup_ppdb_[\w\-]+\.sql$/', $filename)) {
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

        if (!preg_match('/^backup_ppdb_[\w\-]+\.sql$/', $filename)) {
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
}
