<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_persyaratan', function (Blueprint $table) {
            $table->id('id_dokumen');

            $table->foreignId('id_siswa')
                ->constrained('calon_siswa', 'id_siswa')
                ->cascadeOnDelete();

            // Admin yang memverifikasi dokumen (audit trail).
            $table->foreignId('id_admin')
                ->nullable()
                ->constrained('admins', 'id_admin')
                ->nullOnDelete();

            $table->enum('jenis_dokumen', [
                'akta_kelahiran',
                'ijazah_skl',
                'pas_foto',
            ]);

            $table->string('lokasi_file');

            $table->date('tanggal_unggah');

            $table->enum('status_verifikasi', [
                'Menunggu Verifikasi',
                'Terverifikasi',
                'Ditolak',
            ])->default('Menunggu Verifikasi');

            $table->string('keterangan')->nullable();

            $table->timestamps();

            // Satu siswa hanya punya satu baris per jenis dokumen — unggah ulang
            // memperbarui baris yang sama, bukan menambah baris baru.
            $table->unique(['id_siswa', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_persyaratan');
    }
};
