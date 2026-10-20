# 3. Perancangan Database (ERD)

File pendukung: [erd.mmd](erd.mmd) (Mermaid), [erd.dbml](erd.dbml) (dbdiagram.io), [schema.sql](schema.sql) (DDL).

## 3f. Keputusan Desain (dibahas lebih dulu karena memengaruhi seluruh tabel)

| No | Pertanyaan | Keputusan | Alasan (untuk sidang) |
|---|---|---|---|
| D1 | Alat per jenis dengan stok, atau per unit fisik? | **Per jenis dengan stok** (`alat.stok_total`) | Toko menyewakan banyak barang sejenis (misalnya 10 tenda identik) dan pencatatan manual saat ini tidak memberi kode unit. Per unit fisik menuntut kode dan stiker di setiap barang sehingga membebani 60+ transaksi per hari. Kekurangannya, riwayat per unit tidak dapat dilacak. Ini ditulis sebagai keterbatasan dan saran pengembangan. |
| D2 | Bagaimana ketersediaan dihitung per rentang tanggal? | **Dihitung dinamis** dari pesanan aktif, tidak disimpan. `tersedia(hari) = stok_total − stok_perawatan − Σ jumlah pesanan aktif yang mencakup hari itu`. Rentang valid bila `tersedia ≥ jumlah` pada semua hari. Interval setengah terbuka `[mulai, selesai)`. | Stok yang disimpan dan dikurangi/ditambah rawan tidak sinkron. Nilai turunan lebih konsisten. Pesanan `terlambat` tetap dianggap menahan stok sampai hari ini. Penghitungan dilakukan di dalam transaksi dengan `lockForUpdate()` pada baris `alat`. |
| D3 | Tabel detail transaksi? | **Ada: `pesanan_detail`** (N:M antara pesanan dan alat) beserta snapshot harga | Satu pesanan memuat banyak alat. Harga disalin agar perubahan tarif tidak mengubah transaksi lama (menjaga integritas laporan). |
| D4 | Tabel pengembalian dan kondisi alat? | **`pengembalian` (header) dan `pengembalian_detail` (kondisi per baris alat)** | Satu pesanan hanya dikembalikan sekali (1:1), tetapi kondisi tiap alat berbeda, misalnya 2 tenda baik dan 1 rusak ringan. Kondisi memakai empat kolom jumlah agar cocok dengan model stok per jenis. |
| D5 | Tabel denda? | **`denda` terpisah**, jenis `keterlambatan` atau `kerusakan` | Denda memiliki siklus hidup sendiri (belum lunas, lunas), bisa lebih dari satu per pesanan, dan perlu dilaporkan sendiri. Digabung ke `pesanan` akan melanggar 1NF atau meninggalkan kolom bernilai NULL. |
| D6 | Tabel role/hak akses? | **`roles` + `users.role_id`**, bukan Spatie Permission | Hanya tiga role tetap tanpa izin granular. Tabel kecil cukup, mudah dijelaskan, dan tanpa dependensi tambahan. |
| D7 | Soft delete? | **Ya** pada `users`, `pelanggan`, `alat`, `pesanan` (`deleted_at`). Tabel anak tidak. | Keempat tabel dirujuk transaksi historis sehingga penghapusan permanen merusak laporan. FK memakai `RESTRICT`. Tabel anak mengikuti induknya. |
| D8 | Pelanggan walk-in? | **`pelanggan` terpisah dari `users`**, `user_id` boleh NULL | Pelanggan tanpa akun tetap dapat dicatat Admin. Akun (login) dan identitas pelanggan (data bisnis) adalah dua hal berbeda. |
| D9 | Jaminan identitas? | **`jaminan` 1:1 dengan pesanan**, hanya empat digit terakhir nomor | Memenuhi AS-IS (kartu identitas dititipkan) sambil meminimalkan data pribadi (UU PDP). |
| D10 | Status Terlambat disimpan atau diturunkan? | **Disimpan** di `pesanan.status`, diperbarui scheduler per jam, ditambah pengecekan saat halaman dibuka | Memudahkan filter dan laporan. Pengecekan saat halaman dibuka menjaga akurasi di antara dua eksekusi scheduler. |
| D11 | Pembayaran dalam tabel sendiri? | **`pembayaran` (1:N ke pesanan)** | Mendukung catatan koreksi dan jejak siapa yang menerima uang. Tetap manual (tanpa gateway). |

