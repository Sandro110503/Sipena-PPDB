<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PendaftaranJurusan extends Model {
    protected $table = 'pendaftaran_jurusan';
    protected $fillable = ['id_siswa','id_jurusan','tanggal_pendaftaran','urutan_pilihan','status','keterangan_lainnya'];
    protected $casts = ['tanggal_pendaftaran' => 'date'];
    public function siswa() { return $this->belongsTo(CalonSiswa::class,'id_siswa','id_siswa'); }
    public function jurusan() { return $this->belongsTo(Jurusan::class,'id_jurusan','id_jurusan'); }
}
