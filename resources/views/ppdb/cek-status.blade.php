@extends('layouts.public')
@section('title', 'Cek Status Pendaftaran — PPDB SMK')

@section('content')
<div style="max-width:680px;margin:0 auto;padding:2rem 1.25rem">

    <div style="text-align:center;margin-bottom:2rem">
        <div style="width:60px;height:60px;background:#dbeafe;border-radius:14px;display:grid;place-items:center;margin:0 auto .85rem;font-size:1.6rem;color:#1a4a8a">
            <i class="fas fa-search"></i>
        </div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#0f2744">Cek Status Pendaftaran</h1>
        <p style="color:#64748b;margin-top:.35rem;font-size:.9rem">
            Masukkan nomor pendaftaran atau NISN beserta tanggal lahir untuk verifikasi
        </p>
    </div>

    {{-- FORM CEK STATUS --}}
    <div class="card" style="margin-bottom:1.5rem;border-radius:14px">
        <div style="padding:1.5rem">
            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.85rem;color:#991b1b;display:flex;align-items:flex-start;gap:.6rem">
                <i class="fas fa-exclamation-circle" style="margin-top:1px;flex-shrink:0"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form method="POST" action="{{ route('ppdb.cek-status') }}" id="formCek">
                @csrf
                <div style="margin-bottom:1rem">
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#1e293b;margin-bottom:.4rem">
                        Nomor Pendaftaran / NISN <span style="color:#dc2626">*</span>
                    </label>
                    <div style="position:relative">
                        <i class="fas fa-id-card" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.85rem"></i>
                        <input type="text" name="nomor_pendaftaran" id="inputNomor"
                            value="{{ $inputNomor ?? old('nomor_pendaftaran') }}"
                            class="form-control" style="padding-left:2.4rem"
                            placeholder="Nomor pendaftaran atau NISN"
                            autocomplete="off" required>
                    </div>
                </div>
                <div style="margin-bottom:1.25rem">
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#1e293b;margin-bottom:.4rem">
                        Tanggal Lahir <span style="color:#dc2626">*</span>
                        <span style="font-weight:400;color:#64748b">(untuk verifikasi)</span>
                    </label>
                    <div style="position:relative">
                        <i class="fas fa-calendar" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.85rem;z-index:1"></i>
                        <input type="date" name="tanggal_lahir" id="inputTanggal"
                            value="{{ $inputTanggal ?? old('tanggal_lahir') }}"
                            class="form-control" style="padding-left:2.4rem"
                            required>
                    </div>
                    <div style="font-size:.72rem;color:#64748b;margin-top:.3rem">
                        <i class="fas fa-shield-alt"></i> Tanggal lahir digunakan untuk memverifikasi identitas Anda
                    </div>
                </div>
                <button type="submit" class="btn-submit" style="width:100%;justify-content:center">
                    <i class="fas fa-search"></i> Cek Status Pendaftaran
                </button>
            </form>
        </div>
    </div>

    {{-- HASIL --}}
    @if($sudahCari)
        @if($siswa)
        @php
            $cfg = match($siswa->status_penerimaan) {
                'Diterima'  => ['bg'=>'#dcfce7','border'=>'#86efac','color'=>'#166534','icon'=>'fas fa-check-circle','label'=>'DITERIMA'],
                'Ditolak'   => ['bg'=>'#fee2e2','border'=>'#fca5a5','color'=>'#991b1b','icon'=>'fas fa-times-circle','label'=>'TIDAK DITERIMA'],
                'Cadangan'  => ['bg'=>'#dbeafe','border'=>'#93c5fd','color'=>'#1e40af','icon'=>'fas fa-clock','label'=>'CADANGAN'],
                default     => ['bg'=>'#fef3c7','border'=>'#fcd34d','color'=>'#92400e','icon'=>'fas fa-hourglass-half','label'=>'DALAM PROSES'],
            };
        @endphp

        {{-- Banner Status --}}
        <div id="statusBanner" style="background:{{ $cfg['bg'] }};border:2px solid {{ $cfg['border'] }};border-radius:14px;padding:1.5rem;text-align:center;margin-bottom:1.25rem;position:relative">
            <div id="realtimeDot" style="position:absolute;top:12px;right:12px;display:flex;align-items:center;gap:.4rem;font-size:.7rem;color:{{ $cfg['color'] }};opacity:.8">
                <span style="width:8px;height:8px;border-radius:50%;background:{{ $cfg['color'] }};display:inline-block;animation:pulse 2s infinite"></span>
                <span id="realtimeLabel">Live</span>
            </div>
            <i id="statusIcon" class="{{ $cfg['icon'] }}" style="font-size:2.75rem;color:{{ $cfg['color'] }};margin-bottom:.6rem;display:block"></i>
            <div id="statusText" style="font-size:1.5rem;font-weight:800;color:{{ $cfg['color'] }};letter-spacing:.5px">{{ $cfg['label'] }}</div>
            <div id="statusSub" style="font-size:.8rem;color:{{ $cfg['color'] }};opacity:.8;margin-top:.3rem">
                Diperbarui: <span id="updatedAt">{{ $siswa->updated_at->format('d M Y, H:i:s') }}</span>
            </div>
        </div>

        {{-- Info Siswa --}}
        <div class="card" style="margin-bottom:1.25rem;border-radius:14px">
            <div style="padding:.85rem 1.25rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:.875rem;display:flex;align-items:center;gap:.5rem">
                <i class="fas fa-user" style="color:#1a4a8a"></i> Informasi Pendaftar
            </div>
            <div style="padding:1.1rem 1.25rem">
                @php
                $rows = [
                    ['label'=>'Nomor Pendaftaran', 'val'=>$siswa->nomor_pendaftaran, 'mono'=>true],
                    ['label'=>'Nama Lengkap',      'val'=>$siswa->nama_lengkap],
                    ['label'=>'NISN',               'val'=>$siswa->nisn],
                    ['label'=>'Asal Sekolah',       'val'=>$siswa->asal_sekolah],
                    ['label'=>'Tanggal Daftar',     'val'=>$siswa->tanggal_daftar?->format('d M Y')],
                ];
                if ($siswa->tanggal_diterima) {
                    $rows[] = ['label'=>'Tanggal Diterima', 'val'=>$siswa->tanggal_diterima->format('d M Y'), 'highlight'=>true];
                }
                @endphp
                @foreach($rows as $r)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid #f1f5f9;font-size:.875rem;gap:1rem">
                    <span style="color:#64748b;flex-shrink:0">{{ $r['label'] }}</span>
                    <span style="font-weight:600;text-align:right;{{ isset($r['mono']) ? 'font-family:monospace;background:#f1f5f9;padding:.15rem .5rem;border-radius:4px;font-size:.8rem' : '' }}{{ isset($r['highlight']) ? ';color:#166534' : '' }}">
                        {{ $r['val'] ?? '-' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pilihan Jurusan --}}
        <div class="card" style="margin-bottom:1.25rem;border-radius:14px">
            <div style="padding:.85rem 1.25rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:.875rem;display:flex;align-items:center;gap:.5rem">
                <i class="fas fa-school" style="color:#1a4a8a"></i> Pilihan Jurusan
            </div>
            <div id="jurusanContainer" style="padding:.85rem 1.25rem">
                @foreach($siswa->pendaftaranJurusan->sortBy('urutan_pilihan') as $pj)
                @php
                    $jCfg = match($pj->jurusan->kode_jurusan) {
                        'AKL'  => ['bg'=>'#ede9fe','color'=>'#5b21b6'],
                        'TJKT' => ['bg'=>'#dbeafe','color'=>'#1e40af'],
                        'MPLB' => ['bg'=>'#fce7f3','color'=>'#9d174d'],
                        default=> ['bg'=>'#f1f5f9','color'=>'#475569'],
                    };
                    $stStyle = match($pj->status) {
                        'Diterima' => 'background:#dcfce7;color:#166534',
                        'Ditolak'  => 'background:#fee2e2;color:#991b1b',
                        default    => 'background:#f1f5f9;color:#64748b',
                    };
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:.6rem;gap:.75rem">
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <div style="width:36px;height:36px;border-radius:8px;background:{{ $jCfg['bg'] }};display:grid;place-items:center;font-size:.65rem;font-weight:800;color:{{ $jCfg['color'] }};flex-shrink:0">
                            {{ $pj->urutan_pilihan }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.875rem;color:#1e293b">{{ $pj->jurusan->nama_jurusan }}</div>
                            <div style="font-size:.75rem;color:#64748b">Pilihan ke-{{ $pj->urutan_pilihan }}</div>
                        </div>
                    </div>
                    <span style="font-size:.7rem;font-weight:700;padding:.25rem .75rem;border-radius:999px;white-space:nowrap;{{ $stStyle }}">
                        {{ $pj->status }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Log Riwayat Aktivitas --}}
        <div class="card" style="margin-bottom:1.25rem;border-radius:14px">
            <div style="padding:.85rem 1.25rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:.875rem;display:flex;align-items:center;gap:.5rem">
                <i class="fas fa-history" style="color:#1a4a8a"></i> Riwayat Aktivitas
            </div>
            <div style="padding:1rem 1.25rem">
                @php
                $logs = [];
                if ($siswa->tanggal_daftar) {
                    $logs[] = ['waktu'=>$siswa->tanggal_daftar->format('d M Y'),'aksi'=>'Pendaftaran berhasil dikirim','icon'=>'fas fa-paper-plane','color'=>'#1a4a8a','bg'=>'#dbeafe'];
                }
                
                if ($siswa->pembayaran->count()) {
                    $logs[] = ['waktu'=>$siswa->pembayaran->first()->tanggal_bayar->format('d M Y'),'aksi'=>'Pembayaran diterima sistem','icon'=>'fas fa-money-bill-wave','color'=>'#92400e','bg'=>'#fef3c7'];
                }
                if ($siswa->status_penerimaan === 'Diterima' && $siswa->tanggal_diterima) {
                    $logs[] = ['waktu'=>$siswa->tanggal_diterima->format('d M Y'),'aksi'=>'Selamat! Anda DITERIMA di SMK ini','icon'=>'fas fa-trophy','color'=>'#166534','bg'=>'#dcfce7'];
                }
                if ($siswa->status_penerimaan === 'Ditolak') {
                    $logs[] = ['waktu'=>$siswa->updated_at->format('d M Y'),'aksi'=>'Status diperbarui: Tidak Diterima','icon'=>'fas fa-times-circle','color'=>'#991b1b','bg'=>'#fee2e2'];
                }
                if ($siswa->status_penerimaan === 'Cadangan') {
                    $logs[] = ['waktu'=>$siswa->updated_at->format('d M Y'),'aksi'=>'Status: Cadangan — menunggu keputusan akhir','icon'=>'fas fa-clock','color'=>'#1e40af','bg'=>'#dbeafe'];
                }
                usort($logs, fn($a,$b) => $a['waktu'] <=> $b['waktu']);
                @endphp

                @if(empty($logs))
                <div style="text-align:center;color:#94a3b8;font-size:.85rem;padding:.5rem 0">Belum ada riwayat aktivitas.</div>
                @else
                <div style="position:relative;padding-left:1.5rem">
                    <div style="position:absolute;left:.4rem;top:4px;bottom:4px;width:2px;background:linear-gradient(to bottom,#dbeafe,#e2e8f0);border-radius:2px"></div>
                    @foreach(array_reverse($logs) as $idx => $log)
                    <div style="position:relative;margin-bottom:{{ $idx < count($logs)-1 ? '1rem' : '0' }};padding-left:.85rem">
                        <div style="position:absolute;left:-1.1rem;top:.1rem;width:20px;height:20px;border-radius:50%;background:{{ $log['bg'] }};border:2px solid #fff;box-shadow:0 0 0 2px {{ $log['bg'] }};display:grid;place-items:center">
                            <i class="{{ $log['icon'] }}" style="font-size:.5rem;color:{{ $log['color'] }}"></i>
                        </div>
                        <div style="font-size:.825rem;font-weight:700;color:#1e293b">{{ $log['aksi'] }}</div>
                        <div style="font-size:.72rem;color:#94a3b8;margin-top:.1rem"><i class="fas fa-clock"></i> {{ $log['waktu'] }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Tombol Refresh --}}
        <div style="text-align:center;padding-bottom:.5rem">
            <button onclick="fetchStatusNow()" style="background:#0f2744;color:#fff;border:none;padding:.65rem 1.5rem;border-radius:10px;font-family:inherit;font-size:.875rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
                <i class="fas fa-sync-alt" id="iconRefresh"></i> Perbarui Status Sekarang
            </button>
            <div style="font-size:.72rem;color:#94a3b8">
                <span style="display:inline-flex;align-items:center;gap:.3rem">
                    <i class="fas fa-circle" style="color:#22c55e;font-size:.45rem"></i>
                    Diperbarui otomatis setiap <strong style="color:#0f2744">15 detik</strong>
                </span>
            </div>
        </div>

        @else
        {{-- DATA TIDAK DITEMUKAN --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:3rem 1.5rem;text-align:center">
            <div style="width:72px;height:72px;background:#fee2e2;border-radius:50%;display:grid;place-items:center;margin:0 auto 1rem;font-size:2rem;color:#dc2626">
                <i class="fas fa-user-slash"></i>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;color:#0f2744;margin-bottom:.5rem">Data Tidak Ditemukan</h3>
            <p style="color:#64748b;font-size:.875rem;line-height:1.6;max-width:380px;margin:0 auto">
                Nomor pendaftaran/NISN atau tanggal lahir yang Anda masukkan tidak sesuai data dalam sistem.
            </p>
            <div style="margin-top:1.25rem;background:#fef3c7;border-radius:10px;padding:.85rem 1rem;font-size:.8rem;color:#92400e;text-align:left;max-width:380px;margin:1rem auto 0">
                <strong><i class="fas fa-lightbulb"></i> Tips:</strong>
                <ul style="margin:.4rem 0 0 1.1rem;line-height:1.9">
                    <li>Nomor pendaftaran diawali <code style="background:#fff3cd;padding:0 4px;border-radius:3px">PPDB-TAHUN-XXXX</code></li>
                    <li>Atau masukkan NISN 10 digit tanpa spasi</li>
                    <li>Tanggal lahir harus sama persis saat mendaftar</li>
                </ul>
            </div>
        </div>
        @endif
    @endif

</div>

<style>
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }
@keyframes spin  { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
.spinning { animation:spin .7s linear infinite; display:inline-block; }
</style>

@if($sudahCari && $siswa)
@push('scripts')
<script>
const NOMOR   = @json($inputNomor);
const TANGGAL = @json($inputTanggal);
const API_URL = '{{ route("api.status") }}';
let timer = null;

const STATUS_CFG = {
    'Diterima': { bg:'#dcfce7', border:'#86efac', color:'#166534', icon:'fas fa-check-circle',   label:'DITERIMA' },
    'Ditolak':  { bg:'#fee2e2', border:'#fca5a5', color:'#991b1b', icon:'fas fa-times-circle',   label:'TIDAK DITERIMA' },
    'Cadangan': { bg:'#dbeafe', border:'#93c5fd', color:'#1e40af', icon:'fas fa-clock',          label:'CADANGAN' },
    'Menunggu': { bg:'#fef3c7', border:'#fcd34d', color:'#92400e', icon:'fas fa-hourglass-half', label:'DALAM PROSES' },
};
const JURUSAN_CFG = {
    'AKL':  { bg:'#ede9fe', color:'#5b21b6' },
    'TJKT': { bg:'#dbeafe', color:'#1e40af' },
    'MPLB': { bg:'#fce7f3', color:'#9d174d' },
};

function applyBanner(data) {
    const cfg = STATUS_CFG[data.status_penerimaan] || STATUS_CFG['Menunggu'];
    document.getElementById('statusBanner').style.cssText += `;background:${cfg.bg};border-color:${cfg.border}`;
    const icon = document.getElementById('statusIcon');
    icon.className = cfg.icon;
    icon.style.color = cfg.color;
    document.getElementById('statusText').textContent = cfg.label;
    document.getElementById('statusText').style.color  = cfg.color;
    document.getElementById('statusSub').style.color   = cfg.color;
    document.getElementById('realtimeDot').style.color = cfg.color;
    document.querySelector('#realtimeDot span').style.background = cfg.color;
    document.getElementById('updatedAt').textContent = data.updated_at;
}

function applyJurusan(list) {
    const c = document.getElementById('jurusanContainer');
    if (!c || !list) return;
    c.innerHTML = list.map(pj => {
        const jc = JURUSAN_CFG[pj.kode] || { bg:'#f1f5f9', color:'#475569' };
        const st = pj.status === 'Diterima' ? 'background:#dcfce7;color:#166534'
                 : pj.status === 'Ditolak'  ? 'background:#fee2e2;color:#991b1b'
                 :                            'background:#f1f5f9;color:#64748b';
        return `<div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:.6rem;gap:.75rem">
            <div style="display:flex;align-items:center;gap:.75rem">
                <div style="width:36px;height:36px;border-radius:8px;background:${jc.bg};display:grid;place-items:center;font-size:.65rem;font-weight:800;color:${jc.color};flex-shrink:0">${pj.urutan}</div>
                <div>
                    <div style="font-weight:700;font-size:.875rem;color:#1e293b">${pj.nama}</div>
                    <div style="font-size:.75rem;color:#64748b">Pilihan ke-${pj.urutan}</div>
                </div>
            </div>
            <span style="font-size:.7rem;font-weight:700;padding:.25rem .75rem;border-radius:999px;white-space:nowrap;${st}">${pj.status}</span>
        </div>`;
    }).join('');
}

async function fetchStatusNow() {
    const lbl  = document.getElementById('realtimeLabel');
    const icon = document.getElementById('iconRefresh');
    if (lbl)  lbl.textContent = 'Memperbarui...';
    if (icon) icon.classList.add('spinning');

    try {
        const res  = await fetch(`${API_URL}?nomor_pendaftaran=${encodeURIComponent(NOMOR)}&tanggal_lahir=${encodeURIComponent(TANGGAL)}`);
        const data = await res.json();
        if (data.found) {
            applyBanner(data);
            applyJurusan(data.jurusan);
        }
    } catch(e) {
        console.warn('Polling gagal:', e);
    } finally {
        if (lbl)  lbl.textContent = 'Live';
        if (icon) icon.classList.remove('spinning');
    }
}

function startPolling() {
    fetchStatusNow();
    clearInterval(timer);
    timer = setInterval(fetchStatusNow, 15000);
}

document.addEventListener('DOMContentLoaded', startPolling);
document.addEventListener('visibilitychange', () => {
    document.hidden ? clearInterval(timer) : startPolling();
});
</script>
@endpush
@endif
@endsection
