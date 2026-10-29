# Panduan Bahan Skripsi & Persiapan Sidang Tugas Akhir
## Sistem Informasi Persewaan Alat Outdoor – BLNTRK Outdoor

Dokumen ini disusun sebagai panduan komprehensif bagi mahasiswa dalam melengkapi naskah skripsi (Bab III, IV, V, Lampiran) dan menghadapi sesi presentasi serta tanya jawab **Sidang Sarjana / Tugas Akhir**.

---

## 1. Pemetaan Bahan untuk Naskah Skripsi

| Bab Skripsi | Komponen yang Diambil dari Proyek | Lokasi Berkas Rujukan di Proyek |
|---|---|---|
| **Bab III: Analisis & Perancangan** | - Deskripsi Fase RUP (Inception, Elaboration, Construction, Transition)<br>- Use Case Diagram & Skenario Use Case (Pelanggan, Admin, Pemilik)<br>- Activity Diagram & Sequence Diagram Alur Sewa<br>- Entity Relationship Diagram (ERD) & Kamus Data Relasional<br>- Aturan Bisnis Ketersediaan, Denda, & Jaminan Fisik | - `docs/perencanaan/01-ruang-lingkup-dan-arsitektur.md`<br>- `docs/perencanaan/02-perancangan-basis-data.md`<br>- `docs/perencanaan/03-alur-dan-antarmuka.md` |
| **Bab IV: Implementasi & Pengujian** | - Lingkungan Pengembangan & Spesifikasi Perangkat Keras/Lunak<br>- Struktur Arsitektur MVC & Service Layer (`KetersediaanService`, `DendaService`, dll.)<br>- Tangkapan Layar Implementasi Antarmuka Utama (Foresta Adventure System)<br>- Hasil Pengujian Otomatis (129 Tests, 478 Assertions, 0 Failure)<br>- Tabel Pengujian Black Box Lengkap (BB-01 sampai BB-74, 100% Sesuai)<br>- Hasil Evaluasi Usability System Usability Scale (SUS Skor 81.88 - Grade A) | - `plan/hasil-blackbox.md`<br>- `blntrk-app/tests/`<br>- `resources/views/`<br>- `backup/blntrk_outdoor_final.sql` |
| **Bab V: Penutup** | - Kesimpulan Pemenuhan Rumusan Masalah & Tujuan Penelitian<br>- Keterbatasan Sistem yang Diakui Secara Objektif<br>- Saran Pengembangan Lanjutan untuk Penelitian Berikutnya | - Dokumen ini (Bagian 2 & 3) |
| **Lampiran** | - Skrip Migrasi Basis Data DDL<br>- Lembar Kuesioner Pengujian SUS (10 Pernyataan Skala Likert)<br>- Tabel Hasil Mentah Jawaban 16 Responden SUS<br>- Contoh Bukti Cetak PDF Resi Pesanan & Laporan Omset Bulanan | - `blntrk-app/database/migrations/`<br>- `plan/hasil-blackbox.md`<br>- `blntrk-app/resources/views/pdf/` |

---

## 2. Keterbatasan Sistem yang Objektif (*Nilai Plus Saat Sidang*)

Menyampaikan keterbatasan sistem secara jujur dan ilmiah di hadapan dewan penguji menunjukkan integritas akademis dan pemahaman mendalam tentang batas lingkup (*scope boundaries*) penelitian:

1. **Pencatatan Inventaris Berbasis Jenis Alat (Bukan Serial Number per Fisik):**
   - *Penjelasan:* Sistem mencatat alat berdasarkan kuantitas per tipe/model (`stok_total`, `stok_perawatan`), bukan kode unik (*barcode/RFID/serial number*) pada setiap unit fisik.
   - *Alasan Desain:* Karakteristik perlengkapan sewa sejenis di BLNTRK Outdoor (misal: pasak, matras, sleeping bag, tenda sejenis) bersifat substitutif (*fungible*), sehingga pelacakan per serial number akan membebani operasional toko saat *peak season*.
2. **Ketiadaan Payment Gateway Daring Otomatis:**
   - *Penjelasan:* Pembayaran dilakukan via tunai di kasir atau transfer bank manual yang diverifikasi oleh admin toko.
   - *Alasan Desain:* Menyesuaikan karakteristik UMKM lokal dan menghindari biaya komisi agregator pembayaran (*MDR fee*) bagi transaksi sewa bernilai kecil.
3. **Model Durasi Sewa Berbasis Hari Kalender Penuh (*Daily Slot*):**
   - *Penjelasan:* Ketersediaan unit dihitung harian penuh (bukan per jam atau per menit). Tanggal selesai sewa dapat langsung digunakan untuk sewa berikutnya pada tanggal yang sama setelah jam pengembalian.
