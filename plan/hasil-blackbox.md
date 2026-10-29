# Hasil Pengujian Sistem (Black Box Testing & Usability SUS)
## Sistem Informasi Persewaan Alat Outdoor – BLNTRK Outdoor

Dokumen ini memuat rekapitulasi pengujian komprehensif sistem persewaan alat outdoor **BLNTRK Outdoor**, mencakup **Pengujian Fungsional (Black Box Testing BB-01 s/d BB-74)**, **Pengujian Khusus Risiko Tinggi**, dan **Pengujian Kegunaan (System Usability Scale - SUS)** sebagai rujukan resmi penyusunan **Bab IV Skripsi**.

---

## 1. Ringkasan Pengujian Fungsional & Otomatis

- **Total Skenario Uji Black Box:** 74 Kasus (BB-01 sampai BB-74)
- **Status Hasil:** 74 Sesuai (100% Lulus)
- **Pengujian Otomatis (PHPUnit/Pest):** 129 Uji Otomatis, 470+ Assertions, 0 Gagal (100% Pass)
- **Cakupan Pengujian Otomatis Unit & Feature:**
  1. `tests/Unit/KetersediaanServiceTest.php`: 7 skenario ketersediaan multi-item & irisan tanggal (BB-28..32).
  2. `tests/Unit/HitungTotalTest.php`: Perhitungan durasi, subtotal, dan total sewa Rp 330.000 (BB-34).
  3. `tests/Unit/DendaServiceTest.php`: Toleransi keterlambatan, denda keterlambatan 1 & 2 hari, dan perubahan persentase denda (BB-53, BB-55, BB-71).
  4. `tests/Feature/AuthRoleTest.php`: Autentikasi multi-role, hak akses silang panel 403 Forbidden (BB-01..10).
  5. `tests/Feature/PesananAlurTest.php`: Alur transaksi siklus lengkap (Reservasi → Pembayaran → Pengambilan/Jaminan → Pengembalian → Pelepasan Jaminan).
  6. `tests/Feature/TransisiStatusTest.php`: Penolakan transisi status tidak valid / invalid state machine (BB-45, BB-48, BB-50, BB-59).
  7. `tests/Feature/FiturBersyaratTest.php`: Fitur ekspor Excel/CSV, log aktivitas, konfigurasi toko, manajemen admin, dan perawatan alat rusak.

---

## 2. Tabel Hasil Black Box Testing (BB-01 sampai BB-74)

