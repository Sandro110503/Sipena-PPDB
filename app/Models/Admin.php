<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';

    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'no_hp',
        'jenis_kelamin',
        'email',
        'password',
        'role',
        'is_aktif',
        'foto',
        'notif_pendaftar_baru',
        'notif_pembayaran_baru',
        'notif_email',
        'tampilan_rows',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'password'                => 'hashed',
        'is_aktif'                => 'boolean',
        'notif_pendaftar_baru'    => 'boolean',
        'notif_pembayaran_baru'   => 'boolean',
        'notif_email'             => 'boolean',
        'tampilan_rows'           => 'integer',
    ];

    protected $attributes = [
        'notif_pendaftar_baru'    => true,
        'notif_pembayaran_baru'   => true,
        'notif_email'             => false,
        'tampilan_rows'           => 25,
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Admin yang membuat periode PPDB
    public function periodeDibuat()
    {
        return $this->hasMany(
            PeriodePpdb::class,
            'created_by',
            'id_admin'
        );
    }

    // Admin yang mengubah periode PPDB
    public function periodeDiupdate()
    {
        return $this->hasMany(
            PeriodePpdb::class,
            'updated_by',
            'id_admin'
        );
    }

    // Admin yang memverifikasi pembayaran
    public function pembayaranDiverifikasi()
    {
        return $this->hasMany(
            Pembayaran::class,
            'id_admin',
            'id_admin'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            default      => 'Admin',
        };
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : null;
    }

    public function getInisialAttribute(): string
    {
        return strtoupper(substr($this->nama, 0, 1));
    }
}