# Dokumen Perencanaan – Sistem Informasi Penyewaan Alat Outdoor (BLNTRK Outdoor)

Stack: Laravel 11/12 (PHP 8.2+), Blade, MySQL, XAMPP. Metode: RUP (Inception, Elaboration, Construction).

## Daftar Dokumen

| No | File | Isi |
|---|---|---|
| 1 | [01-evaluasi-kebutuhan.md](01-evaluasi-kebutuhan.md) | Gap analysis, usulan fitur, ruang lingkup MVP |
| 2 | [02-perencanaan-proyek.md](02-perencanaan-proyek.md) | Fitur final, NFR, aturan bisnis, status pesanan, jadwal RUP, sitemap |
| 3 | [03-perancangan-database.md](03-perancangan-database.md) | Entitas, relasi, normalisasi, keputusan desain, kamus data |
| 3a | [erd.mmd](erd.mmd) | ERD Mermaid |
| 3b | [erd.dbml](erd.dbml) | ERD DBML (dbdiagram.io) |
| 3c | [schema.sql](schema.sql) | DDL MySQL lengkap |
| 4 | [04-perancangan-laravel.md](04-perancangan-laravel.md) | MVC, model, migration, seeder, route, controller, middleware, validasi, package |
| 5 | [05-perancangan-antarmuka.md](05-perancangan-antarmuka.md) | Layar dan komponen untuk wireframe Figma |
| 6 | [06-rencana-pengujian.md](06-rencana-pengujian.md) | Black box testing dan SUS |
| 7 | [07-roadmap-pengerjaan.md](07-roadmap-pengerjaan.md) | Urutan pengerjaan |
| 8 | [08-cek-konsistensi-skripsi.md](08-cek-konsistensi-skripsi.md) | Hal yang harus disesuaikan di skripsi |

## Asumsi Eksplisit (konfirmasikan ke pemilik toko)

| Kode | Asumsi | Dampak |
|---|---|---|
| A1 | Alat dicatat **per jenis dengan stok**, bukan per unit fisik | Tabel `alat` memiliki `stok_total` |
| A2 | Satuan sewa adalah **hari** (24 jam) | Durasi = tanggal selesai − tanggal mulai (minimal 1) |
| A3 | Denda keterlambatan = **100%** tarif sewa harian per hari terlambat (dapat diubah di `pengaturan`) | Rumus denda |
| A4 | Jam jatuh tempo kembali default **12:00**, toleransi **60 menit** | `pengaturan` |
| A5 | Pesanan belum diambil sampai **23:59 pada tanggal mulai** dibatalkan | Batas pengambilan |
| A6 | Pembayaran sewa, denda, dan biaya kerusakan **manual di toko**, sistem hanya mencatat | Sesuai batasan masalah |
| A7 | Pelanggan walk-in (tanpa akun) dapat dicatat oleh Admin | Tabel `pelanggan` terpisah dari `users` |
| A8 | Hanya ada satu cabang toko | Tanpa tabel cabang |
| A9 | Data harga dan stok pada seeder hanyalah contoh | Ganti dengan data asli |
