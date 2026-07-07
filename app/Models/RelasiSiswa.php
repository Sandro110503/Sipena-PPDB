<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelasiSiswa extends Model
{
    protected $table = 'relasi_siswa';

    protected $fillable = [
        'id_siswa',
        'id_wali',
        'kode_tipe_relasi',
    ];

    public function siswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function wali()
    {
        return $this->belongsTo(WaliOrangTua::class, 'id_wali', 'id_wali');
    }

    public function tipeRelasi()
    {
        return $this->belongsTo(RefTipeRelasi::class, 'kode_tipe_relasi', 'kode_tipe_relasi');
    }
}
