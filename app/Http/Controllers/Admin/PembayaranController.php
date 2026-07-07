<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranSiswa;
use App\Models\MetodePembayaran;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

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

        $pembayaran->update([
            'status_pembayaran' => $request->status_pembayaran,
            'keterangan'        => $request->keterangan,
        ]);

        ActivityLog::catat(
            'Pembayaran',
            'verifikasi',
            "Memverifikasi pembayaran siswa {$pembayaran->siswa?->nama_depan} menjadi \"{$request->status_pembayaran}\"."
        );

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
