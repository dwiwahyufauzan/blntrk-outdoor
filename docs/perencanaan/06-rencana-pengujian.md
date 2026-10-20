# 6. Rencana Pengujian

## 6a. Black Box Testing

Kolom "Hasil" diisi saat pengujian (Sesuai / Tidak Sesuai). Data uji memakai akun dari seeder: `admin@blntrk.test`, `pemilik@blntrk.test`, `budi@blntrk.test` (contoh).

| ID | Fitur | Skenario | Data Uji | Hasil yang Diharapkan |
|---|---|---|---|---|
| BB-01 | Login | Login valid sebagai Admin | Email dan password benar | Masuk ke `/admin` |
| BB-02 | Login | Login valid sebagai Pemilik | Akun Pemilik | Masuk ke `/pemilik` |
| BB-03 | Login | Login valid sebagai Pelanggan | Akun Pelanggan | Masuk ke beranda pelanggan |
| BB-04 | Login | Password salah | Password keliru | Pesan "data login tidak sesuai", tetap di halaman login |
| BB-05 | Login | Field kosong | Email dan password kosong | Pesan validasi wajib diisi |
| BB-06 | Login | Akun nonaktif | User `is_aktif = 0` | Login ditolak dengan pesan |
| BB-07 | Logout | Keluar dari sistem | Klik Logout | Sesi berakhir, kembali ke login |
| BB-08 | Hak akses | Pelanggan membuka `/admin` | URL langsung | 403 Forbidden |
| BB-09 | Hak akses | Admin membuka `/pemilik/pengaturan` | URL langsung | 403 Forbidden |
| BB-10 | Hak akses | Pelanggan membuka pesanan milik pelanggan lain | `/pesanan/{id lain}` | 403 atau 404 |
| BB-11 | Daftar akun | Pendaftaran valid | Data lengkap, email unik | Akun dibuat, diarahkan ke login |
| BB-12 | Daftar akun | Email sudah terdaftar | Email existing | Pesan email sudah digunakan |
| BB-13 | Daftar akun | Konfirmasi password beda | Password tidak sama | Pesan validasi |
| BB-14 | Daftar akun | No HP tidak valid | "abc123" | Pesan validasi |
| BB-15 | Data alat | Tambah alat valid | Semua field, foto jpg 500 KB | Alat tersimpan dan tampil di daftar |
| BB-16 | Data alat | Kode alat duplikat | Kode sudah ada | Pesan kode sudah digunakan |
| BB-17 | Data alat | Harga negatif | -5000 | Pesan validasi |
| BB-18 | Data alat | Upload bukan gambar | File .pdf | Ditolak |
| BB-19 | Data alat | Upload gambar lebih dari 2 MB | 3 MB | Ditolak |
| BB-20 | Data alat | Ubah harga alat | Harga baru | Data diperbarui, pesanan lama tetap memakai harga lama |
| BB-21 | Data alat | Hapus alat yang punya riwayat | Alat pernah disewa | Soft delete: hilang dari katalog, riwayat tetap utuh |
| BB-22 | Data alat | Batalkan penghapusan | Klik Batal | Data tidak berubah |
| BB-23 | Kategori | Tambah dan ubah kategori | Nama baru | Tersimpan |
| BB-24 | Data pelanggan | Tambah pelanggan walk-in | Nama, HP | Tersimpan tanpa akun |
| BB-25 | Data user | Admin tambah user | Role admin/pelanggan | Tersimpan |
| BB-26 | Data user | Admin membuat role pemilik | role=pemilik | Ditolak |
| BB-27 | Katalog | Filter kategori dan pencarian | Kategori "Tenda" | Hanya tenda yang tampil |
| BB-28 | Ketersediaan | Cek alat tersedia pada tanggal | Stok 10, tanpa pesanan | Tampil tersedia 10 |
| BB-29 | Ketersediaan | Stok berkurang oleh pesanan aktif | Stok 2, pesanan aktif jumlah 2 pada tanggal sama | Tidak tersedia |
| BB-30 | Ketersediaan | Tanggal berbeda, tidak beririsan | Pesanan 1–3, cek 3–5 | Tersedia penuh (tanggal selesai dapat dipakai ulang) |
| BB-31 | Ketersediaan | Pesanan dibatalkan membebaskan stok | Batalkan pesanan | Ketersediaan kembali naik |
| BB-32 | Ketersediaan | Stok perawatan mengurangi ketersediaan | stok 5, perawatan 2 | Maksimum 3 |
| BB-33 | Pemesanan | Pesanan valid (1 alat) | Tenda D4 × 1, 3 hari | Tersimpan, status Menunggu, resi tampil, total = 150.000 |
| BB-34 | Pemesanan | Pesanan banyak alat | Tenda 50.000 × 1 + Carrier 30.000 × 2, 3 hari | Total 330.000 |
| BB-35 | Pemesanan | Jumlah melebihi ketersediaan | Jumlah > tersedia | Ditolak dengan pesan |
| BB-36 | Pemesanan | Tanggal selesai ≤ mulai | Selesai = mulai | Pesan validasi |
| BB-37 | Pemesanan | Tanggal mulai di masa lalu | Kemarin | Pesan validasi |
| BB-38 | Pemesanan | Durasi melebihi batas | 20 hari | Ditolak |
| BB-39 | Pemesanan | Dua pelanggan memesan stok terakhir bersamaan | Dua request paralel, stok 1 | Satu berhasil, satu ditolak |
| BB-40 | Pemesanan | Nomor resi unik | Dua pesanan berurutan | Resi berbeda |
| BB-41 | Walk-in | Admin membuat pesanan untuk walk-in | Pelanggan walk-in | Pesanan tersimpan atas nama pelanggan tersebut |
| BB-42 | Pembayaran | Catat pembayaran sesuai total | Jumlah = total | Status Dibayar, tercatat di pembayaran |
| BB-43 | Pembayaran | Jumlah tidak sesuai | Kurang dari total | Ditolak |
| BB-44 | Pengambilan | Konfirmasi pengambilan valid | Jenis KTP, nomor 1234 | Status Disewa, jaminan tercatat dititipkan |
| BB-45 | Pengambilan | Ambil tanpa pembayaran | Status Menunggu | Ditolak |
| BB-46 | Pengambilan | Jaminan kosong | Tidak diisi | Pesan validasi |
| BB-47 | Pembatalan | Pelanggan membatalkan (Menunggu) | Alasan | Status Dibatalkan |
| BB-48 | Pembatalan | Pelanggan membatalkan pesanan Dibayar | – | Ditolak |
| BB-49 | Pembatalan | Admin membatalkan pesanan Dibayar | Alasan | Status Dibatalkan, alasan tersimpan |
| BB-50 | Pembatalan | Batalkan pesanan Disewa | – | Ditolak |
| BB-51 | Pembatalan otomatis | Melewati batas pengambilan | `batas_ambil` lampau, status Menunggu | Setelah scheduler: Dibatalkan, stok bebas |
| BB-52 | Keterlambatan | Sistem menandai terlambat | Disewa, lewat jatuh tempo + toleransi | Status Terlambat |
| BB-53 | Keterlambatan | Dalam toleransi | Lewat 30 menit | Tetap Disewa |
| BB-54 | Pengembalian | Semua alat baik, tepat waktu | Semua jml_baik | Status Dikembalikan, tanpa denda, jaminan dapat dikembalikan |
| BB-55 | Pengembalian | Denda keterlambatan 2 hari | Total tarif harian 110.000, 2 hari | Denda 220.000, belum lunas |
| BB-56 | Pengembalian | Alat rusak ringan | 1 unit rusak ringan | `stok_perawatan` +1, ketersediaan turun 1 |
| BB-57 | Pengembalian | Alat hilang | 1 unit hilang + biaya | `stok_total` −1, denda kerusakan tercatat |
| BB-58 | Pengembalian | Jumlah kondisi tidak sama dengan jumlah sewa | Total kondisi ≠ jumlah | Ditolak |
| BB-59 | Pengembalian | Proses ulang pesanan yang sudah dikembalikan | – | Ditolak |
| BB-60 | Jaminan | Kembalikan jaminan | Klik kembalikan | Status jaminan Dikembalikan, waktu tercatat |
| BB-61 | Denda | Tandai lunas | Denda belum lunas | Status Lunas, waktu dan petugas tercatat |
| BB-62 | Perawatan | Tandai selesai diperbaiki | 1 unit | `stok_perawatan` −1, tersedia kembali |
| BB-63 | Riwayat | Pelanggan melihat riwayat sendiri | Akun pelanggan | Hanya pesanan miliknya |
| BB-64 | Riwayat | Admin melihat semua riwayat | Akun admin | Semua pesanan beserta pembayaran dan pengembalian |
| BB-65 | Riwayat | Pelanggan tanpa transaksi | Akun baru | Pesan "belum ada riwayat" |
| BB-66 | Laporan | Laporan penyewaan per periode | Rentang tanggal | Data sesuai periode dan total benar |
| BB-67 | Laporan | Periode tanpa data | Rentang kosong | Pesan data tidak tersedia |
| BB-68 | Laporan | Cetak PDF | Klik Cetak PDF | File PDF berisi data, kop, periode |
| BB-69 | Laporan | Pemilik melihat laporan | Akun pemilik | Laporan tampil tanpa tombol ubah |
| BB-70 | Dashboard | Angka dashboard sesuai data | Data seeder | Angka sama dengan hasil hitung manual |
| BB-71 | Pengaturan | Pemilik mengubah persen denda | 150 | Perhitungan denda berikutnya memakai 150% |
| BB-72 | Log | Aksi penting tercatat | Catat bayar | Entri log muncul dengan user, aksi, waktu |
| BB-73 | Keamanan | CSRF | Kirim form tanpa token | 419 Page Expired |
| BB-74 | Keamanan | Password tersimpan hash | Cek database | Bukan teks asli |

