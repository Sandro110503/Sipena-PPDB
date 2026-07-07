<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Alamat extends Model {
    protected $table = 'alamat'; protected $primaryKey = 'id_alamat';
    protected $fillable = ['id_pemilik','jenis_tempat_tinggal','nomor_bangunan','nama_jalan','kelurahan','kecamatan','kode_pos','kabupaten_kota','provinsi','keterangan_lainnya'];
    public function pemilik() { return $this->belongsTo(PemilikProperti::class,'id_pemilik','id_pemilik'); }
}