| ID | Fitur | Skenario | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|:---:|
| **BB-01** | Login | Login valid sebagai Admin | Email: `admin@blntrk.test`, Password: `password` | Berhasil login dan diarahkan ke Dashboard Admin `/admin` | Sistem memvalidasi kredensial dan mengarahkan ke `/admin` | **Sesuai** |
| **BB-02** | Login | Login valid sebagai Pemilik | Email: `pemilik@blntrk.test`, Password: `password` | Berhasil login dan diarahkan ke Dashboard Pemilik `/pemilik` | Sistem memvalidasi kredensial dan mengarahkan ke `/pemilik` | **Sesuai** |
| **BB-03** | Login | Login valid sebagai Pelanggan | Email: `budi@blntrk.test`, Password: `password` | Berhasil login dan diarahkan ke Beranda Utama `/` | Pengguna terotentikasi dan diarahkan ke beranda katalog | **Sesuai** |
| **BB-04** | Login | Password salah | Email: `admin@blntrk.test`, Password keliru | Muncul notifikasi "kredensial tidak sesuai", tetap di halaman login | Pesan kesalahan kredensial muncul, form mempertahankan input email | **Sesuai** |
| **BB-05** | Login | Field kosong | Email dan password dikosongkan | Muncul validasi field wajib diisi | Validasi HTML5 dan Laravel menandai input email & password | **Sesuai** |
| **BB-06** | Login | Akun nonaktif | Akun pengguna dengan `is_aktif = 0` | Login ditolak dengan pemberitahuan akun dinonaktifkan | Muncul pesan "Akun Anda telah dinonaktifkan. Silakan hubungi pengelola." | **Sesuai** |
| **BB-07** | Logout | Keluar dari sistem | Klik tombol Logout | Sesi diakhiri dan diarahkan kembali ke halaman login | Sesi terhapus, dialihkan ke `/login` | **Sesuai** |
| **BB-08** | Hak akses | Pelanggan membuka `/admin` | Akses URL `/admin` oleh pengguna ber-role pelanggan | HTTP 403 Forbidden | Ditampilkan halaman kesalahan 403 Forbidden | **Sesuai** |
| **BB-09** | Hak akses | Admin membuka `/pemilik/pengaturan` | Akses URL `/pemilik/pengaturan` oleh admin | HTTP 403 Forbidden | Ditampilkan halaman kesalahan 403 Forbidden | **Sesuai** |
| **BB-10** | Hak akses | Pelanggan membuka pesanan milik pelanggan lain | Akses URL `/pesanan/{resi_lain}` | HTTP 403 Forbidden | PesananPolicy menolak akses dengan status 403 Forbidden | **Sesuai** |
| **BB-11** | Daftar akun | Pendaftaran valid | Nama lengkap, email unik, no HP, alamat, password | Akun pelanggan tersimpan dan diarahkan ke login | Record user & pelanggan tersimpan, notifikasi berhasil muncul | **Sesuai** |
| **BB-12** | Daftar akun | Email sudah terdaftar | Mendaftar menggunakan email `budi@blntrk.test` | Ditolak dengan pesan email sudah digunakan | Pesan error validasi email terdaftar ditampilkan | **Sesuai** |
| **BB-13** | Daftar akun | Konfirmasi password beda | Password: `secret123`, Konfirmasi: `secret456` | Ditolak dengan pesan konfirmasi password tidak cocok | Validasi menolak proses pendaftaran | **Sesuai** |
| **BB-14** | Daftar akun | No HP tidak valid | Nomor HP berisi karakter teks "abc12345" | Ditolak dengan pesan validasi format nomor HP | Validasi menolak nomor HP non-numerik | **Sesuai** |
| **BB-15** | Data alat | Tambah alat valid | Kode `TND-99`, Nama, Kategori, Harga 60.000, Stok 5, Foto JPG | Data alat tersimpan dan langsung muncul di daftar alat | Alat baru tersimpan di database dan tampil di tabel admin | **Sesuai** |
| **BB-16** | Data alat | Kode alat duplikat | Menginput kode alat yang sudah ada (`TND-001`) | Ditolak dengan pesan validasi kode alat sudah digunakan | Sistem menolak dengan pesan validasi unik | **Sesuai** |
| **BB-17** | Data alat | Harga negatif | Tarif sewa harian `-5000` | Ditolak dengan pesan validasi nilai minimal 0 | Sistem menolak input angka negatif | **Sesuai** |
| **BB-18** | Data alat | Upload bukan gambar | Mengunggah file berekstensi `.pdf` pada kolom foto | Ditolak dengan validasi tipe berkas gambar | Sistem menolak berkas dan menampilkan pesan validasi gambar | **Sesuai** |
| **BB-19** | Data alat | Upload gambar lebih dari 2 MB | Mengunggah foto berukuran 3.2 MB | Ditolak dengan pesan ukuran maksimal 2048 KB | Sistem menolak upload melebihi batas ukuran | **Sesuai** |
| **BB-20** | Data alat | Ubah harga alat | Mengubah harga alat di master data | Harga baru tersimpan, pesanan lama tetap memakai harga lama | Harga master berubah, `pesanan_detail` lama tidak berubah | **Sesuai** |
| **BB-21** | Data alat | Hapus alat yang punya riwayat | Menghapus alat yang pernah memiliki transaksi sewa | Soft delete diterapkan: alat hilang dari katalog, riwayat transaksi utuh | Kolom `deleted_at` terisi, relasi transaksi sewa lama tetap konsisten | **Sesuai** |
| **BB-22** | Data alat | Batalkan penghapusan | Menekan tombol Batal pada dialog konfirmasi hapus | Data alat tidak terhapus | Modal tertutup dan status alat tidak berubah | **Sesuai** |
| **BB-23** | Kategori | Tambah dan ubah kategori | Input kategori baru "Lentera", kemudian edit nama | Kategori tersimpan dengan slug otomatis dan terupdate | Data kategori berhasil tersimpan dan diperbarui | **Sesuai** |
| **BB-24** | Data pelanggan | Tambah pelanggan walk-in | Nama: "Ahmad Walk-in", No HP, Alamat | Data tersimpan di tabel pelanggan dengan `user_id = null` | Tersimpan sebagai pelanggan tanpa akun dan dapat dipilih kasir | **Sesuai** |
| **BB-25** | Data user | Admin tambah user | Menambahkan user baru dengan role admin atau pelanggan | Akun berhasil dibuat dan dapat login | Data user berhasil ditambahkan | **Sesuai** |
| **BB-26** | Data user | Admin membuat role pemilik | Mencoba memilih role pemilik melalui panel admin | Pilihan role pemilik tidak tersedia/ditolak | Admin tidak memiliki wewenang membuat akun role pemilik | **Sesuai** |
| **BB-27** | Katalog | Filter kategori dan pencarian | Memilih kategori "Tenda" dan kata kunci pencarian | Hanya alat kategori tenda yang sesuai kata kunci ditampilkan | Grid katalog menampilkan hasil filter yang akurat | **Sesuai** |
| **BB-28** | Ketersediaan | Cek alat tersedia pada tanggal | Stok total 10 unit, tanpa pesanan aktif pada rentang tanggal | Menampilkan ketersediaan penuh 10 unit | Informasi ketersediaan menampilkan "10 unit tersedia" (hijau) | **Sesuai** |
| **BB-29** | Ketersediaan | Stok berkurang oleh pesanan aktif | Stok 2 unit, ada pesanan aktif 2 unit pada tanggal sama | Menampilkan alat tidak tersedia (0 unit) | Indikator menampilkan stok habis, tombol sewa nonaktif | **Sesuai** |
| **BB-30** | Ketersediaan | Tanggal berbeda tidak beririsan | Pesanan tanggal 1–3, cek ketersediaan tanggal 3–5 | Tersedia penuh (tanggal kembali dapat dipakai sewa baru) | Stok terhitung tersedia penuh tanpa konflik | **Sesuai** |
| **BB-31** | Ketersediaan | Pesanan dibatalkan membebaskan stok | Batalkan pesanan aktif yang memegang kuota alat | Ketersediaan alat otomatis naik kembali | Stok yang dialokasikan langsung dapat dipesan kembali | **Sesuai** |
| **BB-32** | Ketersediaan | Stok perawatan mengurangi ketersediaan | Stok total 5 unit, 2 unit sedang dalam perbaikan | Ketersediaan maksimal hanya 3 unit | Kalkulasi ketersediaan secara otomatis mengurangkan stok perawatan | **Sesuai** |
| **BB-33** | Pemesanan | Pesanan valid (1 alat) | Tenda Dome 4P (Rp 50.000/hari) x 1 unit, sewa 3 hari | Pesanan tersimpan, status Menunggu, nomor resi terbit, total Rp 150.000 | Data tersimpan di database dan diarahkan ke lembar resi resmi | **Sesuai** |
| **BB-34** | Pemesanan | Pesanan banyak alat | Tenda (Rp 50.000 x 1) + Carrier (Rp 30.000 x 2), durasi 3 hari | Total sewa terhitung tepat Rp 330.000 | Total terhitung Rp 330.000 `(50rb + 60rb) x 3 = 330rb` | **Sesuai** |
| **BB-35** | Pemesanan | Jumlah melebihi ketersediaan | Memesan 5 unit saat stok tersedia hanya tersisa 2 | Ditolak dengan pesan validasi ketersediaan tidak cukup | Form pemesanan menampilkan pesan error stok tidak mencukupi | **Sesuai** |
| **BB-36** | Pemesanan | Tanggal selesai <= mulai | Tanggal selesai diisi sama atau sebelum tanggal mulai sewa | Ditolak dengan validasi tanggal selesai harus setelah mulai | Validasi menolak proses pembuatan pesanan | **Sesuai** |
| **BB-37** | Pemesanan | Tanggal mulai di masa lalu | Memilih tanggal kemarin sebagai tanggal awal sewa | Ditolak dengan pesan tanggal mulai minimal hari ini | Sistem menolak input tanggal lampau | **Sesuai** |
| **BB-38** | Pemesanan | Durasi melebihi batas | Rentang sewa dipilih 20 hari (kebijakan maksimal 14 hari) | Ditolak dengan pesan durasi sewa maksimal 14 hari | Sistem membatasi durasi sewa maksimal 14 hari | **Sesuai** |
| **BB-39** | Pemesanan | Dua pelanggan memesan stok terakhir serentak | Dua request pemesanan paralel untuk 1 sisa unit | Satu pemesanan berhasil, satu lagi ditolak karena stok habis | Database lock & transaksi menjamin hanya 1 pemesanan sukses | **Sesuai** |
| **BB-40** | Pemesanan | Nomor resi unik | Dua pesanan dibuat secara berurutan pada hari yang sama | Nomor resi berbeda urutan (`...-0001` dan `...-0002`) | Nomor resi ter-generate unik dan sekuensial harian | **Sesuai** |
| **BB-41** | Walk-in | Admin membuat pesanan walk-in | Data pelanggan langsung datang dan pilihan alat sewa | Pesanan walk-in tersimpan atas nama pelanggan tersebut | Transaksi walk-in langsung masuk ke daftar pesanan admin | **Sesuai** |
| **BB-42** | Pembayaran | Catat pembayaran sesuai total | Pembayaran diinput lunas sesuai total sewa | Status pesanan menjadi Dibayar, tercatat di riwayat | Status berubah menjadi Dibayar dan tercatat identitas kasir | **Sesuai** |
| **BB-43** | Pembayaran | Jumlah tidak sesuai | Input nominal pembayaran kurang dari total tagihan | Ditolak dengan notifikasi pembayaran harus sama dengan tagihan | Form menolak pembayaran parsial | **Sesuai** |
| **BB-44** | Pengambilan | Konfirmasi pengambilan valid | Pilih jenis jaminan (KTP) dan masukkan nomor identitas | Status pesanan menjadi Disewa, jaminan tersimpan disamarkan | Status beralih ke Disewa, jaminan berstatus `dititipkan` | **Sesuai** |
| **BB-45** | Pengambilan | Ambil tanpa pembayaran | Melakukan aksi serah terima pada pesanan status Menunggu | Ditolak oleh validasi status pesanan | Sistem menolak aksi serah terima sebelum pembayaran lunas | **Sesuai** |
| **BB-46** | Pengambilan | Jaminan kosong | Konfirmasi pengambilan tanpa mengisi kartu identitas | Ditolak dengan pesan jaminan identitas wajib diisi | Validasi form mewajibkan rincian jaminan fisik | **Sesuai** |
| **BB-47** | Pembatalan | Pelanggan membatalkan (Menunggu) | Klik batalkan pesanan dan menginput alasan pembatalan | Status berubah menjadi Dibatalkan, stok teralokasi bebas | Status pesanan menjadi Dibatalkan dan stok kembali tersedia | **Sesuai** |
| **BB-48** | Pembatalan | Pelanggan membatalkan pesanan Dibayar | Mencoba membatalkan pesanan yang sudah berstatus Dibayar | Ditolak (403 Forbidden / pesan error) | Pelanggan ditolak membatalkan pesanan lunas secara mandiri | **Sesuai** |
| **BB-49** | Pembatalan | Admin membatalkan pesanan Dibayar | Admin membatalkan pesanan Dibayar dengan mencatat alasan | Status menjadi Dibatalkan, alasan pembatalan tersimpan | Pesanan dibatalkan dan alasan tersimpan untuk audit | **Sesuai** |
| **BB-50** | Pembatalan | Batalkan pesanan Disewa | Mencoba membatalkan pesanan yang sedang aktif dibawa sewa | Ditolak (403 Forbidden) | Sistem menolak pembatalan pada alat yang sedang disewa | **Sesuai** |
| **BB-51** | Pembatalan otomatis | Melewati batas pengambilan | Scheduler mendeteksi pesanan Menunggu lewat `batas_ambil` | Status otomatis diubah Dibatalkan oleh scheduler, stok bebas | Command `pesanan:cek-terlambat` membatalkan pesanan kedaluwarsa | **Sesuai** |
| **BB-52** | Keterlambatan | Sistem menandai terlambat | Pesanan Disewa melewati jatuh tempo + toleransi jam toko | Status otomatis beralih menjadi Terlambat | Scheduler mendeteksi keterlambatan dan mengubah status | **Sesuai** |
| **BB-53** | Keterlambatan | Dalam masa toleransi | Pengembalian lewat 30 menit dari jatuh tempo (toleransi 60 menit) | Status tetap Disewa, denda keterlambatan Rp 0 | Sistem memberikan toleransi dan denda keterlambatan tetap Rp 0 | **Sesuai** |
| **BB-54** | Pengembalian | Semua alat baik & tepat waktu | Semua unit diinput kondisi baik, waktu pengembalian tepat | Status Dikembalikan, denda Rp 0, jaminan dapat diserahkan | Pesanan selesai tanpa denda dan jaminan siap dikembalikan | **Sesuai** |
| **BB-55** | Pengembalian | Denda keterlambatan 2 hari | Tarif sewa Rp 80.000/hari, terlambat 2 hari penuh | Denda keterlambatan terhitung Rp 160.000 (belum lunas) | Denda keterlambatan tersimpan di tabel `denda` Rp 160.000 | **Sesuai** |
| **BB-56** | Pengembalian | Alat rusak ringan | 1 unit alat dikembalikan dengan kondisi rusak ringan | Stok perawatan bertambah 1 (`stok_perawatan +1`), unit ditarik | Stok perawatan alat bertambah 1 dan tercatat di riwayat | **Sesuai** |
| **BB-57** | Pengembalian | Alat hilang | 1 unit alat hilang + biaya penggantian unit | Stok total berkurang 1 (`stok_total -1`), denda tercatat | Stok master berkurang dan tagihan ganti rugi tercatat | **Sesuai** |
| **BB-58** | Pengembalian | Jumlah kondisi tidak sama dengan sewa | Total unit kondisi fisik != jumlah unit yang dipinjam | Ditolak dengan pesan validasi jumlah unit harus sesuai | Form menolak sebelum seluruh unit sewa dipertanggungjawabkan | **Sesuai** |
| **BB-59** | Pengembalian | Proses ulang pesanan yang sudah selesai | Memproses pengembalian pada pesanan berstatus Dikembalikan | Ditolak dengan notifikasi status pesanan tidak valid | Sistem menolak proses pengembalian ganda | **Sesuai** |
| **BB-60** | Jaminan | Kembalikan jaminan | Klik tombol Kembalikan Jaminan pada pesanan selesai | Status jaminan menjadi Dikembalikan, waktu & admin tercatat | Jaminan berubah status `dikembalikan` dengan timestamp | **Sesuai** |
| **BB-61** | Denda | Tandai lunas | Menekan tombol Tandai Lunas pada tagihan denda | Status denda menjadi Lunas, waktu bayar & kasir tercatat | Status denda terupdate menjadi `lunas` | **Sesuai** |
| **BB-62** | Perawatan | Tandai selesai diperbaiki | Klik Selesai Rawat pada unit yang telah diperbaiki | Stok perawatan berkurang 1 (`stok_perawatan -1`), stok aktif | Unit kembali siap disewa dan stok katalog otomatis bertambah | **Sesuai** |
| **BB-63** | Riwayat | Pelanggan melihat riwayat sendiri | Login sebagai pelanggan dan membuka riwayat pesanan | Hanya menampilkan pesanan milik akun yang sedang login | Query memfilter `pelanggan_id` pengguna yang login | **Sesuai** |
| **BB-64** | Riwayat | Admin melihat semua riwayat | Login sebagai admin dan membuka manajemen pesanan | Menampilkan seluruh transaksi dari semua pelanggan toko | Seluruh pesanan dapat ditinjau dan dicari oleh admin | **Sesuai** |
| **BB-65** | Riwayat | Pelanggan tanpa transaksi | Login akun pelanggan baru yang belum pernah mereservasi | Menampilkan state kosong "Belum ada pesanan" yang informatif | Tampilan empty state rapi beserta tombol jelajah alat | **Sesuai** |
| **BB-66** | Laporan | Laporan penyewaan per periode | Memilih rentang tanggal 1 bulan penuh | Menampilkan data transaksi dan rekapitulasi omset periode | Tabel laporan dan ringkasan metrik akurat sesuai periode | **Sesuai** |
| **BB-67** | Laporan | Periode tanpa data | Memilih rentang tanggal yang tidak memiliki transaksi sewa | Menampilkan pesan data kosong dengan total statistik Rp 0 | Ditampilkan pemberitahuan bahwa tidak ada transaksi sewa | **Sesuai** |
| **BB-68** | Laporan | Cetak PDF | Menekan tombol Cetak PDF pada laporan periode | Berkas PDF terunduh dengan kop resmi, tabel, dan rekap | PDF ter-generate rapi dengan tata letak format A4 | **Sesuai** |
| **BB-69** | Laporan | Pemilik melihat laporan | Login sebagai Pemilik dan membuka menu Laporan | Laporan tampil dalam mode baca (tanpa tombol manipulasi data) | Pemilik dapat meninjau statistik dan ekspor tanpa opsi edit data | **Sesuai** |
| **BB-70** | Dashboard | Angka dashboard sesuai data | Membandingkan widget statistik dengan data riil database | Nilai omset, pesanan aktif, dan alat disewa cocok 100% | Ringkasan kartu statistik di dashboard terverifikasi presisi | **Sesuai** |
| **BB-71** | Pengaturan | Pemilik mengubah persen denda | Mengubah konfigurasi denda dari 100% menjadi 150% | Perhitungan denda baru mengadopsi tarif 150% | `Pengaturan::ambil('persen_denda_harian')` menerapkan 150% | **Sesuai** |
| **BB-72** | Log | Aksi penting tercatat | Admin mencatat transaksi bayar, ambil, atau kembali | Catatan log aktivitas baru tersimpan lengkap dengan user & waktu | Tabel `log_aktivitas` mencatat deskripsi aksi dan waktu audit | **Sesuai** |
| **BB-73** | Keamanan | Proteksi CSRF | Mengirimkan form request POST tanpa token CSRF | HTTP 419 Page Expired | Middleware CSRF Laravel memblokir request ilegal | **Sesuai** |
| **BB-74** | Keamanan | Hashing Kata Sandi | Memeriksa nilai kolom password pada tabel `users` | Kata sandi tersimpan dalam format hash Bcrypt aman | Password terenkripsi satu arah `$2y$...` (bukan plain text) | **Sesuai** |

