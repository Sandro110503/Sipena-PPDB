<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::withCount([
            'pendaftaran as total_pendaftar',
            'pendaftaran as pilihan1' => fn($q) => $q->where('urutan_pilihan', 1),
            'siswaDiterima as diterima',
        ])->orderBy('kode_jurusan')->get();

        return view('admin.jurusan.index', compact('jurusan'));
    }

    public function create()
    {
        // Saran kode berikutnya: cari kode terbesar + 1
        $nextKode = Jurusan::max('kode_jurusan');
        $nextKode = $nextKode ? str_pad((int)$nextKode + 1, 2, '0', STR_PAD_LEFT) : '01';

        return view('admin.jurusan.form', ['jurusan' => null, 'nextKode' => $nextKode]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan'       => 'required|digits:2|unique:jurusan,kode_jurusan',
            'singkatan'          => 'required|string|max:10',
            'nama_jurusan'       => 'required|string|max:150',
            'deskripsi'          => 'nullable|string',
            'kapasitas'          => 'required|integer|min:1|max:200',
            'keterangan_lainnya' => 'nullable|string|max:255',
        ], [
            'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
            'kode_jurusan.digits'   => 'Kode jurusan harus tepat 2 digit angka (01–99).',
            'kode_jurusan.unique'   => 'Kode jurusan sudah digunakan.',
            'kapasitas.min'         => 'Kapasitas minimal 1 siswa.',
        ]);

        Jurusan::create($request->only([
            'kode_jurusan', 'singkatan', 'nama_jurusan',
            'deskripsi', 'kapasitas', 'keterangan_lainnya',
        ]));

        ActivityLog::catat('Jurusan', 'tambah', "Menambahkan jurusan: {$request->nama_jurusan} ({$request->kode_jurusan}).");

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Jurusan {$request->nama_jurusan} berhasil ditambahkan.");
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.form', ['jurusan' => $jurusan, 'nextKode' => null]);
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan'       => "required|digits:2|unique:jurusan,kode_jurusan,{$jurusan->id_jurusan},id_jurusan",
            'singkatan'          => 'required|string|max:10',
            'nama_jurusan'       => 'required|string|max:150',
            'deskripsi'          => 'nullable|string',
            'kapasitas'          => 'required|integer|min:1|max:200',
            'keterangan_lainnya' => 'nullable|string|max:255',
        ], [
            'kode_jurusan.digits'  => 'Kode jurusan harus tepat 2 digit angka (01–99).',
            'kode_jurusan.unique'  => 'Kode jurusan sudah digunakan jurusan lain.',
        ]);

        $kodeChanged = $jurusan->kode_jurusan !== $request->kode_jurusan;

        $jurusan->update($request->only([
            'kode_jurusan', 'singkatan', 'nama_jurusan',
            'deskripsi', 'kapasitas', 'keterangan_lainnya',
        ]));

        $pesan = "Jurusan {$jurusan->nama_jurusan} berhasil diperbarui.";
        if ($kodeChanged) {
            $pesan .= ' Perhatian: nomor pendaftaran siswa yang sudah ada tidak ikut berubah.';
        }

        ActivityLog::catat('Jurusan', 'ubah', "Memperbarui jurusan: {$jurusan->nama_jurusan} ({$jurusan->kode_jurusan}).");

        return redirect()->route('admin.jurusan.index')->with('success', $pesan);
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->pendaftaran()->exists()) {
            return back()->with('error',
                "Jurusan {$jurusan->nama_jurusan} tidak bisa dihapus karena masih ada siswa yang mendaftar."
            );
        }

        $nama = $jurusan->nama_jurusan;
        $kode = $jurusan->kode_jurusan;
        $jurusan->delete();

        ActivityLog::catat('Jurusan', 'hapus', "Menghapus jurusan: {$nama} ({$kode}).");

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Jurusan {$nama} berhasil dihapus.");
    }
}
