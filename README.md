# BLNTRK Outdoor – Sistem Informasi Persewaan Alat Outdoor

> Aplikasi berbasis web untuk manajemen persewaan perlengkapan alam terbuka, pengecekan ketersediaan stok berbasis tanggal secara real-time, pencatatan jaminan fisik, perhitungan denda otomatis, dan pelaporan operasional toko. Dikembangkan untuk skripsi program studi Informatika/Sistem Informasi.

---

## 🌲 Ikhtisar Proyek

**BLNTRK Outdoor** adalah solusi digital komprehensif bagi penyedia jasa sewa perlengkapan outdoor di Jalancagak, Subang. Sistem mengadopsi prinsip desain antarmuka modern **Foresta Adventure** (*Zero Emoji*, 100% SVG, palet warna alam *Emerald/Sage/Forest Dark*), serta didukung oleh mekanisme *State Machine* yang ketat untuk mengawal integritas alur sewa.

### Fitur Utama Sistem:
1. **Katalog & Ketersediaan Real-Time (F01–F03):** Pengecekan ketersediaan unit alat berdasarkan irisan rentang tanggal sewa (`tanggal_mulai` s/d `tanggal_selesai`), pencegahan *double-booking* dengan database lock (`lockForUpdate`), dan pengurangan kuota untuk unit dalam perbaikan (*maintenance*).
2. **Pemesanan Daring & Walk-in (F04, F07):** Formulir reservasi interaktif, kalkulasi durasi otomatis, pembatasan durasi sewa maksimal 14 hari, nomor resi unik harian (`BLN-yymmdd-xxxx`), serta pencatatan pelanggan langsung di toko oleh kasir (*walk-in*).
3. **Konfirmasi & Serah Terima Jaminan (F05a, F05b):** Pencatatan pembayaran lunas tunai/transfer, serah terima alat fisik dengan kartu identitas jaminan (KTP/SIM/Kartu Pelajar) bernomor tersamar untuk kepatuhan privasi data, dan unduh lembar bukti resi resmi format PDF.
4. **Pengembalian, Kondisi Unit & Denda Otomatis (F06a, F06b, F11):** Inspeksi kondisi fisik per unit (baik, rusak ringan, rusak berat, hilang), sinkronisasi stok perawatan, deteksi keterlambatan dengan jendela toleransi toko, kalkulasi denda harian proporsional, dan penandaan pelunasan denda.
5. **Laporan & Ekspor Berkas (F08, F09, F13):** Rekapitulasi pendapatan dan statistik sewa per periode, cetak PDF berformat A4 rapi, serta ekspor data ke Excel (XLSX) dan CSV.
6. **Manajemen Akun & Log Aktivitas (F10, F12, F15):** Autentikasi multi-peran (Admin, Pemilik, Pelanggan), konfigurasi aturan toko dinamis oleh pemilik, dan pencatatan riwayat aktivitas penting (*audit trail*).

---

## 🛠️ Prasyarat & Lingkungan

- **Bahasa Pemrograman:** PHP &ge; 8.2
- **Framework:** Laravel 12.x
- **Basis Data:** MySQL &ge; 8.0 (atau MariaDB 10.4+ / SQLite untuk lingkungan testing)
- **Node.js & NPM:** Node.js &ge; 18.x & NPM &ge; 9.x
- **Ekstensi PHP Wajib:** `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`
- **Composer:** Composer 2.x

---

## 🚀 Panduan Instalasi Cepat

Buka terminal PowerShell / Command Prompt dan jalankan langkah-langkah berikut:

```powershell
# 1. Pindah ke direktori aplikasi
cd blntrk-app

# 2. Pasang dependensi PHP
composer install

# 3. Pasang dependensi frontend (Node.js) & bangun aset
npm install
npm run build

# 4. Salin berkas konfigurasi lingkungan
copy .env.example .env

# 5. Buat application key unik
php artisan key:generate

# 6. Konfigurasi basis data pada berkas .env
# Sesuaikan DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# Contoh:
# DB_CONNECTION=mysql
# DB_DATABASE=blntrk_outdoor
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi basis data dan seeder data awal
php artisan migrate --seed

# (Opsional) Mengisi 30 transaksi demo historis untuk keperluan sidang:
php artisan db:seed --class=TransaksiDemoSeeder

# 8. Pastikan symlink direktori storage aktif
php artisan storage:link

# 9. Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser pada URL: `http://127.0.0.1:8000`

---

## 👥 Akun Bawaan (Default Seeder)

| Peran (Role) | Alamat Email | Kata Sandi | Halaman Awal | Hak Akses |
|---|---|---|---|---|
| **Admin** | `admin@blntrk.test` | `password` | `/admin` | Manajemen data master alat, transaksi sewa, pembayaran, pengambilan jaminan, pengembalian alat, denda, stok perawatan, laporan toko. |
| **Pemilik** | `pemilik@blntrk.test` | `password` | `/pemilik` | Dashboard eksekutif toko, grafik omset, unduh laporan PDF/Excel, konfigurasi aturan denda & toko, manajemen admin, log aktivitas sistem. |
| **Pelanggan** | `budi@blntrk.test` | `password` | `/` | Katalog alat, cek ketersediaan tanggal, reservasi sewa, bukti resi PDF, pantau status pesanan, pembatalan mandiri sebelum bayar. |