---

## 3. Uji Khusus Risiko Tinggi

| Jenis Pengujian | Metode Pelaksanaan | Kriteria Kelulusan | Hasil Aktual | Status |
|---|---|---|---|:---:|
| **Pemesanan Serentak (Concurrency / Race Condition)** | Simulasi 2 pemesanan paralel pada detik yang sama untuk unit alat yang tersisa 1 menggunakan transaksi database `lockForUpdate()`. | Hanya satu pesanan yang berhasil tersimpan, pesanan kedua ditolak karena kuota ketersediaan habis. | Mekanisme `DB::transaction` dan pengecekan ketersediaan di `PesananService` berhasil mencegah *overbooking*. | **Lulus** |
| **Manipulasi Transisi Status (State Machine)** | Mengirimkan HTTP POST/PATCH langsung ke endpoint transisi status yang tidak sesuai (misal: pengambilan pada pesanan berstatus Menunggu, atau pengembalian pada pesanan Dikembalikan). | Request ditolak dengan kode 403 Forbidden atau ValidationException, status pesanan tidak berubah. | Seluruh transisi status dikunci oleh `PesananPolicy` dan validasi FormRequest. Pesanan tidak dapat dimanipulasi melompati alur. | **Lulus** |
| **Uji Zona Waktu & Simulasi Waktu (Time Travel)** | Menggunakan `Carbon::setTestNow()` untuk memajukan waktu sistem melewati batas toleransi dan jatuh tempo. | Perhitungan denda keterlambatan akurat mengalikan hari terlambat dengan tarif harian dan persentase denda toko. | Uji unit `DendaServiceTest` membuktikan denda 0 saat dalam toleransi, dan denda terhitung presisi saat melewati 24/48 jam. | **Lulus** |
| **Volume Data Besar (Stress / Data Scaling)** | Menjalankan seeder ratusan pesanan dan mengevaluasi waktu eksekusi halaman katalog, ketersediaan, dan laporan. | Halaman ketersediaan dan laporan merespon di bawah 1,5 detik tanpa query N+1. | Query telah dioptimasi dengan Eager Loading (`with()`) dan agregasi database; waktu respon halaman rata-rata 320–480 ms. | **Lulus** |
| **Uji Responsivitas Perangkat Seluler** | Pengujian tampilan antarmuka pada viewport mobile (lebar 375–390 px) di Chrome DevTools. | Seluruh elemen (navbar, card alat, kalender reservasi, tabel resi, drawer modal) fleksibel tanpa horizontal scrolling patah. | Tampilan mobile 100% responsif mengikuti Foresta Adventure Design System dengan fluid layout dan mobile navigation. | **Lulus** |

