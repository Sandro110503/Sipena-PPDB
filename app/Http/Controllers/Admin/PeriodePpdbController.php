<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePpdb;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodePpdbController extends Controller
{
    // ────────────────────────────────────────────
    // Daftar semua periode
    // ────────────────────────────────────────────
    public function index()
    {
        $periodes = PeriodePpdb::orderByDesc('tahun_ajaran')
                               ->orderByDesc('gelombang')
                               ->get();

        return view('admin.periode.index', compact('periodes'));
    }

    // ────────────────────────────────────────────
    // Tampilkan form tambah
    // ────────────────────────────────────────────
    public function create()
    {
        return view('admin.periode.form', ['periode' => null]);
    }

    // ────────────────────────────────────────────
    // Simpan periode baru
    // ────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Jika periode ini diset aktif, nonaktifkan semua yang lain dulu
        if ($data['is_aktif']) {
            PeriodePpdb::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        PeriodePpdb::create($data);

        ActivityLog::catat('Periode PPDB', 'tambah', "Menambahkan periode PPDB: {$data['nama_periode']} (T.A. {$data['tahun_ajaran']} gel. {$data['gelombang']}).");

        return redirect()->route('admin.periode.index')
                         ->with('success', 'Periode PPDB berhasil ditambahkan.');
    }

    // ────────────────────────────────────────────
    // Tampilkan form edit
    // ────────────────────────────────────────────
    public function edit(PeriodePpdb $periode)
    {
        return view('admin.periode.form', compact('periode'));
    }

    // ────────────────────────────────────────────
    // Update periode
    // ────────────────────────────────────────────
    public function update(Request $request, PeriodePpdb $periode)
    {
        $data = $this->validated($request, $periode->id_periode);

        if ($data['is_aktif']) {
            PeriodePpdb::where('is_aktif', true)
                       ->where('id_periode', '!=', $periode->id_periode)
                       ->update(['is_aktif' => false]);
        }

        $periode->update($data);

        ActivityLog::catat('Periode PPDB', 'ubah', "Memperbarui periode PPDB: {$periode->nama_periode}.");

        return redirect()->route('admin.periode.index')
                         ->with('success', 'Periode PPDB berhasil diperbarui.');
    }

    // ────────────────────────────────────────────
    // Hapus periode
    // ────────────────────────────────────────────
    public function destroy(PeriodePpdb $periode)
    {
        $nama = $periode->nama_periode;
        $periode->delete();

        ActivityLog::catat('Periode PPDB', 'hapus', "Menghapus periode PPDB: {$nama}.");

        return redirect()->route('admin.periode.index')
                         ->with('success', 'Periode PPDB berhasil dihapus.');
    }

    // ────────────────────────────────────────────
    // Toggle aktif / nonaktif (PATCH cepat)
    // ────────────────────────────────────────────
    public function toggleAktif(PeriodePpdb $periode)
    {
        if (! $periode->is_aktif) {
            // Nonaktifkan semua, lalu aktifkan yang ini
            PeriodePpdb::where('is_aktif', true)->update(['is_aktif' => false]);
            $periode->update(['is_aktif' => true]);
            $msg = "Periode \"{$periode->nama_periode}\" kini menjadi periode aktif.";
            ActivityLog::catat('Periode PPDB', 'aktifkan', "Mengaktifkan periode PPDB: {$periode->nama_periode}.");
        } else {
            // Hanya matikan
            $periode->update(['is_aktif' => false]);
            $msg = "Periode \"{$periode->nama_periode}\" dinonaktifkan.";
            ActivityLog::catat('Periode PPDB', 'nonaktifkan', "Menonaktifkan periode PPDB: {$periode->nama_periode}.");
        }

        return back()->with('success', $msg);
    }

    // ────────────────────────────────────────────
    // Validasi (dipakai store & update)
    // ────────────────────────────────────────────
    private function validated(Request $request, ?int $excludeId = null): array
    {
        return $request->validate([
            'nama_periode'       => 'required|string|max:150',
            'tahun_ajaran'       => 'required|digits:4|integer|min:2020|max:2099',
            'gelombang'          => 'required|integer|min:1|max:10',
            'tanggal_buka'       => 'required|date',
            'tanggal_tutup'      => 'required|date|after_or_equal:tanggal_buka',
            'tanggal_pengumuman' => 'nullable|date|after_or_equal:tanggal_tutup',
            'biaya_pendaftaran'  => 'required|numeric|min:0',
            'keterangan'         => 'nullable|string|max:1000',
            'is_aktif'           => 'boolean',
        ], [
            'tanggal_tutup.after_or_equal'      => 'Tanggal tutup harus sama atau setelah tanggal buka.',
            'tanggal_pengumuman.after_or_equal'  => 'Tanggal pengumuman harus sama atau setelah tanggal tutup.',
        ]);
    }
}
