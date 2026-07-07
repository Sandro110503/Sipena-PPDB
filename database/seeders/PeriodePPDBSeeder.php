<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeriodePPDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('periode_ppdb')->insert([
            [
                'nama_periode'        => 'PPDB 2026/2027 Gelombang 1',
                'tahun_ajaran'        => 2026,
                'gelombang'           => 1,
                'tanggal_buka'        => '2025-08-01',
                'tanggal_tutup'       => '2025-12-31',
                'tanggal_pengumuman'  => '2026-01-05',
                'biaya_pendaftaran'   => 50000,
                'keterangan'          => 'Pendaftaran Gelombang 1 (Agustus - Desember)',
                'is_aktif'            => false,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ],
            [
                'nama_periode'        => 'PPDB 2026/2027 Gelombang 2',
                'tahun_ajaran'        => 2026,
                'gelombang'           => 2,
                'tanggal_buka'        => '2026-01-01',
                'tanggal_tutup'       => '2026-03-31',
                'tanggal_pengumuman'  => '2026-04-05',
                'biaya_pendaftaran'   => 50000,
                'keterangan'          => 'Pendaftaran Gelombang 2 (Januari - Maret)',
                'is_aktif'            => true,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ],
            [
                'nama_periode'        => 'PPDB 2026/2027 Gelombang 3',
                'tahun_ajaran'        => 2026,
                'gelombang'           => 3,
                'tanggal_buka'        => '2026-04-01',
                'tanggal_tutup'       => '2026-06-30',
                'tanggal_pengumuman'  => '2026-07-05',
                'biaya_pendaftaran'   => 50000,
                'keterangan'          => 'Pendaftaran Gelombang 3 (April - Juni)',
                'is_aktif'            => false,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ],
        ]);
    }
}