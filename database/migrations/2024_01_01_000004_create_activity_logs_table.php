<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Snapshot identitas admin — disimpan terpisah dari foreign key
            // supaya riwayat tetap utuh walau akun admin dihapus.
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')
                ->references('id_admin')
                ->on('admins')
                ->nullOnDelete();
            $table->string('nama_admin')->nullable();

            // Modul/menu yang disentuh, mis. "Pegawai", "Jurusan", "Auth", dst.
            $table->string('modul', 50)->index();

            // Jenis aksi: login, logout, login_gagal, tambah, ubah, hapus,
            // status, verifikasi, aktifkan, nonaktifkan, backup, unduh
            $table->string('aktivitas', 30)->index();

            // Narasi singkat aksi yang dilakukan, dibuat siap-tampil.
            $table->text('deskripsi');

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Hanya created_at — log bersifat immutable, tidak pernah diupdate.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
