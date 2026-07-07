<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaliOrangTua;
use Illuminate\Http\Request;

class WaliController extends Controller
{
    public function index(Request $request)
    {
        $query = WaliOrangTua::with('alamat')
            ->when($request->search, fn($q) =>
                $q->where('nama_depan', 'like', "%{$request->search}%")
                  ->orWhere('nama_belakang', 'like', "%{$request->search}%")
                  ->orWhere('nomor_hp', 'like', "%{$request->search}%")
            );

        $wali = $query->orderBy('nama_depan')->paginate(15)->withQueryString();
        return view('admin.wali.index', compact('wali'));
    }

    public function show(WaliOrangTua $wali)
    {
        $wali->load(['alamat', 'relasiSiswa.siswa', 'relasiSiswa.tipeRelasi', 'relasiSiswa.siswa.alamatCalonSiswa.alamat']);
        return view('admin.wali.show', compact('wali'));
    }
}
