<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefJenisAlamat;
use Illuminate\Http\Request;

class RefJenisAlamatController extends Controller
{
    public function index()
    {
        $jenis = RefJenisAlamat::orderBy('kode_jenis_alamat')->paginate(20);
        return view('admin.ref-jenis-alamat.index', compact('jenis'));
    }

    public function create()
    {
        return view('admin.ref-jenis-alamat.form', ['jenis' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jenis_alamat'      => 'required|string|max:255|unique:ref_jenis_alamat,kode_jenis_alamat',
            'deskripsi_jenis_alamat' => 'required|string|max:255',
        ]);
        RefJenisAlamat::create($request->only('kode_jenis_alamat', 'deskripsi_jenis_alamat'));
        return redirect()->route('admin.ref-jenis-alamat.index')->with('success', 'Jenis alamat berhasil ditambahkan.');
    }

    public function edit(RefJenisAlamat $refJenisAlamat)
    {
        return view('admin.ref-jenis-alamat.form', ['jenis' => $refJenisAlamat]);
    }

    public function update(Request $request, RefJenisAlamat $refJenisAlamat)
    {
        $request->validate(['deskripsi_jenis_alamat' => 'required|string|max:255']);
        $refJenisAlamat->update($request->only('deskripsi_jenis_alamat'));
        return redirect()->route('admin.ref-jenis-alamat.index')->with('success', 'Jenis alamat berhasil diperbarui.');
    }

    public function destroy(RefJenisAlamat $refJenisAlamat)
    {
        $refJenisAlamat->delete();
        return back()->with('success', 'Jenis alamat berhasil dihapus.');
    }
}
