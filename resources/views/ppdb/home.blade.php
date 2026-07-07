@extends('layouts.public')
@section('title','PPDB SMK — Beranda')

@push('styles')
<style>
.hero{

position:relative;

overflow:hidden;

color:#fff;

padding:4rem 1.25rem;

text-align:center;

}

/* FOTO GEDUNG SEKOLAH */

.hero::before{

content:'';

position:absolute;

inset:0;

background:url('{{ asset("images/sekolah.jpeg") }}') center center/cover no-repeat;

filter:blur(2px);

transform:scale(1.05);

}

/* OVERLAY BIRU */

.hero::after{

content:'';

position:absolute;

inset:0;

background:linear-gradient(

135deg,

rgba(15,39,68,.88),

rgba(26,74,138,.82)

);

}

/* KONTEN DI ATAS OVERLAY */

.hero-inner{

position:relative;

z-index:2;

max-width:640px;

margin:0 auto;

}
.hero-badge{display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:.35rem 1rem;font-size:.75rem;margin-bottom:1.25rem;}
.hero-badge span{background:#e8a020;border-radius:999px;width:7px;height:7px;display:inline-block;}
.hero h1{font-size:clamp(1.6rem,5vw,2.5rem);font-weight:800;line-height:1.2;margin-bottom:.85rem;}
.hero p{color:rgba(255,255,255,.75);font-size:.95rem;line-height:1.7;margin-bottom:1.75rem;}
.hero-btns{display:flex;gap:.65rem;justify-content:center;flex-wrap:wrap;}
.btn-hero-primary{background:#e8a020;color:#0f2744;padding:.8rem 1.5rem;border-radius:10px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;font-size:.9rem;touch-action:manipulation;}
.btn-hero-secondary{background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.3);color:#fff;padding:.8rem 1.5rem;border-radius:10px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;font-size:.9rem;}

/* Jurusan */
.jurusan-section{padding:3rem 1.25rem;max-width:960px;margin:0 auto;}
.section-head{text-align:center;margin-bottom:2rem;}
.section-head h2{font-size:1.5rem;font-weight:800;color:#0f2744;}
.section-head p{color:#64748b;margin-top:.4rem;font-size:.9rem;}
.jurusan-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.1rem;}
.jurusan-card{background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:1.5rem 1.25rem;transition:.2s;}
.jurusan-card:hover{box-shadow:0 8px 28px rgba(0,0,0,.09);transform:translateY(-2px);}
.jurusan-icon{width:52px;height:52px;border-radius:12px;display:grid;place-items:center;font-size:1.3rem;margin-bottom:.9rem;}
.jurusan-kode{display:inline-block;border-radius:6px;padding:.18rem .6rem;font-size:.7rem;font-weight:700;margin-bottom:.55rem;}
.jurusan-card h3{font-size:.9rem;font-weight:700;color:#0f2744;margin-bottom:.5rem;line-height:1.4;}
.jurusan-card p{font-size:.8rem;color:#64748b;line-height:1.6;}

/* Alur */
.alur-section{background:#0f2744;color:#fff;padding:3rem 1.25rem;}
.alur-inner{max-width:900px;margin:0 auto;text-align:center;}
.alur-inner h2{font-size:1.4rem;font-weight:800;margin-bottom:.5rem;}
.alur-inner > p{color:rgba(255,255,255,.6);margin-bottom:2rem;font-size:.88rem;}
.alur-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;}
.alur-item{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:1.1rem .85rem;text-align:center;}
.alur-num{background:#e8a020;color:#0f2744;width:28px;height:28px;border-radius:50%;display:grid;place-items:center;font-size:.68rem;font-weight:800;margin:0 auto .65rem;}
.alur-item i{font-size:1.4rem;color:rgba(255,255,255,.7);margin-bottom:.5rem;display:block;}
.alur-item strong{display:block;font-size:.82rem;font-weight:700;margin-bottom:.3rem;}
.alur-item span{font-size:.72rem;color:rgba(255,255,255,.5);line-height:1.5;}

/* CTA */
.cta-section{padding:3rem 1.25rem;text-align:center;}
.cta-section h2{font-size:1.35rem;font-weight:800;color:#0f2744;margin-bottom:.65rem;}
.cta-section p{color:#64748b;margin-bottom:1.5rem;font-size:.88rem;}

@media(max-width:480px){
    .hero{padding:2.5rem 1rem;}
    .hero-btns{flex-direction:column;align-items:center;}
    .btn-hero-primary,.btn-hero-secondary{width:100%;justify-content:center;}
    .alur-grid{grid-template-columns:1fr 1fr;}
}
</style>
@endpush

@section('content')
{{-- HERO --}}
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <span></span> Penerimaan Peserta Didik Baru {{ date('Y') }}/{{ date('Y')+1 }}
        </div>
        <h1>Selamat Datang di PPDB<br>SMK YADIKA 8 JATIMULYA</h1>
        <p>Daftarkan diri Anda secara online dengan mudah dan cepat.<br class="hide-mobile">Pilih jurusan terbaik untuk masa depan Anda.</p>
        <div class="hero-btns">
            <a href="{{ route('ppdb.index') }}" class="btn-hero-primary"><i class="fas fa-edit"></i> Daftar Sekarang</a>
            <a href="{{ route('ppdb.cek-status') }}" class="btn-hero-secondary"><i class="fas fa-search"></i> Cek Status</a>
        </div>
    </div>
</section>

{{-- JURUSAN --}}
<section class="jurusan-section">
    <div class="section-head">
        <h2>Program Keahlian</h2>
        <p>Tiga jurusan unggulan yang tersedia untuk Anda pilih</p>
    </div>
    <div class="jurusan-grid">
        @php $jurusanList = [
            ['kode'=>'AKL','nama'=>'Akuntansi Keuangan Lembaga','icon'=>'fas fa-calculator','color'=>'#5b21b6','bg'=>'#ede9fe','desc'=>'Mempelajari pencatatan keuangan, perpajakan, dan manajemen keuangan lembaga secara profesional.'],
            ['kode'=>'TJKT','nama'=>'Teknik Jaringan Komputer & Telekomunikasi','icon'=>'fas fa-network-wired','color'=>'#1e40af','bg'=>'#dbeafe','desc'=>'Mempelajari instalasi jaringan, keamanan sistem, dan teknologi telekomunikasi modern.'],
            ['kode'=>'MPLB','nama'=>'Manajemen Perkantoran & Layanan Bisnis','icon'=>'fas fa-briefcase','color'=>'#9d174d','bg'=>'#fce7f3','desc'=>'Mempelajari administrasi perkantoran, korespondensi bisnis, dan manajemen layanan profesional.'],
        ]; @endphp
        @foreach($jurusanList as $j)
        <a href="{{ route('jurusan.detail', strtolower($j['kode'])) }}"
   class="jurusan-card"
   style="text-decoration:none;">
            <div class="jurusan-icon" style="background:{{ $j['bg'] }};color:{{ $j['color'] }};">
                <i class="{{ $j['icon'] }}"></i>
            </div>
            <span class="jurusan-kode" style="background:{{ $j['bg'] }};color:{{ $j['color'] }};">{{ $j['kode'] }}</span>
            <h3>{{ $j['nama'] }}</h3>
            <p>{{ $j['desc'] }}</p>
        </a>
        @endforeach
    </div>
</section>

{{-- ALUR --}}
<section class="alur-section">
    <div class="alur-inner">
        <h2>Alur Pendaftaran</h2>
        <p>Ikuti langkah-langkah berikut untuk menyelesaikan pendaftaran</p>
        <div class="alur-grid">
            @php $steps = [
                ['icon'=>'fas fa-file-alt','label'=>'Isi Formulir','desc'=>'Lengkapi data diri dan pilihan jurusan'],
                ['icon'=>'fas fa-money-bill','label'=>'Pembayaran','desc'=>'Lakukan pembayaran pendaftaran'],
                ['icon'=>'fas fa-check-double','label'=>'Verifikasi','desc'=>'Tunggu verifikasi panitia PPDB'],
                ['icon'=>'fas fa-trophy','label'=>'Pengumuman','desc'=>'Cek status'],
            ]; @endphp
            @foreach($steps as $i => $s)
            <div class="alur-item">
                <div class="alur-num">0{{ $i+1 }}</div>
                <i class="{{ $s['icon'] }}"></i>
                <strong>{{ $s['label'] }}</strong>
                <span>{{ $s['desc'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <h2>Siap untuk Mendaftar?</h2>
    <p>Pendaftaran dibuka selama masa PPDB berlangsung.</p>
    <a href="{{ route('ppdb.index') }}" style="background:#0f2744;color:#fff;padding:.9rem 2rem;border-radius:12px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:.6rem;font-size:.95rem;touch-action:manipulation;">
        <i class="fas fa-arrow-right"></i> Mulai Pendaftaran
    </a>
</section>
@endsection
