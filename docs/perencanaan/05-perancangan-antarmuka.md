# 5. Perancangan Antarmuka (Panduan Wireframe Figma)

Prinsip umum: mobile first untuk Pelanggan, desktop first untuk Admin dan Pemilik. Warna status konsisten:
Menunggu (kuning), Dibayar (biru), Disewa (hijau), Terlambat (merah), Dikembalikan (abu-abu tua), Dibatalkan (abu-abu).

Frame Figma yang disarankan: Mobile 390 px untuk layar pelanggan, Desktop 1440 px untuk Admin dan Pemilik.
Buat komponen reusable lebih dulu: Navbar, Sidebar, Badge Status, Kartu Alat, Tabel, Form Field, Modal Konfirmasi, Alert, Pagination.

## Pelanggan

| ID | Layar | Komponen |
|---|---|---|
| P01 | Beranda | Navbar (logo, Katalog, Masuk/Daftar), hero + tombol "Sewa Sekarang", kategori alat, alat populer, info jam buka 24 jam dan lokasi, footer |
| P02 | Login | Email, password, tombol Masuk, tautan Daftar |
| P03 | Daftar Akun | Nama, email, no HP, alamat, password, konfirmasi password, tombol Daftar, pesan validasi |
| P04 | Katalog Alat | Pencarian, filter kategori, grid Kartu Alat (foto, nama, varian, harga/hari, badge stok), pagination |
| P05 | Detail Alat | Foto, deskripsi, harga, pemilih tanggal mulai dan selesai, indikator ketersediaan real-time, jumlah, tombol "Tambah ke Pesanan" |
| P06 | Form Pesanan | Daftar alat terpilih (ubah jumlah, hapus), tanggal mulai dan selesai, durasi otomatis, ringkasan harga per alat dan total, catatan syarat (jaminan identitas dan pembayaran di toko), tombol Pesan |
| P07 | Detail dan Resi Pesanan | Nomor resi (menonjol), badge status, tabel alat, tanggal, total, batas pengambilan, instruksi (bawa KTP, bayar di toko), tombol Batalkan (saat Menunggu), tombol Hubungi Toko via WhatsApp, tombol Cetak/Unduh |
| P08 | Riwayat Pesanan | Filter status, daftar kartu (resi, tanggal, total, badge), pagination |
| P09 | Profil | Edit nama, no HP, alamat, ganti password |
| P10 | Halaman Error 403/404 | Pesan dan tombol kembali |

## Admin

| ID | Layar | Komponen |
|---|---|---|
| A01 | Dashboard | Kartu ringkasan (pesanan menunggu, perlu diambil hari ini, sedang disewa, terlambat, denda belum lunas), daftar "Perlu Tindakan Hari Ini", grafik penyewaan 7 hari |
| A02 | Daftar Alat | Tombol Tambah, pencarian, filter kategori, tabel (foto, kode, nama, varian, harga, stok, perawatan, status aktif), aksi Ubah/Hapus |
| A03 | Form Alat | Kategori, kode, nama, varian, deskripsi, harga, stok, upload foto dengan pratinjau, tombol Simpan |
| A04 | Kategori | Tabel dan modal tambah/ubah |
| A05 | Perawatan Alat | Tabel alat dengan stok perawatan > 0, tombol "Tandai Selesai Diperbaiki" (jumlah) |
| A06 | Data Pelanggan | Pencarian, tabel (nama, HP, jenis: akun/walk-in, jumlah pesanan), tambah/ubah/hapus, riwayat |
| A07 | Data User | Tabel (nama, email, role, status), form tambah/ubah, aktif/nonaktif, reset password |
| A08 | Daftar Pesanan | Tab status, pencarian resi atau nama, tabel (resi, pelanggan, tanggal, total, badge, aksi) |
| A09 | Pesanan Walk-in | Pilih atau tambah pelanggan, pilih tanggal, pilih alat dengan indikator ketersediaan, ringkasan total, tombol Simpan |
| A10 | Detail Pesanan | Info pelanggan (tautan WA), tabel alat, status timeline, panel aksi sesuai status: (Menunggu) Catat Pembayaran / Batalkan; (Dibayar) Konfirmasi Pengambilan + form jaminan; (Disewa/Terlambat) Proses Pengembalian; riwayat pembayaran, jaminan, denda, tombol cetak resi |
| A11 | Modal Catat Pembayaran | Jumlah (terisi total), metode, catatan |
| A12 | Form Pengambilan | Jenis identitas, 4 digit terakhir nomor, keterangan, tombol Konfirmasi |
| A13 | Form Pengembalian | Tanggal dan jam kembali, tabel alat dengan input jumlah baik, rusak ringan, rusak berat, hilang, biaya kerusakan, ringkasan denda keterlambatan otomatis, total tagihan, checklist "Jaminan dikembalikan", tombol Simpan |
| A14 | Daftar Pengembalian | Tabel pesanan aktif dan terlambat (urut jatuh tempo), badge Terlambat menonjol |
| A15 | Denda | Tabel (resi, jenis, jumlah, status), tombol "Tandai Lunas" |
| A16 | Laporan | Filter jenis (penyewaan, pendapatan, denda, alat terlaris) dan periode, tabel hasil, total, tombol Cetak PDF dan Excel |
| A17 | Log Aktivitas | Filter tanggal/user/modul, tabel |

## Pemilik

| ID | Layar | Komponen |
|---|---|---|
| O01 | Dashboard | Kartu pendapatan hari ini, bulan ini, jumlah transaksi, alat terlaris, stok kritis, grafik pendapatan 6 bulan, jumlah terlambat |
| O02 | Laporan | Sama seperti A16, tanpa aksi ubah data |
| O03 | Kelola Admin | Tabel akun Admin dan form tambah/nonaktif |
| O04 | Pengaturan | Persen denda, jam kembali, toleransi, durasi maksimum, nomor WhatsApp toko |
| O05 | Log Aktivitas | Sama seperti A17 |

## Template Dokumen Cetak

| ID | Dokumen | Isi |
|---|---|---|
| D01 | Resi pesanan | Logo, no resi, pelanggan, tabel alat, total, instruksi |
| D02 | Laporan PDF | Kop toko, judul, periode, tabel, total, tanggal cetak, nama pencetak |

## Alur Navigasi Singkat (untuk prototype Figma)

1. Pelanggan: P01 → P04 → P05 → P06 → P07 → P08
2. Admin: A01 → A08 → A10 → (A11 → A12) → A13 → A15 → A16
3. Pemilik: O01 → O02 → O04
