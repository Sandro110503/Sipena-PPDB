<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Jurusan;
use App\Models\PembayaranSiswa;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pendaftar'  => CalonSiswa::count(),
            'menunggu'         => CalonSiswa::where('status_penerimaan', 'Menunggu')->count(),
            'diterima'         => CalonSiswa::where('status_penerimaan', 'Diterima')->count(),
            'ditolak'          => CalonSiswa::where('status_penerimaan', 'Ditolak')->count(),
            'cadangan'         => CalonSiswa::where('status_penerimaan', 'Cadangan')->count(),
            'total_pembayaran' => PembayaranSiswa::where('status_pembayaran', 'Terverifikasi')->sum('jumlah_bayar'),
        ];

        $jurusan = Jurusan::withCount([
            'pendaftaran as pilihan1' => fn($q) => $q->where('urutan_pilihan', 1),
            'pendaftaran as pilihan2' => fn($q) => $q->where('urutan_pilihan', 2),
            'siswaDiterima as diterima',
        ])->get();

        $pendaftarTerbaru = CalonSiswa::with('pendaftaranJurusan.jurusan')
            ->latest()
            ->take(10)
            ->get();

        $perBulan = CalonSiswa::selectRaw("
            DATE_FORMAT(tanggal_daftar, '%b %Y') as bulan,
            COUNT(*) as jumlah
        ")
        ->groupBy('bulan')
        ->orderByRaw('MIN(tanggal_daftar)')
        ->get();

        $perPeriode = CalonSiswa::join(
            'periode_ppdb',
            'calon_siswa.id_periode',
            '=',
            'periode_ppdb.id_periode'
        )
        ->selectRaw('periode_ppdb.nama_periode, COUNT(*) as jumlah')
        ->groupBy('periode_ppdb.id_periode', 'periode_ppdb.nama_periode')
        ->orderBy('periode_ppdb.id_periode')
        ->get();

        // Distribusi pembayaran per metode (hanya yang sudah terverifikasi).
        $pembayaranPerMetode = PembayaranSiswa::join(
            'metode_pembayaran',
            'pembayaran_siswa.kode_metode_bayar',
            '=',
            'metode_pembayaran.kode_metode_bayar'
        )
        ->where('pembayaran_siswa.status_pembayaran', 'Terverifikasi')
        ->selectRaw('metode_pembayaran.deskripsi_metode_bayar as metode, COUNT(*) as jumlah, SUM(pembayaran_siswa.jumlah_bayar) as total')
        ->groupBy('metode_pembayaran.kode_metode_bayar', 'metode_pembayaran.deskripsi_metode_bayar')
        ->orderByDesc('jumlah')
        ->get();

        // Distribusi status verifikasi pembayaran (semua transaksi masuk).
        $pembayaranPerStatus = PembayaranSiswa::selectRaw('status_pembayaran, COUNT(*) as jumlah')
            ->groupBy('status_pembayaran')
            ->get()
            ->pluck('jumlah', 'status_pembayaran');

        // Distribusi status verifikasi dokumen persyaratan.
        $dokumenPerStatus = \App\Models\DokumenPersyaratan::selectRaw('status_verifikasi, COUNT(*) as jumlah')
            ->groupBy('status_verifikasi')
            ->get()
            ->pluck('jumlah', 'status_verifikasi');

        return view('admin.dashboard.index', compact(
            'stats', 'jurusan', 'pendaftarTerbaru', 'perBulan', 'perPeriode',
            'pembayaranPerMetode', 'pembayaranPerStatus', 'dokumenPerStatus'
        ));
    }
}