## 3a. Entitas dan Atribut (ringkas)

Tipe dan constraint lengkap ada di [schema.sql](schema.sql) dan kamus data (3e).

| Entitas | PK | FK | Constraint penting |
|---|---|---|---|
| roles | id | – | `nama` UNIQUE |
| users | id | role_id → roles | `email` UNIQUE, soft delete |
| pelanggan | id | user_id → users (NULL) | `user_id` UNIQUE, soft delete |
| kategori_alat | id | – | `nama`, `slug` UNIQUE |
| alat | id | kategori_id → kategori_alat | `kode_alat` UNIQUE, CHECK `stok_perawatan ≤ stok_total`, `harga ≥ 0`, soft delete |
| pesanan | id | pelanggan_id, dibuat_oleh, diproses_oleh | `no_resi` UNIQUE, CHECK `tanggal_selesai > tanggal_mulai`, index (status, tanggal) |
| pesanan_detail | id | pesanan_id, alat_id | UNIQUE (pesanan_id, alat_id), `jumlah > 0` |
| pembayaran | id | pesanan_id, dicatat_oleh | `jumlah > 0` |
| jaminan | id | pesanan_id, diterima_oleh, dikembalikan_oleh | `pesanan_id` UNIQUE |
| pengembalian | id | pesanan_id, diterima_oleh | `pesanan_id` UNIQUE |
| pengembalian_detail | id | pengembalian_id, pesanan_detail_id | `pesanan_detail_id` UNIQUE, penjumlahan kondisi divalidasi di aplikasi |
| denda | id | pesanan_id, pengembalian_id, pengembalian_detail_id (NULL), dilunasi_oleh (NULL) | `jumlah ≥ 0` |
| pengaturan | kunci | – | – |
| log_aktivitas | id | user_id (NULL) | index (modul, ref_id) |

Tabel bawaan Laravel (`password_reset_tokens`, `sessions`, `cache`, `jobs`) tidak digambar di ERD.

## 3b. Relasi dan Kardinalitas

| No | Relasi | Kardinalitas | Penjelasan |
|---|---|---|---|
| 1 | roles – users | 1:N | Satu role dimiliki banyak user. Satu user hanya satu role. |
| 2 | users – pelanggan | 1:0..1 | Pelanggan terdaftar punya satu akun. Walk-in tidak punya akun. |
| 3 | kategori_alat – alat | 1:N | Satu kategori memuat banyak alat. |
| 4 | pelanggan – pesanan | 1:N | Satu pelanggan dapat membuat banyak pesanan. |
| 5 | users – pesanan (dibuat_oleh, diproses_oleh) | 1:N (dua relasi) | Membedakan pelaku pembuatan (Pelanggan atau Admin) dan Admin yang memproses. |
| 6 | pesanan – alat | **N:M** | Satu pesanan memuat banyak alat dan satu alat disewa banyak pesanan. Diurai oleh `pesanan_detail`, yang menyimpan `jumlah` dan harga. |
| 7 | pesanan – pembayaran | 1:N | Mendukung koreksi atau pembayaran bertahap. |
| 8 | pesanan – jaminan | 1:0..1 | Jaminan baru ada saat alat diambil. |
| 9 | pesanan – pengembalian | 1:0..1 | Satu pesanan dikembalikan satu kali (semua alat sekaligus). |
| 10 | pengembalian – pengembalian_detail | 1:N | Satu baris kondisi untuk tiap alat dalam pesanan. |
| 11 | pesanan_detail – pengembalian_detail | 1:0..1 | Tiap baris detail diperiksa tepat satu kali. |
| 12 | pesanan / pengembalian – denda | 1:N | Satu pengembalian dapat menghasilkan denda keterlambatan dan beberapa denda kerusakan. |
| 13 | pengembalian_detail – denda | 1:N (opsional) | Denda kerusakan merujuk pada baris alat yang rusak. |
| 14 | users – log_aktivitas | 1:N | Satu user mencatat banyak aktivitas. |

## 3c. Normalisasi sampai 3NF

**Bentuk tidak normal (UNF)**, meniru buku catatan toko:

