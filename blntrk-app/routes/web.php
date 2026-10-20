<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\Pemilik;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BLNTRK Outdoor
|--------------------------------------------------------------------------
*/

// Halaman Utama Publik
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// Fallback /dashboard sesuai role yang login
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user?->role?->nama) {
        'pemilik' => redirect()->route('pemilik.dashboard'),
        'admin'   => redirect()->route('admin.dashboard'),
        default   => redirect()->route('beranda'),
    };
})->middleware(['auth'])->name('dashboard');

// Profil Pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Area Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Master Data Kategori
    Route::resource('kategori', Admin\KategoriController::class)->except('show');

    // Master Data Alat
    Route::patch('alat/{alat}/toggle', [Admin\AlatController::class, 'toggle'])->name('alat.toggle');
    Route::resource('alat', Admin\AlatController::class);

    // Master Data Pelanggan
    Route::resource('pelanggan', Admin\PelangganController::class);

    // Master Data Pengguna
    Route::patch('user/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('user.toggle');
    Route::post('user/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('user.reset-password');
    Route::resource('user', Admin\UserController::class)->except('show');
});

// Area Khusus Pemilik
Route::middleware(['auth', 'role:pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/', [Pemilik\DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
