<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table      = 'jurusan';
    protected $primaryKey = 'id_jurusan';

    protected $fillable = [
        'kode_jurusan',      // 2 digit angka (01-99), awalan nomor pendaftaran: {KK}{MM}{YYYY}{NNN}
        'singkatan',         // label pendek untuk preview / badge di UI
        'nama_jurusan',
        'deskripsi',
        'kapasitas',
        'keterangan_lainnya',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranJurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function siswaDiterima()
    {
        return $this->pendaftaran()->where('status', 'Diterima');
    }
}
