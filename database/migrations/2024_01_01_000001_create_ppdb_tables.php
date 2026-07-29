<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_jenis_alamat', function (Blueprint $table) {
            $table->string('kode_jenis_alamat')->primary();
            $table->string('deskripsi_jenis_alamat');
            $table->timestamps();
        });

        Schema::create('ref_tipe_relasi', function (Blueprint $table) {
            $table->string('kode_tipe_relasi')->primary();
            $table->string('deskripsi_tipe_relasi');
            $table->timestamps();
        });

        Schema::create('metode_pembayaran', function (Blueprint $table) {
            $table->string('kode_metode_bayar')->primary();
            $table->string('deskripsi_metode_bayar');
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {

            $table->id('id_admin');

            $table->string('nip',20)->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('no_hp',15)->nullable();
            $table->enum('jenis_kelamin',['L','P'])->nullable();

            $table->string('foto')->nullable();

            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role',[
                'superadmin',
                'admin',
                'operator'
            ])->default('admin');

            $table->boolean('is_aktif')->default(true);

            $table->boolean('notif_pendaftar_baru')->default(true);
            $table->boolean('notif_pembayaran_baru')->default(true);
            $table->boolean('notif_email')->default(false);

            $table->unsignedSmallInteger('tampilan_rows')->default(25);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('periode_ppdb', function (Blueprint $table) {

            $table->id('id_periode');

            $table->string('nama_periode');
            $table->year('tahun_ajaran');

            $table->tinyInteger('gelombang')->default(1);

            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');

            $table->date('tanggal_pengumuman')->nullable();

            $table->decimal('biaya_pendaftaran', 10, 2)->default(0);

            $table->text('keterangan')->nullable();

            $table->boolean('is_aktif')->default(false);

            // Admin pembuat periode
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins', 'id_admin')
                ->nullOnDelete();

            // Admin yang terakhir mengubah periode
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('admins', 'id_admin')
                ->nullOnDelete();

            $table->timestamps();
        });

        // ─── JURUSAN ─────────────────────────────────────────────────────────
        // kode_jurusan : 2 digit angka (01–99), dipakai sebagai awalan
        //                nomor pendaftaran → {KK}{MM}{YYYY}{NNN}
        //                contoh: 01062026001
        //                (jurusan ke-01, Juni 2026, pendaftar ke-001)
        // singkatan    : label pendek untuk preview / badge (maks 10 kar.)
        //                misal: AKL, TJKT, MPLB
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id('id_jurusan');
            $table->char('kode_jurusan', 2)->unique()
                  ->comment('2 digit angka (01-99). Awalan nomor pendaftaran: {KK}{MM}{YYYY}{NNN}');
            $table->string('singkatan', 10)
                  ->comment('Label pendek untuk preview/badge, misal: AKL, TJKT');
            $table->string('nama_jurusan');
            $table->text('deskripsi')->nullable();
            $table->integer('kapasitas')->default(36);
            $table->string('keterangan_lainnya')->nullable();
            $table->timestamps();
        });

        Schema::create('pemilik_properti', function (Blueprint $table) {
            $table->id('id_pemilik');
            $table->string('nama_pemilik');
            $table->string('nomor_kontak')->nullable();
            $table->string('keterangan_lainnya')->nullable();
            $table->timestamps();
        });

        Schema::create('alamat', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->foreignId('id_pemilik')->nullable()->constrained('pemilik_properti', 'id_pemilik')->nullOnDelete();
            $table->enum('jenis_tempat_tinggal', ['Rumah Orang Tua/Wali', 'Sewa']);
            $table->string('nomor_bangunan')->nullable();
            $table->string('nama_jalan');
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi');
            $table->string('keterangan_lainnya')->nullable();
            $table->timestamps();
        });

        // ─── CALON SISWA ─────────────────────────────────────────────────────
        // nomor_pendaftaran format: {KK}{MM}{YYYY}{NNN}  — semua angka (11 digit)
        // contoh: 01062026001  →  jurusan 01, Juni 2026, pendaftar ke-001
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id('id_siswa');
            $table->foreignId('id_periode')
                    ->constrained('periode_ppdb','id_periode')
                    ->cascadeOnDelete();
            $table->char('nomor_pendaftaran', 11)->unique()
                  ->comment('Format 11 digit angka: {KK}{MM}{YYYY}{NNN}  contoh: 01062026001');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_depan');
            $table->string('nama_tengah')->nullable();
            $table->string('nama_belakang')->nullable();
            $table->string('nomor_hp');
            $table->string('email')->unique();
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->string('nisn', 10)->unique();
            $table->string('asal_sekolah');
            $table->string('tahun_lulus', 4);
            $table->date('tanggal_daftar')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->enum('status_penerimaan', ['Menunggu', 'Diterima', 'Ditolak', 'Cadangan'])->default('Menunggu');
            $table->string('foto')->nullable();
            $table->text('keterangan_lainnya')->nullable();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('alamat_calon_siswa', function (Blueprint $table) {
            $table->id('id_alamat_siswa');
            $table->string('kode_jenis_alamat');
            $table->foreignId('id_siswa')->constrained('calon_siswa', 'id_siswa')->cascadeOnDelete();
            $table->foreignId('id_alamat')->constrained('alamat', 'id_alamat')->cascadeOnDelete();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('keterangan_lainnya')->nullable();
            $table->foreign('kode_jenis_alamat')->references('kode_jenis_alamat')->on('ref_jenis_alamat');
            $table->timestamps();
        });

        Schema::create('wali_orang_tua', function (Blueprint $table) {
            $table->id('id_wali');
            $table->foreignId('id_alamat')->nullable()->constrained('alamat', 'id_alamat')->nullOnDelete();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_depan');
            $table->string('nama_belakang')->nullable();
            $table->string('hubungan');
            $table->string('nomor_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('keterangan_lainnya')->nullable();
            $table->timestamps();
        });

        Schema::create('relasi_siswa', function (Blueprint $table) {
            $table->foreignId('id_siswa')->constrained('calon_siswa', 'id_siswa')->cascadeOnDelete();
            $table->foreignId('id_wali')->constrained('wali_orang_tua', 'id_wali')->cascadeOnDelete();
            $table->string('kode_tipe_relasi');
            $table->foreign('kode_tipe_relasi')->references('kode_tipe_relasi')->on('ref_tipe_relasi');
            $table->primary(['id_siswa', 'id_wali']);
            $table->timestamps();
        });

        Schema::create('pendaftaran_jurusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('calon_siswa', 'id_siswa')->cascadeOnDelete();
            $table->foreignId('id_jurusan')->constrained('jurusan', 'id_jurusan');
            $table->date('tanggal_pendaftaran');
            $table->tinyInteger('urutan_pilihan')->default(1);
            $table->enum('status', ['Aktif', 'Diterima', 'Ditolak'])->default('Aktif');
            $table->string('keterangan_lainnya')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran_siswa', function (Blueprint $table) {
            $table->id('id_pembayaran');

            $table->string('kode_metode_bayar');

            $table->foreignId('id_siswa')
                ->constrained('calon_siswa', 'id_siswa')
                ->cascadeOnDelete();

            // admin yang memverifikasi pembayaran
            $table->foreignId('id_admin')
                ->nullable()
                ->constrained('admins', 'id_admin')
                ->nullOnDelete();

            $table->decimal('jumlah_bayar', 15, 2);

            $table->date('tanggal_bayar');

            $table->string('keterangan')->nullable();

            $table->enum('status_pembayaran', [
                'Menunggu Verifikasi',
                'Terverifikasi',
                'Ditolak'
            ])->default('Menunggu Verifikasi');

            $table->string('bukti_bayar')->nullable();

            $table->foreign('kode_metode_bayar')
                ->references('kode_metode_bayar')
                ->on('metode_pembayaran');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_siswa');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('periode_ppdb');
        Schema::dropIfExists('pendaftaran_jurusan');
        Schema::dropIfExists('relasi_siswa');
        Schema::dropIfExists('wali_orang_tua');
        Schema::dropIfExists('alamat_calon_siswa');
        Schema::dropIfExists('calon_siswa');
        Schema::dropIfExists('alamat');
        Schema::dropIfExists('pemilik_properti');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('metode_pembayaran');
        Schema::dropIfExists('ref_tipe_relasi');
        Schema::dropIfExists('ref_jenis_alamat');
    }
};
