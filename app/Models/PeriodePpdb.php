<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PeriodePpdb extends Model
{
    protected $table      = 'periode_ppdb';
    protected $primaryKey = 'id_periode';

    protected $fillable = [
        'nama_periode',
        'tahun_ajaran',
        'gelombang',
        'tanggal_buka',
        'tanggal_tutup',
        'tanggal_pengumuman',
        'biaya_pendaftaran',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'tanggal_buka'         => 'date',
        'tanggal_tutup'        => 'date',
        'tanggal_pengumuman'   => 'date',
        'biaya_pendaftaran'    => 'decimal:2',
        'is_aktif'             => 'boolean',
    ];

    // ────────────────────────────────────────────
    // Scopes
    // ────────────────────────────────────────────

    /** Periode yang saat ini ditandai aktif */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    /** Periode yang sedang dalam rentang tanggal buka–tutup */
    public function scopeSedangBerjalan(Builder $query): Builder
    {
        $now = Carbon::today();
        return $query->where('tanggal_buka', '<=', $now)
                     ->where('tanggal_tutup', '>=', $now);
    }

    // ────────────────────────────────────────────
    // Helpers (static)
    // ────────────────────────────────────────────

    /**
     * Ambil periode aktif yang benar-benar sedang berjalan.
     * Digunakan oleh PendaftaranController untuk membuka/menutup pendaftaran.
     */
    public static function periodeAktif(): ?self
    {
        return static::aktif()->sedangBerjalan()->first();
    }

    /**
     * Cek apakah pendaftaran saat ini sedang dibuka.
     */
    public static function pendaftaranTerbuka(): bool
    {
        return static::periodeAktif() !== null;
    }

    // ────────────────────────────────────────────
    // Accessors
    // ────────────────────────────────────────────

    /** Status teks untuk tampilan */
    public function getStatusAttribute(): string
    {
        $now = Carbon::today();

        if (! $this->is_aktif) {
            return 'Tidak Aktif';
        }
        if ($now->lt($this->tanggal_buka)) {
            return 'Belum Dibuka';
        }
        if ($now->gt($this->tanggal_tutup)) {
            return 'Sudah Ditutup';
        }
        return 'Sedang Berjalan';
    }

    /** Warna badge berdasarkan status */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Sedang Berjalan' => 'success',
            'Belum Dibuka'    => 'warning',
            'Sudah Ditutup'   => 'danger',
            default           => 'secondary',
        };
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id_admin');
    }

    public function editor()
    {
        return $this->belongsTo(Admin::class, 'updated_by', 'id_admin');
    }

    /** Format biaya pendaftaran */
    public function getBiayaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_pendaftaran, 0, ',', '.');
    }
}
