<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PemilikProperti extends Model { protected $table='pemilik_properti'; protected $primaryKey='id_pemilik'; protected $fillable=['nama_pemilik','nomor_kontak','keterangan_lainnya']; }
