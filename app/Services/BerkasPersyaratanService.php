<?php

namespace App\Services;

use App\Models\DokumenPersyaratan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Mengelola unggahan berkas persyaratan siswa (Akta Kelahiran, Ijazah/SKL, Pas Foto).
 *
 * Versi lengkap: memakai tabel `dokumen_persyaratan` sebagai sumber data utama,
 * sehingga setiap dokumen punya status verifikasi (Menunggu Verifikasi/Terverifikasi/
 * Ditolak), riwayat siapa & kapan diverifikasi, dan bisa masuk ke statistik dashboard.
 * File fisiknya sendiri tetap disimpan di storage seperti biasa.
 */
class BerkasPersyaratanService
{
    public static function jenisDokumen(): array
    {
        return [
            'akta_kelahiran' => [
                'label'  => 'Akta Kelahiran',
                'icon'   => 'fa-file-alt',
                'mimes'  => 'pdf,jpg,jpeg,png',
                'max_kb' => 2048,
            ],
            'ijazah_skl' => [
                'label'  => 'Ijazah / SKL',
                'icon'   => 'fa-graduation-cap',
                'mimes'  => 'pdf,jpg,jpeg,png',
                'max_kb' => 2048,
            ],
            'pas_foto' => [
                'label'  => 'Pas Foto',
                'icon'   => 'fa-image',
                'mimes'  => 'jpg,jpeg,png',
                'max_kb' => 1024,
            ],
        ];
    }

    protected static function direktori(int $idSiswa): string
    {
        return "berkas-siswa/{$idSiswa}";
    }

    public static function status(int $idSiswa): array
    {
        $dokumen = DokumenPersyaratan::where('id_siswa', $idSiswa)
            ->with('verifikator')
            ->get()
            ->keyBy('jenis_dokumen');

        $hasil = [];

        foreach (static::jenisDokumen() as $kode => $meta) {
            $d = $dokumen->get($kode);

            $hasil[$kode] = array_merge($meta, [
                'kode'       => $kode,
                'ada'        => (bool) $d,
                'record'     => $d,
                'path'       => $d?->lokasi_file,
                'url'        => $d ? Storage::disk('public')->url($d->lokasi_file) : null,
                'ukuran_kb'  => $d && Storage::disk('public')->exists($d->lokasi_file)
                    ? round(Storage::disk('public')->size($d->lokasi_file) / 1024)
                    : null,
                'ekstensi'   => $d ? strtoupper(pathinfo($d->lokasi_file, PATHINFO_EXTENSION)) : null,
                'status'     => $d?->status_verifikasi ?? 'Belum Diunggah',
                'keterangan' => $d?->keterangan,
                'verifikator'=> $d?->verifikator?->nama,
                'tanggal_unggah' => $d?->tanggal_unggah,
            ]);
        }

        return $hasil;
    }

    public static function lengkap(int $idSiswa): bool
    {
        return collect(static::status($idSiswa))->every(fn ($d) => $d['ada']);
    }

    public static function jumlahLengkap(int $idSiswa): array
    {
        $status = static::status($idSiswa);
        return [
            'lengkap' => collect($status)->where('ada', true)->count(),
            'total'   => count($status),
        ];
    }

    public static function jumlahTerverifikasi(int $idSiswa): array
    {
        $status = static::status($idSiswa);
        return [
            'terverifikasi' => collect($status)->where('status', 'Terverifikasi')->count(),
            'total'         => count($status),
        ];
    }

    public static function simpan(int $idSiswa, string $jenis, UploadedFile $file): DokumenPersyaratan
    {
        $existing = DokumenPersyaratan::where('id_siswa', $idSiswa)->where('jenis_dokumen', $jenis)->first();

        if ($existing && Storage::disk('public')->exists($existing->lokasi_file)) {
            Storage::disk('public')->delete($existing->lokasi_file);
        }

        $namaFile = $jenis . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs(static::direktori($idSiswa), $namaFile, 'public');

        return DokumenPersyaratan::updateOrCreate(
            ['id_siswa' => $idSiswa, 'jenis_dokumen' => $jenis],
            [
                'lokasi_file'        => $path,
                'tanggal_unggah'     => now(),
                'status_verifikasi'  => 'Menunggu Verifikasi',
                'keterangan'         => null,
                'id_admin'           => null,
            ]
        );
    }

    public static function hapus(int $idSiswa, string $jenis): bool
    {
        $dokumen = DokumenPersyaratan::where('id_siswa', $idSiswa)->where('jenis_dokumen', $jenis)->first();

        if (! $dokumen) {
            return false;
        }

        if (Storage::disk('public')->exists($dokumen->lokasi_file)) {
            Storage::disk('public')->delete($dokumen->lokasi_file);
        }

        return (bool) $dokumen->delete();
    }
}
