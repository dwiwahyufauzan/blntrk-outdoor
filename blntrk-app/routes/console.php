<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menandai pesanan terlambat dan membatalkan pesanan kedaluwarsa (tidak lagi dikerjakan saat halaman dibuka)
Schedule::command('pesanan:perbarui-status')->everyFiveMinutes()->withoutOverlapping();

// Pengingat jatuh tempo dan keterlambatan lewat email (aktif bila MAIL_MAILER bukan log)
Schedule::command('pesanan:kirim-pengingat')->hourly()->withoutOverlapping();
