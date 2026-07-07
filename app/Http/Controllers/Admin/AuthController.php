<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Maksimal percobaan login sebelum akun dikunci sementara.
     */
    protected int $maxAttempts = 5;

    /**
     * Durasi lockout dalam menit.
     */
    protected int $decayMinutes = 5;

    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi format input
        $request->validate([
            'nip' => [
                'required',
                'string',
                'min:5',
                'max:30',
                'regex:/^[0-9]+$/', // NIP hanya angka
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ], [
            'nip.required'  => 'NIP wajib diisi.',
            'nip.min'       => 'NIP minimal 5 karakter.',
            'nip.max'       => 'NIP maksimal 30 karakter.',
            'nip.regex'     => 'NIP hanya boleh berisi angka.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        // 2. Cek rate limiting (brute force protection)
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $menit   = ceil($seconds / 60);

            ActivityLog::catatLoginGagal($request->nip);

            throw ValidationException::withMessages([
                'nip' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$menit} menit.",
            ]);
        }

        // 3. Cek apakah NIP terdaftar di database
        $admin = Admin::where('nip', $request->nip)->first();

        if (!$admin) {
            RateLimiter::hit($throttleKey, $this->decayMinutes * 60);
            ActivityLog::catatLoginGagal($request->nip);

            throw ValidationException::withMessages([
                'nip' => 'NIP tidak ditemukan dalam sistem.',
            ]);
        }

        // 4. Cek apakah akun aktif
        if (!$admin->is_aktif) {
            ActivityLog::catatLoginGagal($request->nip);

            throw ValidationException::withMessages([
                'nip' => 'Akun Anda tidak aktif.',
            ]);
        }

        // 5. Cek password
        if (!Hash::check($request->password, $admin->password)) {
            RateLimiter::hit($throttleKey, $this->decayMinutes * 60);
            ActivityLog::catatLoginGagal($request->nip);

            $sisaPercobaan = $this->maxAttempts - RateLimiter::attempts($throttleKey);

            throw ValidationException::withMessages([
                'password' => $sisaPercobaan > 0
                    ? "Password salah. Sisa percobaan: {$sisaPercobaan} kali."
                    : "Password salah. Akun dikunci sementara selama {$this->decayMinutes} menit.",
            ]);
        }

        // 6. Login berhasil — reset rate limiter
        RateLimiter::clear($throttleKey);

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLog::catat('Auth', 'login', 'Berhasil login ke dashboard admin.');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        ActivityLog::catat('Auth', 'logout', 'Keluar dari dashboard admin.');

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda berhasil keluar.');
    }

    /**
     * Generate throttle key unik berdasarkan NIP + IP address.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('nip')) . '|' . $request->ip();
    }
}