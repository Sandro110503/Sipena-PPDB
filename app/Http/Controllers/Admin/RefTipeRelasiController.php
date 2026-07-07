<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefTipeRelasi;
use Illuminate\Http\Request;

class RefTipeRelasiController extends Controller
{
    public function index()
    {
        $tipe = RefTipeRelasi::orderBy('kode_tipe_relasi')->paginate(20);
        return view('admin.ref-tipe-relasi.index', compact('tipe'));
    }

    public function create()
    {
        return view('admin.ref-tipe-relasi.form', ['tipe' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_tipe_relasi'      => 'required|string|max:255|unique:ref_tipe_relasi,kode_tipe_relasi',
            'deskripsi_tipe_relasi' => 'required|string|max:255',
        ]);
        RefTipeRelasi::create($request->only('kode_tipe_relasi', 'deskripsi_tipe_relasi'));
        return redirect()->route('admin.ref-tipe-relasi.index')->with('success', 'Tipe relasi berhasil ditambahkan.');
    }

    public function edit(RefTipeRelasi $refTipeRelasi)
    {
        return view('admin.ref-tipe-relasi.form', ['tipe' => $refTipeRelasi]);
    }

    public function update(Request $request, RefTipeRelasi $refTipeRelasi)
    {
        $request->validate(['deskripsi_tipe_relasi' => 'required|string|max:255']);
        $refTipeRelasi->update($request->only('deskripsi_tipe_relasi'));
        return redirect()->route('admin.ref-tipe-relasi.index')->with('success', 'Tipe relasi berhasil diperbarui.');
    }

    public function destroy(RefTipeRelasi $refTipeRelasi)
    {
        $refTipeRelasi->delete();
        return back()->with('success', 'Tipe relasi berhasil dihapus.');
    }
}
