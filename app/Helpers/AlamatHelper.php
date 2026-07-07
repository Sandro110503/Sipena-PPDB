<?php

namespace App\Helpers;

use App\Models\Alamat;

class AlamatHelper
{
    /**
     * Format satu record alamat menjadi satu baris teks.
     * Contoh: "Jl. Merdeka No. 10, Kel. Sukamaju, Kec. Bandung Wetan, Kota Bandung, Jawa Barat 40115"
     */
    public static function formatLengkap(?Alamat $alamat): string
    {
        if (! $alamat) {
            return '-';
        }

        $parts = array_filter([
            $alamat->nama_jalan,
            $alamat->kelurahan,
            $alamat->kecamatan,
            $alamat->kabupaten_kota,
            $alamat->provinsi,
            $alamat->kode_pos,
        ], fn ($v) => filled($v));

        return $parts ? implode(', ', $parts) : '-';
    }

    /**
     * Format alamat siswa untuk laporan/export.
     *
     * Jika siswa tinggal bersama orang tua/wali (alamat siswa = alamat ortu),
     * field alamat siswa cukup ditandai "(-)" agar tidak duplikat dengan
     * kolom Alamat Orang Tua.
     *
     * @param  \App\Models\AlamatCalonSiswa|null  $alamatSiswaRecord  Record alamat_calon_siswa milik siswa
     * @param  \App\Models\WaliOrangTua|null       $wali               Wali/orang tua siswa (relasi pertama)
     */
    public static function formatAlamatSiswaUntukExport($alamatSiswaRecord, $wali): string
    {
        if (! $alamatSiswaRecord || ! $alamatSiswaRecord->alamat) {
            return '-';
        }

        $tinggalBersamaOrtu = $wali
            && $wali->id_alamat
            && $alamatSiswaRecord->id_alamat === $wali->id_alamat;

        if ($tinggalBersamaOrtu) {
            return '(-)';
        }

        return self::formatLengkap($alamatSiswaRecord->alamat);
    }
}