<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MetodePembayaran extends Model { protected $table='metode_pembayaran'; protected $primaryKey='kode_metode_bayar'; public $incrementing=false; protected $keyType='string'; protected $fillable=['kode_metode_bayar','deskripsi_metode_bayar']; }
