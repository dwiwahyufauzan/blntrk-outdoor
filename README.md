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
| `disewa` → `disewa` (diperpanjang) | Admin | **Perpanjang Sewa**: stok hari tambahan dicek, selisih biaya dibayar saat itu juga |
| `disewa` → `terlambat` | Sistem | Lewat jatuh tempo + toleransi (scheduler) |
| `disewa`/`terlambat` → `dikembalikan` | Admin | Form pengembalian; cek kondisi per unit, denda dihitung |
| `menunggu`/`dibayar` → `dibatalkan` | Admin (alasan wajib) | Pelanggan hanya bisa batal saat `menunggu`. |
| `menunggu` → `dibatalkan` otomatis | Sistem | Melewati batas pengambilan (`batas_ambil`) |
| `dibayar` → `dibatalkan` otomatis (**no-show**) | Sistem | Tidak diambil hingga `batas_ambil` + toleransi hari; stok dilepas |

Sebelum pengambilan, jadwal dapat dipindah (**Ubah Jadwal**): pelanggan/admin saat `menunggu` (harga dihitung ulang), admin saat `dibayar` (durasi harus tetap). Ketersediaan stok dicek ulang dengan kunci baris yang sama seperti pemesanan.

---

## Fitur

**Pelanggan**
- Beranda, katalog alat (stok tersedia real-time, alat habis dipisahkan), detail alat, dan cek ketersediaan per rentang tanggal.
- Keranjang sewa (tambah alat atau paket bundling, ubah jumlah, checkout) dan **paket bundling hemat**.
- Reservasi sewa dengan durasi otomatis (maksimal 14 hari), nomor resi unik harian `BLN-yymmdd-####`, resi cetak dan PDF.
- **Kalender ketersediaan** 6 minggu (heatmap) di halaman detail alat.
- Pantau pesanan (tab aktif / riwayat), batal mandiri sebelum dibayar, ubah jadwal sebelum dibayar, kelola profil dan sesi login.
- **Unggah bukti transfer** (JPG/PNG/WEBP/PDF, maks. 4 MB) pada pesanan `menunggu`; berkas disimpan di disk privat dan hanya dibuka lewat rute terautentikasi milik pemesan/admin.
- Email pengingat jatuh tempo (H-1) dan peringatan keterlambatan untuk pelanggan terdaftar (aktif bila SMTP dikonfigurasi).

**Admin (kasir)**
- Dashboard operasional dan manajemen pesanan, termasuk pesanan **walk-in**.
- **Catat Pembayaran** (tunai / transfer, bukti transfer pelanggan terlihat di detail pesanan), **Serah Terima** dengan jaminan KTP / SIM / Kartu Pelajar (nomor disamarkan, hanya 4 digit terakhir yang disimpan) atau **deposit uang**, **Batalkan Pesanan**, **Perpanjang Sewa**, **Ubah Jadwal**.
- **Peringatan Dashboard**: dashboard memberi peringatan pesanan dibayar yang tidak diambil (no-show).
- **Pengembalian**: kondisi per unit (baik, rusak ringan, rusak berat, hilang), stok perawatan otomatis, unit hilang mengurangi stok total.
- **Denda**: keterlambatan (tarif harian × hari × persen, dengan toleransi), kerusakan, kehilangan (sesuai harga beli), denda manual, dan tandai lunas. Jaminan **ditahan** selama ada denda belum lunas.
- Master data: kategori, alat, paket bundling, kategori denda, pelanggan, pengguna. Menu perawatan alat dengan **riwayat perawatan** (unit masuk/selesai diperbaiki, siapa, kapan, mengapa), laporan, dan tautan WhatsApp (pengingat, tagihan denda, peringatan terlambat).

