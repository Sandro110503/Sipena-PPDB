<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RefJenisAlamat extends Model { protected $table='ref_jenis_alamat'; protected $primaryKey='kode_jenis_alamat'; public $incrementing=false; protected $keyType='string'; protected $fillable=['kode_jenis_alamat','deskripsi_jenis_alamat']; }
