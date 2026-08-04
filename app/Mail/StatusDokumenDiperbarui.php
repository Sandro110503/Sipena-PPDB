<?php

namespace App\Mail;

use App\Models\DokumenPersyaratan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Dikirim ke calon siswa setiap kali panitia mengubah status verifikasi
 * salah satu dokumen persyaratannya (Terverifikasi / Ditolak).
 */
class StatusDokumenDiperbarui extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DokumenPersyaratan $dokumen)
    {
        $this->dokumen->loadMissing('siswa');
    }

    public function build()
    {
        $subjek = match ($this->dokumen->status_verifikasi) {
            'Terverifikasi' => 'Dokumen Anda Telah Terverifikasi ✓',
            'Ditolak'       => 'Dokumen Anda Ditolak — Perlu Diunggah Ulang',
            default         => 'Status Dokumen Diperbarui',
        };

        return $this->subject($subjek)->view('emails.status-dokumen');
    }
}