**Pemilik**
- Dashboard eksekutif: pendapatan dibanding bulan lalu, rasio denda terhadap pendapatan, okupansi per alat, grafik, dan laporan (penyewaan, pendapatan, denda, alat terlaris, stok kritis) dalam **PDF dan CSV**.
- Pengaturan toko, aturan denda, dan kebijakan no-show; manajemen akun admin, **log aktivitas** (audit trail).

**Integritas data**
- Pemesanan atomik dengan `lockForUpdate` (anti double-booking) dan retry nomor resi saat bentrok.
- Admin tidak dapat menghapus/menonaktifkan-tanpa-peringatan/mengurangi stok alat yang masih dibutuhkan pesanan aktif; keranjang memakai stok per tanggal, bukan stok global.
- Pesanan milik orang lain dijawab 404 untuk pelanggan (resi tidak bisa ditebak), `PesananPolicy`, middleware `role`, throttle pada pembuatan pesanan.

---

## Teknologi

| Bagian | Teknologi |
|---|---|
| Bahasa / Framework | PHP ≥ 8.3, Laravel 13 |
| Basis data | PostgreSQL (produksi di Render), MySQL ≥ 8.0 / MariaDB 10.4+, SQLite untuk dev dan test |
| Frontend | Blade, Alpine.js 3, Tailwind CSS 3, Vite |
| PDF | `barryvdh/laravel-dompdf` |
| Autentikasi | Laravel Breeze, multi-peran (admin, pemilik, pelanggan) |
| Pengujian dan kualitas | PHPUnit 12, Pint, Larastan (PHPStan level 5), uji concurrency lintas proses |

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

Perintah `pesanan:perbarui-status` berjalan **tiap 5 menit**. Perintah ini menandai pesanan terlambat, membatalkan pesanan `menunggu` yang kedaluwarsa, dan membatalkan pesanan `dibayar` yang tidak diambil (no-show). Perintah `pesanan:kirim-pengingat` (tiap jam) mengirim email pengingat jatuh tempo dan peringatan terlambat bila `MAIL_MAILER` bukan `log`. Selama pengembangan jalankan:

```bash
php artisan schedule:work
```

Di server produksi, tambahkan cron: `* * * * * cd /path/blntrk-app && php artisan schedule:run >> /dev/null 2>&1`. Membuka halaman **tidak lagi** mengubah data; seluruh perubahan status dikerjakan oleh scheduler. Container Docker (`entrypoint.sh`) sudah menjalankan `schedule:run` tiap menit.

Hosting gratis seperti Render menidurkan service saat sepi, sehingga loop di dalam container ikut berhenti. Sebagai gantinya, isi `CRON_TOKEN` lalu minta layanan cron eksternal (mis. cron-job.org) memanggil `GET /cron/jadwal` tiap 5 menit dengan header `Authorization: Bearer <CRON_TOKEN>`. Tanpa `CRON_TOKEN`, endpoint ini mati (404). Pemeriksaan kesehatan termasuk koneksi database tersedia di `GET /health` (sedangkan `/up` hanya mengecek aplikasi).

---

## Akun Bawaan (Seeder)

