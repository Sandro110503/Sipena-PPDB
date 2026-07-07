<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metode = MetodePembayaran::orderBy('kode_metode_bayar')->paginate(20);
        return view('admin.metode-pembayaran.index', compact('metode'));
    }

    public function create()
    {
        return view('admin.metode-pembayaran.form', ['metode' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_metode_bayar'    => 'required|string|max:255|unique:metode_pembayaran,kode_metode_bayar',
            'deskripsi_metode_bayar' => 'required|string|max:255',
        ]);

        MetodePembayaran::create($request->only('kode_metode_bayar', 'deskripsi_metode_bayar'));
        return redirect()->route('admin.metode-pembayaran.index')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(MetodePembayaran $metodePembayaran)
    {
        return view('admin.metode-pembayaran.form', ['metode' => $metodePembayaran]);
    }

    public function update(Request $request, MetodePembayaran $metodePembayaran)
    {
        $request->validate([
            'deskripsi_metode_bayar' => 'required|string|max:255',
        ]);

        $metodePembayaran->update($request->only('deskripsi_metode_bayar'));
        return redirect()->route('admin.metode-pembayaran.index')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(MetodePembayaran $metodePembayaran)
    {
        $metodePembayaran->delete();
        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
