<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menandai pesanan terlambat dan membatalkan pesanan kedaluwarsa (tidak lagi dikerjakan saat halaman dibuka)
Schedule::command('pesanan:perbarui-status')->everyFiveMinutes()->withoutOverlapping();
