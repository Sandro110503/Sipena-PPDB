<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusDokumenDiperbarui;
use App\Models\ActivityLog;
use App\Models\DokumenPersyaratan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DokumenController extends Controller
{
    public function verifikasi(Request $request, DokumenPersyaratan $dokumen)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:Menunggu Verifikasi,Terverifikasi,Ditolak',
            'keterangan'        => 'nullable|string|max:500',
        ]);

        $statusBerubah = $dokumen->status_verifikasi !== $request->status_verifikasi;

        $dokumen->update([
            'status_verifikasi' => $request->status_verifikasi,
            'keterangan'        => $request->keterangan,
            'id_admin'          => Auth::guard('admin')->id(),
        ]);

        $dokumen->loadMissing('siswa');

        ActivityLog::catat(
            'Dokumen',
            'verifikasi',
            "Memverifikasi dokumen {$dokumen->jenis_dokumen} milik siswa {$dokumen->siswa?->nama_depan} menjadi \"{$request->status_verifikasi}\"."
        );

        if ($statusBerubah && in_array($request->status_verifikasi, ['Terverifikasi', 'Ditolak']) && $dokumen->siswa?->email) {
            try {
                Mail::to($dokumen->siswa->email)->send(new StatusDokumenDiperbarui($dokumen));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }
}