### Cadangan Uji Otomatis (opsional, nilai tambah)
Uji unit untuk `KetersediaanService` (BB-28 sampai BB-32), `DendaService` (BB-55), dan perhitungan total (BB-33, BB-34) memakai PHPUnit/Pest. Tampilkan hasilnya sebagai bukti tambahan.

## 6b. Pengujian Kegunaan (Usability) – SUS

**Metode:** System Usability Scale (SUS), 10 pernyataan, skala Likert 1–5.

| No | Pernyataan |
|---|---|
| 1 | Saya merasa akan sering menggunakan sistem ini. |
| 2 | Saya merasa sistem ini terlalu rumit. |
| 3 | Saya merasa sistem ini mudah digunakan. |
| 4 | Saya membutuhkan bantuan orang teknis untuk menggunakan sistem ini. |
| 5 | Saya merasa fitur-fitur sistem ini terintegrasi dengan baik. |
| 6 | Saya merasa ada terlalu banyak ketidakkonsistenan pada sistem ini. |
| 7 | Saya merasa kebanyakan orang akan cepat belajar menggunakan sistem ini. |
| 8 | Saya merasa sistem ini membingungkan. |
| 9 | Saya merasa percaya diri menggunakan sistem ini. |
| 10 | Saya perlu belajar banyak hal sebelum dapat menggunakan sistem ini. |

