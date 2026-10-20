-- Skema MySQL 8 - BLNTRK Outdoor (acuan; implementasi sebenarnya lewat migration Laravel)
CREATE DATABASE IF NOT EXISTS blntrk_outdoor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blntrk_outdoor;

CREATE TABLE roles (
  id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(20) NOT NULL UNIQUE,
  label VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id TINYINT UNSIGNED NOT NULL,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  email_verified_at DATETIME NULL,
  password VARCHAR(255) NOT NULL,
  is_aktif TINYINT(1) NOT NULL DEFAULT 1,
  remember_token VARCHAR(100) NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pelanggan (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL UNIQUE,
  nama_lengkap VARCHAR(100) NOT NULL,
  no_hp VARCHAR(20) NOT NULL,
  alamat TEXT NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
  CONSTRAINT fk_pelanggan_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE kategori_alat (
  id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL UNIQUE,
  slug VARCHAR(60) NOT NULL UNIQUE,
  deskripsi TEXT NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE alat (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kategori_id SMALLINT UNSIGNED NOT NULL,
  kode_alat VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  varian VARCHAR(50) NULL,
  deskripsi TEXT NULL,
  foto VARCHAR(255) NULL,
  harga_per_hari DECIMAL(10,2) NOT NULL,
  stok_total INT UNSIGNED NOT NULL DEFAULT 0,
  stok_perawatan INT UNSIGNED NOT NULL DEFAULT 0,
  is_aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
  CONSTRAINT fk_alat_kategori FOREIGN KEY (kategori_id) REFERENCES kategori_alat(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_alat_harga CHECK (harga_per_hari >= 0),
  CONSTRAINT ck_alat_stok CHECK (stok_perawatan <= stok_total)
) ENGINE=InnoDB;

CREATE TABLE pesanan (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  no_resi VARCHAR(20) NOT NULL UNIQUE,
  pelanggan_id BIGINT UNSIGNED NOT NULL,
  dibuat_oleh BIGINT UNSIGNED NOT NULL,
  diproses_oleh BIGINT UNSIGNED NULL,
  tanggal_mulai DATE NOT NULL,
  tanggal_selesai DATE NOT NULL,
  durasi_hari SMALLINT UNSIGNED NOT NULL,
  jatuh_tempo DATETIME NOT NULL,
  batas_ambil DATETIME NOT NULL,
  status ENUM('menunggu','dibayar','disewa','terlambat','dikembalikan','dibatalkan') NOT NULL DEFAULT 'menunggu',
  total_sewa DECIMAL(12,2) NOT NULL,
  diambil_pada DATETIME NULL,
  dibatalkan_pada DATETIME NULL,
  alasan_batal VARCHAR(255) NULL,
  catatan VARCHAR(255) NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
  CONSTRAINT fk_pesanan_pelanggan FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pesanan_dibuat FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pesanan_diproses FOREIGN KEY (diproses_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT ck_pesanan_tanggal CHECK (tanggal_selesai > tanggal_mulai),
  INDEX idx_pesanan_status_tgl (status, tanggal_mulai, tanggal_selesai),
  INDEX idx_pesanan_pelanggan (pelanggan_id)
) ENGINE=InnoDB;

CREATE TABLE pesanan_detail (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id BIGINT UNSIGNED NOT NULL,
  alat_id BIGINT UNSIGNED NOT NULL,
  jumlah SMALLINT UNSIGNED NOT NULL,
  harga_per_hari DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  UNIQUE KEY uq_detail (pesanan_id, alat_id),
  CONSTRAINT fk_detail_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detail_alat FOREIGN KEY (alat_id) REFERENCES alat(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_detail_jumlah CHECK (jumlah > 0)
) ENGINE=InnoDB;

CREATE TABLE pembayaran (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id BIGINT UNSIGNED NOT NULL,
  jumlah DECIMAL(12,2) NOT NULL,
  metode ENUM('tunai','transfer') NOT NULL DEFAULT 'tunai',
  dibayar_pada DATETIME NOT NULL,
  dicatat_oleh BIGINT UNSIGNED NOT NULL,
  catatan VARCHAR(255) NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL,
  CONSTRAINT fk_bayar_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_bayar_user FOREIGN KEY (dicatat_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_bayar_jumlah CHECK (jumlah > 0)
) ENGINE=InnoDB;

CREATE TABLE jaminan (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id BIGINT UNSIGNED NOT NULL UNIQUE,
  jenis ENUM('ktp','sim','kartu_pelajar','lainnya') NOT NULL,
  nomor_identitas_samar VARCHAR(30) NOT NULL,
  keterangan VARCHAR(255) NULL,
  status ENUM('dititipkan','dikembalikan') NOT NULL DEFAULT 'dititipkan',
  diterima_pada DATETIME NOT NULL,
  diterima_oleh BIGINT UNSIGNED NOT NULL,
  dikembalikan_pada DATETIME NULL,
  dikembalikan_oleh BIGINT UNSIGNED NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL,
  CONSTRAINT fk_jaminan_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_jaminan_terima FOREIGN KEY (diterima_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_jaminan_kembali FOREIGN KEY (dikembalikan_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE pengembalian (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id BIGINT UNSIGNED NOT NULL UNIQUE,
  dikembalikan_pada DATETIME NOT NULL,
  hari_terlambat SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  diterima_oleh BIGINT UNSIGNED NOT NULL,
  catatan TEXT NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL,
  CONSTRAINT fk_kembali_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_kembali_user FOREIGN KEY (diterima_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pengembalian_detail (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pengembalian_id BIGINT UNSIGNED NOT NULL,
  pesanan_detail_id BIGINT UNSIGNED NOT NULL UNIQUE,
  jml_baik SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  jml_rusak_ringan SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  jml_rusak_berat SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  jml_hilang SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  catatan VARCHAR(255) NULL,
  CONSTRAINT fk_kd_kembali FOREIGN KEY (pengembalian_id) REFERENCES pengembalian(id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_kd_detail FOREIGN KEY (pesanan_detail_id) REFERENCES pesanan_detail(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;
-- Aturan jml_baik + jml_rusak_ringan + jml_rusak_berat + jml_hilang = pesanan_detail.jumlah
-- divalidasi di aplikasi (Form Request), karena CHECK lintas tabel tidak didukung MySQL.

CREATE TABLE denda (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id BIGINT UNSIGNED NOT NULL,
  pengembalian_id BIGINT UNSIGNED NOT NULL,
  pengembalian_detail_id BIGINT UNSIGNED NULL,
  jenis ENUM('keterlambatan','kerusakan') NOT NULL,
  keterangan VARCHAR(255) NULL,
  jumlah DECIMAL(12,2) NOT NULL,
  status_bayar ENUM('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas',
  dilunasi_pada DATETIME NULL,
  dilunasi_oleh BIGINT UNSIGNED NULL,
  created_at DATETIME NULL, updated_at DATETIME NULL,
  CONSTRAINT fk_denda_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_denda_kembali FOREIGN KEY (pengembalian_id) REFERENCES pengembalian(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_denda_kd FOREIGN KEY (pengembalian_detail_id) REFERENCES pengembalian_detail(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_denda_user FOREIGN KEY (dilunasi_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT ck_denda_jumlah CHECK (jumlah >= 0)
) ENGINE=InnoDB;

CREATE TABLE pengaturan (
  kunci VARCHAR(50) PRIMARY KEY,
  nilai VARCHAR(255) NOT NULL,
  keterangan VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE log_aktivitas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  aksi VARCHAR(50) NOT NULL,
  modul VARCHAR(50) NOT NULL,
  ref_id BIGINT UNSIGNED NULL,
  deskripsi VARCHAR(255) NULL,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_log_modul (modul, ref_id),
  INDEX idx_log_waktu (created_at)
) ENGINE=InnoDB;

-- Data awal
INSERT INTO roles (nama, label) VALUES ('pemilik','Pemilik'),('admin','Admin'),('pelanggan','Pelanggan');
INSERT INTO pengaturan (kunci, nilai, keterangan) VALUES
 ('persen_denda_harian','100','Persen tarif harian per hari terlambat'),
 ('jam_kembali','12:00','Jam jatuh tempo pengembalian'),
 ('toleransi_menit','60','Toleransi keterlambatan (menit)'),
 ('maks_durasi_hari','14','Durasi sewa maksimum'),
 ('nama_toko','BLNTRK Outdoor','Nama toko pada laporan'),
 ('no_wa_toko','6281234567890','Nomor WhatsApp toko (contoh)');

-- Query ketersediaan satu alat pada satu hari (ganti :alat dan :tgl)
-- SELECT a.stok_total - a.stok_perawatan - COALESCE((
--   SELECT SUM(d.jumlah)
--   FROM pesanan_detail d
--   JOIN pesanan p ON p.id = d.pesanan_id
--   WHERE d.alat_id = a.id
--     AND p.status IN ('menunggu','dibayar','disewa','terlambat')
--     AND p.deleted_at IS NULL
--     AND :tgl >= p.tanggal_mulai AND :tgl < p.tanggal_selesai
-- ), 0) AS tersedia
-- FROM alat a WHERE a.id = :alat;
-- Ketersediaan satu rentang = nilai minimum "tersedia" dari seluruh hari pada rentang tersebut.