`Transaksi(no_resi, nama_pelanggan, no_hp, alamat, tanggal_mulai, tanggal_selesai, {kode_alat, nama_alat, kategori, harga_per_hari, jumlah, subtotal}, total, status, jenis_jaminan, kondisi_kembali, denda, nama_petugas, role_petugas)`

**1NF**: hilangkan grup berulang `{alat…}`. Setiap baris = satu alat dalam satu pesanan. Atribut menjadi atomik. Kunci: (no_resi, kode_alat).

**2NF**: hilangkan ketergantungan parsial pada sebagian kunci (no_resi, kode_alat).
- Bergantung hanya pada `no_resi`: data pelanggan, tanggal, total, status, jaminan, petugas, menjadi tabel **pesanan**.
- Bergantung hanya pada `kode_alat`: nama_alat, kategori, harga, menjadi tabel **alat**.
- Bergantung pada keduanya: jumlah, subtotal (dan harga snapshot), menjadi tabel **pesanan_detail**.

**3NF**: hilangkan ketergantungan transitif.
- `pesanan`: nama_pelanggan, no_hp, alamat bergantung pada pelanggan, bukan resi → **pelanggan**.
- `pesanan`: role_petugas bergantung pada petugas → **users** dan **roles**.
- `alat`: nama_kategori bergantung pada kategori → **kategori_alat**.
- Data jaminan, pengembalian, kondisi, dan denda memiliki determinan sendiri → **jaminan**, **pengembalian**, **pengembalian_detail**, **denda**.

**Hasil**: setiap atribut non-kunci bergantung penuh pada kunci utama dan hanya padanya.

> [!NOTE]
> **Denormalisasi terkendali (dijelaskan di sidang):**
> - `pesanan_detail.harga_per_hari` adalah snapshot yang disengaja, bukan pelanggaran 3NF. Nilainya adalah fakta historis transaksi.
> - `pesanan_detail.subtotal`, `pesanan.total_sewa`, dan `durasi_hari` adalah nilai turunan yang disimpan untuk jejak audit dan kecepatan laporan. Keduanya dihitung ulang dan diverifikasi di aplikasi.

## 3d. ERD

Lihat [erd.mmd](erd.mmd) dan [erd.dbml](erd.dbml). Untuk skripsi, tempel DBML ke dbdiagram.io, ekspor PNG, lalu masukkan sebagai gambar bernomor.

## 3e. Kamus Data

Format kolom: Atribut, Tipe, Panjang/Jenis, Constraint, Keterangan. PK = primary key, FK = foreign key, UK = unique, NN = not null.

### roles
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | TINYINT UNSIGNED | PK, AI | ID role |
| nama | VARCHAR(20) | UK, NN | pemilik, admin, pelanggan |
| label | VARCHAR(30) | NN | Nama tampilan |

### users
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID user |
| role_id | TINYINT UNSIGNED | FK → roles.id, NN | Hak akses |
| nama | VARCHAR(100) | NN | Nama user |
| email | VARCHAR(100) | UK, NN | Identitas login |
| email_verified_at | DATETIME | NULL | Verifikasi (opsional) |
| password | VARCHAR(255) | NN | Hash bcrypt |
| is_aktif | TINYINT(1) | NN, default 1 | Akun aktif |
| remember_token | VARCHAR(100) | NULL | Token sesi |
| created_at, updated_at | DATETIME | NULL | Jejak waktu |
| deleted_at | DATETIME | NULL | Soft delete |

### pelanggan
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID pelanggan |
| user_id | BIGINT UNSIGNED | FK → users.id, UK, NULL | NULL = walk-in |
| nama_lengkap | VARCHAR(100) | NN | Nama sesuai identitas |
| no_hp | VARCHAR(20) | NN | Nomor WhatsApp/telepon |
| alamat | TEXT | NULL | Alamat |
| created_at, updated_at, deleted_at | DATETIME | NULL | Jejak dan soft delete |

### kategori_alat
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | SMALLINT UNSIGNED | PK, AI | ID kategori |
| nama | VARCHAR(50) | UK, NN | Mis. Tenda, Carrier |
| slug | VARCHAR(60) | UK, NN | Untuk URL |
| deskripsi | TEXT | NULL | Keterangan |
| created_at, updated_at | DATETIME | NULL | Jejak waktu |

