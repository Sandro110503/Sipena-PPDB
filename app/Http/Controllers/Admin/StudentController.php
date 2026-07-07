<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\Jurusan;
use App\Models\ActivityLog;
use App\Models\PembayaranSiswa;
use App\Models\PeriodePpdb;
use App\Exports\SiswaExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    protected array $validStatus = ['Menunggu', 'Diterima', 'Ditolak', 'Cadangan'];
    protected array $validStatusBayar = ['Menunggu', 'Diverifikasi', 'Ditolak'];

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
        ], [
            'search.max'      => 'Kata kunci pencarian maksimal 100 karakter.',
            'status.in'       => 'Status filter tidak valid.',
            'jurusan.integer' => 'Jurusan tidak valid.',
            'jurusan.exists'  => 'Jurusan yang dipilih tidak ditemukan.',
            'periode.integer' => 'Periode tidak valid.',
            'periode.exists'  => 'Periode yang dipilih tidak ditemukan.',
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
            ->latest();

        $siswa   = $query->paginate(15)->withQueryString();
        $jurusan = Jurusan::all();
        $periode = PeriodePpdb::orderByDesc('tanggal_buka')->get();

        return view('admin.students.index', compact('siswa', 'jurusan', 'periode'));
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

        return view('admin.students.show', compact('calonSiswa'));
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
        ], [
            'status.in'      => 'Filter status tidak valid.',
            'jurusan.exists' => 'Jurusan yang dipilih tidak ditemukan.',
        ]);

        $siswa = CalonSiswa::with([
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
            ->get();

        if ($siswa->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa yang sesuai filter untuk diekspor.');
        }

        $jurusanFilter = $request->jurusan ? Jurusan::find($request->jurusan) : null;

        $pdf = Pdf::loadView('admin.students.pdf', compact('siswa', 'jurusanFilter'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('data-siswa-ppdb-' . date('Ymd') . '.pdf');
    }

    // -------------------------------------------------------------------------
    // EXPORT EXCEL
    // -------------------------------------------------------------------------

    public function exportExcel(Request $request)
    {
        $request->validate([
            'status'  => ['nullable', 'in:' . implode(',', $this->validStatus)],
            'jurusan' => ['nullable', 'integer', 'exists:jurusan,id_jurusan'],
        ], [
            'status.in'      => 'Filter status tidak valid.',
            'jurusan.exists' => 'Jurusan yang dipilih tidak ditemukan.',
        ]);

        $count = CalonSiswa::when($request->status, fn($q) => $q->where('status_penerimaan', $request->status))
            ->when($request->jurusan, fn($q) =>
                $q->whereHas('pendaftaranJurusan', fn($qj) =>
                    $qj->where('id_jurusan', $request->jurusan)
                       ->where('urutan_pilihan', 1)
                )
            )
            ->count();

        if ($count === 0) {
            return back()->with('error', 'Tidak ada data siswa yang sesuai filter untuk diekspor.');
        }

        return Excel::download(
            new SiswaExport($request->status, $request->jurusan),
            'data-siswa-ppdb-' . date('Ymd') . '.xlsx'
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

        if (empty($pembayaran->bukti_pembayaran)) {
            return back()->with('error', 'Tidak dapat memverifikasi pembayaran karena bukti pembayaran belum diunggah.');
        }

        $statusLama = $pembayaran->status_pembayaran;

        $updateData = ['status_pembayaran' => $request->status];

        if ($request->filled('catatan')) {
            $updateData['catatan_verifikasi'] = $request->catatan;
        }

        $pembayaran->update($updateData);

        $namaSiswa = $pembayaran->siswa?->nama_depan ?? 'Tidak diketahui';

        ActivityLog::catat(
            'Pembayaran',
            'verifikasi',
            "Mengubah status pembayaran siswa {$namaSiswa} dari \"{$statusLama}\" menjadi \"{$request->status}\"."
        );

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}