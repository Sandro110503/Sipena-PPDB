<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\MetodePembayaranController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\WaliController;
use App\Http\Controllers\Admin\RefTipeRelasiController;
use App\Http\Controllers\Admin\RefJenisAlamatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PeriodePpdbController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\Siswa\AuthSiswaController;
use App\Http\Controllers\Siswa\PortalSiswaController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

// ==============================
// PUBLIK - PPDB
// ==============================
Route::middleware('web')->group(function () {

    Route::get('/', fn() => view('ppdb.home'))->name('home');

    Route::prefix('daftar')->name('ppdb.')->group(function () {
        Route::get('/',  [PendaftaranController::class, 'index'])->name('index');
        Route::post('/', [PendaftaranController::class, 'store'])->name('store');
        Route::get('/sukses', [PendaftaranController::class, 'sukses'])->name('sukses');
        Route::match(['GET','POST'], '/cek-status', [PendaftaranController::class, 'cekStatus'])->name('cek-status');
    });

    // API polling real-time
    Route::get('/api/status', [PendaftaranController::class, 'statusApi'])->name('api.status');

    // ==============================
    // PORTAL SISWA
    // ==============================
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/login',  [AuthSiswaController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthSiswaController::class, 'login'])->name('login.post');
        Route::post('/logout',[AuthSiswaController::class, 'logout'])->name('logout');

        // Reset password (publik, tanpa login)
        Route::get('/reset-password',  [PortalSiswaController::class, 'showResetPassword'])->name('reset-password');
        Route::post('/reset-password', [PortalSiswaController::class, 'prosesResetPassword'])->name('reset-password.proses');

        Route::middleware('auth.siswa')->group(function () {
            Route::get('/dashboard',   [PortalSiswaController::class, 'dashboard'])->name('dashboard');
            Route::get('/pembayaran',  [PortalSiswaController::class, 'formPembayaran'])->name('pembayaran');
            Route::post('/pembayaran', [PortalSiswaController::class, 'uploadBukti'])->name('pembayaran.upload');

            // ── Halaman Pengaturan Akun (profil + alamat + password + notifikasi) ──
            Route::get('/pengaturan',             [PortalSiswaController::class, 'pengaturan'])->name('pengaturan');
            Route::put('/pengaturan/profil',      [PortalSiswaController::class, 'updateProfil'])->name('pengaturan.profil');
            Route::delete('/pengaturan/foto',     [PortalSiswaController::class, 'hapusFoto'])->name('pengaturan.hapus-foto');
            Route::put('/pengaturan/alamat',      [PortalSiswaController::class, 'updateAlamat'])->name('pengaturan.alamat');
            Route::patch('/pengaturan/password',  [PortalSiswaController::class, 'gantiPassword'])->name('pengaturan.password');
            Route::patch('/pengaturan/notifikasi',[PortalSiswaController::class, 'updateNotifikasi'])->name('pengaturan.notifikasi');

            // ── Redirect lama agar link lama tidak 404 ──────────────────────────
            Route::get('/profil', [PortalSiswaController::class, 'profil'])->name('profil');
            Route::get('/alamat', [PortalSiswaController::class, 'editAlamat'])->name('alamat');

            // Backward-compat: route lama yang mungkin masih tersimpan di session/flash
            Route::put('/profil',          [PortalSiswaController::class, 'updateProfil'])->name('profil.update');
            Route::patch('/ganti-password',[PortalSiswaController::class, 'gantiPassword'])->name('ganti-password');
            Route::put('/alamat',          [PortalSiswaController::class, 'updateAlamat'])->name('alamat.update');
        });
    });

    Route::get('/jurusan/{slug}', function ($slug) {
        $slug = strtolower($slug);
        $jurusan = [
            'akl'  => 'Akuntansi Keuangan Lembaga',
            'tjkt' => 'Teknik Jaringan Komputer dan Telekomunikasi',
            'mplb' => 'Manajemen Perkantoran dan Layanan Bisnis',
        ];
        abort_unless(isset($jurusan[$slug]), 404);
        return view('public.jurusan-detail', compact('slug'));
    })->name('jurusan.detail');

    // ==============================
    // ADMIN
    // ==============================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('auth:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Data Siswa
            Route::prefix('siswa')->name('siswa.')->group(function () {
                Route::get('/',                             [StudentController::class, 'index'])->name('index');
                Route::get('/export/pdf',                   [StudentController::class, 'exportPdf'])->name('export-pdf');
                Route::get('/export/excel',                 [StudentController::class, 'exportExcel'])->name('export-excel');
                Route::patch('/pembayaran/{id}/verifikasi', [StudentController::class, 'verifikasiPembayaran'])->name('verifikasi-pembayaran');
                Route::get('/{calonSiswa}',                 [StudentController::class, 'show'])->name('show');
                Route::patch('/{calonSiswa}/status',        [StudentController::class, 'updateStatus'])->name('update-status');
            });

            // CRUD Pegawai
            Route::prefix('pegawai')->name('pegawai.')->group(function () {
                Route::get('/',                         [PegawaiController::class, 'index'])->name('index');
                Route::get('/tambah',                   [PegawaiController::class, 'create'])->name('create');
                Route::post('/',                        [PegawaiController::class, 'store'])->name('store');
                Route::get('/{pegawai}/edit',           [PegawaiController::class, 'edit'])->name('edit');
                Route::put('/{pegawai}',                [PegawaiController::class, 'update'])->name('update');
                Route::delete('/{pegawai}',             [PegawaiController::class, 'destroy'])->name('destroy');
                Route::patch('/{pegawai}/toggle-aktif', [PegawaiController::class, 'toggleAktif'])->name('toggle-aktif');
            });

            // CRUD Jurusan
            Route::prefix('jurusan')->name('jurusan.')->group(function () {
                Route::get('/',               [JurusanController::class, 'index'])->name('index');
                Route::get('/tambah',         [JurusanController::class, 'create'])->name('create');
                Route::post('/',              [JurusanController::class, 'store'])->name('store');
                Route::get('/{jurusan}/edit', [JurusanController::class, 'edit'])->name('edit');
                Route::put('/{jurusan}',      [JurusanController::class, 'update'])->name('update');
                Route::delete('/{jurusan}',   [JurusanController::class, 'destroy'])->name('destroy');
            });

            // CRUD Periode PPDB
            Route::prefix('periode')->name('periode.')->group(function () {
                Route::get('/',                         [PeriodePpdbController::class, 'index'])->name('index');
                Route::get('/tambah',                   [PeriodePpdbController::class, 'create'])->name('create');
                Route::post('/',                        [PeriodePpdbController::class, 'store'])->name('store');
                Route::get('/{periode}/edit',           [PeriodePpdbController::class, 'edit'])->name('edit');
                Route::put('/{periode}',                [PeriodePpdbController::class, 'update'])->name('update');
                Route::delete('/{periode}',             [PeriodePpdbController::class, 'destroy'])->name('destroy');
                Route::patch('/{periode}/toggle-aktif', [PeriodePpdbController::class, 'toggleAktif'])->name('toggle-aktif');
            });

            // Profil & Pengaturan Admin
            Route::prefix('profil')->name('profil.')->group(function () {
                Route::get('/',                 [ProfileController::class, 'index'])->name('index');
                Route::put('/',                 [ProfileController::class, 'updateProfil'])->name('update');
                Route::post('/upload-foto',     [ProfileController::class, 'uploadFoto'])->name('upload-foto');
                Route::delete('/hapus-foto',    [ProfileController::class, 'hapusFoto'])->name('hapus-foto');
                Route::patch('/ganti-password', [ProfileController::class, 'gantiPassword'])->name('ganti-password');
                Route::patch('/notifikasi',     [ProfileController::class, 'updateNotifikasi'])->name('notifikasi');
            });

            // Verifikasi Pembayaran
            Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
                Route::get('/',                          [PembayaranController::class, 'index'])->name('index');
                Route::get('/{pembayaran}',              [PembayaranController::class, 'show'])->name('show');
                Route::patch('/{pembayaran}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('verifikasi');
            });

            // Metode Pembayaran
            Route::prefix('metode-pembayaran')->name('metode-pembayaran.')->group(function () {
                Route::get('/',                             [MetodePembayaranController::class, 'index'])->name('index');
                Route::get('/tambah',                       [MetodePembayaranController::class, 'create'])->name('create');
                Route::post('/',                            [MetodePembayaranController::class, 'store'])->name('store');
                Route::get('/{metodePembayaran}/edit',      [MetodePembayaranController::class, 'edit'])->name('edit');
                Route::put('/{metodePembayaran}',           [MetodePembayaranController::class, 'update'])->name('update');
                Route::delete('/{metodePembayaran}',        [MetodePembayaranController::class, 'destroy'])->name('destroy');
            });

            // Wali / Orang Tua
            Route::prefix('wali')->name('wali.')->group(function () {
                Route::get('/',       [WaliController::class, 'index'])->name('index');
                Route::get('/{wali}', [WaliController::class, 'show'])->name('show');
            });

            // Referensi: Tipe Relasi
            Route::prefix('ref-tipe-relasi')->name('ref-tipe-relasi.')->group(function () {
                Route::get('/',                       [RefTipeRelasiController::class, 'index'])->name('index');
                Route::get('/tambah',                 [RefTipeRelasiController::class, 'create'])->name('create');
                Route::post('/',                      [RefTipeRelasiController::class, 'store'])->name('store');
                Route::get('/{refTipeRelasi}/edit',   [RefTipeRelasiController::class, 'edit'])->name('edit');
                Route::put('/{refTipeRelasi}',        [RefTipeRelasiController::class, 'update'])->name('update');
                Route::delete('/{refTipeRelasi}',     [RefTipeRelasiController::class, 'destroy'])->name('destroy');
            });

            // Referensi: Jenis Alamat
            Route::prefix('ref-jenis-alamat')->name('ref-jenis-alamat.')->group(function () {
                Route::get('/',                        [RefJenisAlamatController::class, 'index'])->name('index');
                Route::get('/tambah',                  [RefJenisAlamatController::class, 'create'])->name('create');
                Route::post('/',                       [RefJenisAlamatController::class, 'store'])->name('store');
                Route::get('/{refJenisAlamat}/edit',   [RefJenisAlamatController::class, 'edit'])->name('edit');
                Route::put('/{refJenisAlamat}',        [RefJenisAlamatController::class, 'update'])->name('update');
                Route::delete('/{refJenisAlamat}',     [RefJenisAlamatController::class, 'destroy'])->name('destroy');
            });

            // Log Aktivitas
            Route::prefix('activity-log')->name('activity-log.')->group(function () {
                Route::get('/',                 [ActivityLogController::class, 'index'])->name('index');
                Route::get('/{activityLog}',    [ActivityLogController::class, 'show'])->name('show');
                Route::delete('/{activityLog}', [ActivityLogController::class, 'destroy'])->name('destroy');
                Route::post('/bersihkan',       [ActivityLogController::class, 'bersihkan'])->name('bersihkan');
            });

            // Backup Database
            Route::prefix('backup')->name('backup.')->group(function () {
                Route::get('/',                    [BackupController::class, 'index'])->name('index');
                Route::post('/proses',             [BackupController::class, 'backup'])->name('proses');
                Route::post('/pengaturan',         [BackupController::class, 'simpanPengaturan'])->name('pengaturan');
                Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
                Route::delete('/hapus/{filename}', [BackupController::class, 'hapus'])->name('hapus');
            });
        });
    });

}); // ← tutup Route::middleware('web')