Berlaku untuk lokal/testing (kata sandi semua akun: `password`). **Di produksi** `UserSeeder` hanya membuat akun pemilik dan admin pada instalasi pertama (tabel `users` masih kosong) memakai `SEED_DEFAULT_PASSWORD` (wajib diisi), dengan email dari `SEED_OWNER_EMAIL`/`SEED_ADMIN_EMAIL`. Restart atau deploy berikutnya tidak menghidupkan kembali akun seeder. Akun pelanggan contoh hanya dibuat bila `SEED_DEMO=true`.

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
php artisan test                                   # seluruh suite (246 test, 3 uji concurrency dilewati)
php artisan test --filter=RevisiKlienTest          # regresi revisi klien
php artisan test tests/Unit/KetersediaanServiceTest.php
php artisan test tests/Feature/PengembalianDendaTest.php
./vendor/bin/pint --test                           # gaya kode
./vendor/bin/phpstan analyse --memory-limit=1G     # Larastan level 5 (baseline: phpstan-baseline.neon)
```

**Uji concurrency** (bukti anti double-booking): 10 proses PHP terpisah memesan stok yang sama bersamaan terhadap database sungguhan. Tidak berjalan pada SQLite `:memory:`; CI menjalankannya pada PostgreSQL dan MySQL.
```bash
RUN_CONCURRENCY_TESTS=1 DB_CONNECTION=pgsql DB_HOST=127.0.0.1 DB_DATABASE=blntrk_test DB_USERNAME=... DB_PASSWORD=... \
  php artisan test --testsuite=Concurrency
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
- Ekspor laporan berformat **CSV** (UTF-8) dan dapat dibuka langsung di Excel (rute `laporan.csv`).
- **Diskon paket bundling otomatis**: bila isi pesanan/keranjang memenuhi suatu paket aktif (termasuk kelipatannya), selisih harga eceran dan harga paket dipotong dan dicatat pada `pesanan.diskon_paket`. Denda keterlambatan memakai tarif harian setelah diskon.
- URL lama `/alat`, `/profil`, dan `/sesi` dialihkan (301) ke `/katalog`, `/profile`, dan `/sesi-login`.
- CI GitHub Actions (`.github/workflows/ci.yml`) menjalankan: Pint + Larastan, test SQLite dengan coverage (pcov), seluruh test pada **PostgreSQL**, serta uji concurrency pada PostgreSQL dan MySQL.
- Nilai di `phpstan-baseline.neon` adalah temuan lama (sebagian besar tipe cast/relasi Eloquent yang belum dibaca Larastan); CI hanya gagal untuk temuan baru. Kurangi baseline secara bertahap.

### Deployment (Render)

- `render.yaml` memakai **PostgreSQL terkelola** (`blntrk-db`) sehingga data tidak hilang saat redeploy. SQLite hanya untuk lokal/percobaan.
- Isi di dashboard Render: `APP_KEY` (`php artisan key:generate --show`) dan `SEED_DEFAULT_PASSWORD` (kuat; dipakai sekali saat instalasi pertama).
- **Email**: `MAIL_MAILER=log` (bawaan) berarti email tidak terkirim, sehingga tautan "Lupa kata sandi" disembunyikan dan reset dilakukan admin/pemilik. Untuk mengaktifkannya set `MAIL_MAILER=smtp` dan isi `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` (Brevo/Mailgun/Resend SMTP). Pengingat otomatis ikut aktif.
- **Foto alat permanen**: set `FOTO_DISK=s3` dan `AWS_*` (mis. bucket Cloudflare R2: `AWS_ENDPOINT`, `AWS_URL` publik, `AWS_USE_PATH_STYLE_ENDPOINT=true`). **Bukti transfer** memakai `BUKTI_DISK` (bawaan `local`, privat); di Render isi `s3` dengan bucket **privat** agar tidak hilang saat redeploy.
- Health check Render memakai `/health` (termasuk koneksi database); scheduler dari luar lewat `CRON_TOKEN` (lihat bagian penjadwalan).
- Hanya ada satu Dockerfile (`blntrk-app/Dockerfile`, dengan `pdo_pgsql`); `render.yaml` memakai `rootDir: blntrk-app`.
- `SEED_DEMO=true` memuat katalog alat, paket, dan transaksi contoh (hanya untuk demo). Biarkan `false` untuk data nyata.
- `TRUSTED_PROXIES` berisi IP/CIDR proxy dipisah koma; `*` hanya untuk platform berIP dinamis seperti Render.
- Format kode: `./vendor/bin/pint`.

---

## Lisensi dan Hak Cipta

Aplikasi dan dokumentasi ini dikembangkan untuk penyelesaian Tugas Akhir / Skripsi Program Studi Informatika / Sistem Informasi. Hak cipta dilindungi undang-undang.
