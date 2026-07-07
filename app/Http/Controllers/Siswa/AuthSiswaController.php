<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthSiswaController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('siswa')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        return view('siswa.auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi format input dasar
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Nomor pendaftaran atau NISN wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Cari siswa berdasarkan nomor_pendaftaran ATAU nisn
        $siswa = CalonSiswa::where('nomor_pendaftaran', $request->login)
            ->orWhere('nisn', $request->login)
            ->first();

        // 3. Jika akun tidak ditemukan -> error spesifik di field 'login'
        if (!$siswa) {
            throw ValidationException::withMessages([
                'login' => 'Nomor pendaftaran/NISN tidak ditemukan.',
            ]);
        }

        // 4. Jika akun ditemukan tapi password salah -> error spesifik di field 'password'
        if (!Hash::check($request->password, $siswa->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }

        // 5. Login berhasil
        Auth::guard('siswa')->login($siswa, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('siswa.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('siswa.login');
    }
}