### alat
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID alat |
| kategori_id | SMALLINT UNSIGNED | FK → kategori_alat.id, NN | Kategori |
| kode_alat | VARCHAR(20) | UK, NN | Mis. TND-D4 |
| nama | VARCHAR(100) | NN | Nama alat |
| varian | VARCHAR(50) | NULL | Ukuran/kapasitas |
| deskripsi | TEXT | NULL | Spesifikasi |
| foto | VARCHAR(255) | NULL | Path gambar |
| harga_per_hari | DECIMAL(10,2) | NN, ≥ 0 | Tarif sewa |
| stok_total | INT UNSIGNED | NN, default 0 | Jumlah milik toko (tidak termasuk hilang) |
| stok_perawatan | INT UNSIGNED | NN, default 0, ≤ stok_total | Sedang diperbaiki |
| is_aktif | TINYINT(1) | NN, default 1 | Tampil di katalog |
| created_at, updated_at, deleted_at | DATETIME | NULL | Jejak dan soft delete |

### pesanan
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID pesanan |
| no_resi | VARCHAR(20) | UK, NN | Format BLN-YYMMDD-0001 |
| pelanggan_id | BIGINT UNSIGNED | FK → pelanggan.id, NN | Penyewa |
| dibuat_oleh | BIGINT UNSIGNED | FK → users.id, NN | Pelaku pemesanan |
| diproses_oleh | BIGINT UNSIGNED | FK → users.id, NULL | Admin pemroses terakhir |
| tanggal_mulai | DATE | NN | Awal sewa |
| tanggal_selesai | DATE | NN, > mulai | Akhir sewa |
| durasi_hari | SMALLINT UNSIGNED | NN | Selisih hari |
| jatuh_tempo | DATETIME | NN | Batas kembali |
| batas_ambil | DATETIME | NN | Batas pengambilan |
| status | ENUM | NN, default menunggu | menunggu, dibayar, disewa, terlambat, dikembalikan, dibatalkan |
| total_sewa | DECIMAL(12,2) | NN | Total biaya sewa |
| diambil_pada | DATETIME | NULL | Waktu pengambilan |
| dibatalkan_pada | DATETIME | NULL | Waktu pembatalan |
| alasan_batal | VARCHAR(255) | NULL | Alasan |
| catatan | VARCHAR(255) | NULL | Catatan |
| created_at, updated_at, deleted_at | DATETIME | NULL | Jejak dan soft delete |

### pesanan_detail
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID detail |
| pesanan_id | BIGINT UNSIGNED | FK → pesanan.id, NN, ON DELETE CASCADE | Induk |
| alat_id | BIGINT UNSIGNED | FK → alat.id, NN | Alat |
| jumlah | SMALLINT UNSIGNED | NN, > 0 | Jumlah disewa |
| harga_per_hari | DECIMAL(10,2) | NN | Snapshot tarif |
| subtotal | DECIMAL(12,2) | NN | harga × jumlah × durasi |
| (pesanan_id, alat_id) | – | UK | Alat tidak ganda dalam satu pesanan |

### pembayaran
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID pembayaran |
| pesanan_id | BIGINT UNSIGNED | FK → pesanan.id, NN | Pesanan |
| jumlah | DECIMAL(12,2) | NN, > 0 | Nominal diterima |
| metode | ENUM | NN, default tunai | tunai, transfer (manual) |
| dibayar_pada | DATETIME | NN | Waktu diterima |
| dicatat_oleh | BIGINT UNSIGNED | FK → users.id, NN | Admin |
| catatan | VARCHAR(255) | NULL | Keterangan |
| created_at, updated_at | DATETIME | NULL | Jejak waktu |

### jaminan
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID jaminan |
| pesanan_id | BIGINT UNSIGNED | FK → pesanan.id, UK, NN | Pesanan |
| jenis | ENUM | NN | ktp, sim, kartu_pelajar, lainnya |
| nomor_identitas_samar | VARCHAR(30) | NN | Empat digit terakhir |
| keterangan | VARCHAR(255) | NULL | Catatan |
| status | ENUM | NN, default dititipkan | dititipkan, dikembalikan |
| diterima_pada / diterima_oleh | DATETIME / BIGINT | NN / FK users | Waktu dan petugas penerima |
| dikembalikan_pada / dikembalikan_oleh | DATETIME / BIGINT | NULL / FK users | Waktu dan petugas pengembali |
| created_at, updated_at | DATETIME | NULL | Jejak waktu |

