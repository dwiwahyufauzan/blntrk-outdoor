# Rencana Implementasi – BLNTRK Outdoor

Folder ini berisi rencana teknis per tahap, dari instalasi sampai finalisasi. Dokumen perancangan ada di `docs/perencanaan/`. Kerjakan **berurutan**. Setiap plan memiliki langkah, kode acuan, dan kriteria selesai (Definition of Done).

| Plan | File | Fokus | Minggu | Prioritas |
|---|---|---|---|---|
| 1 | [plan1-instalasi-lingkungan.md](plan1-instalasi-lingkungan.md) | XAMPP, Composer, Laravel, Git, konfigurasi | 8 | P1 |
| 2 | [plan2-database-migration-seeder.md](plan2-database-migration-seeder.md) | Migration, Model, Seeder | 9 | P1 |
| 3 | [plan3-autentikasi-role.md](plan3-autentikasi-role.md) | Breeze, role, middleware, layout | 9–10 | P1 |
| 4 | [plan4-master-data.md](plan4-master-data.md) | CRUD kategori, alat, pelanggan, user | 10 | P1 |
| 5 | [plan5-katalog-ketersediaan.md](plan5-katalog-ketersediaan.md) | Katalog dan KetersediaanService | 11 | P1 |
| 6 | [plan6-pemesanan.md](plan6-pemesanan.md) | Pemesanan pelanggan dan walk-in | 11–12 | P1 |
| 7 | [plan7-konfirmasi-pesanan.md](plan7-konfirmasi-pesanan.md) | Pembayaran, pengambilan, jaminan, pembatalan | 12 | P1 |
| 8 | [plan8-pengembalian-denda.md](plan8-pengembalian-denda.md) | Pengembalian, kondisi alat, denda, scheduler | 13 | P1 |
| 9 | [plan9-riwayat-laporan-dashboard.md](plan9-riwayat-laporan-dashboard.md) | Riwayat, laporan PDF, dashboard | 14 | P2 |
| 10 | [plan10-fitur-bersyarat.md](plan10-fitur-bersyarat.md) | Perawatan, Excel, WhatsApp, log aktivitas | 14–15 | P3 |
| 11 | [plan11-pengujian.md](plan11-pengujian.md) | Uji otomatis, Black Box, SUS | 15–16 | P1 |
| 12 | [plan12-finalisasi-dokumentasi.md](plan12-finalisasi-dokumentasi.md) | Data demo, backup, bahan skripsi | 16 | P1 |

## Konvensi
- Nama folder proyek Laravel: `blntrk-app` (di dalam folder ini atau repositori terpisah; lihat plan 1).
- Bahasa penamaan: tabel, model, dan route memakai bahasa Indonesia, sesuai ERD.
- Satu cabang git per plan: `plan2-database`, `plan3-auth`, dan seterusnya. Gabungkan ke `main` setelah DoD terpenuhi.
- Perintah memakai **PowerShell** (Windows).
- Setiap akhir plan: commit, cadangkan database, dan perbarui centang di file plan.

## Aturan Prioritas
Jika waktu mepet, potong Plan 10 lebih dulu, lalu fitur P2 non-esensial (Excel, grafik). **Plan 1–8 dan 11 tidak boleh dipotong.**
