# Plan 1 – Instalasi Lingkungan dan Proyek Laravel

**Tujuan:** lingkungan pengembangan siap dan proyek Laravel kosong berjalan dan terhubung ke MySQL.
**Estimasi:** 1–2 hari.

## 1. Prasyarat

| Perangkat | Versi | Catatan |
|---|---|---|
| XAMPP | PHP 8.2 atau lebih baru (Laravel 11/12 butuh ≥ 8.2) | Aktifkan Apache dan MySQL |
| Composer | 2.x | https://getcomposer.org |
| Node.js | LTS 20+ | Untuk Vite dan Breeze |
| Git | terbaru | Kontrol versi |
| VS Code | – | Ekstensi: PHP Intelephense, Laravel Blade Snippets, Laravel Extra Intellisense, EditorConfig |
| Figma | akun gratis | Wireframe |
| Browser | Chrome | DevTools untuk uji responsif |

## 2. Langkah

### 2.1 Verifikasi
```powershell
php -v
composer -V
node -v
npm -v
git --version
```
Jika `php` tidak dikenali, tambahkan `C:\xampp\php` ke PATH sistem lalu buka terminal baru.

### 2.2 Aktifkan ekstensi PHP
Edit `C:\xampp\php\php.ini`, pastikan tidak diberi tanda `;`:
`extension=zip`, `extension=gd`, `extension=mbstring`, `extension=pdo_mysql`, `extension=fileinfo`, `extension=intl`, `extension=openssl`. Restart Apache.

### 2.3 Buat database
Buka http://localhost/phpmyadmin → buat database `blntrk_outdoor` dengan collation `utf8mb4_unicode_ci`.

### 2.4 Buat proyek
```powershell
cd "C:\Users\Dwi Wahyu Fauzan\blntrk-outdoor"
composer create-project laravel/laravel blntrk-app
cd blntrk-app
php artisan --version
```

### 2.5 Konfigurasi `.env`
```
APP_NAME="BLNTRK Outdoor"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blntrk_outdoor
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=database
```
Di `config/app.php` pastikan `'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta')`. **Penting:** zona waktu salah akan merusak perhitungan keterlambatan.

### 2.6 Bahasa Indonesia (opsional namun disarankan)
```powershell
composer require laravel-lang/lang --dev
php artisan lang:add id
```
Pesan validasi menjadi bahasa Indonesia.

### 2.7 Jalankan
```powershell
php artisan serve
```
Buka http://localhost:8000 dan pastikan halaman selamat datang Laravel muncul.

### 2.8 Git
```powershell
git init
git add .
git commit -m "Inisialisasi proyek Laravel"
git branch -M main
```
`.env` sudah masuk `.gitignore` (periksa). Tambahkan juga `/storage/*.key`. Buat `.env.example` tanpa kredensial.

### 2.9 Struktur awal folder
```powershell
New-Item -ItemType Directory app\Services, app\Enums, app\Policies | Out-Null
```

### 2.10 Konvensi kode
- PSR-12, `php artisan pint` sebelum commit.
- `.editorconfig` bawaan Laravel dipakai.

## 3. Risiko
| Risiko | Penanganan |
|---|---|
| Port MySQL bentrok | Ubah `DB_PORT` atau hentikan layanan MySQL lain |
| Versi PHP XAMPP lama | Unduh XAMPP versi PHP 8.2+ |
| `composer` lambat | Pakai koneksi stabil, atau `composer config -g repos.packagist composer https://repo.packagist.org` |
| SSL error cURL | Setel `curl.cainfo` di `php.ini` |

## 4. Definition of Done
- [x] `php artisan serve` siap dan konfigurasi Laravel 13 berjalan sempurna
- [x] `php artisan migrate` berhasil ke database MySQL `blntrk_outdoor`
- [x] Zona waktu Asia/Jakarta & Locale `id` aktif
- [x] Folder arsitektur (`app/Services`, `app/Enums`, `app/Policies`) dibuat
- [x] Repositori git root aktif dengan commit pertama
- [x] Konfigurasi `.env` dan `.gitignore` aman dan rapi
