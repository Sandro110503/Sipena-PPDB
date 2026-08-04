<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DokumenPersyaratan extends Model {
    protected $table = 'dokumen_persyaratan'; protected $primaryKey = 'id_dokumen';
    protected $fillable = ['id_siswa','id_admin','jenis_dokumen','lokasi_file','tanggal_unggah','status_verifikasi','keterangan'];
    protected $casts = ['tanggal_unggah' => 'date'];
    public function siswa() { return $this->belongsTo(CalonSiswa::class,'id_siswa','id_siswa'); }
    // Admin yang melakukan verifikasi terakhir (audit trail).
    public function verifikator() { return $this->belongsTo(Admin::class,'id_admin','id_admin'); }
}
