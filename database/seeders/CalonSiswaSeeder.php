<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CalonSiswaSeeder extends Seeder
{
   public function run(): void
    {
        $faker = Faker::create('id_ID');

        // ── Ambil SEMUA periode yang ada, bukan cuma yang aktif ──────────
        // supaya data seeder tersebar merata ke tiap gelombang.
        $periodeList = DB::table('periode_ppdb')
            ->orderBy('id_periode')
            ->get();

        if ($periodeList->isEmpty()) {
            $this->command->error('Belum ada data periode_ppdb. Jalankan PeriodePpdbSeeder terlebih dahulu.');
            return;
        }

        $jumlahPeriode = $periodeList->count();

        DB::table('ref_jenis_alamat')->updateOrInsert(
            ['kode_jenis_alamat' => 'RP'],
            [
                'deskripsi_jenis_alamat' => 'Rumah Orang Tua',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        DB::table('ref_jenis_alamat')->updateOrInsert(
            ['kode_jenis_alamat' => 'SW'],
            [
                'deskripsi_jenis_alamat' => 'Sewa/Kost',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        DB::table('ref_tipe_relasi')->updateOrInsert(
            ['kode_tipe_relasi' => 'AY'],
            [
                'deskripsi_tipe_relasi' => 'Ayah',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $jurusan = DB::table('jurusan')->get();

        if ($jurusan->isEmpty()) {
            $this->command->error('Data jurusan belum tersedia.');
            return;
        }

        $provinsi = [
            'DKI Jakarta',
            'Jawa Barat',
            'Jawa Tengah',
            'Jawa Timur',
            'Banten'
        ];

        $kota = [
            'Jakarta Timur',
            'Jakarta Selatan',
            'Bekasi',
            'Bogor',
            'Depok',
            'Bandung',
            'Tangerang',
            'Semarang',
            'Surabaya',
            'Malang'
        ];

        $pekerjaan = [
            'PNS',
            'Guru',
            'Petani',
            'Pedagang',
            'Wiraswasta',
            'Karyawan Swasta',
            'Dokter',
            'Perawat',
            'TNI',
            'POLRI'
        ];

        // Daftar nama manual per gender, karena data gender pada
        // Faker locale id_ID tidak akurat (banyak nama tertukar).
        $namaLaki = [
            'Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Gilang', 'Hendra',
            'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Made', 'Nanda', 'Oki', 'Putra',
            'Rizky', 'Surya', 'Taufik', 'Umar', 'Wahyu', 'Yusuf', 'Zaki', 'Agus',
            'Bayu', 'Dimas', 'Farhan', 'Guntur', 'Hadi', 'Ilham',
        ];

        $namaPerempuan = [
            'Ayu', 'Bella', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana',
            'Indah', 'Julia', 'Kartika', 'Lestari', 'Maya', 'Nadia', 'Oktavia', 'Putri',
            'Rani', 'Sari', 'Tania', 'Utami', 'Vina', 'Wulan', 'Yuni', 'Zahra',
            'Anisa', 'Bunga', 'Diana', 'Farah', 'Gina', 'Ika',
        ];

        $namaBelakang = [
            'Saragih', 'Waskita', 'Siregar', 'Usada', 'Kuswoyo', 'Simatupang',
            'Nugroho', 'Wibowo', 'Santoso', 'Kusuma', 'Pratama', 'Setiawan',
            'Hutagalung', 'Purnama', 'Wijaya', 'Halim', 'Gunawan', 'Suryanto',
            'Kurniawan', 'Sinaga', 'Panjaitan', 'Hidayat', 'Ramadhan', 'Firmansyah',
        ];

        for ($i = 1; $i <= 60; $i++) {

            $jurusanDipilih = $jurusan->random();

            // ── Sebar merata ke tiap periode secara round-robin ──────────
            // i=1 -> periode ke-0, i=2 -> periode ke-1, dst, lalu berulang.
            $periodeTerpilih = $periodeList[($i - 1) % $jumlahPeriode];

            // Tanggal daftar acak, tapi HARUS berada dalam rentang
            // tanggal_buka - tanggal_tutup periode yang dipilih, supaya
            // datanya realistis dan konsisten dengan periode tersebut.
            $tanggalDaftar = $faker->dateTimeBetween(
                $periodeTerpilih->tanggal_buka,
                $periodeTerpilih->tanggal_tutup
            );

            $nomorPendaftaran =
                str_pad($jurusanDipilih->kode_jurusan, 2, '0', STR_PAD_LEFT)
                . $tanggalDaftar->format('m')
                . $tanggalDaftar->format('Y')
                . str_pad($i, 3, '0', STR_PAD_LEFT);

            // ==========================
            // ALAMAT ORANG TUA
            // ==========================

            $idAlamatOrtu = DB::table('alamat')->insertGetId([
                'id_pemilik'           => null,
                'jenis_tempat_tinggal' => 'Rumah Orang Tua/Wali',
                'nomor_bangunan'       => rand(1, 500),
                'nama_jalan'           => 'Jl. ' . $faker->streetName(),
                'kelurahan'            => ucfirst($faker->word()),
                'kecamatan'            => ucfirst($faker->word()),
                'kode_pos'             => $faker->postcode(),
                'kabupaten_kota'       => $faker->randomElement($kota),
                'provinsi'             => $faker->randomElement($provinsi),
                'keterangan_lainnya'   => null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            // ==========================
            // STATUS TEMPAT TINGGAL
            // ==========================

            $tinggalBersamaOrtu = $faker->boolean(70);

            if ($tinggalBersamaOrtu) {

                $idAlamatSiswa = $idAlamatOrtu;
                $kodeJenisAlamat = 'RP';

            } else {

                $idAlamatSiswa = DB::table('alamat')->insertGetId([
                    'id_pemilik'           => null,
                    'jenis_tempat_tinggal' => 'Sewa',
                    'nomor_bangunan'       => rand(1, 500),
                    'nama_jalan'           => 'Jl. ' . $faker->streetName(),
                    'kelurahan'            => ucfirst($faker->word()),
                    'kecamatan'            => ucfirst($faker->word()),
                    'kode_pos'             => $faker->postcode(),
                    'kabupaten_kota'       => $faker->randomElement($kota),
                    'provinsi'             => $faker->randomElement($provinsi),
                    'keterangan_lainnya'   => 'Tempat tinggal siswa',
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                $kodeJenisAlamat = 'SW';
            }

            // ==========================
            // CALON SISWA
            // ==========================

            $jenisKelamin = $faker->randomElement(['L', 'P']);

            // Nama depan & tengah diambil dari daftar manual sesuai gender.
            $namaDepan  = $jenisKelamin === 'L' ? $faker->randomElement($namaLaki) : $faker->randomElement($namaPerempuan);
            $namaTengah = $jenisKelamin === 'L' ? $faker->randomElement($namaLaki) : $faker->randomElement($namaPerempuan);

            $idSiswa = DB::table('calon_siswa')->insertGetId([
                'id_periode'        => $periodeTerpilih->id_periode,

                'nomor_pendaftaran' => $nomorPendaftaran,
                'jenis_kelamin'     => $jenisKelamin,
                'nama_depan'        => strtoupper($namaDepan),
                'nama_tengah'       => strtoupper($namaTengah),
                'nama_belakang'     => strtoupper($faker->randomElement($namaBelakang)),
                'nomor_hp'          => '08' . $faker->numerify('##########'),
                'email'             => "siswa{$i}_" . time() . "@gmail.com",
                'tanggal_lahir'     => $faker->dateTimeBetween('-17 years', '-15 years')->format('Y-m-d'),
                'tempat_lahir'      => strtoupper($faker->city()),
                'nisn' => (string) $faker->unique()->numberBetween(1000000000, 9999999999),
                'asal_sekolah'      => 'SMP Negeri ' . rand(1, 100),
                'tahun_lulus'       => '2026',
                'tanggal_daftar'    => $tanggalDaftar,
                'tanggal_diterima'  => null,
                'status_penerimaan' => 'Menunggu',
                'foto'              => null,
                'keterangan_lainnya'=> null,
                'password'          => Hash::make('12345678'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ==========================
            // ALAMAT SISWA
            // ==========================

            DB::table('alamat_calon_siswa')->insert([
                'kode_jenis_alamat' => $kodeJenisAlamat,
                'id_siswa'          => $idSiswa,
                'id_alamat'         => $idAlamatSiswa,
                'tanggal_mulai'     => now(),
                'tanggal_selesai'   => null,
                'keterangan_lainnya'=> null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ==========================
            // WALI
            // ==========================

            $idWali = DB::table('wali_orang_tua')->insertGetId([
                'id_alamat'          => $idAlamatOrtu,
                'jenis_kelamin'      => 'L',
                'nama_depan'         => strtoupper($faker->randomElement($namaLaki)),
                'nama_belakang'      => strtoupper($faker->randomElement($namaBelakang)),
                'hubungan'           => 'AY',
                'nomor_hp'           => '08' . $faker->numerify('##########'),
                'email'              => "ayah{$i}@gmail.com",
                'pekerjaan'          => $faker->randomElement($pekerjaan),
                'keterangan_lainnya' => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            DB::table('relasi_siswa')->insert([
                'id_siswa'         => $idSiswa,
                'id_wali'          => $idWali,
                'kode_tipe_relasi' => 'AY',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('pendaftaran_jurusan')->insert([
                'id_siswa'             => $idSiswa,
                'id_jurusan'           => $jurusanDipilih->id_jurusan,
                'tanggal_pendaftaran'  => $tanggalDaftar,
                'urutan_pilihan'       => 1,
                'status'               => 'Aktif',
                'keterangan_lainnya'   => null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            $this->command->info("Data {$i}/60 berhasil dibuat (Periode: {$periodeTerpilih->nama_periode})");
        }

        $this->command->info('Seeder calon siswa selesai — 60 data tersebar merata ke ' . $jumlahPeriode . ' periode.');
    }
}