### pengembalian
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID pengembalian |
| pesanan_id | BIGINT UNSIGNED | FK → pesanan.id, UK, NN | Pesanan |
| dikembalikan_pada | DATETIME | NN | Waktu aktual |
| hari_terlambat | SMALLINT UNSIGNED | NN, default 0 | Hasil perhitungan |
| diterima_oleh | BIGINT UNSIGNED | FK → users.id, NN | Admin |
| catatan | TEXT | NULL | Catatan umum |
| created_at, updated_at | DATETIME | NULL | Jejak waktu |

### pengembalian_detail
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID |
| pengembalian_id | BIGINT UNSIGNED | FK → pengembalian.id, NN, CASCADE | Induk |
| pesanan_detail_id | BIGINT UNSIGNED | FK → pesanan_detail.id, UK, NN | Baris alat |
| jml_baik | SMALLINT UNSIGNED | NN, default 0 | Kondisi baik |
| jml_rusak_ringan | SMALLINT UNSIGNED | NN, default 0 | Rusak ringan |
| jml_rusak_berat | SMALLINT UNSIGNED | NN, default 0 | Rusak berat |
| jml_hilang | SMALLINT UNSIGNED | NN, default 0 | Hilang |
| catatan | VARCHAR(255) | NULL | Keterangan kondisi |

Aturan: jumlah keempat kolom = `pesanan_detail.jumlah`.

### denda
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID denda |
| pesanan_id | BIGINT UNSIGNED | FK → pesanan.id, NN | Pesanan |
| pengembalian_id | BIGINT UNSIGNED | FK → pengembalian.id, NN | Sumber |
| pengembalian_detail_id | BIGINT UNSIGNED | FK → pengembalian_detail.id, NULL | Alat rusak/hilang |
| jenis | ENUM | NN | keterlambatan, kerusakan |
| keterangan | VARCHAR(255) | NULL | Rincian |
| jumlah | DECIMAL(12,2) | NN, ≥ 0 | Nominal tagihan |
| status_bayar | ENUM | NN, default belum_lunas | belum_lunas, lunas |
| dilunasi_pada / dilunasi_oleh | DATETIME / BIGINT | NULL / FK users | Pelunasan manual |
| created_at, updated_at | DATETIME | NULL | Jejak waktu |

### pengaturan
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| kunci | VARCHAR(50) | PK | Nama parameter |
| nilai | VARCHAR(255) | NN | Nilai |
| keterangan | VARCHAR(255) | NULL | Deskripsi |

### log_aktivitas
| Atribut | Tipe | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT UNSIGNED | PK, AI | ID log |
| user_id | BIGINT UNSIGNED | FK → users.id, NULL | Pelaku (NULL = sistem) |
| aksi | VARCHAR(50) | NN | Mis. konfirmasi_bayar |
| modul | VARCHAR(50) | NN | Mis. pesanan |
| ref_id | BIGINT UNSIGNED | NULL | ID data terkait |
| deskripsi | VARCHAR(255) | NULL | Rincian |
| ip_address | VARCHAR(45) | NULL | Alamat IP |
| created_at | DATETIME | NN | Waktu |

## Catatan Penting Implementasi Ketersediaan

- Pesanan berstatus `terlambat` memiliki `tanggal_selesai` yang sudah lewat. Dalam perhitungan, perlakukan rentangnya sebagai `[tanggal_mulai, max(tanggal_selesai, hari_ini + 1))` agar stok tetap tertahan.
- Pembuatan pesanan harus dalam `DB::transaction` dengan `lockForUpdate()` pada baris `alat` yang dipesan, supaya dua pelanggan tidak mengambil stok terakhir bersamaan.
- Saat pengembalian: `jml_rusak_*` menambah `alat.stok_perawatan`, `jml_hilang` mengurangi `alat.stok_total`. Perhatikan CHECK `stok_perawatan ≤ stok_total`.
