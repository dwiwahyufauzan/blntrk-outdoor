# BLNTRK Outdoor – Sistem Informasi Persewaan Alat Outdoor

> Aplikasi web untuk mengelola persewaan perlengkapan alam terbuka: katalog dan stok berbasis tanggal, keranjang dan paket bundling, pembayaran, jaminan identitas, pengembalian, denda otomatis, serta laporan operasional toko. Dikembangkan sebagai proyek skripsi Informatika / Sistem Informasi.

---

## Ikhtisar

**BLNTRK Outdoor** adalah solusi digital untuk penyedia sewa perlengkapan outdoor di Jalancagak, Subang. Alur sewa dijaga oleh *state machine* yang ketat, stok dihitung per hari, dan tampilan mengikuti desain **Foresta Adventure** (tanpa emoji, ikon SVG, palet Emerald/Sage/Forest Dark).

### Alur status pesanan

```
menunggu ──► dibayar ──► disewa ──► (terlambat) ──► dikembalikan
    │            │
    └────────────┴──► dibatalkan
```

| Transisi | Pelaku | Keterangan |
|---|---|---|
| Pesanan dibuat → `menunggu` | Pelanggan / Admin (walk-in) | Stok langsung ditahan |
| `menunggu` → `dibayar` | Admin | Tombol **Catat Pembayaran**; nominal harus sama dengan total sewa |
| `dibayar` → `disewa` | Admin | Tombol **Konfirmasi Serah Terima**; jaminan KTP/SIM dicatat |
| `disewa` → `terlambat` | Sistem | Lewat jatuh tempo + toleransi (scheduler / saat halaman dibuka) |
| `disewa`/`terlambat` → `dikembalikan` | Admin | Form pengembalian; cek kondisi per unit, denda dihitung |
| `menunggu`/`dibayar` → `dibatalkan` | Admin (alasan wajib) | Pelanggan hanya bisa batal saat `menunggu` |
| `menunggu` → `dibatalkan` otomatis | Sistem | Melewati batas pengambilan (`batas_ambil`) |

---

## Fitur

**Pelanggan**
- Beranda, katalog alat (stok tersedia real-time, alat habis dipisahkan), detail alat, dan cek ketersediaan per rentang tanggal.
- Keranjang sewa (tambah alat atau paket bundling, ubah jumlah, checkout) dan **paket bundling hemat**.
- Reservasi sewa dengan durasi otomatis (maksimal 14 hari), nomor resi unik harian `BLN-yymmdd-####`, resi cetak dan PDF.
- Pantau pesanan (tab aktif / riwayat), batal mandiri sebelum dibayar, kelola profil dan sesi login.

**Admin (kasir)**
- Dashboard operasional dan manajemen pesanan, termasuk pesanan **walk-in**.
- **Catat Pembayaran** (tunai / transfer), **Serah Terima** dengan jaminan KTP / SIM / Kartu Pelajar (nomor disamarkan, hanya 4 digit terakhir yang disimpan), **Batalkan Pesanan**.
- **Pengembalian**: kondisi per unit (baik, rusak ringan, rusak berat, hilang), stok perawatan otomatis, unit hilang mengurangi stok total.
- **Denda**: keterlambatan (tarif harian × hari × persen, dengan toleransi), kerusakan, kehilangan (sesuai harga beli), denda manual, dan tandai lunas. Jaminan **ditahan** selama ada denda belum lunas.
- Master data: kategori, alat, paket bundling, kategori denda, pelanggan, pengguna. Menu perawatan alat, laporan, dan tautan WhatsApp (pengingat, tagihan denda, peringatan terlambat).

**Pemilik**
- Dashboard eksekutif dan grafik, laporan (penyewaan, pendapatan, denda, alat terlaris, stok kritis) dalam **PDF dan CSV**.
- Pengaturan toko dan aturan denda, manajemen akun admin, **log aktivitas** (audit trail).

**Integritas data**
- Pemesanan atomik dengan `lockForUpdate` (anti double-booking) dan retry nomor resi saat bentrok.
- Pesanan milik orang lain dijawab 404 untuk pelanggan (resi tidak bisa ditebak), `PesananPolicy`, middleware `role`, throttle pada pembuatan pesanan.

---

## Teknologi

| Bagian | Teknologi |
|---|---|
| Bahasa / Framework | PHP ≥ 8.3, Laravel 13 |
| Basis data | MySQL ≥ 8.0 / MariaDB 10.4+ (SQLite didukung untuk dev dan test) |
| Frontend | Blade, Alpine.js 3, Tailwind CSS 3, Vite |
| PDF | `barryvdh/laravel-dompdf` |
| Autentikasi | Laravel Breeze, multi-peran (admin, pemilik, pelanggan) |
| Pengujian | PHPUnit 12 |

