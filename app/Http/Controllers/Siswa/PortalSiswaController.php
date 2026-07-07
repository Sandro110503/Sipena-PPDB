<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\AlamatCalonSiswa;
use App\Models\MetodePembayaran;
use App\Models\PembayaranSiswa;
use Illuminate\Http\Request;
use App\Models\PeriodePpdb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PortalSiswaController extends Controller
{
    private function siswa()
    {
        return Auth::guard('siswa')->user();
    }

    // ── Dashboard ──────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $siswa = $this->siswa()->load([
            'pendaftaranJurusan.jurusan',
            'pembayaran.metodePembayaran',
        ]);

        $pembayaranTerverifikasi = $siswa->pembayaran->where('status_pembayaran', 'Terverifikasi')->first();
        $pembayaranMenunggu      = $siswa->pembayaran->where('status_pembayaran', 'Menunggu Verifikasi')->first();
        $pembayaranDitolak       = $siswa->pembayaran->where('status_pembayaran', 'Ditolak')->first();
        $sudahBayar              = $pembayaranTerverifikasi !== null;
        $bisaUploadBayar         = !$sudahBayar && !$pembayaranMenunggu;

        return view('siswa.dashboard', compact(
            'siswa', 'sudahBayar', 'bisaUploadBayar',
            'pembayaranTerverifikasi', 'pembayaranMenunggu', 'pembayaranDitolak'
        ));
    }

    // ── Pembayaran ─────────────────────────────────────────────────────────────

    public function formPembayaran()
    {
        $siswa = $this->siswa()->load(['pembayaran.metodePembayaran', 'pendaftaranJurusan.jurusan']);

        $pembayaranTerverifikasi = $siswa->pembayaran->where('status_pembayaran', 'Terverifikasi')->first();
        $pembayaranDitolak       = $siswa->pembayaran->where('status_pembayaran', 'Ditolak')->first();
        $pembayaranMenunggu      = $siswa->pembayaran->where('status_pembayaran', 'Menunggu Verifikasi')->first();
        $metode                  = MetodePembayaran::all();
        $periodePpdb             = PeriodePpdb::periodeAktif(); // ← pastikan baris ini ada

        return view('siswa.pembayaran', compact(
            'siswa', 'metode',
            'pembayaranTerverifikasi', 'pembayaranDitolak', 'pembayaranMenunggu',
            'periodePpdb'                                   // ← pastikan ini juga ada
        ));
    }

    public function uploadBukti(Request $request)
    {
        $siswa = $this->siswa();

        if ($siswa->pembayaran()->where('status_pembayaran', 'Terverifikasi')->exists()) {
            return redirect()->route('siswa.pembayaran')
                ->with('error', 'Pembayaran Anda sudah terverifikasi.');
        }
        if ($siswa->pembayaran()->where('status_pembayaran', 'Menunggu Verifikasi')->exists()) {
            return redirect()->route('siswa.pembayaran')
                ->with('error', 'Bukti pembayaran Anda sedang dalam proses verifikasi. Harap tunggu.');
        }

        $request->validate([
            'kode_metode_bayar' => 'required|exists:metode_pembayaran,kode_metode_bayar',
            'jumlah_bayar'      => 'required|numeric|min:1000',
            'tanggal_bayar'     => 'required|date|before_or_equal:today',
            'bukti_bayar'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'keterangan'        => 'nullable|string|max:255',
        ], [
            'kode_metode_bayar.required'    => 'Metode pembayaran wajib dipilih.',
            'jumlah_bayar.min'              => 'Jumlah pembayaran tidak valid.',
            'tanggal_bayar.before_or_equal' => 'Tanggal pembayaran tidak boleh melebihi hari ini.',
            'bukti_bayar.mimes'             => 'Format file harus JPG, PNG, atau PDF.',
            'bukti_bayar.max'               => 'Ukuran file maksimal 3MB.',
        ]);

        foreach ($siswa->pembayaran()->where('status_pembayaran', 'Ditolak')->get() as $d) {
            if ($d->bukti_bayar) Storage::disk('public')->delete($d->bukti_bayar);
            $d->delete();
        }

        $path = $request->file('bukti_bayar')->store('bukti-pembayaran', 'public');

        PembayaranSiswa::create([
            'id_siswa'          => $siswa->id_siswa,
            'kode_metode_bayar' => $request->kode_metode_bayar,
            'jumlah_bayar'      => $request->jumlah_bayar,
            'tanggal_bayar'     => $request->tanggal_bayar,
            'bukti_bayar'       => $path,
            'keterangan'        => $request->keterangan,
            'status_pembayaran' => 'Menunggu Verifikasi',
        ]);

        return redirect()->route('siswa.pembayaran')
            ->with('success', 'Bukti pembayaran berhasil diunggah! Silakan tunggu verifikasi dari panitia.');
    }

    // ── Halaman Pengaturan (tab: profil | alamat | password | notifikasi) ──────

    public function pengaturan()
    {
        $siswa = $this->siswa()->load(['alamatCalonSiswa.alamat']);

        $notifPrefs = session('notif_prefs_' . $siswa->id_siswa, [
            'notif_status'     => true,
            'notif_pembayaran' => true,
            'notif_dokumen'    => false,
            'notif_pengumuman' => true,
        ]);

        return view('siswa.pengaturan', compact('siswa', 'notifPrefs'));
    }

    // ── Update Profil (Data Diri) ───────────────────────────────────────────────

    public function updateProfil(Request $request)
    {
        $siswa = $this->siswa();

        $request->validate([
            'nama_depan'    => 'required|string|max:100',
            'nama_tengah'   => 'nullable|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'nomor_hp'      => 'required|string|max:20',
            'email'         => 'required|email|max:150|unique:calon_siswa,email,' . $siswa->id_siswa . ',id_siswa',
            'tempat_lahir'  => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'email.unique' => 'Email ini sudah digunakan akun lain.',
            'foto.max'     => 'Ukuran foto maksimal 2MB.',
            'foto.mimes'   => 'Format foto harus JPG atau PNG.',
        ]);

        $data = $request->only([
            'nama_depan', 'nama_tengah', 'nama_belakang',
            'nomor_hp', 'email', 'tempat_lahir', 'tanggal_lahir',
        ]);

        if ($request->hasFile('foto')) {
            if ($siswa->foto) Storage::disk('public')->delete($siswa->foto);
            $data['foto'] = $request->file('foto')->store('foto-siswa', 'public');
        }

        $siswa->update($data);

        return redirect()->route('siswa.pengaturan')
            ->with('success', 'Data diri berhasil diperbarui.')
            ->with('tab', 'profil');
    }

    // ── Hapus Foto Profil ──────────────────────────────────────────────────────

    public function hapusFoto()
    {
        $siswa = $this->siswa();

        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
            $siswa->update(['foto' => null]);
        }

        return redirect()->route('siswa.pengaturan')
            ->with('success', 'Foto profil berhasil dihapus.')
            ->with('tab', 'profil');
    }

    // ── Update Alamat ──────────────────────────────────────────────────────────

    public function updateAlamat(Request $request)
    {
        $siswa = $this->siswa()->load(['alamatCalonSiswa.alamat']);

        $request->validate([
            'jenis_tempat_tinggal' => 'required|string|max:50',
            'nama_jalan'           => 'required|string|max:255',
            'kelurahan'            => 'nullable|string|max:100',
            'kecamatan'            => 'nullable|string|max:100',
            'kabupaten_kota'       => 'required|string|max:100',
            'provinsi'             => 'required|string|max:100',
            'kode_pos'             => 'nullable|string|max:10',
        ]);

        $alamatSiswa = $siswa->alamatCalonSiswa->first();

        if ($alamatSiswa && $alamatSiswa->alamat) {
            $alamatSiswa->alamat->update($request->only([
                'jenis_tempat_tinggal', 'nama_jalan', 'kelurahan',
                'kecamatan', 'kabupaten_kota', 'provinsi', 'kode_pos',
            ]));
        } else {
            $alamat = Alamat::create($request->only([
                'jenis_tempat_tinggal', 'nama_jalan', 'kelurahan',
                'kecamatan', 'kabupaten_kota', 'provinsi', 'kode_pos',
            ]));

            AlamatCalonSiswa::create([
                'id_siswa'          => $siswa->id_siswa,
                'id_alamat'         => $alamat->id_alamat,
                'kode_jenis_alamat' => 'RUMAH',
                'tanggal_mulai'     => now(),
            ]);
        }

        return redirect()->route('siswa.pengaturan')
            ->with('success', 'Alamat berhasil diperbarui.')
            ->with('tab', 'alamat');
    }

    // ── Ganti Password ─────────────────────────────────────────────────────────

    public function gantiPassword(Request $request)
    {
        $siswa = $this->siswa();

        $request->validate([
            'password_lama'              => 'required',
            'password_baru'              => 'required|min:8|confirmed',
            'password_baru_confirmation' => 'required',
        ], [
            'password_baru.min'       => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->password_lama, $siswa->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->with('tab', 'password');
        }

        $siswa->update(['password' => Hash::make($request->password_baru)]);

        return redirect()->route('siswa.pengaturan')
            ->with('success', 'Password berhasil diubah.')
            ->with('tab', 'password');
    }

    // ── Update Preferensi Notifikasi ───────────────────────────────────────────

    public function updateNotifikasi(Request $request)
    {
        $siswa = $this->siswa();

        $prefs = [
            'notif_status'     => $request->boolean('notif_status'),
            'notif_pembayaran' => $request->boolean('notif_pembayaran'),
            'notif_dokumen'    => $request->boolean('notif_dokumen'),
            'notif_pengumuman' => $request->boolean('notif_pengumuman'),
        ];

        // Simpan ke session (persist ke DB: tambah kolom JSON notif_prefs di calon_siswa lalu aktifkan baris di bawah)
        session(['notif_prefs_' . $siswa->id_siswa => $prefs]);
        // $siswa->update(['notif_prefs' => $prefs]);

        return redirect()->route('siswa.pengaturan')
            ->with('success', 'Preferensi notifikasi berhasil disimpan.')
            ->with('tab', 'notifikasi');
    }

    // ── Redirect lama /profil & /alamat → /pengaturan ─────────────────────────

    public function profil()
    {
        return redirect()->route('siswa.pengaturan');
    }

    public function editAlamat()
    {
        return redirect()->route('siswa.pengaturan')->with('tab', 'alamat');
    }

    // ── Reset Password (publik, tanpa login) ───────────────────────────────────

    public function showResetPassword()
    {
        return view('siswa.auth.reset-password');
    }

    public function prosesResetPassword(Request $request)
    {
        $request->validate([
            'nisn'                       => 'required|string',
            'nomor_pendaftaran'          => 'required|string',
            'tanggal_lahir'              => 'required|date',
            'password_baru'              => 'required|min:8|confirmed',
            'password_baru_confirmation' => 'required',
        ], [
            'password_baru.min'       => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $siswa = \App\Models\CalonSiswa::where('nisn', $request->nisn)
            ->where('nomor_pendaftaran', $request->nomor_pendaftaran)
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$siswa) {
            return back()->withErrors([
                'nisn' => 'Data tidak ditemukan. Pastikan NISN, nomor pendaftaran, dan tanggal lahir sesuai.',
            ])->withInput($request->except(['password_baru', 'password_baru_confirmation']));
        }

        $siswa->update(['password' => Hash::make($request->password_baru)]);

        return redirect()->route('siswa.login')
            ->with('success', 'Password berhasil diatur ulang. Silakan login dengan password baru.');
    }
}