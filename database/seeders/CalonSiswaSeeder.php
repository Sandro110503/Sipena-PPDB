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

        DB::table('ref_tipe_relasi')->updateOrInsert(
            ['kode_tipe_relasi' => 'IB'],
            [
                'deskripsi_tipe_relasi' => 'Ibu',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // ── Referensi metode pembayaran ───────────────────────────────────
        // Cek dulu isi tabelnya — kalau project ini sudah punya seeder
        // metode pembayaran sendiri (mis. QRIS, TF, TN), JANGAN tambah kode
        // baru lagi supaya tidak dobel/duplikat secara makna. Kode baru
        // hanya di-seed kalau tabelnya benar-benar masih kosong.
        $daftarMetodeBayar = DB::table('metode_pembayaran')->pluck('kode_metode_bayar')->all();

        if (empty($daftarMetodeBayar)) {
            $metodeBayarRef = [
                'TF'    => 'Transfer Bank',
                'TN'    => 'Tunai',
                'QRIS'  => 'QRIS',
                'VA'    => 'Virtual Account',
            ];

            foreach ($metodeBayarRef as $kode => $deskripsi) {
                DB::table('metode_pembayaran')->updateOrInsert(
                    ['kode_metode_bayar' => $kode],
                    [
                        'deskripsi_metode_bayar' => $deskripsi,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            $daftarMetodeBayar = array_keys($metodeBayarRef);
        } else {
            $this->command->info('Memakai metode_pembayaran yang sudah ada: ' . implode(', ', $daftarMetodeBayar));
        }

        // ── Daftar admin untuk verifikator pembayaran (opsional) ──────────
        // Kalau tabel admins masih kosong, id_admin di pembayaran akan
        // dibiarkan null (kolomnya memang nullable).
        $daftarAdmin = DB::table('admins')->pluck('id_admin');
        if ($daftarAdmin->isEmpty()) {
            $this->command->warn('Tabel admins masih kosong — id_admin pada pembayaran akan diisi null. Jalankan AdminSeeder dulu kalau ingin verifikatornya terisi.');
        }

        $jurusan = DB::table('jurusan')->get();

        if ($jurusan->isEmpty()) {
            $this->command->error('Data jurusan belum tersedia.');
            return;
        }

        // ── Mapping kota -> provinsi ──────────────────────────────────────
        // Dibuat berpasangan (bukan array terpisah) supaya kota & provinsi
        // ortu selalu konsisten secara geografis, dan supaya logika
        // "ortu di Bekasi / di luar Bekasi & Jabar" bisa dievaluasi dengan benar.
        $kotaProvinsiMap = [
            'Jakarta Timur'    => 'DKI Jakarta',
            'Jakarta Selatan'  => 'DKI Jakarta',
            'Bekasi'           => 'Jawa Barat',
            'Bogor'            => 'Jawa Barat',
            'Depok'            => 'Jawa Barat',
            'Bandung'          => 'Jawa Barat',
            'Tangerang'        => 'Banten',
            'Semarang'         => 'Jawa Tengah',
            'Surabaya'         => 'Jawa Timur',
            'Malang'           => 'Jawa Timur',
        ];
        $daftarKota = array_keys($kotaProvinsiMap);

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

        // Nama belakang netral: aman dipakai untuk pria maupun wanita.
        // Nama marga Batak (Siregar, Simatupang, Hutagalung, Sinaga, Panjaitan,
        // Saragih) diwariskan dari garis ayah dan bentuknya tidak berubah
        // untuk anak laki-laki maupun perempuan, jadi masuk kelompok ini.
        $namaBelakangNetral = [
            'Saragih', 'Waskita', 'Siregar', 'Usada', 'Simatupang',
            'Nugroho', 'Santoso', 'Kusuma', 'Pratama',
            'Hutagalung', 'Purnama', 'Wijaya', 'Halim',
            'Sinaga', 'Panjaitan', 'Hidayat', 'Ramadhan',
        ];

        // Nama belakang khusus pria: berakhiran "-wan"/"-anto"/"-syah" dsb,
        // yang dalam konvensi nama Jawa terasa maskulin (mis. "wan" = pria,
        // seperti pada "wartawan", "budayawan"), jadi hanya dipakai untuk
        // jenis_kelamin laki-laki.
        $namaBelakangPria = [
            'Kuswoyo', 'Wibowo', 'Setiawan', 'Gunawan', 'Suryanto',
            'Kurniawan', 'Firmansyah',
        ];

        // Helper untuk memilih nama belakang sesuai gender:
        // pria bisa dapat nama netral atau nama khusus pria,
        // wanita hanya dari kelompok netral.
        $pilihNamaBelakang = function (string $jenisKelamin) use ($faker, $namaBelakangNetral, $namaBelakangPria): string {
            $pool = $jenisKelamin === 'L'
                ? array_merge($namaBelakangNetral, $namaBelakangPria)
                : $namaBelakangNetral;

            return $faker->randomElement($pool);
        };

        // Helper kecil untuk menyeragamkan format email dari sebuah nama.
        $buatSlugEmail = function (string $nama): string {
            return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama));
        };

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

            // Kota & provinsi ortu diambil dari mapping yang sama supaya konsisten.
            $kotaOrtu     = $faker->randomElement($daftarKota);
            $provinsiOrtu = $kotaProvinsiMap[$kotaOrtu];

            $idAlamatOrtu = DB::table('alamat')->insertGetId([
                'id_pemilik'           => null,
                'jenis_tempat_tinggal' => 'Rumah Orang Tua/Wali',
                'nomor_bangunan'       => rand(1, 500),
                'nama_jalan'           => 'Jl. ' . $faker->streetName(),
                'kelurahan'            => ucfirst($faker->word()),
                'kecamatan'            => ucfirst($faker->word()),
                'kode_pos'             => $faker->postcode(),
                'kabupaten_kota'       => $kotaOrtu,
                'provinsi'             => $provinsiOrtu,
                'keterangan_lainnya'   => null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            // ==========================
            // STATUS TEMPAT TINGGAL
            // ==========================
            // Aturan:
            // - Jika ortu berada di kota Bekasi -> siswa tinggal bersama ortu
            //   (tidak perlu sewa/kost lagi, karena sudah dekat sekolah).
            // - Jika ortu di luar kota Bekasi ATAU di luar provinsi Jawa Barat
            //   -> siswa wajib sewa/kost, dan lokasi kost-nya di kota Bekasi
            //   (dekat SMK Yadika 8 Jatimulya).

            $ortuDiBekasi = ($kotaOrtu === 'Bekasi');

            if ($ortuDiBekasi) {

                $tinggalBersamaOrtu = true;
                $idAlamatSiswa      = $idAlamatOrtu;
                $kodeJenisAlamat    = 'RP';

            } else {

                $tinggalBersamaOrtu = false;

                $idAlamatSiswa = DB::table('alamat')->insertGetId([
                    'id_pemilik'           => null,
                    'jenis_tempat_tinggal' => 'Sewa',
                    'nomor_bangunan'       => rand(1, 500),
                    'nama_jalan'           => 'Jl. ' . $faker->streetName(),
                    'kelurahan'            => ucfirst($faker->word()),
                    'kecamatan'            => ucfirst($faker->word()),
                    'kode_pos'             => $faker->postcode(),
                    'kabupaten_kota'       => 'Bekasi',
                    'provinsi'             => 'Jawa Barat',
                    'keterangan_lainnya'   => 'Tempat tinggal siswa (kost/sewa dekat sekolah)',
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

            // Email pakai nama depan siswa + index (index menjaga keunikan
            // walau nama depan sama, karena daftar nama manual terbatas).
            $emailSiswa = $buatSlugEmail($namaDepan) . $i . '@gmail.com';

            $idSiswa = DB::table('calon_siswa')->insertGetId([
                'id_periode'        => $periodeTerpilih->id_periode,

                'nomor_pendaftaran' => $nomorPendaftaran,
                'jenis_kelamin'     => $jenisKelamin,
                'nama_depan'        => strtoupper($namaDepan),
                'nama_tengah'       => strtoupper($namaTengah),
                'nama_belakang'     => strtoupper($pilihNamaBelakang($jenisKelamin)),
                'nomor_hp'          => '08' . $faker->numerify('##########'),
                'email'             => $emailSiswa,
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
            // Wali diacak 50:50 antara Ayah dan Ibu (bukan hardcode Ayah
            // terus), supaya kode 'IB' pada ref_tipe_relasi juga terpakai.

            $waliAdalahAyah = $faker->boolean(50);

            $jenisKelaminWali = $waliAdalahAyah ? 'L' : 'P';
            $kodeRelasiWali   = $waliAdalahAyah ? 'AY' : 'IB';
            $namaDepanWali    = $waliAdalahAyah
                ? $faker->randomElement($namaLaki)
                : $faker->randomElement($namaPerempuan);

            // Email pakai nama depan wali + index, supaya konsisten dengan
            // aturan email siswa dan tetap unik antar baris data.
            $emailWali = $buatSlugEmail($namaDepanWali) . $i . '@gmail.com';

            $idWali = DB::table('wali_orang_tua')->insertGetId([
                'id_alamat'          => $idAlamatOrtu,
                'jenis_kelamin'      => $jenisKelaminWali,
                'nama_depan'         => strtoupper($namaDepanWali),
                'nama_belakang'      => strtoupper($pilihNamaBelakang($jenisKelaminWali)),
                'hubungan'           => $kodeRelasiWali,
                'nomor_hp'           => '08' . $faker->numerify('##########'),
                'email'              => $emailWali,
                'pekerjaan'          => $faker->randomElement($pekerjaan),
                'keterangan_lainnya' => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            DB::table('relasi_siswa')->insert([
                'id_siswa'         => $idSiswa,
                'id_wali'          => $idWali,
                'kode_tipe_relasi' => $kodeRelasiWali,
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

            // ==========================
            // PEMBAYARAN
            // ==========================
            // Tidak semua siswa langsung bayar. Sekitar 70% sudah melakukan
            // pembayaran; sisanya dianggap belum bayar (tetap 'Menunggu',
            // tanpa baris di pembayaran_siswa).
            // Dari yang sudah bayar, statusnya dibagi:
            //   65% Terverifikasi, 20% Menunggu Verifikasi, 15% Ditolak.

            $sudahBayar = $faker->boolean(70);

            if ($sudahBayar) {

                $rollStatus = $faker->numberBetween(1, 100);

                if ($rollStatus <= 65) {
                    $statusPembayaran = 'Terverifikasi';
                } elseif ($rollStatus <= 85) {
                    $statusPembayaran = 'Menunggu Verifikasi';
                } else {
                    $statusPembayaran = 'Ditolak';
                }

                // Tanggal bayar harus setelah tanggal daftar, tapi tidak
                // melebihi hari ini ataupun batas tutup periode.
                $batasAtasBayar = min($periodeTerpilih->tanggal_tutup, now()->format('Y-m-d'));
                $tanggalBayar   = $faker->dateTimeBetween($tanggalDaftar, $batasAtasBayar);

                // Verifikator hanya terisi kalau statusnya sudah diproses
                // admin (Terverifikasi/Ditolak); kalau masih menunggu, belum
                // ada admin yang menyentuhnya.
                $idAdminVerifikator = null;
                if ($statusPembayaran !== 'Menunggu Verifikasi' && $daftarAdmin->isNotEmpty()) {
                    $idAdminVerifikator = $daftarAdmin->random();
                }

                DB::table('pembayaran_siswa')->insert([
                    'kode_metode_bayar' => $faker->randomElement($daftarMetodeBayar),
                    'id_siswa'          => $idSiswa,
                    'id_admin'          => $idAdminVerifikator,
                    'jumlah_bayar'      => $periodeTerpilih->biaya_pendaftaran,
                    'tanggal_bayar'     => $tanggalBayar,
                    'keterangan'        => null,
                    'status_pembayaran' => $statusPembayaran,
                    'bukti_bayar'       => 'bukti-pembayaran/bukti-pembayaran-dummy.jpg',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // Pembayaran terverifikasi otomatis meluluskan status
                // penerimaan siswa, konsisten dengan alur verifikasi
                // otomatis pada sistem SIPENA.
                if ($statusPembayaran === 'Terverifikasi') {
                    DB::table('calon_siswa')
                        ->where('id_siswa', $idSiswa)
                        ->update([
                            'status_penerimaan' => 'Diterima',
                            'tanggal_diterima'  => $tanggalBayar,
                            'updated_at'        => now(),
                        ]);
                }
            }

            $this->command->info("Data {$i}/60 berhasil dibuat (Periode: {$periodeTerpilih->nama_periode})");
        }

        $this->command->info('Seeder calon siswa selesai — 60 data tersebar merata ke ' . $jumlahPeriode . ' periode.');
    }
}