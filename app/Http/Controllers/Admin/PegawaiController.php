<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = Admin::query()
            ->when($request->search, fn($q) =>
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nip', 'like', "%{$request->search}%")
                  ->orWhere('jabatan', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->status !== null && $request->status !== '', fn($q) =>
                $q->where('is_aktif', $request->status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        // Hanya superadmin yang bisa tambah pegawai
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Hanya Super Admin yang dapat menambah pegawai.');
        }
        return view('admin.pegawai.form', ['pegawai' => null]);
    }

    public function store(Request $request)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'nip'            => 'required|string|max:20|unique:admins,nip',
            'nama'           => 'required|string|max:100',
            'jabatan'        => 'required|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'no_hp'          => 'nullable|string|max:15',
            'email'          => 'nullable|email|max:100|unique:admins,email',
            'role'           => 'required|in:superadmin,admin,operator',
            'password'       => ['required', Password::min(8)->letters()->numbers()],
        ], [
            'nip.unique'      => 'NIP sudah terdaftar.',
            'email.unique'    => 'Email sudah digunakan.',
            'password.min'    => 'Password minimal 8 karakter.',
        ]);

        Admin::create([
            'nip'           => $request->nip,
            'nama'          => $request->nama,
            'jabatan'       => $request->jabatan,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->no_hp,
            'email'         => $request->email,
            'role'          => $request->role,
            'password'      => Hash::make($request->password),
            'is_aktif'      => true,
        ]);

        ActivityLog::catat('Pegawai', 'tambah', "Menambahkan pegawai baru: {$request->nama} ({$request->nip}), role {$request->role}.");

        return redirect()->route('admin.pegawai.index')
            ->with('success', "Pegawai {$request->nama} berhasil ditambahkan.");
    }

    public function edit(Admin $pegawai)
    {
        // Admin biasa hanya bisa edit diri sendiri
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin->isSuperAdmin() && $currentAdmin->id !== $pegawai->id_admin) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Anda tidak punya akses untuk mengedit pegawai lain.');
        }

        return view('admin.pegawai.form', compact('pegawai'));
    }

    public function update(Request $request, Admin $pegawai)
    {
        $currentAdmin = Auth::guard('admin')->user();
        if (!$currentAdmin->isSuperAdmin() && $currentAdmin->id !== $pegawai->id_admin) {
            abort(403);
        }

        $request->validate([
            'nip'           => "required|string|max:20|unique:admins,nip,{$pegawai->id_admin},id_admin",
            'nama'          => 'required|string|max:100',
            'jabatan'       => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp'         => 'nullable|string|max:15',
            'email'         => "required|email|max:100|unique:admins,email,{$pegawai->id_admin},id_admin",
            'role'          => 'required|in:superadmin,admin',
            'password'      => ['nullable', Password::min(8)->letters()->numbers()],
        ], [
            'nip.unique'   => 'NIP sudah digunakan pegawai lain.',
            'email.unique' => 'Email sudah digunakan pegawai lain.',
        ]);

        $data = $request->only(['nip','nama','jabatan','jenis_kelamin','no_hp','email','role']);
        $data['is_aktif'] = $request->boolean('is_aktif');

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Jangan bisa nonaktifkan diri sendiri
        if ($currentAdmin->id === $pegawai->id_admin && !$data['is_aktif']) {
            $data['is_aktif'] = true;
        }

        $pegawai->update($data);

        ActivityLog::catat('Pegawai', 'ubah', "Memperbarui data pegawai: {$pegawai->nama} ({$pegawai->nip}).");

        return redirect()->route('admin.pegawai.index')
            ->with('success', "Data pegawai {$pegawai->nama} berhasil diperbarui.");
    }

    public function destroy(Admin $pegawai)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        // Tidak bisa hapus diri sendiri
        if (Auth::guard('admin')->id() === $pegawai->id_admin) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $nama = $pegawai->nama;
        $nip  = $pegawai->nip;
        $pegawai->delete();

        ActivityLog::catat('Pegawai', 'hapus', "Menghapus pegawai: {$nama} ({$nip}).");

        return redirect()->route('admin.pegawai.index')
            ->with('success', "Pegawai {$nama} berhasil dihapus.");
    }

    public function toggleAktif(Admin $pegawai)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403);
        }
        if (Auth::guard('admin')->id() === $pegawai->id_admin) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $pegawai->update(['is_aktif' => !$pegawai->is_aktif]);
        $status = $pegawai->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::catat('Pegawai', $pegawai->is_aktif ? 'aktifkan' : 'nonaktifkan', "Akun pegawai {$pegawai->nama} {$status}.");

        return back()->with('success', "Akun {$pegawai->nama} berhasil {$status}.");
    }
}