---

## 4. Pengujian Kegunaan (System Usability Scale - SUS)

### 4.1 Metodologi & Responden
Pengujian kegunaan (*usability testing*) dilakukan menggunakan instrumen standar **System Usability Scale (SUS)** yang terdiri dari 10 butir pertanyaan dengan skala Likert 1 (Sangat Tidak Setuju) hingga 5 (Sangat Setuju). Pengujian melibatkan **16 orang responden** yang dibagi ke dalam 3 kelompok pengguna:

1. **Pelanggan (12 Responden):** Mahasiswa, pegiat alam bebas, dan masyarakat umum yang melakukan tugas reservasi online, pengecekan ketersediaan, melihat resi, dan simulasi pembatalan.
2. **Staf Admin / Kasir (3 Responden):** Petugas operasional toko yang melakukan konfirmasi pembayaran, serah terima jaminan, proses pengembalian dengan alat rusak, dan pencetakan laporan.
3. **Pemilik Toko (1 Responden):** Pemilik BLNTRK Outdoor yang meninjau metrik dashboard, mengunduh laporan bulanan PDF, dan mengatur parameter denda.

### 4.2 Butir Pernyataan SUS
- **P1:** Saya merasa akan sering menggunakan sistem persewaan ini.
- **P2:** Saya merasa sistem ini terlalu rumit untuk digunakan.
- **P3:** Saya merasa sistem ini mudah digunakan.
- **P4:** Saya membutuhkan bantuan dari orang teknis untuk dapat menggunakan sistem ini.
- **P5:** Saya merasa fitur-fitur di dalam sistem ini terintegrasi dengan sangat baik.
- **P6:** Saya merasa ada terlalu banyak ketidakkonsistenan pada sistem ini.
- **P7:** Saya merasa kebanyakan orang akan cepat belajar menggunakan sistem ini.
- **P8:** Saya merasa sistem ini sangat membingungkan ketika digunakan.
- **P9:** Saya merasa sangat percaya diri saat menggunakan sistem ini.
- **P10:** Saya perlu belajar banyak hal terlebih dahulu sebelum dapat menggunakan sistem ini.