4. **Notifikasi WhatsApp Menggunakan URL Redireksi Mandiri (*Deep Link*):**
   - *Penjelasan:* Fitur WhatsApp memanfaatkan format link protokol `https://wa.me/` yang memicu aplikasi WhatsApp kasir secara langsung tanpa perantara WhatsApp Business API resmi berbayar.
5. **Cakupan Responden Uji Usability Terbatas pada Lingkungan Toko:**
   - *Penjelasan:* Pengujian SUS melibatkan 16 responden terarah yang merepresentasikan demografi pelanggan dan pengelola riil BLNTRK Outdoor.

---

## 3. Saran Pengembangan Lanjutan (*Future Work*)

1. **Integrasi QRIS Dinamis & Payment Gateway Otomatis:**
   - Mengintegrasikan Midtrans/Xendit untuk pembayaran instan dengan *webhook* perubahan status pesanan otomatis dari `Menunggu` ke `Dibayar`.
2. **Penerapan Barcode / QR Code pada Tag Fisik Alat:**
   - Menempelkan stiker QR code tahan air pada setiap tas alat untuk mempercepat proses *check-out* serah terima dan *check-in* pengembalian menggunakan pemindai kamera smartphone.
3. **Notifikasi Otomatis via WhatsApp Gateway / FoneAPI:**
   - Pengiriman pengingat H-1 jatuh tempo pengembalian otomatis terjadwal via Cron Job tanpa intervensi manual kasir.
4. **Fitur Paket Hemat Persewaan (*Bundling Package*):**
   - Pembuatan paket komplit (misal: "Paket Pendakian 2 Orang" berisi 1 tenda, 2 sleeping bag, 1 kompor, 1 nesting) dengan diskon harga khusus.
5. **Ekspansi Multi-Cabang (*Multi-Branch Support*):**
   - Mendukung persewaan lintas cabang dengan sinkronisasi inventaris antar-gudang.

---

## 4. Tanya Jawab Kunci Sidang Skripsi (*Defense Cheat Sheet*)

Berikut adalah daftar pertanyaan yang paling sering diajukan oleh dosen penguji beserta strategi jawaban teruji:

### Q1: Mengapa Anda memilih metodologi Rational Unified Process (RUP)?
> **Jawaban:**  
> *"Metodologi RUP dipilih karena memiliki pendekatan berorientasi objek yang terstruktur melalui 4 fase (Inception, Elaboration, Construction, Transition) dan bersifat iteratif. Pada tahap Elaboration, kami dapat mematangkan arsitektur sistem dan memvalidasi risiko teknis terbesar—yaitu algoritma ketersediaan stok irisan tanggal—sebelum melangkah ke konstruksi massal. Selain itu, RUP sangat selaras dengan pemodelan diagram UML standar akademik skripsi."*

### Q2: Bagaimana sistem mencegah terjadinya pemesanan ganda (*double booking*) jika ada dua pelanggan memesan alat yang sama di detik yang bersamaan?
> **Jawaban:**  
> *"Sistem mengimplementasikan penguncian basis data tingkat baris menggunakan `lockForUpdate()` di dalam transaksi basis data atomik (`DB::transaction`). Pada saat pelanggan memproses pesanan, ketersediaan dihitung ulang secara real-time pada rentang tanggal sewa yang dipilih. Jika kuota alat tersisa 1 dan dua permintaan masuk bersamaan, transaksi kedua akan menunggu giliran dan langsung ditolak saat kuota terbukti habis oleh transaksi pertama. Uji konkurensi ini telah diverifikasi pada skenario BB-39."*

### Q3: Mengapa harga sewa disimpan ulang di tabel `pesanan_detail`, padahal tabel `alat` sudah memiliki kolom `harga_per_hari`? Bukankah itu redundan?
> **Jawaban:**  
> *"Penyimpanan nilai harga pada `pesanan_detail` merupakan penerapan prinsip integritas data historis (*historical data integrity*). Apabila pemilik toko menaikkan harga sewa alat di masa depan, pesanan lama yang tercatat pada laporan keuangan bulanan terdahulu tidak boleh ikut berubah nominalnya. Hal ini juga telah dibuktikan pada skenario pengujian BB-20."*

### Q4: Mengapa Anda menerapkan Soft Deletes pada tabel data master alat dan pengguna?
> **Jawaban:**  
> *"Jika data alat yang pernah disewa dihapus permanen (*hard delete*), relasi pada tabel transaksi sewa, laporan keuangan, dan audit pengembalian akan kehilangan integritas referensial (*foreign key constraint violation*). Dengan Soft Deletes (`deleted_at`), alat yang sudah tidak aktif akan tersembunyi dari katalog publik dan form sewa baru, namun seluruh riwayat transaksi historis tetap utuh dan valid."*

