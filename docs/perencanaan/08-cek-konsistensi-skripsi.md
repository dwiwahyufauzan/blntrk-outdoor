# 8. Cek Konsistensi dengan Skripsi

Daftar penyesuaian pada draf `Draft Skripsi Rafly.docx` agar selaras dengan rancangan baru.

## A. Perbaikan Umum

| No | Lokasi | Masalah | Perbaikan |
|---|---|---|---|
| 1 | Subbab 1.6 | Menyebut Transition | Hapus Transition, tulis hanya Inception, Elaboration, Construction |
| 2 | Judul | Sampul dan Latar Belakang berbeda | Samakan: "Sistem Informasi Penyewaan Alat Outdoor Berbasis Web (Studi Kasus: BLNTRK Outdoor)" |
| 3 | 1.3 Batasan Masalah | Hanya 2 butir | Tambahkan: sewa per hari, alat per jenis (stok), notifikasi tautan WhatsApp, satu cabang (lihat dokumen 01) |
| 4 | 1.5 Manfaat | Menyebut "pembayaran" | Ubah menjadi "pencatatan status pembayaran" |
| 5 | Rumusan masalah dan tujuan | Belum mencakup ketersediaan per tanggal, denda, dan kondisi alat | Tambahkan agar fitur terakomodasi |
| 6 | Tabel 1 Workflow RUP | Kolom Transition (bila ada) | Selaraskan dengan tabel di dokumen 02 |
| 7 | Bab II | Belum ada landasan teori ERD, normalisasi, MVC, Black Box, SUS | Tambahkan subbab teori |
| 8 | Penelitian terdahulu (Tabel 2) | – | Bandingkan: fitur ketersediaan rentang tanggal, denda, kondisi alat sebagai kebaruan |
| 9 | Sistematika/Bab III | Struktur alat dan metode | Tambahkan 3.2.x Perancangan Database dan Antarmuka |
| 10 | Caption Gambar 19 dan 20 | Dobel "Kelola Pengembalian" | Gambar 20 = Activity Kelola Laporan |
| 11 | Daftar Simbol dan Lampiran | Masih kosong | Isi sesuai notasi UML dan lampiran (kuesioner, wawancara, tabel uji) |

## B. Kebutuhan Fungsional (Tabel 6) dan Non-Fungsional (Tabel 7)

Ganti dengan daftar fitur final (dokumen 02, bagian 2a). Fitur baru: kategori alat, harga per hari, cek ketersediaan per tanggal, pesanan walk-in, pencatatan pembayaran, jaminan identitas, pembatalan, denda, kondisi alat, dashboard, laporan PDF. Tambahkan NF05 (responsif), NF07 (konsistensi stok), dan NF09 (privasi).

## C. Use Case

| Use case lama | Perubahan | Use case baru |
|---|---|---|
| Login, Daftar Akun | Tetap | – |
| Kelola Data Alat | Aktor hanya Admin; hapus Pelanggan dari pre-condition; tambah kategori, harga per hari, stok, foto | Kelola Kategori Alat, Kelola Perawatan Alat |
| Kelola Data User | Perbaiki "mengubah/mengedit"; pembatasan role | Kelola Data Pelanggan (isi celah F02) |
| Pemesanan Sewa Alat | Pelanggan dan Admin (walk-in); tanggal mulai dan selesai; cek ketersediaan | **Cek Ketersediaan Alat** (include), **Buat Pesanan Walk-in** |
| Konfirmasi Pesanan Penyewaan | **Pecah**: hanya Admin | **Catat Pembayaran**, **Konfirmasi Pengambilan** (include Catat Jaminan), **Batalkan Pesanan** (Pelanggan, Admin) |
| Kelola Pengembalian | Tambah kondisi per alat, denda, jaminan | **Hitung Denda** (include), **Catat Pelunasan Denda**, **Kembalikan Jaminan** |
| Kelola Riwayat Penyewaan | Tetap | – |
| Kelola Laporan | Jenis laporan dan ekspor PDF | **Cetak Laporan** (extend) |
| – | Baru | **Lihat Dashboard**, **Kelola Pengaturan** (Pemilik), **Lihat Log Aktivitas** (bersyarat) |

Perbarui: Gambar 4 (diagram umum), Tabel 8 (definisi use case), dan skenario Tabel 9–17. Untuk Tabel 13, hapus aktor Pelanggan dari Konfirmasi (skenario hanya Admin). Skenario baru ditambahkan.

## D. Activity Diagram