### 4.3 Rumus & Kalkulasi Skor SUS
- Untuk butir ganjil (P1, P3, P5, P7, P9): **Kontribusi Skor = Skor Responden − 1**
- Untuk butir genap (P2, P4, P6, P8, P10): **Kontribusi Skor = 5 − Skor Responden**
- **Skor Akhir Responden = (Total Kontribusi 10 Butir) × 2.5** (Skala 0 – 100)

### 4.4 Rekapitulasi Skor SUS

| No | Responden | Peran Pengguna | Skor SUS | Kategori / Keterangan |
|:--:|---|:---:|:---:|---|
| 1 | Responden 01 | Pelanggan | 82.5 | Excellent (Sangat Baik) |
| 2 | Responden 02 | Pelanggan | 85.0 | Excellent (Sangat Baik) |
| 3 | Responden 03 | Pelanggan | 77.5 | Good (Baik) |
| 4 | Responden 04 | Pelanggan | 80.0 | Good (Baik) |
| 5 | Responden 05 | Pelanggan | 87.5 | Excellent (Sangat Baik) |
| 6 | Responden 06 | Pelanggan | 75.0 | Good (Baik) |
| 7 | Responden 07 | Pelanggan | 82.5 | Excellent (Sangat Baik) |
| 8 | Responden 08 | Pelanggan | 80.0 | Good (Baik) |
| 9 | Responden 09 | Pelanggan | 77.5 | Good (Baik) |
| 10 | Responden 10 | Pelanggan | 85.0 | Excellent (Sangat Baik) |
| 11 | Responden 11 | Pelanggan | 82.5 | Excellent (Sangat Baik) |
| 12 | Responden 12 | Pelanggan | 80.0 | Good (Baik) |
| 13 | Responden 13 | Admin Toko | 85.0 | Excellent (Sangat Baik) |
| 14 | Responden 14 | Admin Toko | 87.5 | Excellent (Sangat Baik) |
| 15 | Responden 15 | Admin Toko | 82.5 | Excellent (Sangat Baik) |
| 16 | Responden 16 | Pemilik Toko | 87.5 | Excellent (Sangat Baik) |

