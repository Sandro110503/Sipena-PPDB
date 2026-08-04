<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusPembayaranDiperbarui;
use App\Models\PembayaranSiswa;
use App\Models\MetodePembayaran;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranSiswa::with(['siswa', 'metodePembayaran'])
            ->when($request->status, fn($q) => $q->where('status_pembayaran', $request->status))
            ->when($request->search, fn($q) =>
                $q->whereHas('siswa', fn($qs) =>
                    $qs->where('nama_depan', 'like', "%{$request->search}%")
                       ->orWhere('nomor_pendaftaran', 'like', "%{$request->search}%")
                )
            )
            ->latest('tanggal_bayar');

        $pembayaran = $query->paginate(15)->withQueryString();
        $totalTerverifikasi = PembayaranSiswa::where('status_pembayaran', 'Terverifikasi')->sum('jumlah_bayar');

        return view('admin.pembayaran.index', compact('pembayaran', 'totalTerverifikasi'));
    }

    public function show(PembayaranSiswa $pembayaran)
    {
        $pembayaran->load(['siswa.pendaftaranJurusan.jurusan', 'metodePembayaran']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function verifikasi(Request $request, PembayaranSiswa $pembayaran)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:Menunggu Verifikasi,Terverifikasi,Ditolak',
            'keterangan'        => 'nullable|string|max:500',
        ]);

        $statusBerubah = $pembayaran->status_pembayaran !== $request->status_pembayaran;

        $pembayaran->update([
            'status_pembayaran' => $request->status_pembayaran,
            'keterangan'        => $request->keterangan,
            // Catat admin yang melakukan verifikasi (audit trail).
            'id_admin'          => Auth::guard('admin')->id(),
        ]);

        ActivityLog::catat(
            'Pembayaran',
            'verifikasi',
            "Memverifikasi pembayaran siswa {$pembayaran->siswa?->nama_depan} menjadi \"{$request->status_pembayaran}\"."
        );

        // Kabari siswa lewat email hanya jika statusnya benar-benar berubah,
        // dan hanya untuk keputusan final (Terverifikasi/Ditolak).
        if ($statusBerubah && in_array($request->status_pembayaran, ['Terverifikasi', 'Ditolak']) && $pembayaran->siswa?->email) {
            try {
                Mail::to($pembayaran->siswa->email)->send(new StatusPembayaranDiperbarui($pembayaran));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Unduh kwitansi PDF pembayaran yang sudah terverifikasi (sisi admin).
     */
    public function kwitansi(PembayaranSiswa $pembayaran)
    {
        abort_unless($pembayaran->status_pembayaran === 'Terverifikasi', 404);

        $pembayaran->load(['siswa.pendaftaranJurusan.jurusan', 'metodePembayaran', 'verifikator']);

        $pdf = Pdf::loadView('pembayaran.kwitansi-pdf', compact('pembayaran'))->setPaper('a5', 'portrait');

        ActivityLog::catat('Pembayaran', 'unduh', "Mengunduh kwitansi pembayaran siswa {$pembayaran->siswa?->nama_depan}.");

        return $pdf->download('kwitansi-' . $pembayaran->siswa?->nomor_pendaftaran . '.pdf');
    }
}
