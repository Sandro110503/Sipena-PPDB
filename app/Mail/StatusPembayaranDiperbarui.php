<?php

namespace App\Mail;

use App\Models\PembayaranSiswa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Dikirim ke calon siswa setiap kali panitia mengubah status pembayarannya
 * (Terverifikasi / Ditolak), sehingga siswa tidak perlu login berulang kali
 * hanya untuk mengecek apakah pembayarannya sudah dikonfirmasi.
 */
class StatusPembayaranDiperbarui extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PembayaranSiswa $pembayaran)
    {
        $this->pembayaran->loadMissing(['siswa', 'metodePembayaran']);
    }

    public function build()
    {
        $status = $this->pembayaran->status_pembayaran;

        $subjek = match ($status) {
            'Terverifikasi' => 'Pembayaran Anda Telah Terverifikasi ✓',
            'Ditolak'       => 'Pembayaran Anda Ditolak — Perlu Diunggah Ulang',
            default         => 'Status Pembayaran Diperbarui',
        };

        return $this->subject($subjek)->view('emails.status-pembayaran');
    }
}
