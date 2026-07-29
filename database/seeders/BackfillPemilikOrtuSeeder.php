<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder backfill — TIDAK menghapus/reset data apapun.
 *
 * Tujuannya cuma satu: cari baris `alamat` dengan
 * jenis_tempat_tinggal = 'Rumah Orang Tua/Wali' yang id_pemilik-nya
 * masih NULL, lalu buatkan satu baris `pemilik_properti` (atas nama
 * wali yang terhubung ke alamat tersebut) dan isi id_pemilik-nya.
 *
 * Alamat tipe 'Sewa' sengaja TIDAK disentuh — tetap null seperti semula.
 * Data calon_siswa, admin, wali_orang_tua, dan alamat yang SUDAH terisi
 * id_pemilik tidak diubah sama sekali.
 */
class BackfillPemilikOrtuSeeder extends Seeder
{
    public function run(): void
    {
        $alamatList = DB::table('alamat')
            ->where('jenis_tempat_tinggal', 'Rumah Orang Tua/Wali')
            ->whereNull('id_pemilik')
            ->get();

        if ($alamatList->isEmpty()) {
            $this->command->info('Tidak ada alamat "Rumah Orang Tua/Wali" dengan id_pemilik kosong. Tidak ada yang perlu diperbaiki.');
            return;
        }

        $diperbaiki = 0;
        $dilewati   = 0;

        foreach ($alamatList as $alamat) {

            // Ambil wali yang terhubung ke alamat ini. Kalau lebih dari satu
            // wali memakai alamat yang sama, dipilih yang id_wali paling
            // kecil (paling pertama dibuat) sebagai representasi pemilik.
            $wali = DB::table('wali_orang_tua')
                ->where('id_alamat', $alamat->id_alamat)
                ->orderBy('id_wali')
                ->first();

            if (!$wali) {
                // Alamat "Rumah Orang Tua/Wali" tapi tidak ada data wali
                // yang terhubung — data tidak konsisten, jadi dilewati saja
                // (tidak dipaksakan diisi dengan data asal-asalan).
                $this->command->warn("Alamat #{$alamat->id_alamat} tidak punya data wali terkait — dilewati.");
                $dilewati++;
                continue;
            }

            $idPemilik = DB::table('pemilik_properti')->insertGetId([
                'nama_pemilik'       => trim($wali->nama_depan . ' ' . $wali->nama_belakang),
                'nomor_kontak'       => $wali->nomor_hp,
                'keterangan_lainnya' => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            DB::table('alamat')
                ->where('id_alamat', $alamat->id_alamat)
                ->update([
                    'id_pemilik' => $idPemilik,
                    'updated_at' => now(),
                ]);

            $diperbaiki++;
        }

        $this->command->info("Backfill selesai — {$diperbaiki} alamat diperbaiki, {$dilewati} dilewati (tidak ada data wali terkait).");
    }
}
