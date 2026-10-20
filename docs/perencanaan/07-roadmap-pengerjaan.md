# 7. Urutan Pengerjaan (Roadmap)

Prinsip: bangun **jalur sewa inti** lebih dulu (alat → pesanan → bayar → ambil → kembali), baru fitur pendukung. Setiap langkah harus dapat didemonstrasikan.

## Prioritas

| Prioritas | Isi | Keterangan |
|---|---|---|
| P1 (inti) | Autentikasi dan role, master alat, ketersediaan, pemesanan, pembayaran, pengambilan, pengembalian dan denda | Tanpa ini sistem tidak berguna |
| P2 | Riwayat, laporan PDF, dashboard, pembatalan otomatis, jaminan | Dibutuhkan skripsi (F07, F08) |
| P3 (bersyarat) | Perawatan, Excel, tautan WA, log aktivitas | Hanya bila waktu tersisa |
| P4 | Pengujian dan penulisan | Dijalankan paralel sejak iterasi 2 |

## Langkah Demi Langkah

| No | Langkah | Perintah/Isi | Hasil | Minggu |
|---|---|---|---|---|
| 1 | Persiapan lingkungan | Instal XAMPP (PHP 8.2+), Composer, Node.js, Git, VS Code | Lingkungan siap | 8 |
| 2 | Buat proyek | `composer create-project laravel/laravel blntrk-app`; atur `.env` (DB `blntrk_outdoor`); `git init` | Proyek berjalan di `localhost` | 8 |
| 3 | Autentikasi | `composer require laravel/breeze --dev`; `php artisan breeze:install blade` | Login, register, logout | 9 |
| 4 | Migration | Buat 15 migration sesuai urutan dokumen 04, lalu `php artisan migrate` | Skema database terbentuk | 9 |
| 5 | Model dan relasi | Buat Model, `$fillable`, relasi, SoftDeletes, Enum status | Relasi teruji lewat `tinker` | 9 |
| 6 | Seeder | Role, user, pengaturan, kategori, alat | `php artisan migrate:fresh --seed` berhasil | 9 |
| 7 | Middleware role | `CekRole` dan layout per role (Pelanggan, Admin, Pemilik) | Setiap role masuk ke dashboard masing-masing, akses silang 403 | 10 |
| 8 | CRUD master data | Kategori, alat (upload foto), pelanggan, user | Admin dapat mengelola data | 10 |
| 9 | Katalog publik | Daftar alat, filter, detail | Pelanggan melihat alat | 11 |
| 10 | **KetersediaanService** | Hitung ketersediaan per rentang tanggal, endpoint JSON, uji unit | Aturan BR-3 lolos uji | 11 |
| 11 | **Pemesanan** | Form pesanan, `PesananService::buat` (transaksi, lock, snapshot harga, resi), halaman resi | Pelanggan membuat pesanan | 11–12 |
| 12 | **Konfirmasi admin** | Daftar pesanan, bayar, pengambilan + jaminan, pembatalan, state machine | Status Menunggu → Disewa | 12 |
| 13 | Pesanan walk-in | Form admin | Admin mencatat pelanggan datang langsung | 12 |
| 14 | **Pengembalian** | Form kondisi, hitung denda, update stok, kembalikan jaminan | Status Dikembalikan, denda tercatat | 13 |
| 15 | Scheduler | Command status terlambat dan batal otomatis | Status berubah sesuai aturan | 13 |
| 16 | Denda dan riwayat | Halaman denda (lunas), riwayat pelanggan dan admin | F07 selesai | 14 |
| 17 | Laporan | Query laporan, tampilan, PDF (DomPDF), resi PDF | F08 selesai | 14 |
| 18 | Dashboard | Kartu ringkasan, Chart.js | Dashboard Admin dan Pemilik | 14 |
| 19 | **Titik evaluasi** | Semua P1 dan P2 selesai? Jika belum, hentikan P3 | Keputusan ruang lingkup | 14 |
| 20 | Fitur bersyarat | Perawatan, Excel, WA, log aktivitas (sesuai waktu) | Tambahan | 14–15 |
| 21 | Black box testing | Jalankan BB-01 sampai BB-74, catat hasil, perbaiki bug | Tabel hasil pengujian | 15 |
| 22 | Usability testing | Kuesioner SUS ke responden | Skor SUS | 15–16 |
| 23 | Finalisasi | Data demo, `.env.example`, README, dump database, tangkapan layar untuk skripsi | Siap sidang | 16 |

## Aturan Kerja
- Satu cabang git per fitur dan commit kecil teratur (bukti progres untuk pembimbing).
- Setelah langkah 12 dan 14, demokan ke pembimbing.
- Jika tertinggal lebih dari 1 minggu, potong P3, bukan P1.
- Simpan tangkapan layar dari awal untuk Bab IV.
- Cadangkan database (`mysqldump`) setiap akhir iterasi.

## Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Ketersediaan rentang tanggal terlalu rumit | Pakai algoritma harian sederhana (dokumen 04), uji unit, jangan optimasi dini |
| Pemilik toko sulit dijumpai untuk validasi | Jadwalkan wawancara awal, kirim ringkasan aturan bisnis lewat WhatsApp untuk disetujui |
| Fitur melebar | Patuhi tabel ruang lingkup (dokumen 01) dan catat ide baru sebagai saran pengembangan |
| Scheduler tidak jalan di XAMPP | Pengecekan status saat halaman dibuka plus `schedule:work` saat demo |
| Responden SUS sedikit | Targetkan pelanggan nyata toko dengan kode QR dan Google Form |
