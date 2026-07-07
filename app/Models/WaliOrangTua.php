<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WaliOrangTua extends Model {
    protected $table='wali_orang_tua'; protected $primaryKey='id_wali';
    protected $fillable=['id_alamat','jenis_kelamin','nama_depan','nama_belakang','hubungan','nomor_hp','email','pekerjaan','keterangan_lainnya'];
    public function alamat() { return $this->belongsTo(Alamat::class,'id_alamat','id_alamat'); }
    public function relasiSiswa() { return $this->hasMany(RelasiSiswa::class,'id_wali','id_wali'); }
    public function getNamaLengkapAttribute() { return "{$this->nama_depan} {$this->nama_belakang}"; }
}