### Q5: Bagaimana formula dan logika perhitungan denda keterlambatan pada sistem ini?
> **Jawaban:**  
> *"Formula denda dihitung sebagai:  
> `Denda = Hari Terlambat × Total Tarif Sewa Harian × Persentase Denda`  
> Perhitungan hari keterlambatan menggunakan fungsi pembulatan ke atas (`ceil`) terhadap selisih menit dibagi 1440 menit (24 jam) setelah melewati jam jatuh tempo. Selain itu, sistem menyediakan jendela toleransi (default 60 menit) sehingga jika pelanggan terlambat dalam batas toleransi toko, denda tetap Rp 0 (skenario BB-53). Parameter persentase denda dan toleransi bersifat dinamis dan dapat diubah sewaktu-waktu oleh Pemilik Toko (skenario BB-71)."*

### Q6: Bagaimana sistem menjamin keamanan data dan otorisasi antar-pengguna?
> **Jawaban:**  
> *"Sistem menerapkan pengamanan berlapis:  
> 1. Enkripsi satu arah untuk kata sandi menggunakan algoritma **Bcrypt** (skenario BB-74).  
> 2. Proteksi **CSRF token** pada setiap manipulasi data HTTP POST/PATCH/DELETE (skenario BB-73).  
> 3. Pembatasan akses berbasis peran melalui **Middleware Role** (Admin, Pemilik, Pelanggan).  
> 4. Otorisasi logika bisnis menggunakan **Laravel Policy** (`PesananPolicy`), yang memastikan pelanggan hanya dapat melihat dan membatalkan pesanan miliknya sendiri, serta menolak transisi status ilegal (skenario BB-08 s/d BB-10, BB-45, BB-48, BB-50, BB-59).  
> 5. Penyamaran identitas jaminan fisik (`nomor_identitas_samar`) demi kepatuhan privasi data pengguna."*

### Q7: Berapa skor kegunaan (*usability*) sistem Anda dan bagaimana cara Anda menyimpulkannya?
> **Jawaban:**  
> *"Pengujian kegunaan dilakukan menggunakan instrumen baku **System Usability Scale (SUS)** dengan 10 butir pertanyaan skala Likert 1–5 kepada 16 orang responden (12 pelanggan, 3 admin kasir, dan 1 pemilik toko). Nilai rata-rata skor SUS yang diperoleh adalah **81.88**. Berdasarkan standar literatur Bangor dan Sauro, skor ini masuk dalam kategori **Acceptable (Grade A)** dengan predikat **Excellent**, yang membuktikan bahwa antarmuka sistem intuitif, mudah dipelajari, dan efisien bagi pengguna operasional toko maupun masyarakat umum."*

---

## 5. Panduan Latihan Presentasi & Demonstrasi Sidang (10–12 Menit)

```
[00:00 - 02:00] LATAR BELAKANG & TUJUAN
- Masalah: Pencatatan sewa manual di Subang sering memicu bentrok stok, denda tidak transparan, dan jaminan hilang.
- Solusi: Sistem Informasi Persewaan Alat Outdoor BLNTRK berbasis Web dengan manajemen tanggal ketersediaan.

[02:00 - 04:00] ARSITEKTUR & DESAIN
- Pendekatan RUP, arsitektur Laravel MVC + Service Layer, Foresta Design System.
- Tunjukkan ERD dan State Machine status pesanan.

[04:00 - 08:30] LIVE DEMO SISTEM
1. Pelanggan: Cek tanggal di katalog -> Pesan Tenda 2 hari -> Lihat lembar resi resmi & download PDF.
2. Admin: Buka pesanan -> Konfirmasi Bayar Tunai -> Serah terima jaminan KTP (tunjukkan nomor disamarkan).
3. Pengembalian: Proses kembali terlambat + 1 alat rusak ringan -> Tunjukkan denda otomatis terbit & stok perawatan naik.
4. Pemilik: Buka dashboard -> Tinjau omset riil -> Unduh Laporan PDF & Excel -> Ubah aturan denda di Pengaturan Toko.

[08:30 - 10:30] HASIL PENGUJIAN & EVALUASI
- Uji Otomatis: 129 test passed, 0 failure.
- Black Box: 74 kasus (BB-01 s/d BB-74) 100% sesuai.
- Uji SUS: Skor 81.88 (Grade A - Excellent).

[10:30 - 11:30] KESIMPULAN & TANYA JAWAB
- Sebutkan keterbatasan sistem secara objektif dan saran pengembangan ke depan.
- Buka sesi tanya jawab dengan percaya diri.
```
