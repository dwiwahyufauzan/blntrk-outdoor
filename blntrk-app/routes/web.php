<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PaketBundlingController;
use App\Http\Controllers\Pelanggan\PesananController;
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

// Katalog & Cek Ketersediaan Alat
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{alat}', [KatalogController::class, 'show'])->name('katalog.show');
Route::get('/katalog/{alat}/ketersediaan', [KatalogController::class, 'ketersediaan'])->name('katalog.ketersediaan');

// Alias rute /alat sesuai dokumen skripsi
Route::get('/alat', [KatalogController::class, 'index'])->name('alat.index');
Route::get('/alat/{alat}', [KatalogController::class, 'show'])->name('alat.show');
Route::get('/alat/{alat}/ketersediaan', [KatalogController::class, 'ketersediaan'])->name('alat.ketersediaan');

// Fitur Keranjang Belanja (Sewa Alat Outdoor)
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/tambah', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
Route::post('/keranjang/tambah-paket/{paket}', [KeranjangController::class, 'tambahPaket'])->name('keranjang.tambah-paket');
Route::patch('/keranjang/{alatId}', [KeranjangController::class, 'update'])->name('keranjang.update');
Route::delete('/keranjang/kosongkan', [KeranjangController::class, 'kosongkan'])->name('keranjang.kosongkan');
Route::delete('/keranjang/{alatId}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
Route::post('/keranjang/checkout', [KeranjangController::class, 'checkout'])->name('keranjang.checkout');

// Fitur Paket Bundling Hemat (Paket 1, Paket 2, Paket 3)
Route::get('/paket-bundling', [PaketBundlingController::class, 'index'])->name('paket-bundling.index');
Route::get('/paket-bundling/{slug}', [PaketBundlingController::class, 'show'])->name('paket-bundling.show');
Route::post('/paket-bundling/{slug}/sewa-langsung', [PaketBundlingController::class, 'sewaLangsung'])->name('paket-bundling.sewa-langsung');

// Fallback /dashboard sesuai role yang login
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user?->role?->nama) {
        'pemilik' => redirect()->route('pemilik.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('beranda'),
    };
})->middleware(['auth'])->name('dashboard');

// Profil Pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Sesi Login Pengguna
    Route::get('/sesi-login', [\App\Http\Controllers\SesiController::class, 'index'])->name('sesi.index');
    Route::get('/sesi', [\App\Http\Controllers\SesiController::class, 'index'])->name('sesi.alias');

    // Alur Pemesanan & Resi Pelanggan
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/buat', [PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [PesananController::class, 'store'])->middleware('throttle:10,1')->name('pesanan.store');
    Route::get('/pesanan/{resi}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{resi}/resi', [PesananController::class, 'resi'])->name('pesanan.resi');
    Route::get('/pesanan/{resi}/pdf', [PesananController::class, 'cetakPdf'])->name('pesanan.pdf');
    Route::patch('/pesanan/{resi}/batal', [PesananController::class, 'batal'])->name('pesanan.batal');
});

// Area Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Laporan & Cetak PDF / Excel (F08, T13)
    Route::get('laporan', [Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/pdf', [Admin\LaporanController::class, 'cetakPdf'])->name('laporan.pdf');
    Route::get('laporan/excel', [Admin\LaporanController::class, 'eksporExcel'])->name('laporan.excel');

    // Cetak PDF Resi Pesanan
    Route::get('pesanan/{pesanan}/pdf', [Admin\LaporanController::class, 'cetakResi'])->name('pesanan.pdf');

    // Perawatan Alat (T12)
    Route::get('perawatan', [Admin\PerawatanController::class, 'index'])->name('perawatan.index');
    Route::patch('perawatan/{alat}/selesai', [Admin\PerawatanController::class, 'selesai'])->name('perawatan.selesai');
    Route::patch('perawatan/{alat}/tambah', [Admin\PerawatanController::class, 'tambah'])->name('perawatan.tambah');

    // Manajemen Pesanan: Pembayaran, Pengambilan/Jaminan, Pembatalan
    Route::post('pesanan/{pesanan}/bayar', [Admin\PesananController::class, 'bayar'])->name('pesanan.bayar');
    Route::post('pesanan/{pesanan}/ambil', [Admin\PesananController::class, 'ambil'])->name('pesanan.ambil');
    Route::patch('pesanan/{pesanan}/batal', [Admin\PesananController::class, 'batal'])->name('pesanan.batal');
    Route::patch('pesanan/{pesanan}/jaminan/kembali', [Admin\PesananController::class, 'kembalikanJaminan'])->name('pesanan.jaminan.kembali');

    // Pengembalian & Denda
    Route::get('pesanan/{pesanan}/pengembalian', [Admin\PengembalianController::class, 'create'])->name('pesanan.pengembalian.create');
    Route::post('pesanan/{pesanan}/pengembalian', [Admin\PengembalianController::class, 'store'])->name('pesanan.pengembalian.store');
    Route::post('pesanan/{pesanan}/denda', [Admin\PesananController::class, 'tambahDenda'])->name('pesanan.denda.tambah');
    Route::get('pesanan/{pesanan}/hitung-denda', [Admin\PengembalianController::class, 'hitungDenda'])->name('pesanan.hitung-denda');
    Route::patch('denda/{denda}/lunas', [Admin\DendaController::class, 'lunas'])->name('denda.lunas');
    Route::get('denda', [Admin\DendaController::class, 'index'])->name('denda.index');

    // Master Data Kategori Denda
    Route::patch('kategori-denda/{kategoriDenda}/toggle', [Admin\KategoriDendaController::class, 'toggle'])->name('kategori-denda.toggle');
    Route::resource('kategori-denda', Admin\KategoriDendaController::class)->except('show');

    // Manajemen Paket Bundling
    Route::patch('paket-bundling/{paketBundling}/toggle', [Admin\PaketBundlingController::class, 'toggle'])->name('paket-bundling.toggle');
    Route::resource('paket-bundling', Admin\PaketBundlingController::class)->except('show');

    Route::resource('pesanan', Admin\PesananController::class);

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
    Route::get('laporan', [Pemilik\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/pdf', [Pemilik\LaporanController::class, 'cetakPdf'])->name('laporan.pdf');
    Route::get('laporan/excel', [Pemilik\LaporanController::class, 'eksporExcel'])->name('laporan.excel');

    // Pengaturan Toko & Aturan Denda
    Route::get('pengaturan', [Pemilik\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('pengaturan', [Pemilik\PengaturanController::class, 'update'])->name('pengaturan.update');

    // Manajemen Akun Admin
    Route::patch('admin/{admin}/toggle', [Pemilik\AdminController::class, 'toggle'])->name('admin.toggle');
    Route::post('admin/{admin}/reset-password', [Pemilik\AdminController::class, 'resetPassword'])->name('admin.reset-password');
    Route::resource('admin', Pemilik\AdminController::class)->except('show');

    // Log Aktivitas Sistem (T15)
    Route::get('log-aktivitas', [Pemilik\LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
});

require __DIR__.'/auth.php';
