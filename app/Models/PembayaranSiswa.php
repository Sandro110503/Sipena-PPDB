<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PembayaranSiswa extends Model {
    protected $table = 'pembayaran_siswa'; protected $primaryKey = 'id_pembayaran';
    protected $fillable = ['kode_metode_bayar','id_siswa','id_admin','jumlah_bayar','tanggal_bayar','keterangan','status_pembayaran','bukti_bayar'];
    protected $casts = ['tanggal_bayar' => 'date', 'jumlah_bayar' => 'decimal:2'];
    public function siswa() { return $this->belongsTo(CalonSiswa::class,'id_siswa','id_siswa'); }
    public function metodePembayaran() { return $this->belongsTo(MetodePembayaran::class,'kode_metode_bayar','kode_metode_bayar'); }
    // Admin yang melakukan verifikasi terakhir (audit trail).
    public function verifikator() { return $this->belongsTo(Admin::class,'id_admin','id_admin'); }
}