| Diagram | Perubahan |
|---|---|
| Sewa Alat | Tambah langkah cek ketersediaan per tanggal dan keputusan "tersedia?" dengan cabang tidak tersedia |
| Konfirmasi Pesanan | Pisah menjadi Pembayaran, Pengambilan (dengan jaminan), Pembatalan |
| Kelola Pengembalian | Tambah percabangan terlambat (hitung denda) dan kondisi alat (rusak dan hilang, update stok), pengembalian jaminan |
| Kelola Laporan | Perbaiki caption; tambah pilih jenis dan periode, cetak PDF |
| Kelola Data Alat | Tambah kategori dan upload foto |
| Baru | Batalkan Pesanan, Catat Pelunasan Denda, Pesanan Walk-in, Proses Otomatis (scheduler, opsional) |
| Login, Daftar Akun, Kelola User, Riwayat | Cek konsistensi saja (tambah validasi akun nonaktif) |

## E. Robustness Diagram (Gambar 23)

- Tambah **Entity** baru: Kategori, Pesanan Detail, Pembayaran, Jaminan, Pengembalian, Pengembalian Detail, Denda, Pengaturan, Log Aktivitas.
- Tambah **Control**: Cek Ketersediaan, Hitung Total, Hitung Denda, Perbarui Status, Catat Log.
- Tambah **Boundary**: Form Pembayaran, Form Pengambilan, Form Pengembalian, Halaman Dashboard, Halaman Denda, Halaman Pengaturan.
- Disarankan memecah satu robustness diagram besar menjadi satu per use case utama agar terbaca.

## F. Sequence Diagram (belum ada di Bab III padahal tercantum di Bab II)

Buat minimal untuk: (1) Pemesanan Sewa Alat, (2) Catat Pembayaran dan Konfirmasi Pengambilan, (3) Proses Pengembalian dan Denda, (4) Cetak Laporan. Lifeline: Aktor, Boundary (View), Controller, Service, Model/Database.

## G. Class Diagram

Selaraskan dengan ERD (dokumen 03) dan model Eloquent (dokumen 04):
- Kelas: Role, User, Pelanggan, KategoriAlat, Alat, Pesanan, PesananDetail, Pembayaran, Jaminan, Pengembalian, PengembalianDetail, Denda, Pengaturan, LogAktivitas.
- Multiplicity mengikuti tabel relasi di dokumen 03 bagian 3b.
- Operasi penting: `Alat.hitungKetersediaan()`, `Pesanan.hitungTotal()`, `Pesanan.ubahStatus()`, `Pengembalian.hitungDenda()`.
- Atribut dan tipe data dicocokkan dengan kamus data.
- Satukan penamaan istilah di seluruh dokumen (misalnya "Pesanan", bukan campur dengan "Pemesanan" dan "Transaksi").

## H. Penambahan Bab III dan IV

| Bagian | Isi baru |
|---|---|
| 3.x Aturan Bisnis | Ringkasan BR-1 sampai BR-8 dan diagram status pesanan |
| 3.x Perancangan Database | ERD, kamus data, normalisasi, keputusan desain |
| 3.x Perancangan Antarmuka | Wireframe Figma |
| 3.x Perancangan Arsitektur | MVC Laravel dan struktur folder |
| Bab IV | Implementasi, tangkapan layar, tabel Black Box Testing, hasil SUS |
| Bab V | Kesimpulan, keterbatasan (stok per jenis, tanpa pembayaran online), saran pengembangan lanjutan (T16 sampai T20) |

## I. Daftar Periksa Akhir Sebelum Sidang

- [ ] Batasan masalah mencakup semua keputusan desain (hari, stok, tanpa online, notifikasi manual).
- [ ] Setiap kebutuhan fungsional memiliki use case, activity, dan baris pengujian (traceability matrix).
- [ ] Istilah status pesanan identik di seluruh bab.
- [ ] Nama tabel pada ERD sama dengan class diagram dan kode.
- [ ] Hasil Black Box dan SUS selaras dengan fitur yang benar-benar dibangun (jangan menguji fitur yang tidak ada).
- [ ] Saran pengembangan lanjutan tidak dilaporkan sebagai fitur yang sudah selesai.
- [ ] Siap menjelaskan: mengapa stok per jenis, bagaimana ketersediaan dihitung, mengapa snapshot harga, mengapa soft delete.

### Traceability Matrix (contoh format)

| Kebutuhan | Use case | Activity | Tabel DB | Uji |
|---|---|---|---|---|
| F04 Pemesanan | Pemesanan Sewa Alat, Cek Ketersediaan | Sewa Alat | pesanan, pesanan_detail | BB-28 sampai BB-40 |
| F05 Konfirmasi | Catat Pembayaran, Konfirmasi Pengambilan, Batalkan | Konfirmasi Pesanan | pembayaran, jaminan | BB-42 sampai BB-51 |
| F06 Pengembalian | Kelola Pengembalian, Hitung Denda | Kelola Pengembalian | pengembalian, pengembalian_detail, denda | BB-52 sampai BB-62 |
| F08 Laporan | Kelola Laporan | Kelola Laporan | semua transaksi | BB-66 sampai BB-69 |