Prasyarat tambahan: Node.js ≥ 18, Composer 2.x, serta ekstensi PHP `pdo_mysql` (atau `pdo_sqlite`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`.

---

## Instalasi

```bash
cd blntrk-app

composer install
npm install && npm run build

cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Atur basis data di `.env`, pilih salah satu.

**MySQL** (default `.env.example`): buat database `blntrk_outdoor`, lalu sesuaikan `DB_USERNAME` dan `DB_PASSWORD`.

**SQLite** (tanpa server database):
```bash
# di .env: DB_CONNECTION=sqlite, hapus baris DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD
touch database/database.sqlite
```

Lanjutkan:
```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve              # http://127.0.0.1:8000
```

Opsional, 30 transaksi demo historis:
```bash
php artisan db:seed --class=TransaksiDemoSeeder
```

> `.env` dan `database/*.sqlite` diabaikan git, sehingga pengaturan lokal tidak ikut ter-push. Untuk pengembangan dengan hot reload aset, jalankan `npm run dev`.

### Penjadwalan status otomatis

Perintah `pesanan:perbarui-status` berjalan **tiap jam**. Perintah ini menandai pesanan terlambat dan membatalkan pesanan yang kedaluwarsa. Selama pengembangan jalankan:

```bash
php artisan schedule:work
```

Di server produksi, tambahkan cron: `* * * * * cd /path/blntrk-app && php artisan schedule:run >> /dev/null 2>&1`. Status juga disinkronkan saat halaman detail pesanan atau halaman denda dibuka.

---

## Akun Bawaan (Seeder)

Kata sandi semua akun: `password`.

| Peran | Email | Halaman awal |
|---|---|---|
| Admin | `admin@blntrk.test` | `/admin` |
| Pemilik | `pemilik@blntrk.test` | `/pemilik` |
| Pelanggan | `budi@blntrk.test`, `siti@blntrk.test` | `/` |

Seeder juga membuat 8 kategori alat, 23 alat, 4 paket bundling, 4 kategori denda, dan pengaturan toko.

**Pengaturan toko bawaan** (dapat diubah pemilik di `/pemilik/pengaturan`): jam kembali `12:00`, toleransi `60` menit, denda harian `100%` dari tarif sewa, maksimal durasi `14` hari.

> **Ganti kata sandi bawaan sebelum aplikasi dipakai di server publik.**

---

## Panduan Menjalankan Alur Sewa (Admin)

1. Buka **Admin → Pesanan**, lalu pilih pesanan berstatus *Menunggu*.
2. Klik **Catat Pembayaran**, pilih metode, lalu simpan. Status menjadi *Sudah Dibayar*.
3. Klik **Konfirmasi Serah Terima**, isi jenis dan nomor identitas jaminan. Status menjadi *Sedang Disewa*.
4. Saat alat kembali, klik **Proses Pengembalian**, isi kondisi tiap unit (total harus sama dengan jumlah disewa), lalu simpan. Denda tercatat otomatis.
5. Lunasi denda di **Admin → Denda** (**Tandai Lunas**) atau di kartu denda pada detail pesanan.
6. Setelah semua denda lunas, klik **Kembalikan Kartu Identitas**.

---

## Pengujian

```bash
php artisan test                                   # seluruh suite (173 test)
php artisan test --filter=RevisiKlienTest          # regresi revisi klien
php artisan test tests/Unit/KetersediaanServiceTest.php
php artisan test tests/Feature/PengembalianDendaTest.php
```

Cakupan: autentikasi dan peran, master data, katalog dan stok, keranjang dan paket bundling, alur pesanan dan transisi status, konfirmasi, pengembalian dan denda, laporan dan dashboard, serta regresi tampilan admin. Dokumen pengujian fungsional (black-box) dan evaluasi SUS ada di [`plan/hasil-blackbox.md`](plan/hasil-blackbox.md).

---

## Struktur Direktori

```
blntrk-outdoor/
├── blntrk-app/                   # Aplikasi Laravel
│   ├── app/
│   │   ├── Console/Commands/     # pesanan:perbarui-status, pesanan:cek-kadaluarsa
│   │   ├── Enums/                # StatusPesanan, MetodePembayaran, JenisDenda, JenisJaminan
│   │   ├── Http/Controllers/     # Admin, Pemilik, Pelanggan, Auth, Katalog, Keranjang, PaketBundling
│   │   ├── Http/Requests/        # Validasi form
│   │   ├── Models/               # Alat, Pesanan, Jaminan, Pengembalian, Denda, PaketBundling, ...
│   │   ├── Policies/             # PesananPolicy
│   │   └── Services/             # Pesanan, Ketersediaan, Pengembalian, Denda, Keranjang,
│   │                             # Laporan, LaporanExport, WhatsApp, Log
│   ├── database/                 # 19 migrasi, seeder, factory
│   ├── resources/views/          # Blade: admin, pemilik, pelanggan, publik, pdf
│   ├── routes/                   # web.php, auth.php, console.php (scheduler)
│   └── tests/                    # Unit dan Feature test
├── docs/perencanaan/             # Evaluasi kebutuhan, perancangan DB / Laravel / UI, ERD
├── plan/                         # Rencana implementasi Plan 1–12 dan hasil pengujian
├── dokumen skripsi/              # Draf dokumen skripsi
└── PULL_REQUEST.md               # Ringkasan fitur PR terakhir
```

---

## Catatan Pengembangan

- Setiap halaman admin yang memakai `@push('scripts')` bergantung pada `@stack('scripts')` di `layouts/admin.blade.php`. Jangan hapus baris itu.
- Hindari `@json(...)` dengan ekspresi yang mengandung koma langsung di Blade. Siapkan datanya dulu dengan `@php`.
- Ekspor laporan berformat **CSV** (UTF-8) dan dapat dibuka langsung di Excel.
- Format kode: `./vendor/bin/pint`.

---

## Lisensi dan Hak Cipta

Aplikasi dan dokumentasi ini dikembangkan untuk penyelesaian Tugas Akhir / Skripsi Program Studi Informatika / Sistem Informasi. Hak cipta dilindungi undang-undang.
