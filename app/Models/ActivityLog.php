<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    // Log bersifat immutable: hanya created_at, tanpa updated_at.
    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'nama_admin', 'modul', 'aktivitas',
        'deskripsi', 'ip_address', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id_admin');
    }

    /**
     * Daftar label & warna badge per jenis aktivitas — dipakai di view.
     */
    public static function konfigAktivitas(): array
    {
        return [
            'login'        => ['label' => 'Login',        'bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'sign-in-alt'],
            'login_gagal'  => ['label' => 'Login Gagal',   'bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'exclamation-triangle'],
            'logout'       => ['label' => 'Logout',        'bg' => '#e2e8f0', 'color' => '#475569', 'icon' => 'sign-out-alt'],
            'tambah'       => ['label' => 'Tambah',        'bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'plus'],
            'ubah'         => ['label' => 'Ubah',          'bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'edit'],
            'hapus'        => ['label' => 'Hapus',         'bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'trash'],
            'status'       => ['label' => 'Ubah Status',   'bg' => '#ede9fe', 'color' => '#5b21b6', 'icon' => 'exchange-alt'],
            'verifikasi'   => ['label' => 'Verifikasi',    'bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'check-double'],
            'aktifkan'     => ['label' => 'Aktifkan',      'bg' => '#dcfce7', 'color' => '#166534', 'icon' => 'toggle-on'],
            'nonaktifkan'  => ['label' => 'Nonaktifkan',   'bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'toggle-off'],
            'backup'       => ['label' => 'Backup',        'bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'database'],
            'unduh'        => ['label' => 'Unduh',         'bg' => '#e0f2fe', 'color' => '#0369a1', 'icon' => 'download'],
        ];
    }

    public function getKonfigAttribute(): array
    {
        return static::konfigAktivitas()[$this->aktivitas] ?? [
            'label' => ucfirst($this->aktivitas), 'bg' => '#f1f5f9', 'color' => '#475569', 'icon' => 'circle',
        ];
    }

    /**
     * Catat satu baris log aktivitas admin yang sedang login.
     *
     * @param string $modul     Nama modul/menu, mis. "Pegawai", "Jurusan PPDB".
     * @param string $aktivitas Jenis aksi, lihat konfigAktivitas().
     * @param string $deskripsi Narasi singkat, mis. "Menambahkan pegawai Budi Santoso".
     */
    public static function catat(string $modul, string $aktivitas, string $deskripsi): self
    {
        $admin = Auth::guard('admin')->user();

        return static::create([
            'admin_id'   => $admin?->id_admin,
            'nama_admin' => $admin?->nama ?? 'Sistem',
            'modul'      => $modul,
            'aktivitas'  => $aktivitas,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'created_at' => now(),
        ]);
    }

    /**
     * Catat login gagal — dipanggil sebelum admin berhasil ter-autentikasi,
     * sehingga butuh nama/nip secara manual (belum ada Auth user).
     */
    public static function catatLoginGagal(string $nip): self
    {
        return static::create([
            'admin_id'   => null,
            'nama_admin' => "NIP: {$nip}",
            'modul'      => 'Auth',
            'aktivitas'  => 'login_gagal',
            'deskripsi'  => "Percobaan login gagal dengan NIP {$nip}.",
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'created_at' => now(),
        ]);
    }
}
