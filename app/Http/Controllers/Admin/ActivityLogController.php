<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses log aktivitas.');
        }

        $logs = ActivityLog::query()
            ->with('admin')
            ->when($request->search, fn($q) =>
                $q->where('deskripsi', 'like', "%{$request->search}%")
                  ->orWhere('nama_admin', 'like', "%{$request->search}%")
            )
            ->when($request->modul, fn($q) => $q->where('modul', $request->modul))
            ->when($request->aktivitas, fn($q) => $q->where('aktivitas', $request->aktivitas))
            ->when($request->admin_id, fn($q) => $q->where('admin_id', $request->admin_id))
            ->when($request->dari, fn($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->sampai, fn($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $daftarModul = ActivityLog::query()->distinct()->orderBy('modul')->pluck('modul');
        $daftarAdmin = Admin::orderBy('nama')->get(['id_admin', 'nama']);
        $konfigAktivitas = ActivityLog::konfigAktivitas();

        $hariIni = ActivityLog::whereDate('created_at', today())->count();

        return view('admin.activity-log.index', compact(
            'logs', 'daftarModul', 'daftarAdmin', 'konfigAktivitas', 'hariIni'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        $activityLog->load('admin');
        return view('admin.activity-log.show', ['log' => $activityLog]);
    }

    /**
     * Hapus satu baris log. Hanya superadmin.
     */
    public function destroy(ActivityLog $activityLog)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        $activityLog->delete();

        return back()->with('success', 'Log aktivitas berhasil dihapus.');
    }

    /**
     * Bersihkan log lama (lebih dari N hari). Hanya superadmin.
     */
    public function bersihkan(Request $request)
    {
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'lebih_dari_hari' => 'required|integer|min:7|max:730',
        ]);

        $batas = now()->subDays((int) $request->lebih_dari_hari);
        $jumlah = ActivityLog::where('created_at', '<', $batas)->count();
        ActivityLog::where('created_at', '<', $batas)->delete();

        ActivityLog::catat(
            'Log Aktivitas',
            'hapus',
            "Membersihkan {$jumlah} log aktivitas yang lebih lama dari {$request->lebih_dari_hari} hari."
        );

        return back()->with('success', "Berhasil membersihkan {$jumlah} log aktivitas lama.");
    }
}
