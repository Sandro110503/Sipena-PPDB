<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AlamatCalonSiswa extends Model {
    protected $table='alamat_calon_siswa'; protected $primaryKey='id_alamat_siswa';
    protected $fillable=['kode_jenis_alamat','id_siswa','id_alamat','tanggal_mulai','tanggal_selesai','keterangan_lainnya'];
    public function alamat() { return $this->belongsTo(Alamat::class,'id_alamat','id_alamat'); }
    public function siswa() { return $this->belongsTo(CalonSiswa::class,'id_siswa','id_siswa'); }
    public function jenisAlamat() { return $this->belongsTo(RefJenisAlamat::class,'kode_jenis_alamat','kode_jenis_alamat'); }
}