**Perhitungan:** butir ganjil: skor − 1; butir genap: 5 − skor. Jumlahkan semua butir, kalikan 2,5 (hasil 0–100). Skor akhir = rata-rata seluruh responden.
**Interpretasi:** di bawah 51 buruk, 51–68 marginal, ≥ 68 dapat diterima (rata-rata industri), ≥ 80 sangat baik.

| Kelompok | Jumlah responden | Tugas uji |
|---|---|---|
| Pelanggan | 10–15 | Daftar, cari alat, cek ketersediaan, buat pesanan, lihat resi, batalkan |
| Admin / karyawan | 2–3 | Konfirmasi bayar, pengambilan, pengembalian dengan denda, laporan |
| Pemilik | 1 | Lihat dashboard dan laporan |
| **Total** | **±15–20** | |

Saran: 5 orang sudah cukup menemukan sebagian besar masalah usability, tetapi 10 responden ke atas lebih diterima untuk skor SUS. Pisahkan hasil SUS per kelompok karena kebutuhan Admin dan Pelanggan berbeda. Catat juga waktu penyelesaian tugas dan jumlah kesalahan (opsional) serta kritik terbuka.

**Prosedur:** (1) jelaskan tujuan, (2) berikan skenario tugas tanpa panduan, (3) amati dan catat, (4) isi kuesioner SUS (Google Form), (5) wawancara singkat.
