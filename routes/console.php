<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup otomatis: dicek setiap hari jam 01:00. Command sendiri yang
// menentukan apakah hari ini sudah waktunya backup, berdasarkan
// pengaturan mingguan/bulanan yang diatur admin di halaman Backup Database.
Schedule::command('backup:otomatis')->dailyAt('01:00');