---

## 🧪 Pengujian Sistem (*Automated Tests*)

Aplikasi dilengkapi dengan pengujian otomatis menggunakan **PHPUnit/Pest** untuk menjamin kestabilan dan mencegah regresi (*zero regression*):

```powershell
# Jalankan seluruh rangkaian uji otomatis (129 tests)
php artisan test

# Menjalankan pengujian spesifik logika bisnis:
php artisan test tests/Unit/KetersediaanServiceTest.php
php artisan test tests/Unit/HitungTotalTest.php
php artisan test tests/Unit/DendaServiceTest.php
php artisan test tests/Feature/PesananAlurTest.php
php artisan test tests/Feature/TransisiStatusTest.php
```

Hasil pengujian otomatis: **129 passed, 478 assertions, 0 failures**.  
Laporan pengujian fungsional lengkap (BB-01 sampai BB-74) dan evaluasi System Usability Scale (SUS) dapat dilihat pada dokumen:  
[`plan/hasil-blackbox.md`](file:///c:/Users/Dwi%20Wahyu%20Fauzan/blntrk-outdoor/plan/hasil-blackbox.md)

---

## ⏱️ Skenario Demonstrasi Sidang (10 Menit)

1. **Alur Pelanggan (2–3 Menit):**
   - Buka Beranda publik `http://localhost:8000`.
   - Pilih alat (misal: *Tenda Dome 4P*), gunakan kalkulator tanggal sewa untuk mengecek ketersediaan unit.
   - Buat pesanan reservasi sewa 2 hari, sistem menerbitkan nomor resi resmi dan kalkulasi total biaya sewa yang presisi.
   - Tunjukkan tampilan lembar resi dan unduh berkas bukti resi format PDF.
2. **Alur Admin & Kasir (4–5 Menit):**
   - Masuk sebagai Admin (`admin@blntrk.test`).
   - Buka detail pesanan, konfirmasi penerimaan pembayaran lunas (tunai/transfer).
   - Lakukan serah terima barang: pilih jenis jaminan fisik (KTP/SIM) dan input nomor identitas (sistem otomatis menyamarkan angka demi privasi).
   - Buka menu Pengembalian: simulasikan pelanggan mengembalikan unit lewat jatuh tempo dengan 1 alat robek ringan.
   - Tunjukkan kalkulasi denda keterlambatan otomatis, biaya perbaikan kerusakan, kenaikan stok perawatan, dan pelunasan denda serta serah terima kembali kartu jaminan.
3. **Alur Pemilik Toko (2–3 Menit):**
   - Masuk sebagai Pemilik (`pemilik@blntrk.test`).
   - Tinjau metrik omset dan grafik transaksi di Dashboard Pemilik.
   - Buka menu Laporan, filter periode transaksi, dan ekspor laporan ke format PDF dan Excel.
   - Buka Pengaturan Toko untuk mendemonstrasikan perubahan parameter denda/toleransi secara langsung.
   - Tunjukkan rekam jejak pada menu Log Aktivitas Sistem (*audit trail*).

---

## 📂 Struktur Direktori Proyek

```
blntrk-outdoor/
├── blntrk-app/                  # Source code utama aplikasi Laravel 12
│   ├── app/
│   │   ├── Enums/               # StatusPesanan, MetodePembayaran, JenisDenda
│   │   ├── Http/Controllers/    # Admin, Pemilik, Pelanggan, Auth, Katalog
│   │   ├── Models/              # Alat, Pesanan, Jaminan, Pengembalian, Denda, dll.
│   │   ├── Policies/            # PesananPolicy (Keamanan & State Machine)
│   │   └── Services/            # KetersediaanService, PesananService, DendaService, LogService
│   ├── database/
│   │   ├── migrations/          # 12 file migrasi skema tabel
│   │   └── seeders/             # Role, User, Pengaturan, Alat, TransaksiDemoSeeder
│   ├── resources/views/         # Antarmuka Blade (Desain Foresta Adventure, 100% SVG)
│   └── tests/                   # 129 Unit & Feature automated test suites
├── docs/                        # Dokumen perencanaan, analisis RUP, perancangan skripsi
└── plan/                        # Lembar rencana implementasi (Plan 1 s/d 12) & hasil-blackbox.md
```

---

## 📄 Lisensi & Hak Cipta

Aplikasi dan seluruh dokumentasi ini dikembangkan untuk penyelesaian Tugas Akhir / Skripsi Sarjana Program Studi Informatika / Sistem Informasi. Hak cipta dilindungi undang-undang.
