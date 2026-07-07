@extends('layouts.admin')
@section('title','Detail Log Aktivitas')
@section('page-title','Detail Log Aktivitas')

@section('content')
@php $cfg = $log->konfig; @endphp

<div style="margin-bottom:1.1rem">
    <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali ke Log Aktivitas
    </a>
</div>

<div class="card" style="max-width:640px">
    <div class="card-header">
        <span><i class="fas fa-history" style="color:#1a4a8a;margin-right:.4rem"></i> Detail Aktivitas</span>
        <span class="badge" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }}">
            <i class="fas fa-{{ $cfg['icon'] }}" style="margin-right:.3rem;font-size:.62rem"></i>{{ $cfg['label'] }}
        </span>
    </div>
    <div class="card-body">
        <div class="grid-2" style="gap:.85rem 1.5rem">
            <div>
                <div class="form-label" style="margin-bottom:.15rem">Waktu</div>
                <div style="font-size:.9rem">{{ $log->created_at->format('d M Y, H:i:s') }}</div>
            </div>
            <div>
                <div class="form-label" style="margin-bottom:.15rem">Modul</div>
                <div style="font-size:.9rem">{{ $log->modul }}</div>
            </div>
            <div>
                <div class="form-label" style="margin-bottom:.15rem">Pegawai</div>
                <div style="font-size:.9rem">{{ $log->nama_admin ?? 'Sistem' }}</div>
            </div>
            <div>
                <div class="form-label" style="margin-bottom:.15rem">NIP / Role</div>
                <div style="font-size:.9rem">{{ $log->admin?->nip ?? '-' }} @if($log->admin) &middot; {{ $log->admin->role_label }} @endif</div>
            </div>
            <div>
                <div class="form-label" style="margin-bottom:.15rem">Alamat IP</div>
                <div style="font-size:.9rem"><code style="font-size:.78rem;background:#f1f5f9;padding:.15rem .45rem;border-radius:4px">{{ $log->ip_address ?? '-' }}</code></div>
            </div>
            <div>
                <div class="form-label" style="margin-bottom:.15rem">Perangkat / Browser</div>
                <div style="font-size:.78rem;color:var(--muted);word-break:break-all">{{ $log->user_agent ?? '-' }}</div>
            </div>
        </div>
        <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0">
        <div>
            <div class="form-label" style="margin-bottom:.3rem">Deskripsi</div>
            <div style="font-size:.92rem;line-height:1.6">{{ $log->deskripsi }}</div>
        </div>
    </div>
</div>
@endsection
