<?php

namespace App\Mail;

use App\Models\DokumenPersyaratan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Dikirim ke panitia/admin ketika calon siswa mengunggah dokumen persyaratan
 * baru (Akta Kelahiran, Ijazah/SKL, Pas Foto) yang menunggu verifikasi.
 */
class DokumenBaruDiunggah extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DokumenPersyaratan $dokumen)
    {
        $this->dokumen->loadMissing('siswa');
    }

    public function build()
    {
        return $this->subject('Dokumen Baru Diunggah — ' . ($this->dokumen->siswa->nomor_pendaftaran ?? '-'))
            ->view('emails.dokumen-baru');
    }
}
