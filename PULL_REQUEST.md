# Pull Request: Fitur Stok Real-Time, Keranjang Belanja, Paket Bundling, Manajemen Denda & Ganti Rugi, Serta Universal Pop-up Modal

## 📌 Ringkasan Perubahan (Summary)
Pull Request ini mengintegrasikan serangkaian fitur baru, perbaikan alur bisnis persewaan outdoor, penyempurnaan UI/UX Foresta Adventure, serta standarisasi interaksi modal pop-up konfirmasi di seluruh platform **BLNTRK Outdoor**.

Seluruh fitur telah dilengkapi dengan automated feature tests dan terverifikasi **170 tests lulus 100% (Green)**.

---

## 🚀 Fitur Utama & Penyempurnaan yang Diimplementasikan

### 1. Manajemen Stok Real-Time & Restock Otomatis
- **Stok Tersedia Dinamis:**
  - Ditambahkan accessor `stok_tersedia` pada `Alat.php` yang memperhitungkan stok fisik dikurangi unit aktif yang sedang disewa (`menunggu`, `dibayar`, `disewa`, `terlambat`).
  - Eager-loading agregasi massal via `Alat::muatStokTersedia()` untuk mencegah masalah query $N+1$.
- **Pengurangan Otomatis Saat Disewa:** Stok langsung berkurang saat reservasi disewa/aktif.
- **Restock Otomatis Saat Pengembalian:** Unit alat otomatis ter-restock kembali saat pesanan selesai diproses pengembalian oleh admin. Jika terdapat barang yang rusak atau hilang, stok fisik atau perawatan disesuaikan permanen.
- **Pencegahan Pemesanan Alat Kosong (Out of Stock):** Pemesanan barang kosong diblokir baik pada katalog, form pesanan, keranjang, maupun saat checkout.
- **Katalog Satu Halaman Full & Pemisahan Out of Stock:**
  - Katalog menampilkan seluruh produk dalam satu halaman tanpa pembagian paginasi.
  - Barang yang out of stock dipisahkan di bagian paling bawah dengan kartu *muted/grayscale*, badge merah *Stok Habis*, dan tombol disabled *Stok Habis (Sedang Disewa)*.

---

### 2. Fitur Keranjang Belanja (Cart System)
- **Manajemen Keranjang Fleksibel:**
  - Menambah alat atau paket bundling ke keranjang.
  - Update kuantitas barang dengan validasi stok tersedia.
  - Hapus item per satuan dan tombol kosongkan seluruh keranjang.
- **Checkout Terintegrasi:**
  - Ringkasan total sewa, durasi sewa, dan integrasi langsung ke alur pembayaran/pemesanan.
  - Floating badge jumlah item keranjang di navbar pelanggan.

---

### 3. Fitur Paket Bundling Outdoor (Paket 1, Paket 2, Paket 3)
- **CRUD Admin Paket Bundling:**
  - Admin dapat mengelola nama paket, deskripsi, harga per hari, status aktif, gambar, dan relasi alat pendukung beserta kuantitasnya.
- **Katalog Publik Paket Bundling:**
  - Halaman khusus paket bundling dengan tampilan kartu rasio 1:1, rincian isi item dalam paket, penghematan harga sewa, dan tombol sewa langsung atau tambah ke keranjang.
- **Seeder Paket:** Disediakan seeder awal untuk Paket Hemat Berdua (Paket 1), Paket Family Camp (Paket 2), dan Paket Ekspedisi Summit (Paket 3).

---

### 4. Manajemen Denda, Ganti Rugi Berdasarkan Harga Beli, & Penahanan Jaminan
- **Kategori Denda Fleksibel:**
  - Menu kelola kategori denda di panel admin (keterlambatan, kehilangan, kerusakan ringan/berat, kotor/lumpur).
- **Ganti Rugi Kehilangan Berdasarkan Harga Beli:**
  - Penambahan kolom `harga_beli` pada master data alat outdoor.
  - Jika alat hilang saat pengembalian, nominal ganti rugi otomatis mengambil nilai dari `harga_beli` alat tersebut (bukan nominal paten).
- **Penahanan Jaminan:**
  - Jaminan pelanggan ditahan otomatis jika pesanan memiliki denda yang belum diselesaikan/lunas.
  - Tombol penyerahan jaminan di panel admin otomatis terkunci hingga denda diselesaikan.
- **Transparansi Informasi Denda untuk Pelanggan:**
  - Bagian edukasi kebijakan denda di beranda publik.
  - Informasi denda, rincian keterlambatan per hari, dan nominal ganti rugi tampil transparan pada detail pesanan pelanggan.

---

### 5. Universal Pop-up Modal System (Foresta Adventure UI)
- **Menggantikan Dialog Browser Bawaan:** Menghilangkan `window.confirm()` bawaan browser.
- **Komponen Terpusat `<x-modal-konfirmasi />`:**
  - Desain modern khas Foresta Adventure (backdrop blur, warna Foresta `#5edd5b` & `#0e1b15`, font Urbanist, rounded-2xl).
  - Tiga varian visual: `danger` (hapus/pembatalan), `warning` (perhatian/jual), `primary` (konfirmasi umum).
  - Terintegrasi di seluruh panel Admin, Pemilik Toko, dan Halaman Publik/Pelanggan via atribut deklaratif `data-konfirmasi`.

---

### 6. Perapihan Visual Resi, Dokumen Cetak, & Navbar/Sidebar
- **Dokumen Resi Standar Cetak Dokumen:**
  - Tampilan resi web dan PDF disesuaikan agar tampak seperti dokumen resmi cetak invoice/surat jalan profesional lengkap dengan identitas toko, nomor resi, rincian item, dan kolom tanda tangan.
