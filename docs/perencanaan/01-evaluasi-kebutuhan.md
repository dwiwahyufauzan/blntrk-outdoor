# 1. Evaluasi Kebutuhan dan Usulan Penyempurnaan

## 1a. Analisis Celah (Gap)

| No | Temuan | Sumber | Dampak | Usulan |
|---|---|---|---|---|
| G1 | Ketersediaan dicek dari stok saat ini, bukan rentang tanggal | Skenario Pemesanan | Pesanan bentrok, atau alat terlihat habis padahal akan kembali | Cek ketersediaan per rentang tanggal |
| G2 | Harga hanya "total harga", tanpa tarif per hari | F04, Tabel 16 | Total tidak dapat diaudit | Harga per hari, disimpan sebagai snapshot di detail pesanan |
| G3 | Jaminan identitas tidak tercatat di sistem | Alur AS-IS | Tidak ada bukti jaminan | Tabel jaminan |
| G4 | Tidak ada aturan keterlambatan dan denda | F06 | Keterlambatan tidak terpantau | Status Terlambat dan denda |
| G5 | Kondisi alat hanya teks bebas dan tidak memengaruhi stok | Skenario Pengembalian | Alat rusak tetap dianggap tersedia | Kondisi baku, biaya kerusakan, stok perawatan |
| G6 | Pesanan tidak diambil menahan stok tanpa batas | Skenario Pemesanan | Stok terkunci | Batas waktu pengambilan |
| G7 | Tidak ada pembatalan dan status Dibatalkan | Seluruh use case | Pesanan tidak bisa ditutup | Use case Batalkan Pesanan |
| G8 | Tidak ada kategori atau variasi alat | F02 | Pencarian dan stok tidak akurat | Kategori dan varian |
| G9 | Laporan tidak dirinci jenis, periode, dan format | Skenario Laporan | Tidak bisa dicetak | Laporan terperinci dan PDF |
| G10 | Tidak ada dashboard | – | Pemilik harus membuka laporan | Dashboard ringkas |
| G11 | Pelanggan walk-in tidak tertangani | AS-IS (60+ pelanggan/hari) | Admin tidak bisa mencatat langsung | Pesanan oleh Admin |
| G12 | Tidak ada log aktivitas | – | Perubahan tidak dapat ditelusuri | Log aktivitas |

### Inkonsistensi yang ditemukan di draf skripsi
1. Subbab 1.6 menyebut tahap **Transition**, sedangkan batasan masalah hanya sampai Construction.
2. Judul sampul ("Studi Kasus: BLNTRK Outdoor") berbeda dengan judul di Latar Belakang ("Pada Toko BLNTRK").
3. Use case Konfirmasi Pesanan memuat aktor Pelanggan dan Admin, tetapi skenarionya hanya Admin. Pembayaran dan pengambilan digabung.
4. Kelola Data Alat mencantumkan Pelanggan pada pre-condition, padahal Pelanggan hanya melihat katalog.
5. Kelola Data User tertulis "mengubah dan mengedit" (sama artinya), skenario memuat hapus.
6. F02 mencakup Kelola Data Pelanggan, tetapi tidak ada use case-nya.
7. Pemesanan memakai "tanggal sewa", F04 menyebut "durasi/tanggal".
8. Caption Gambar 19 dan 20 sama ("Kelola Pengembalian"); Gambar 20 sebenarnya Kelola Laporan.
9. Batasan masalah baru dua butir.
10. Manfaat penelitian menyebut "pembayaran", padahal sistem hanya mencatat status pembayaran.

## 1b. Usulan Fitur

Kesulitan: R = rendah, S = sedang, T = tinggi.

| Kode | Fitur | Masalah yang diselesaikan | Aktor | Prioritas | Kesulitan | Konflik "tanpa bayar online"? |
|---|---|---|---|---|---|---|
| T01 | Kategori dan varian alat | G8 | Admin, Pelanggan | Wajib | R | Tidak |
| T02 | Harga sewa per hari | G2 | Admin | Wajib | R | Tidak |
| T03 | Ketersediaan berbasis rentang tanggal | G1 | Pelanggan, Sistem | Wajib | T | Tidak |
| T04 | Pencatatan jaminan identitas | G3 | Admin | Wajib | R | Tidak |
| T05 | Denda keterlambatan | G4 | Sistem, Admin | Wajib | S | Tidak (dibayar manual) |
| T06 | Kondisi alat saat kembali dan stok rusak | G5 | Admin | Wajib | S | Tidak |
| T07 | Pembatalan dan batas waktu pengambilan | G6, G7 | Pelanggan, Admin, Sistem | Wajib | S | Tidak |
| T08 | Pesanan walk-in oleh Admin | G11 | Admin | Wajib | R | Tidak |
| T09 | Dashboard Admin dan Pemilik | G10 | Admin, Pemilik | Wajib | S | Tidak |
| T10 | Cetak laporan PDF | G9 | Admin, Pemilik | Wajib | S | Tidak |
| T11 | Biaya kerusakan atau kehilangan | G5 | Admin | Sebaiknya | S | Tidak (dibayar manual) |
| T12 | Status perawatan alat | G5 | Admin | Sebaiknya | S | Tidak |
| T13 | Ekspor Excel | G9 | Admin, Pemilik | Sebaiknya | R | Tidak |
| T14 | Tautan WhatsApp (wa.me) | Informasi cepat | Admin, Pelanggan | Sebaiknya | R | Tidak |
| T15 | Log aktivitas | G12 | Admin, Pemilik | Sebaiknya | S | Tidak |
| T16 | Paket sewa (bundling) | Paket siap pakai | Admin, Pelanggan | Opsional | T | Tidak |
| T17 | Notifikasi WhatsApp otomatis (API) | Pengingat otomatis | Sistem | Opsional | T | Tidak, tetapi bergantung pihak ketiga |
| T18 | Pembayaran online (QRIS/gateway/DP) | Kemudahan bayar | Pelanggan | Opsional | T | **Ya, bertentangan** |
| T19 | Ulasan dan rating | Kepercayaan | Pelanggan | Opsional | S | Tidak |
| T20 | Kalender visual ketersediaan | UX | Pelanggan | Opsional | T | Tidak |

## 1c. Ruang Lingkup

| Kategori | Fitur | Alasan |
|---|---|---|
| **MVP (masuk skripsi)** | F01–F09 yang disempurnakan + T01–T10 | Menutup G1–G11, realistis untuk satu orang dalam 3–4 bulan |
| **MVP bersyarat** | T11–T15 | Dikerjakan jika waktu tersisa setelah fitur inti dan pengujian awal |
| **Saran pengembangan lanjutan** | T16–T20 | Tinggi usaha atau bertentangan dengan batasan |

## 1d. Penanda Pertentangan Batasan

- **T18 bertentangan** dengan batasan pertama. Tulis hanya sebagai saran pengembangan lanjutan.
- **T05 dan T11 tidak bertentangan.** Denda dan biaya kerusakan hanya dicatat sebagai tagihan lalu dibayar manual di toko.
- **T17** tidak bertentangan, tetapi bergantung pada layanan pihak ketiga. MVP memakai T14 (tautan wa.me).

## Usulan Batasan Masalah (Bab I)
1. Tidak ada pembayaran online. Pembayaran sewa, denda, dan biaya kerusakan dilakukan manual di toko dan hanya dicatat statusnya.
2. Satuan sewa adalah hari. Alat dicatat per jenis dengan stok, bukan per unit fisik.
3. Pengembangan sampai tahap Construction (tanpa Transition).
4. Notifikasi berupa tautan WhatsApp manual, bukan otomatis.
