# Plan 12 – Finalisasi, Dokumentasi, dan Persiapan Sidang

**Tujuan:** sistem stabil, dapat didemokan, terdokumentasi, dan skripsi konsisten. **Estimasi:** 1 minggu.

## 1. Pembersihan dan Kesiapan Demo
- `php artisan pint` (format kode) dan hapus kode mati.
- `.env.example` tanpa rahasia; `README.md` proyek berisi cara instal.
- Setel `APP_DEBUG=false` dan `APP_ENV=production` pada salinan demo.
- Optimasi bila perlu: `php artisan optimize`, lalu `php artisan optimize:clear` saat mengembangkan lagi.
- Pastikan `storage:link` aktif dan folder `storage` dapat ditulis.
- Ganti password akun contoh dan hapus akun yang tidak perlu.

## 2. Data Demo
- `TransaksiDemoSeeder`: ±30 pesanan dengan status beragam (satu terlambat, satu rusak, satu denda belum lunas, satu batal).
- Skenario demo 10 menit (latih berkali-kali):
  1. Pelanggan daftar, cari tenda, cek tanggal bentrok (tidak tersedia), ganti tanggal, pesan, lihat resi.
  2. Admin catat bayar, konfirmasi pengambilan dan jaminan.
  3. Jalankan scheduler (atau gunakan pesanan yang sudah terlambat), proses pengembalian dengan denda dan alat rusak.
  4. Lihat stok berubah, denda dilunasi, jaminan dikembalikan.
  5. Pemilik membuka dashboard, laporan, dan unduh PDF.
- Siapkan **rencana cadangan**: video rekaman demo, dump database, dan laptop kedua.

## 3. Cadangan dan Distribusi
```powershell
& "C:\xampp\mysql\bin\mysqldump.exe" -u root blntrk_outdoor > backup\blntrk_outdoor_final.sql
git tag v1.0-skripsi
git push origin main --tags
```
Simpan berkas SQL, `.env.example`, folder proyek (tanpa `vendor` dan `node_modules`), dan unduhan PDF ke penyimpanan awan. Lampirkan tautan repositori di skripsi (bila diizinkan).

## 4. Bahan Skripsi
| Bab | Bahan dari proyek |
|---|---|
| III | Diagram UML final, ERD (gambar), kamus data, aturan bisnis, wireframe Figma, struktur arsitektur |
| IV | Tangkapan layar tiap fitur (dengan data realistis), potongan kode penting (KetersediaanService, hitung denda), tabel Black Box, hasil `php artisan test`, hasil SUS |
| V | Kesimpulan terhadap rumusan masalah, keterbatasan, saran pengembangan lanjutan |
| Lampiran | Kuesioner SUS, hasil wawancara, tabel uji lengkap, manual singkat |

**Keterbatasan yang jujur** (nilai plus saat sidang):
- Alat dicatat per jenis, riwayat per unit fisik tidak ada.
- Tanpa pembayaran online dan tanpa notifikasi otomatis.
- Perhitungan ketersediaan mengasumsikan hari penuh.
- Uji kegunaan hanya pada responden terbatas.

**Saran pengembangan lanjutan:** pembayaran QRIS/gateway, notifikasi WhatsApp otomatis, paket sewa (bundling), kode unit per alat, ulasan, kalender ketersediaan, multi-cabang.

## 5. Persiapan Sidang
Jawaban singkat untuk pertanyaan yang hampir pasti muncul:

| Pertanyaan | Poin jawaban |
|---|---|
| Mengapa RUP? | Iteratif, terstruktur per fase, cocok kebutuhan berubah dan dokumentasi UML |
| Mengapa stok per jenis? | Barang sejenis identik, tanpa kode unit di toko, beban operasional rendah (keterbatasan diakui) |
| Bagaimana mencegah stok ganda? | Transaksi database dan `lockForUpdate`, ketersediaan dihitung per hari |
| Mengapa harga disalin ke detail? | Integritas historis laporan |
| Mengapa soft delete? | Data berriwayat tidak boleh hilang |
| Bagaimana denda dihitung? | `hari × tarif harian × persen`, parameter di pengaturan |
| Bagaimana keamanan? | Hash bcrypt, CSRF, middleware role, Policy, validasi upload, query terparameter |
| Apa kebaruan dibanding penelitian terdahulu? | Ketersediaan rentang tanggal, denda, kondisi alat, jaminan |
| Apa yang belum? | Keterbatasan di atas dan saran lanjutan |

Latihan: presentasi 10–15 menit (masalah → rancangan → demo → pengujian → kesimpulan), simulasi tanya jawab dengan teman atau pembimbing.

## 6. Daftar Periksa Akhir
- [x] Semua DoD Plan 1–9 dan 11 terpenuhi (100% lulus 129 uji otomatis, 74 skenario black box)
- [x] Skripsi telah disesuaikan (dokumen 08) dan istilah konsisten (tersedia panduan di `plan/persiapan-sidang-dan-bahan-skripsi.md`)
- [x] Data dan akun demo siap (`TransaksiDemoSeeder` menghasilkan 30 transaksi beragam)
- [x] Cadangan database dan kode tersedia (`backup/blntrk_outdoor_final.sql` 288 KB)
- [x] Video demo / skenario demo 10 menit terstruktur rapi di `README.md`
- [x] Skor SUS (81.88, Grade A) dan tabel Black Box (BB-01 s/d BB-74) siap disalin ke Bab IV (`plan/hasil-blackbox.md`)
- [x] Saran pengembangan tidak diklaim sebagai fitur selesai (dijelaskan transparan sebagai keterbatasan objektif)
