<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CalonSiswa extends Authenticatable
{
    protected $table      = 'calon_siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'id_periode', 'nomor_pendaftaran', 'jenis_kelamin', 'nama_depan', 'nama_tengah',
        'nama_belakang', 'nomor_hp', 'email', 'tanggal_lahir', 'tempat_lahir',
        'nisn', 'asal_sekolah', 'tahun_lulus', 'tanggal_daftar',
        'tanggal_diterima', 'status_penerimaan', 'foto', 'keterangan_lainnya', 'password',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'tanggal_daftar'   => 'date',
        'tanggal_diterima' => 'date',
        'password'         => 'hashed',
    ];

    // ── Accessor ──────────────────────────────────────────────────────────────

    public function getNamaLengkapAttribute(): string
    {
        return trim("{$this->nama_depan} {$this->nama_tengah} {$this->nama_belakang}");
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function alamatCalonSiswa(): HasMany
    {
        return $this->hasMany(AlamatCalonSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function pendaftaranJurusan(): HasMany
    {
        return $this->hasMany(PendaftaranJurusan::class, 'id_siswa', 'id_siswa');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function relasiSiswa(): HasMany
    {
        return $this->hasMany(RelasiSiswa::class, 'id_siswa', 'id_siswa');
    }

    // ── Generator Nomor Pendaftaran ───────────────────────────────────────────
    //
    // Format: {KK}{MM}{YYYY}{NNN}  — semua angka, total 11 digit
    //   KK   = kode_jurusan 2 digit (01–99)
    //   MM   = bulan pendaftaran 2 digit (01–12)
    //   YYYY = tahun pendaftaran 4 digit
    //   NNN  = urutan 3 digit per kombinasi kode+bulan+tahun (001–999)
    //
    // Contoh: 01062026001  (jurusan 01, Juni 2026, pendaftar ke-1)
    //         02062026003  (jurusan 02, Juni 2026, pendaftar ke-3)
    //
    // @param  string $kodeJurusan  Nilai kode_jurusan dari tabel jurusan (2 digit angka)
    // @return string               11 digit angka
    // ─────────────────────────────────────────────────────────────────────────
    public static function generateNomorPendaftaran(string $kodeJurusan): string
    {
        $kode  = str_pad($kodeJurusan, 2, '0', STR_PAD_LEFT);
        $bulan = date('m');
        $tahun = date('Y');
    
        $periodeAktif = \App\Models\PeriodePpdb::periodeAktif();
    
        if (! $periodeAktif) {
            throw new \RuntimeException('Tidak ada periode PPDB aktif saat ini.');
        }
    
        // Lock baris-baris pada periode ini agar aman dari race condition
        // ketika ada 2 pendaftar submit hampir bersamaan.
        $urutan = static::lockForUpdate()->count() + 1;
    
        return $kode . $bulan . $tahun . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }
    public function periode()
    {
        return $this->belongsTo(PeriodePpdb::class, 'id_periode', 'id_periode');
    }
}