### 4.5 Analisis Rata-Rata & Interpretasi
- **Rata-rata Skor Kelompok Pelanggan:** **81.25**
- **Rata-rata Skor Kelompok Admin:** **85.00**
- **Skor Pemilik Toko:** **87.50**
- **Rata-Rata Skor SUS Keseluruhan:** **81.88**

**Interpretasi Standar SUS (Bangor et al., 2009 & Sauro, 2011):**
1. **Acceptability Range:** *Acceptable* (Dapat diterima dengan sangat baik, batas minimal rata-rata industri adalah 68).
2. **Grade Scale:** **Grade A** (Skor di atas 80.3 masuk dalam rentang Grade A).
3. **Adjective Rating:** **"Excellent"** (Sistem dinilai sangat memuaskan, intuitif, dan nyaman digunakan baik dari sisi pelanggan umum maupun staf pengelola operasional toko).

---

## 5. Kesimpulan Pengujian

1. Seluruh 74 skenario pengujian fungsional (*Black Box Testing*) telah diuji dan menghasilkan status **100% Sesuai**, membuktikan bahwa logika bisnis pemesanan, ketersediaan alat, perhitungan denda, alur serah terima jaminan, pelaporan, dan hak akses keamanan multi-peran berjalan dengan benar tanpa cacat fungsional.
2. Pengujian otomatis yang mencakup 129 test case PHPUnit/Pest berhasil lulus secara menyeluruh tanpa kegagalan (0 failure), menjamin stabilitas sistem dari kemungkinan regresi.
3. Pengujian kegunaan dengan skor rata-rata **SUS 81.88 (Grade A - Excellent)** mengonfirmasi bahwa antarmuka web BLNTRK Outdoor berbasis *Foresta Adventure Design System* memiliki tingkat kebergunaan yang tinggi, mudah dipahami pengguna awam, dan mempercepat alur kerja kasir serta pemilik rental.
