<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** Tampilkan halaman profil */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profil.index', compact('admin'));
    }

    /** Update data diri */
    public function updateProfil(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'nama'          => 'required|string|max:100',
            'jabatan'       => 'required|string|max:100',
            'email'         => 'nullable|email|max:100|unique:admins,email,' . $admin->id,
            'no_hp'         => ['nullable', 'string', 'max:15', 'regex:/^[0-9\+\-\s]+$/'],
            'jenis_kelamin' => 'required|in:L,P',
        ], [
            'nama.required'          => 'Nama lengkap wajib diisi.',
            'jabatan.required'       => 'Jabatan wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah digunakan akun lain.',
            'no_hp.regex'            => 'Format nomor HP tidak valid. Gunakan angka, +, atau -.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $admin->update($request->only('nama', 'jabatan', 'email', 'no_hp', 'jenis_kelamin'));

        ActivityLog::catat('Profil', 'update_profil', 'Memperbarui data profil.');

        return back()->with('success', 'Data profil berhasil diperbarui.')->with('tab', 'profil');
    }

    /** Upload foto profil */
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto.required' => 'Pilih file foto terlebih dahulu.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.mimes'    => 'Format harus JPG, JPEG, PNG, atau WEBP.',
            'foto.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        $admin = Auth::guard('admin')->user();

        // Hapus foto lama
        if ($admin->foto && Storage::disk('public')->exists($admin->foto)) {
            Storage::disk('public')->delete($admin->foto);
        }

        $path = $request->file('foto')->store('admin/foto', 'public');
        $admin->update(['foto' => $path]);

        ActivityLog::catat('Profil', 'upload_foto', 'Mengganti foto profil.');

        return back()->with('success', 'Foto profil berhasil diperbarui.')->with('tab', 'profil');
    }

    /** Hapus foto profil */
    public function hapusFoto()
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->foto && Storage::disk('public')->exists($admin->foto)) {
            Storage::disk('public')->delete($admin->foto);
        }
        $admin->update(['foto' => null]);

        ActivityLog::catat('Profil', 'hapus_foto', 'Menghapus foto profil.');

        return back()->with('success', 'Foto profil berhasil dihapus.')->with('tab', 'profil');
    }

    /** Ganti password */
    public function gantiPassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'password_lama.required'  => 'Password lama wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru.min'       => 'Password minimal 8 karakter.',
        ]);

        if (! Hash::check($request->password_lama, $admin->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama yang Anda masukkan salah.'])
                ->with('tab', 'password');
        }

        if ($request->password_lama === $request->password_baru) {
            return back()
                ->withErrors(['password_baru' => 'Password baru tidak boleh sama dengan password lama.'])
                ->with('tab', 'password');
        }

        $admin->update(['password' => Hash::make($request->password_baru)]);

        ActivityLog::catat('Keamanan', 'ganti_password', 'Berhasil mengganti password akun.');

        return back()->with('success', 'Password berhasil diubah.')->with('tab', 'password');
    }

    /** Simpan preferensi notifikasi */
    public function updateNotifikasi(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'tampilan_rows' => 'required|integer|in:10,25,50,100',
        ]);

        $admin->update([
            'notif_pendaftar_baru'  => $request->boolean('notif_pendaftar_baru'),
            'notif_pembayaran_baru' => $request->boolean('notif_pembayaran_baru'),
            'notif_dokumen_baru'    => $request->boolean('notif_dokumen_baru'),
            'notif_email'           => $request->boolean('notif_email'),
            'tampilan_rows'         => $request->tampilan_rows,
        ]);

        ActivityLog::catat('Profil', 'update_notifikasi', 'Memperbarui preferensi notifikasi.');

        return back()->with('success', 'Preferensi berhasil disimpan.')->with('tab', 'notifikasi');
    }
}
