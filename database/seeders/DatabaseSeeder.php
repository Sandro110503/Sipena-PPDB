<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Jurusan;
use App\Models\MetodePembayaran;
use App\Models\RefJenisAlamat;
use App\Models\RefTipeRelasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ref Jenis Alamat (hanya 2 jenis sesuai kebutuhan)
        RefJenisAlamat::insert([
            ['kode_jenis_alamat' => 'RP', 'deskripsi_jenis_alamat' => 'Rumah Orang Tua/Wali'],
            ['kode_jenis_alamat' => 'SW', 'deskripsi_jenis_alamat' => 'Sewa/Kontrak/Kost'],
        ]);

        // Ref Tipe Relasi
        RefTipeRelasi::insert([
            ['kode_tipe_relasi' => 'AY', 'deskripsi_tipe_relasi' => 'Ayah'],
            ['kode_tipe_relasi' => 'IB', 'deskripsi_tipe_relasi' => 'Ibu'],
            ['kode_tipe_relasi' => 'WL', 'deskripsi_tipe_relasi' => 'Wali'],
        ]);

        // Metode Pembayaran
        MetodePembayaran::insert([
            ['kode_metode_bayar' => 'TF', 'deskripsi_metode_bayar' => 'Transfer Bank'],
            ['kode_metode_bayar' => 'TN', 'deskripsi_metode_bayar' => 'Tunai'],
            ['kode_metode_bayar' => 'VA', 'deskripsi_metode_bayar' => 'Virtual Account'],
        ]); 

        // Jurusan SMK
        Jurusan::insert([
            [
                'kode_jurusan' => '01',
                'singkatan'    => 'AKL',
                'nama_jurusan' => 'Akuntansi Keuangan Lembaga',
                'deskripsi'    => 'Kompetensi keahlian di bidang akuntansi dan keuangan lembaga.',
                'kapasitas'    => 36,
            ],
            [
                'kode_jurusan' => '02',
                'singkatan'    => 'TJKT',
                'nama_jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi',
                'deskripsi'    => 'Kompetensi keahlian di bidang jaringan komputer dan telekomunikasi.',
                'kapasitas'    => 36,
            ],
            [
                'kode_jurusan' => '03',
                'singkatan'    => 'MPLB',
                'nama_jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis',
                'deskripsi'    => 'Kompetensi keahlian di bidang manajemen perkantoran dan layanan bisnis.',
                'kapasitas'    => 36,
            ],
        ]);

        // Admin default (login pakai NIP)
        Admin::create([
            'nip'           => '199901012024011001',
            'nama'          => 'Super Admin',
            'jabatan'       => 'Kepala Panitia PPDB',
            'jenis_kelamin' => 'L',
            'email'         => 'admin@ppdb-smk.sch.id',
            'password'      => Hash::make('admin123'),
            'role'          => 'superadmin',
            'is_aktif'      => true,
        ]);

       // $this->call([
//            PeriodePPDBSeeder::class,
         //   CalonSiswaSeeder::class,
       // ]);
    }
}