- **Perapihan Panel Admin & Navigasi:**
  - Logo sidebar menyatu dengan navbar atas.
  - Profil admin dan pelanggan menggunakan dropdown elegan di navbar.
  - Halaman login dan edit profil admin menggunakan tema panel operasional yang konsisten.

---

## 📂 Rincian Berkas yang Ditambahkan & Dimodifikasi

### 📁 Berkas Baru (Added):
1. **Model & Controller:**
   - `app/Http/Controllers/KeranjangController.php`
   - `app/Http/Controllers/PaketBundlingController.php`
   - `app/Http/Controllers/Admin/PaketBundlingController.php`
   - `app/Http/Controllers/Admin/KategoriDendaController.php`
   - `app/Http/Controllers/SesiController.php`
   - `app/Models/PaketBundling.php`
   - `app/Models/PaketBundlingDetail.php`
   - `app/Models/KategoriDenda.php`
   - `app/Services/KeranjangService.php`
2. **Database Migrations & Seeders:**
   - `database/migrations/2026_03_02_000001_create_kategori_denda_table.php`
   - `database/migrations/2026_10_30_111544_add_harga_beli_to_alat_table.php`
   - `database/migrations/2026_10_30_154807_create_paket_bundling_table.php`
   - `database/seeders/PaketBundlingSeeder.php`
3. **Views & Komponen:**
   - `resources/views/components/modal-konfirmasi.blade.php`
   - `resources/views/keranjang/index.blade.php`
   - `resources/views/paket-bundling/index.blade.php`
   - `resources/views/admin/paket-bundling/` (index, create, edit, show)
   - `resources/views/admin/kategori-denda/` (index, create, edit)
   - `resources/views/pesanan/resi.blade.php`
   - `resources/views/sesi/login-admin.blade.php`
4. **Automated Feature Tests:**
   - `tests/Feature/StokDanKetersediaanProdukTest.php`
   - `tests/Feature/KeranjangDanPaketBundlingTest.php`
   - `tests/Feature/KategoriDendaDanPenahananJaminanTest.php`
   - `tests/Feature/NavbarProfilPelangganTest.php`

### 📝 Berkas yang Dimodifikasi (Modified):
- `app/Models/Alat.php`
- `app/Models/Denda.php`
- `app/Enums/JenisDenda.php`
- `app/Providers/AppServiceProvider.php`
- `app/Services/PengembalianService.php`
- `app/Services/PesananService.php`
- `app/Http/Controllers/KatalogController.php`
- `app/Http/Controllers/BerandaController.php`
- `app/Http/Controllers/Admin/AlatController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/DendaController.php`
- `app/Http/Controllers/Admin/PengembalianController.php`
- `app/Http/Controllers/Admin/PesananController.php`
- `resources/views/katalog/index.blade.php` & `show.blade.php`
- `resources/views/beranda.blade.php`
- `resources/views/pesanan/create.blade.php` & `show.blade.php`
- `resources/views/layouts/admin.blade.php`, `layouts/publik.blade.php`, `layouts/pemilik.blade.php`
- Seluruh index view untuk konfirmasi aksi hapus modal di panel admin & pemilik.
- `routes/web.php`

---

## 🧪 Hasil Pengujian (Automated Tests)
Semua skenario pengujian otomatis dijalankan menggunakan PHPUnit di lingkungan Laravel:

```bash
php artisan test
```

**Hasil:**
```text
   PASS  Tests\Unit\...
   PASS  Tests\Feature\AlatTest
   PASS  Tests\Feature\AutentikasiTest
   PASS  Tests\Feature\BerandaTest
   PASS  Tests\Feature\DashboardAdminTest
   PASS  Tests\Feature\DashboardPemilikTest
   PASS  Tests\Feature\DendaDanGantiRugiTest
   PASS  Tests\Feature\KategoriAlatTest
   PASS  Tests\Feature\KategoriDendaDanPenahananJaminanTest
   PASS  Tests\Feature\KatalogTest
   PASS  Tests\Feature\KeranjangDanPaketBundlingTest
   PASS  Tests\Feature\LaporanTest
   PASS  Tests\Feature\NavbarProfilPelangganTest
   PASS  Tests\Feature\PelangganTest
   PASS  Tests\Feature\PengembalianDendaTest
   PASS  Tests\Feature\PengembalianTest
   PASS  Tests\Feature\PesananTest
   PASS  Tests\Feature\StokDanKetersediaanProdukTest
   PASS  Tests\Feature\UserManagementTest

   Tests:    170 passed (698 assertions)
   Duration: 26.30s
   Status:   ALL GREEN
```

---

## ✅ Checklist Pengujian Manual
- [x] Keterangan stok tampil akurat di katalog, beranda, detail alat, dan form sewa.
- [x] Menyewa alat mengurangi stok tersedia secara otomatis.
- [x] Menyelesaikan pengembalian merestock alat kembali secara real-time.
- [x] Alat dengan stok 0 tidak dapat dipesan di halaman manapun.
- [x] Katalog menampilkan semua alat dalam 1 halaman utuh tanpa tombol pagination.
- [x] Barang yang out of stock dipisahkan ke bagian bawah.
- [x] Seluruh tombol hapus dan konfirmasi memicu pop-up Foresta Adventure yang seragam.
- [x] Fitur keranjang belanja dapat menambah, merubah kuantitas, dan checkout pesanan.
- [x] Paket bundling dapat dikelola admin dan disewa pelanggan.
- [x] Denda kehilangan otomatis menggunakan harga beli alat.
- [x] Jaminan tertahan jika denda belum diselesaikan.
- [x] Dokumen resi dan laporan cetak rapi siap cetak.
