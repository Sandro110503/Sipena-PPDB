<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusPembayaranDiperbarui;
use App\Models\CalonSiswa;
use App\Models\Jurusan;
use App\Models\ActivityLog;
use App\Models\PembayaranSiswa;
use App\Models\PeriodePpdb;
use App\Exports\SiswaExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    protected array $validStatus = ['Menunggu', 'Diterima', 'Ditolak', 'Cadangan'];
    // Sebelumnya nilainya ['Menunggu','Diverifikasi','Ditolak'] — tidak cocok dengan
    // nilai status_pembayaran yang sebenarnya dipakai di seluruh aplikasi
    // (lihat PembayaranController::verifikasi dan migrasi tabel pembayaran_siswa).
    protected array $validStatusBayar = ['Menunggu Verifikasi', 'Terverifikasi', 'Ditolak'];

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $request->validate([
            'search'  => ['nullable', 'string', 'max:100'],
            'status'  => ['nullable', 'in:' . implode(',', $this->validStatus)],
            'jurusan' => ['nullable', 'integer', 'exists:jurusan,id_jurusan'],
            'periode' => ['nullable', 'integer', 'exists:periode_ppdb,id_periode'],
            'bulan'   => ['nullable', 'date_format:Y-m'],
        ], [
            'search.max'        => 'Kata kunci pencarian maksimal 100 karakter.',
            'status.in'         => 'Status filter tidak valid.',
            'jurusan.integer'   => 'Jurusan tidak valid.',
            'jurusan.exists'    => 'Jurusan yang dipilih tidak ditemukan.',
            'periode.integer'   => 'Periode tidak valid.',
            'periode.exists'    => 'Periode yang dipilih tidak ditemukan.',
            'bulan.date_format' => 'Format bulan tidak valid.',
        ]);

        $query = CalonSiswa::with([
            'periode',
            'pendaftaranJurusan.jurusan',
            'pembayaran' => function ($q) {
                $q->latest('id_pembayaran');
            }
        ])
            ->when($request->search, fn($q) =>
                $q->where('nama_depan', 'like', "%{$request->search}%")
                ->orWhere('nisn', 'like', "%{$request->search}%")
                ->orWhere('nomor_pendaftaran', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn($q) =>
                $q->where('status_penerimaan', $request->status)
            )
            ->when($request->jurusan, fn($q) =>
                $q->whereHas('pendaftaranJurusan', fn($qj) =>
                    $qj->where('id_jurusan', $request->jurusan)
                    ->where('urutan_pilihan', 1)
                )
            )
            ->when($request->periode, fn($q) =>
                $q->where('id_periode', $request->periode)
            )
            ->when($request->bulan, fn($q) =>
                $q->whereRaw("DATE_FORMAT(tanggal_daftar, '%Y-%m') = ?", [$request->bulan])
            )
            ->latest();

        $siswa   = $query->paginate(15)->withQueryString();
        $jurusan = Jurusan::all();
        $periode = PeriodePpdb::orderByDesc('tanggal_buka')->get();

        // Daftar bulan-tahun yang benar-benar ada datanya, untuk isi dropdown filter.
        $bulanOptions = CalonSiswa::whereNotNull('tanggal_daftar')
            ->selectRaw("DATE_FORMAT(tanggal_daftar, '%Y-%m') as ym")
            ->distinct()
            ->orderByDesc('ym')
            ->pluck('ym');

        return view('admin.students.index', compact('siswa', 'jurusan', 'periode', 'bulanOptions'));
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    public function show(CalonSiswa $calonSiswa)
    {
        $calonSiswa->load([
            'pendaftaranJurusan.jurusan',
            'pembayaran.metodePembayaran',
            'relasiSiswa.wali',
            'alamatCalonSiswa.alamat.pemilik',
            'alamatCalonSiswa.jenisAlamat',
        ]);

        $berkas = \App\Services\BerkasPersyaratanService::status($calonSiswa->id_siswa);

        return view('admin.students.show', compact('calonSiswa', 'berkas'));
    }

    // -------------------------------------------------------------------------
    // UPDATE STATUS PENERIMAAN
    // -------------------------------------------------------------------------

    public function updateStatus(Request $request, CalonSiswa $calonSiswa)
    {
        $request->validate([
            'status_penerimaan' => [
                'required',
                'string',
                'in:' . implode(',', $this->validStatus),
            ],
        ], [
            'status_penerimaan.required' => 'Status penerimaan wajib dipilih.',
            'status_penerimaan.in'       => 'Status penerimaan tidak valid. Pilih salah satu: ' . implode(', ', $this->validStatus) . '.',
        ]);

        if ($calonSiswa->status_penerimaan === $request->status_penerimaan) {
            return back()->with('info', 'Status siswa tidak berubah.');
        }

        if ($calonSiswa->status_penerimaan === 'Diterima' && $request->status_penerimaan === 'Menunggu') {
            return back()->with('error', 'Siswa yang sudah diterima tidak dapat dikembalikan ke status Menunggu.');
        }

        $statusLama = $calonSiswa->status_penerimaan;

        $calonSiswa->update([
            'status_penerimaan' => $request->status_penerimaan,
            'tanggal_diterima'  => $request->status_penerimaan === 'Diterima' ? now() : null,
        ]);

        if ($request->status_penerimaan === 'Diterima') {
            $pilihan1 = $calonSiswa->pendaftaranJurusan()
                ->where('urutan_pilihan', 1)
                ->first();

            if ($pilihan1) {
                $pilihan1->update(['status' => 'Diterima']);
            }
        } else {
            $calonSiswa->pendaftaranJurusan()->update(['status' => 'Aktif']);
        }

        ActivityLog::catat(
            'Data Siswa',
            'status',
            "Mengubah status penerimaan siswa {$calonSiswa->nama_depan} ({$calonSiswa->nomor_pendaftaran}) dari \"{$statusLama}\" menjadi \"{$request->status_penerimaan}\"."
        );

        return back()->with('success', 'Status siswa berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // EXPORT PDF
    // -------------------------------------------------------------------------

    public function exportPdf(Request $request)
    {
        $request->validate([
            'status'  => ['nullable', 'in:' . implode(',', $this->validStatus)],
            'jurusan' => ['nullable', 'integer', 'exists:jurusan,id_jurusan'],
            'periode' => ['nullable', 'integer', 'exists:periode_ppdb,id_periode'],
            'bulan'   => ['nullable', 'date_format:Y-m'],
        ], [
            'status.in'         => 'Filter status tidak valid.',
            'jurusan.exists'    => 'Jurusan yang dipilih tidak ditemukan.',
            'periode.exists'    => 'Periode yang dipilih tidak ditemukan.',
            'bulan.date_format' => 'Format bulan tidak valid.',
        ]);

        $siswa = CalonSiswa::with([
                'periode',
                'pendaftaranJurusan.jurusan',
                'alamatCalonSiswa.alamat',
                'relasiSiswa.wali.alamat',
            ])
            ->when($request->status, fn($q) => $q->where('status_penerimaan', $request->status))
            ->when($request->jurusan, fn($q) =>
                $q->whereHas('pendaftaranJurusan', fn($qj) =>
                    $qj->where('id_jurusan', $request->jurusan)
                       ->where('urutan_pilihan', 1)
                )
            )
            ->when($request->periode, fn($q) => $q->where('id_periode', $request->periode))
            ->when($request->bulan, fn($q) =>
                $q->whereRaw("DATE_FORMAT(tanggal_daftar, '%Y-%m') = ?", [$request->bulan])
            )
            ->get();

        if ($siswa->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa yang sesuai filter untuk diekspor.');
        }

        $jurusanFilter = $request->jurusan ? Jurusan::find($request->jurusan) : null;
        $periodeFilter = $request->periode ? PeriodePpdb::find($request->periode) : null;

        $pdf = Pdf::loadView('admin.students.pdf', compact('siswa', 'jurusanFilter', 'periodeFilter'))
            ->setPaper('a4', 'landscape');

        $namaFile = 'data-siswa-ppdb'
            . ($request->bulan ? '-' . $request->bulan : '')
            . ($request->periode ? '-periode' . $request->periode : '')
            . '-' . date('Ymd') . '.pdf';

        return $pdf->download($namaFile);
    }

    // -------------------------------------------------------------------------
    // EXPORT EXCEL
    // -------------------------------------------------------------------------

    public function exportExcel(Request $request)
    {
        $request->validate([
            'status'  => ['nullable', 'in:' . implode(',', $this->validStatus)],
            'jurusan' => ['nullable', 'integer', 'exists:jurusan,id_jurusan'],
            'periode' => ['nullable', 'integer', 'exists:periode_ppdb,id_periode'],
            'bulan'   => ['nullable', 'date_format:Y-m'],
        ], [
            'status.in'        => 'Filter status tidak valid.',
            'jurusan.exists'   => 'Jurusan yang dipilih tidak ditemukan.',
            'periode.exists'   => 'Periode yang dipilih tidak ditemukan.',
            'bulan.date_format'=> 'Format bulan tidak valid.',
        ]);

        $count = CalonSiswa::when($request->status, fn($q) => $q->where('status_penerimaan', $request->status))
            ->when($request->jurusan, fn($q) =>
                $q->whereHas('pendaftaranJurusan', fn($qj) =>
                    $qj->where('id_jurusan', $request->jurusan)
                       ->where('urutan_pilihan', 1)
                )
            )
            ->when($request->periode, fn($q) => $q->where('id_periode', $request->periode))
            ->when($request->bulan, fn($q) =>
                $q->whereRaw("DATE_FORMAT(tanggal_daftar, '%Y-%m') = ?", [$request->bulan])
            )
            ->count();

        if ($count === 0) {
            return back()->with('error', 'Tidak ada data siswa yang sesuai filter untuk diekspor.');
        }

        $namaFile = 'data-siswa-ppdb'
            . ($request->bulan ? '-' . $request->bulan : '')
            . ($request->periode ? '-periode' . $request->periode : '')
            . '-' . date('Ymd') . '.xlsx';

        return Excel::download(
            new SiswaExport($request->status, $request->jurusan, $request->periode, $request->bulan),
            $namaFile
        );
    }

    // -------------------------------------------------------------------------
    // VERIFIKASI PEMBAYARAN
    // -------------------------------------------------------------------------

    public function verifikasiPembayaran(Request $request, $id)
    {
        $request->validate([
            'status'  => [
                'required',
                'string',
                'in:' . implode(',', $this->validStatusBayar),
            ],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'status.required' => 'Status pembayaran wajib dipilih.',
            'status.in'       => 'Status pembayaran tidak valid. Pilih: ' . implode(', ', $this->validStatusBayar) . '.',
            'catatan.max'     => 'Catatan maksimal 500 karakter.',
        ]);

        $pembayaran = PembayaranSiswa::findOrFail($id);

        if ($pembayaran->status_pembayaran === $request->status) {
            return back()->with('info', 'Status pembayaran tidak berubah.');
        }

        // Sebelumnya mengecek kolom 'bukti_pembayaran' yang tidak ada di skema DB
        // (nama kolom sebenarnya adalah 'bukti_bayar'), sehingga pengecekan ini
        // tidak pernah berfungsi. Sudah diperbaiki.
        if (empty($pembayaran->bukti_bayar)) {
            return back()->with('error', 'Tidak dapat memverifikasi pembayaran karena bukti pembayaran belum diunggah.');
        }

        $statusLama = $pembayaran->status_pembayaran;

        $updateData = [
            'status_pembayaran' => $request->status,
            // Catat admin yang melakukan verifikasi (audit trail), konsisten
            // dengan PembayaranController::verifikasi.
            'id_admin' => Auth::guard('admin')->id(),
        ];

        // Sebelumnya menulis ke kolom 'catatan_verifikasi' yang tidak ada di skema DB
        // (nama kolom sebenarnya adalah 'keterangan'), sehingga catatan yang diisi
        // admin tidak pernah benar-benar tersimpan. Sudah diperbaiki.
        if ($request->filled('catatan')) {
            $updateData['keterangan'] = $request->catatan;
        }

        $pembayaran->update($updateData);

        $namaSiswa = $pembayaran->siswa?->nama_depan ?? 'Tidak diketahui';

        ActivityLog::catat(
            'Pembayaran',
            'verifikasi',
            "Mengubah status pembayaran siswa {$namaSiswa} dari \"{$statusLama}\" menjadi \"{$request->status}\"."
        );

        // Kabari siswa lewat email, konsisten dengan PembayaranController::verifikasi.
        if (in_array($request->status, ['Terverifikasi', 'Ditolak']) && $pembayaran->siswa?->email) {
            try {
                Mail::to($pembayaran->siswa->email)->send(new StatusPembayaranDiperbarui($pembayaran));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}