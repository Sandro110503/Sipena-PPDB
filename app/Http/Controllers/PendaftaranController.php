<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\AlamatCalonSiswa;
use App\Models\CalonSiswa;
use App\Models\Jurusan;
use App\Models\PendaftaranJurusan;
use App\Models\PeriodePpdb;
use App\Models\WaliOrangTua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function index()
    {
        $periode = PeriodePpdb::periodeAktif();

        if (! $periode) {
            $periodeMendatang = PeriodePpdb::where('is_aktif', true)
                ->where('tanggal_buka', '>', now())
                ->orderBy('tanggal_buka')
                ->first();

            $periodeLewat = PeriodePpdb::where('is_aktif', true)
                ->where('tanggal_tutup', '<', now())
                ->orderByDesc('tanggal_tutup')
                ->first();

            return view('ppdb.ditutup', compact('periodeMendatang', 'periodeLewat'));
        }

        $jurusan = Jurusan::withCount(['siswaDiterima as diterima'])
            ->orderBy('kode_jurusan')
            ->get();

        return view('ppdb.index', compact('jurusan', 'periode'));
    }

    public function store(Request $request)
    {
        if (! PeriodePpdb::pendaftaranTerbuka()) {
            return redirect()->route('ppdb.index')
                            ->with('error', 'Pendaftaran PPDB saat ini sedang ditutup.');
        }

        // ── Ambil periode aktif (dibutuhkan untuk id_periode) ────────────
        $periode = PeriodePpdb::where('is_aktif', 1)
            ->whereDate('tanggal_buka', '<=', now())
            ->whereDate('tanggal_tutup', '>=', now())
            ->first();

        if (! $periode) {
            return redirect()->route('ppdb.index')
                            ->with('error', 'Tidak ada periode pendaftaran yang aktif.');
        }

        // ── Uppercase semua string kecuali password & email ───────────────
        $skip = ['password', 'password_confirmation'];
        $merged = [];
        foreach ($request->all() as $key => $value) {
            if (is_string($value) && ! in_array($key, $skip)) {
                $merged[$key] = ($key === 'email')
                    ? strtolower(trim($value))
                    : strtoupper(trim($value));
            } else {
                $merged[$key] = $value;
            }
        }
        $request->merge($merged);

        $tinggalSendiri = $request->tinggal_bersama_ortu === '0';

        $rules = [
            // Data Pribadi
            'nama_depan'            => ['required', 'string', 'max:100', 'regex:/^[^0-9]+$/u'],
            'nama_tengah'           => ['nullable', 'string', 'max:100', 'regex:/^[^0-9]+$/u'],
            'nama_belakang'         => ['nullable', 'string', 'max:100', 'regex:/^[^0-9]+$/u'],
            'jenis_kelamin'         => 'required|in:L,P',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'nisn'                  => 'required|string|size:10|unique:calon_siswa,nisn',
            'asal_sekolah'          => 'required|string|max:150',
            'tahun_lulus'           => 'required|digits:4',
            'email'                 => 'required|email|unique:calon_siswa,email',
            'nomor_hp'              => 'required|string|max:15',
            'password'              => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'foto'                  => 'nullable|image|max:2048',

            // Status tinggal
            'tinggal_bersama_ortu'  => 'required|in:0,1',

            // Jurusan
            'pilihan_1'             => 'required|exists:jurusan,id_jurusan',

            // Data Wali
            'wali_nama_depan'       => ['required', 'string', 'max:100', 'regex:/^[^0-9]+$/u'],
            'wali_nama_belakang'    => ['nullable', 'string', 'max:100', 'regex:/^[^0-9]+$/u'],
            'wali_jenis_kelamin'    => 'required|in:L,P',
            'wali_hubungan'         => 'required|in:AY,IB,WL',
            'wali_nomor_hp'         => 'required|string|max:15',
            'wali_pekerjaan'        => 'nullable|string|max:100',
            'wali_nama_jalan'       => 'required|string|max:255',
            'wali_kelurahan'        => 'nullable|string|max:100',
            'wali_kecamatan'        => 'nullable|string|max:100',
            'wali_kabupaten_kota'   => 'required|string|max:100',
            'wali_provinsi'         => 'required|string|max:100',
            'wali_kode_pos'         => 'nullable|string|max:10',
        ];

        // Alamat siswa sendiri — hanya wajib jika tinggal sendiri
        if ($tinggalSendiri) {
            $rules['nama_jalan']     = 'required|string|max:255';
            $rules['kabupaten_kota'] = 'required|string|max:100';
            $rules['provinsi']       = 'required|string|max:100';
            $rules['kelurahan']      = 'nullable|string|max:100';
            $rules['kecamatan']      = 'nullable|string|max:100';
            $rules['kode_pos']       = 'nullable|string|max:10';
        }

        $request->validate($rules, [
            // ── Data Pribadi ──────────────────────────────────────────────────────
            'nama_depan.required'           => 'Nama depan wajib diisi.',
            'nama_depan.max'                => 'Nama depan maksimal 100 karakter.',
            'nama_depan.regex'              => 'Nama depan tidak boleh mengandung angka.',
            'nama_tengah.max'               => 'Nama tengah maksimal 100 karakter.',
            'nama_tengah.regex'             => 'Nama tengah tidak boleh mengandung angka.',
            'nama_belakang.max'             => 'Nama belakang maksimal 100 karakter.',
            'nama_belakang.regex'           => 'Nama belakang tidak boleh mengandung angka.',
            'jenis_kelamin.required'        => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'              => 'Jenis kelamin tidak valid.',
            'tempat_lahir.required'         => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max'              => 'Tempat lahir maksimal 100 karakter.',
            'tanggal_lahir.required'        => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date'            => 'Format tanggal lahir tidak valid.',
            'nisn.required'                 => 'NISN wajib diisi.',
            'nisn.size'                     => 'NISN harus tepat 10 digit angka.',
            'nisn.unique'                   => 'NISN sudah terdaftar dalam sistem.',
            'asal_sekolah.required'         => 'Asal sekolah wajib diisi.',
            'asal_sekolah.max'              => 'Asal sekolah maksimal 150 karakter.',
            'tahun_lulus.required'          => 'Tahun lulus wajib diisi.',
            'tahun_lulus.digits'            => 'Tahun lulus harus berupa 4 digit angka.',
            'email.required'                => 'Email wajib diisi.',
            'email.email'                   => 'Format email tidak valid.',
            'email.unique'                  => 'Email sudah digunakan, gunakan email lain.',
            'nomor_hp.required'             => 'Nomor HP wajib diisi.',
            'nomor_hp.max'                  => 'Nomor HP maksimal 15 karakter.',
            'password.required'             => 'Password wajib diisi.',
            'password.min'                  => 'Password minimal 8 karakter.',
            'password.letters'              => 'Password harus mengandung minimal satu huruf.',
            'password.numbers'              => 'Password harus mengandung minimal satu angka.',
            'password.symbols'              => 'Password harus mengandung minimal satu simbol (contoh: ! @ # $ % &).',
            'password.confirmed'            => 'Konfirmasi password tidak cocok.',
            'foto.image'                    => 'File foto harus berupa gambar (jpg, jpeg, png, dll).',
            'foto.max'                      => 'Ukuran foto maksimal 2 MB.',

            // ── Status Tinggal ────────────────────────────────────────────────────
            'tinggal_bersama_ortu.required' => 'Status tempat tinggal wajib dipilih.',
            'tinggal_bersama_ortu.in'       => 'Status tempat tinggal tidak valid.',

            // ── Jurusan ───────────────────────────────────────────────────────────
            'pilihan_1.required'            => 'Pilihan jurusan wajib dipilih.',
            'pilihan_1.exists'              => 'Jurusan yang dipilih tidak tersedia.',

            // ── Alamat Siswa (jika tinggal sendiri) ───────────────────────────────
            'nama_jalan.required'           => 'Alamat (nama jalan) tempat tinggal siswa wajib diisi.',
            'nama_jalan.max'                => 'Alamat siswa maksimal 255 karakter.',
            'kelurahan.max'                 => 'Kelurahan siswa maksimal 100 karakter.',
            'kecamatan.max'                 => 'Kecamatan siswa maksimal 100 karakter.',
            'kabupaten_kota.required'       => 'Kabupaten/kota tempat tinggal siswa wajib diisi.',
            'kabupaten_kota.max'            => 'Kabupaten/kota siswa maksimal 100 karakter.',
            'provinsi.required'             => 'Provinsi tempat tinggal siswa wajib diisi.',
            'provinsi.max'                  => 'Provinsi siswa maksimal 100 karakter.',
            'kode_pos.max'                  => 'Kode pos siswa maksimal 10 karakter.',

            // ── Data Orang Tua / Wali ─────────────────────────────────────────────
            'wali_nama_depan.required'      => 'Nama depan orang tua/wali wajib diisi.',
            'wali_nama_depan.max'           => 'Nama depan orang tua/wali maksimal 100 karakter.',
            'wali_nama_depan.regex'         => 'Nama depan orang tua/wali tidak boleh mengandung angka.',
            'wali_nama_belakang.max'        => 'Nama belakang orang tua/wali maksimal 100 karakter.',
            'wali_nama_belakang.regex'      => 'Nama belakang orang tua/wali tidak boleh mengandung angka.',
            'wali_jenis_kelamin.required'   => 'Jenis kelamin orang tua/wali wajib dipilih.',
            'wali_jenis_kelamin.in'         => 'Jenis kelamin orang tua/wali tidak valid.',
            'wali_hubungan.required'        => 'Hubungan dengan siswa wajib dipilih.',
            'wali_hubungan.in'              => 'Hubungan tidak valid. Pilih Ayah, Ibu, atau Wali.',
            'wali_nomor_hp.required'        => 'Nomor HP orang tua/wali wajib diisi.',
            'wali_nomor_hp.max'             => 'Nomor HP orang tua/wali maksimal 15 karakter.',
            'wali_pekerjaan.max'            => 'Pekerjaan orang tua/wali maksimal 100 karakter.',

            // ── Alamat Orang Tua / Wali ───────────────────────────────────────────
            'wali_nama_jalan.required'      => 'Alamat (nama jalan) orang tua/wali wajib diisi.',
            'wali_nama_jalan.max'           => 'Alamat orang tua/wali maksimal 255 karakter.',
            'wali_kelurahan.max'            => 'Kelurahan orang tua/wali maksimal 100 karakter.',
            'wali_kecamatan.max'            => 'Kecamatan orang tua/wali maksimal 100 karakter.',
            'wali_kabupaten_kota.required'  => 'Kabupaten/kota orang tua/wali wajib diisi.',
            'wali_kabupaten_kota.max'       => 'Kabupaten/kota orang tua/wali maksimal 100 karakter.',
            'wali_provinsi.required'        => 'Provinsi orang tua/wali wajib diisi.',
            'wali_provinsi.max'             => 'Provinsi orang tua/wali maksimal 100 karakter.',
            'wali_kode_pos.max'             => 'Kode pos orang tua/wali maksimal 10 karakter.',
        ]);

        DB::transaction(function () use ($request, $tinggalSendiri, $periode, &$nomorPendaftaran) {

            // ── Upload foto ───────────────────────────────────────────────
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto-siswa', 'public');
            }

            // ── Buat calon siswa ──────────────────────────────────────────
            $jurusanDipilih = \App\Models\Jurusan::findOrFail($request->pilihan_1);
            $siswa = CalonSiswa::create([
                'id_periode'        => $periode->id_periode,   // ← FIX: tambahkan id_periode
                'nomor_pendaftaran' => CalonSiswa::generateNomorPendaftaran($jurusanDipilih->kode_jurusan),
                'nama_depan'        => $request->nama_depan,
                'nama_tengah'       => $request->nama_tengah,
                'nama_belakang'     => $request->nama_belakang,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'tempat_lahir'      => $request->tempat_lahir,
                'tanggal_lahir'     => $request->tanggal_lahir,
                'nisn'              => $request->nisn,
                'asal_sekolah'      => $request->asal_sekolah,
                'tahun_lulus'       => $request->tahun_lulus,
                'email'             => $request->email,
                'nomor_hp'          => $request->nomor_hp,
                'password'          => Hash::make($request->password),
                'foto'              => $fotoPath,
                'tanggal_daftar'    => now(),
                'status_penerimaan' => 'Menunggu',
            ]);
            $nomorPendaftaran = $siswa->nomor_pendaftaran;

            // ── Alamat orang tua / wali ───────────────────────────────────
            $alamatOrtu = Alamat::create([
                'jenis_tempat_tinggal' => 'Rumah Orang Tua/Wali',
                'nama_jalan'           => $request->wali_nama_jalan,
                'kelurahan'            => $request->wali_kelurahan,
                'kecamatan'            => $request->wali_kecamatan,
                'kode_pos'             => $request->wali_kode_pos,
                'kabupaten_kota'       => $request->wali_kabupaten_kota,
                'provinsi'             => $request->wali_provinsi,
            ]);

            // ── Data orang tua / wali ─────────────────────────────────────
            $wali = WaliOrangTua::create([
                'id_alamat'     => $alamatOrtu->id_alamat,
                'jenis_kelamin' => $request->wali_jenis_kelamin,
                'nama_depan'    => $request->wali_nama_depan,
                'nama_belakang' => $request->wali_nama_belakang,
                'hubungan'      => $request->wali_hubungan,
                'nomor_hp'      => $request->wali_nomor_hp,
                'pekerjaan'     => $request->wali_pekerjaan,
            ]);

            DB::table('relasi_siswa')->insert([
                'id_siswa'         => $siswa->id_siswa,
                'id_wali'          => $wali->id_wali,
                'kode_tipe_relasi' => $request->wali_hubungan,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // ── Alamat calon siswa ────────────────────────────────────────
            if ($tinggalSendiri) {
                $alamatSiswa = Alamat::create([
                    'jenis_tempat_tinggal' => 'Sewa',
                    'nama_jalan'           => $request->nama_jalan,
                    'kelurahan'            => $request->kelurahan,
                    'kecamatan'            => $request->kecamatan,
                    'kode_pos'             => $request->kode_pos,
                    'kabupaten_kota'       => $request->kabupaten_kota,
                    'provinsi'             => $request->provinsi,
                ]);
            } else {
                // Tinggal bersama ortu → reuse alamat ortu
                $alamatSiswa = $alamatOrtu;
            }

            AlamatCalonSiswa::create([
                'kode_jenis_alamat' => $tinggalSendiri ? 'SW' : 'RP',
                'id_siswa'          => $siswa->id_siswa,
                'id_alamat'         => $alamatSiswa->id_alamat,
                'tanggal_mulai'     => now(),
            ]);

            // ── Pendaftaran Jurusan ───────────────────────────────────────
            PendaftaranJurusan::create([
                'id_siswa'            => $siswa->id_siswa,
                'id_jurusan'          => $request->pilihan_1,
                'tanggal_pendaftaran' => now(),
                'urutan_pilihan'      => 1,
                'status'              => 'Aktif',
            ]);
        });

        return redirect()->route('ppdb.sukses')
            ->with('success', 'Pendaftaran berhasil!')
            ->with('nomor_pendaftaran', $nomorPendaftaran);
    }

    public function sukses()
    {
        return view('ppdb.sukses');
    }

    public function cekStatus(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('ppdb.cek-status', [
                'siswa'        => null,
                'sudahCari'    => false,
                'inputNomor'   => '',
                'inputTanggal' => '',
            ]);
        }

        $request->validate([
            'nomor_pendaftaran' => 'required|string|min:3',
            'tanggal_lahir'     => 'required|date',
        ], [
            'nomor_pendaftaran.required' => 'Nomor pendaftaran atau NISN wajib diisi.',
            'tanggal_lahir.required'     => 'Tanggal lahir wajib diisi untuk verifikasi.',
        ]);

        $siswa = CalonSiswa::with(['pendaftaranJurusan.jurusan', 'pembayaran'])
            ->where(function ($q) use ($request) {
                $q->where('nomor_pendaftaran', $request->nomor_pendaftaran)
                  ->orWhere('nisn', $request->nomor_pendaftaran);
            })
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        return view('ppdb.cek-status', [
            'siswa'        => $siswa,
            'sudahCari'    => true,
            'inputNomor'   => $request->nomor_pendaftaran,
            'inputTanggal' => $request->tanggal_lahir,
        ]);
    }

    public function statusApi(Request $request)
    {
        $request->validate([
            'nomor_pendaftaran' => 'required|string',
            'tanggal_lahir'     => 'required|date',
        ]);

        $siswa = CalonSiswa::with(['pendaftaranJurusan.jurusan'])
            ->where(function ($q) use ($request) {
                $q->where('nomor_pendaftaran', $request->nomor_pendaftaran)
                  ->orWhere('nisn', $request->nomor_pendaftaran);
            })
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$siswa) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found'             => true,
            'nama_lengkap'      => $siswa->nama_lengkap,
            'nomor_pendaftaran' => $siswa->nomor_pendaftaran,
            'status_penerimaan' => $siswa->status_penerimaan,
            'tanggal_diterima'  => $siswa->tanggal_diterima?->format('d M Y'),
            'updated_at'        => $siswa->updated_at->format('d M Y H:i:s'),
            'jurusan'           => $siswa->pendaftaranJurusan->map(fn($pj) => [
                'urutan' => $pj->urutan_pilihan,
                'nama'   => $pj->jurusan->nama_jurusan,
                'kode'   => $pj->jurusan->kode_jurusan,
                'status' => $pj->status,
            ])->values(),
        ]);
    }
}