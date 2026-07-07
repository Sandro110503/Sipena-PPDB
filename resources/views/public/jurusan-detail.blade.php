@extends('layouts.public')

@section('title','Detail Jurusan')

@push('styles')

<style>
.hero-jurusan{
    background:url('{{ asset("images/sekolah.jpeg") }}') center/cover;
    position:relative;
    padding:100px 20px;
    text-align:center;
    color:white;
}

.hero-jurusan::before{
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(
        135deg,
        rgba(15,39,68,.92),
        rgba(26,74,138,.85)
    );
}

.hero-content{
    position:relative;
    z-index:2;
}

.detail-container{
    max-width:1000px;
    margin:40px auto;
    padding:0 20px;
}

.card-detail{
    background:white;
    border-radius:16px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.card-detail h2{
    color:#0f2744;
    margin-bottom:15px;
}

.card-detail p{
    color:#64748b;
    line-height:1.8;
}

.list{
    margin-top:20px;
}

.list li{
    margin-bottom:10px;
}
</style>

@endpush

@section('content')

@if($slug == 'akl')

<section class="hero-jurusan">
    <div class="hero-content">
        <h1>Akuntansi Keuangan Lembaga (AKL)</h1>
        <p>Membangun kompetensi di bidang akuntansi dan keuangan</p>
    </div>
</section>

<div class="detail-container">
    <div class="card-detail">


        <h2>Deskripsi Jurusan</h2>

        <p>
            Jurusan Akuntansi Keuangan Lembaga mempelajari pencatatan
            transaksi keuangan, penyusunan laporan keuangan,
            perpajakan, audit, dan penggunaan aplikasi akuntansi.
        </p>

        <h2>Materi yang Dipelajari</h2>

        <ul class="list">
            <li>Akuntansi Dasar</li>
            <li>Akuntansi Perusahaan Jasa</li>
            <li>Akuntansi Perusahaan Dagang</li>
            <li>Perpajakan</li>
            <li>Spreadsheet</li>
            <li>Komputer Akuntansi</li>
        </ul>

        <h2>Peluang Karir</h2>

        <ul class="list">
            <li>Staff Accounting</li>
            <li>Admin Keuangan</li>
            <li>Teller Bank</li>
            <li>Kasir Profesional</li>
            <li>Auditor Junior</li>
        </ul>

    </div>
</div>

@elseif($slug == 'tjkt')

<section class="hero-jurusan">
    <div class="hero-content">
        <h1>Teknik Jaringan Komputer dan Telekomunikasi (TJKT)</h1>
        <p>Menguasai jaringan komputer, server, dan teknologi telekomunikasi modern</p>
    </div>
</section>

<div class="detail-container">
    <div class="card-detail">

        <h2>Deskripsi Jurusan</h2>

        <p>
            Jurusan Teknik Jaringan Komputer dan Telekomunikasi (TJKT)
            mempelajari instalasi jaringan komputer, konfigurasi server,
            keamanan jaringan, fiber optik, hingga teknologi komunikasi digital
            yang banyak digunakan di dunia industri saat ini.
        </p>

        <h2>Materi yang Dipelajari</h2>

        <ul class="list">
            <li>Dasar Jaringan Komputer</li>
            <li>Administrasi Sistem Jaringan</li>
            <li>Konfigurasi Router dan Switch</li>
            <li>Fiber Optik</li>
            <li>Keamanan Jaringan (Network Security)</li>
            <li>Server Linux dan Windows</li>
            <li>Cloud Computing</li>
            <li>Internet of Things (IoT)</li>
        </ul>

        <h2>Peluang Karir</h2>

        <ul class="list">
            <li>Network Administrator</li>
            <li>IT Support</li>
            <li>Network Engineer</li>
            <li>Teknisi Fiber Optik</li>
            <li>System Administrator</li>
            <li>Data Center Technician</li>
            <li>Cyber Security Junior</li>
        </ul>

    </div>
</div>

@elseif($slug == 'mplb')

<section class="hero-jurusan">
    <div class="hero-content">
        <h1>Manajemen Perkantoran dan Layanan Bisnis (MPLB)</h1>
        <p>Mencetak tenaga administrasi profesional yang siap kerja</p>
    </div>
</section>

<div class="detail-container">
    <div class="card-detail">

        <h2>Deskripsi Jurusan</h2>

        <p>
            Jurusan Manajemen Perkantoran dan Layanan Bisnis (MPLB)
            mempelajari administrasi perkantoran modern, pengelolaan dokumen,
            pelayanan pelanggan, komunikasi bisnis, serta pengelolaan kegiatan
            administrasi perusahaan secara profesional.
        </p>

        <h2>Materi yang Dipelajari</h2>

        <ul class="list">
            <li>Administrasi Perkantoran</li>
            <li>Kearsipan Digital</li>
            <li>Korespondensi Bisnis</li>
            <li>Komunikasi Bisnis</li>
            <li>Otomatisasi Tata Kelola Perkantoran</li>
            <li>Pelayanan Prima (Service Excellent)</li>
            <li>Manajemen Agenda dan Rapat</li>
            <li>Aplikasi Perkantoran</li>
        </ul>

        <h2>Peluang Karir</h2>

        <ul class="list">
            <li>Staff Administrasi</li>
            <li>Administrative Assistant</li>
            <li>Customer Service</li>
            <li>Front Office Officer</li>
            <li>Receptionist</li>
            <li>Secretary Assistant</li>
            <li>Office Administrator</li>
        </ul>

    </div>
</div>

@endif

@endsection
