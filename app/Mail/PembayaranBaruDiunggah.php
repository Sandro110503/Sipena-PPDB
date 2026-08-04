<?php

namespace App\Mail;

use App\Models\PembayaranSiswa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Dikirim ke panitia/admin ketika calon siswa mengunggah bukti pembayaran baru
 * yang berstatus "Menunggu Verifikasi", agar panitia dapat segera menindaklanjuti
 * tanpa perlu mengecek dashboard secara manual.
 */
class PembayaranBaruDiunggah extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PembayaranSiswa $pembayaran)
    {
        $this->pembayaran->loadMissing(['siswa', 'metodePembayaran']);
    }

    public function build()
    {
        return $this->subject('Bukti Pembayaran Baru — ' . ($this->pembayaran->siswa->nomor_pendaftaran ?? '-'))
            ->view('emails.pembayaran-baru');
    }
}
