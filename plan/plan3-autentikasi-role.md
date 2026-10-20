# Plan 3 – Autentikasi, Role, dan Layout

**Tujuan:** tiga role dapat login ke area masing-masing, akses silang ditolak, dan layout dasar tersedia.
**Fitur:** F01, F03. **Estimasi:** 3–4 hari.

## 1. Instal Breeze
```powershell
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
```
Gunakan `npm run dev` saat mengembangkan tampilan.

## 2. Sesuaikan Model User
```php
protected $fillable = ['role_id','nama','email','password','is_aktif'];
protected function casts(): array { return ['password' => 'hashed', 'email_verified_at' => 'datetime']; }
use SoftDeletes;
public function role() { return $this->belongsTo(Role::class); }
public function pelanggan() { return $this->hasOne(Pelanggan::class); }
public function punyaRole(string ...$r): bool { return in_array($this->role->nama, $r); }
```
Kolom bawaan `name` diganti `nama` (ubah di view Breeze, `RegisteredUserController`, dan `ProfileController`).

## 3. Registrasi Pelanggan (F03)
- Ubah `RegisteredUserController@store`: validasi lewat `RegisterPelangganRequest` (nama, email unique, no_hp, alamat, password confirmed).
- Dalam `DB::transaction`: buat `User` (role pelanggan) dan `Pelanggan` (user_id).
- Setelah daftar, arahkan ke login dengan pesan sukses (sesuai skenario skripsi).
- Tidak ada pendaftaran role lain dari publik.

## 4. Login dan Pengalihan Berdasarkan Role
Ubah `AuthenticatedSessionController@store`:
```php
$request->authenticate();
if (! $request->user()->is_aktif) { Auth::logout(); return back()->withErrors(['email' => 'Akun nonaktif.']); }
$request->session()->regenerate();
return redirect()->intended(match ($request->user()->role->nama) {
    'pemilik' => route('pemilik.dashboard'),
    'admin'   => route('admin.dashboard'),
    default   => route('beranda'),
});
```

## 5. Middleware CekRole
```powershell
php artisan make:middleware CekRole
```
```php
public function handle(Request $request, Closure $next, string ...$roles)
{
    $user = $request->user();
    if (! $user || ! in_array($user->role->nama, $roles)) abort(403);
    return $next($request);
}
```
Daftarkan di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => \App\Http\Middleware\CekRole::class]);
})
```

## 6. Kerangka Route (`routes/web.php`)
```php
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
});
Route::middleware(['auth','role:pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/', [Pemilik\DashboardController::class, 'index'])->name('dashboard');
});
Route::middleware(['auth','role:pelanggan'])->group(function () { /* plan 6 */ });
require __DIR__.'/auth.php';
```

## 7. Layout Blade
```
resources/views/layouts/{publik,admin,pemilik}.blade.php
resources/views/components/{badge-status,alert,tombol,kartu-alat}.blade.php
```
- `layouts/publik`: navbar, konten, footer (pelanggan).
- `layouts/admin` dan `layouts/pemilik`: sidebar, header, area konten, flash message.
- Komponen `badge-status` memakai `StatusPesanan::warna()`.
- CSS: gunakan Bootstrap 5 (CDN atau npm) atau CSS sendiri. Tetapkan palet warna dari wireframe Figma.
- Sertakan meta viewport (mobile first).

## 8. Keamanan
- `@csrf` pada semua form (bawaan Breeze).
- Rate limiting login bawaan (`LoginRequest`).
- Pastikan `APP_DEBUG=false` saat demo.

## 9. Pengujian Manual (BB-01 sampai BB-14)
Login dengan tiga akun; akses `/admin` dari pelanggan (harus 403); login salah; daftar akun baru; email ganda.

## 10. Definition of Done
- [ ] Tiga role login dan masuk dashboard masing-masing (halaman sementara)
- [ ] Akses silang antar role menghasilkan 403
- [ ] Registrasi pelanggan membuat `users` dan `pelanggan` dalam satu transaksi
- [ ] Akun nonaktif ditolak login
- [ ] Layout publik, admin, dan pemilik tampil responsif
- [ ] Tangkapan layar login dan registrasi (Bab IV)
