<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RefTipeRelasi extends Model { protected $table='ref_tipe_relasi'; protected $primaryKey='kode_tipe_relasi'; public $incrementing=false; protected $keyType='string'; protected $fillable=['kode_tipe_relasi','deskripsi_tipe_relasi']; }
