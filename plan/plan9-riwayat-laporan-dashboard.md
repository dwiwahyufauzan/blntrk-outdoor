# Plan 9 – Riwayat, Laporan, dan Dashboard

**Tujuan:** riwayat transaksi, laporan yang dapat dicetak PDF, dan dashboard ringkas. **Fitur:** F07, F08, T09, T10. **Estimasi:** 5–6 hari.

## 1. Riwayat (F07)
- **Pelanggan** `/pesanan`: hanya miliknya, filter status, kartu dengan resi, tanggal, total, badge, tautan detail. Detail menampilkan pembayaran, denda, dan status pengembalian. Kosong → "Belum ada riwayat pemesanan".
- **Admin** `/admin/pesanan`: sudah ada (Plan 7). Tambahkan filter rentang tanggal dan tampilan detail lengkap (pesanan, pembayaran, jaminan, pengembalian, denda).
- Gunakan eager loading (`with([...])`) untuk mencegah N+1.

## 2. Laporan (F08)
```powershell
composer require barryvdh/laravel-dompdf
php artisan make:class Services/LaporanService
php artisan make:controller Admin/LaporanController
php artisan make:controller Pemilik/LaporanController
```

| Jenis | Isi | Sumber |
|---|---|---|
| Penyewaan | Daftar pesanan per periode (resi, pelanggan, tanggal, status, total) | `pesanan` |
| Pendapatan | Pendapatan sewa per hari/bulan, plus denda lunas | `pembayaran`, `denda` (lunas) |
| Denda | Daftar denda dan status | `denda` |
| Alat terlaris | Jumlah disewa dan pendapatan per alat | `pesanan_detail` |
| Stok kritis (opsional) | Alat dengan tersedia rendah | `alat` |

**Aturan penting laporan** (hindari salah hitung di sidang):
- Pendapatan sewa dihitung dari `pembayaran.dibayar_pada` (kas masuk), bukan dari tanggal pesanan.
- Pesanan `dibatalkan` tidak masuk pendapatan (kecuali ada pembayaran yang dicatat dan alasan tertulis).
- Denda dihitung sebagai pendapatan **hanya yang berstatus lunas**.
- Filter periode wajib (`dari`, `sampai`), default bulan berjalan.

`LaporanService` mengembalikan collection per jenis, dipakai oleh tampilan HTML, PDF, dan Excel.

### PDF
```php
$pdf = Pdf::loadView('pdf.laporan-'.$jenis, ['data'=>$data,'dari'=>$dari,'sampai'=>$sampai,'toko'=>Pengaturan::ambil('nama_toko')])->setPaper('a4', 'portrait');
return $pdf->download("laporan-{$jenis}-{$dari}-{$sampai}.pdf");
```
Template `resources/views/pdf/`: kop toko, judul, periode, tabel, total, tanggal cetak, nama pencetak. CSS sederhana (DomPDF tidak mendukung flex/grid). Tambahkan juga `pdf/resi.blade.php` untuk resi pesanan.

Laporan kosong → pesan "Data laporan tidak tersedia" (sesuai skenario skripsi).
Pemilik memakai `LaporanService` yang sama, view hanya-baca.

## 3. Dashboard (T09)
**Admin:** kartu (pesanan menunggu, perlu diambil hari ini, sedang disewa, terlambat, denda belum lunas), daftar "perlu tindakan" (terlambat dan batas ambil hari ini), grafik penyewaan 7 hari.
**Pemilik:** pendapatan hari ini dan bulan ini, jumlah transaksi, alat terlaris, stok kritis, grafik pendapatan 6 bulan, jumlah terlambat.
**Pelanggan:** pesanan aktif dan terakhir.

```powershell
# Chart.js via CDN pada layout; data dikirim sebagai JSON dari controller
```
Query ringkasan memakai `selectRaw` dan `groupBy`; cache 1–5 menit bila lambat.

## 4. Pengujian (BB-63 sampai BB-70)
Hitung manual dari data seeder dan bandingkan dengan laporan dan dashboard. **Siapkan `TransaksiDemoSeeder`** (±30 pesanan dengan berbagai status dan tanggal) agar laporan bermakna.

## 5. Definition of Done
- [ ] Riwayat Pelanggan dan Admin benar sesuai hak akses
- [ ] Empat jenis laporan tampil dengan filter periode
- [ ] PDF laporan dan resi terunduh, rapi
- [ ] Angka dashboard dan laporan sesuai hitungan manual
- [ ] Pemilik hanya melihat, tidak dapat mengubah
- [ ] Tangkapan layar laporan, PDF, dan dashboard
