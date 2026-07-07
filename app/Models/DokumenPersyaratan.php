<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DokumenPersyaratan extends Model {
    protected $table = 'dokumen_persyaratan'; protected $primaryKey = 'id_dokumen';
    protected $fillable = ['id_siswa','jenis_dokumen','lokasi_file','tanggal_unggah','status_verifikasi','keterangan'];
    protected $casts = ['tanggal_unggah' => 'date'];
    public function siswa() { return $this->belongsTo(CalonSiswa::class,'id_siswa','id_siswa'); }
}
