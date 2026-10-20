# Plan 4 – Master Data (Kategori, Alat, Pelanggan, User)

**Tujuan:** Admin dapat mengelola data dasar. **Fitur:** F02, F09. **Estimasi:** 4–5 hari.

## 1. Controller dan Route (di grup `admin`)
```powershell
php artisan make:controller Admin/KategoriController --resource --model=KategoriAlat
php artisan make:controller Admin/AlatController --resource --model=Alat
php artisan make:controller Admin/PelangganController --resource --model=Pelanggan
php artisan make:controller Admin/UserController --resource --model=User
php artisan make:request AlatRequest
php artisan make:request KategoriRequest
php artisan make:request PelangganRequest
php artisan make:request UserRequest
```
```php
Route::resource('kategori', KategoriController::class)->except('show');
Route::resource('alat', AlatController::class);
Route::resource('pelanggan', PelangganController::class);
Route::resource('user', UserController::class)->except('show');
```

## 2. Kategori
- Daftar, tambah, ubah, hapus. `slug` dibuat dari `Str::slug($nama)`.
- Hapus ditolak bila masih punya alat (cek `alat()->exists()`), tampilkan pesan.

## 3. Alat (urutan kerja)
1. **Index**: pencarian (`nama`/`kode`), filter kategori, paginate 10, tabel dengan foto kecil, harga, stok, stok perawatan, status.
2. **Create/Edit**: field sesuai `AlatRequest` (dokumen 04, 4d). Pratinjau foto dengan JavaScript sederhana (`FileReader`).
3. **Upload foto**:
```php
if ($request->hasFile('foto')) {
    if ($alat?->foto) Storage::disk('public')->delete($alat->foto);
    $data['foto'] = $request->file('foto')->store('alat', 'public');
}
```
```powershell
php artisan storage:link
```
4. **Hapus**: soft delete. Konfirmasi lewat modal (sesuai skenario "konfirmasi penghapusan"). Alat yang dihapus hilang dari katalog tetapi riwayat tetap utuh (`withTrashed()` pada relasi `PesananDetail::alat`).
5. **Toggle aktif**: `PATCH /admin/alat/{alat}/toggle`.
6. **Validasi stok**: `stok_perawatan ≤ stok_total` (di Request).
7. Ubah harga tidak memengaruhi pesanan lama (karena snapshot).

## 4. Pelanggan
- Daftar dengan penanda "Akun" atau "Walk-in", pencarian nama/HP.
- Tambah pelanggan walk-in: nama, HP, alamat (`user_id` NULL).
- Hapus: soft delete, ditolak bila masih ada pesanan aktif.
- Detail pelanggan menampilkan riwayat pesanan.

## 5. User
- Admin hanya dapat membuat atau mengubah user bertipe `admin` dan `pelanggan` (Rule `in:admin,pelanggan`).
- Akun Pemilik hanya dari seeder. Akun Admin dikelola Pemilik (Plan 9) dan Admin lain tidak menghapus dirinya sendiri.
- Fitur: tambah, ubah, aktif/nonaktif, reset password (password sementara ditampilkan sekali), soft delete.
- Saat membuat user role pelanggan, otomatis buat baris `pelanggan`.
- Validasi `unique` email dengan `->ignore($user->id)` saat ubah.

## 6. Tampilan
- Tabel responsif, modal konfirmasi hapus, flash message (`session('sukses')`), pagination.
- Gunakan komponen Blade dari Plan 3.

## 7. Seeder Foto
Siapkan 23 foto produk atau gambar placeholder berlisensi aman di `storage/app/public/alat`. Jangan memakai gambar tanpa izin. Foto sendiri dari toko lebih baik.

## 8. Pengujian (BB-15 sampai BB-26)
Tambah alat valid, kode duplikat, harga negatif, upload PDF, upload 3 MB, hapus alat berriwayat, buat user role pemilik (harus ditolak).

## 9. Definition of Done
- [x] CRUD kategori, alat, pelanggan, dan user berfungsi dengan validasi
- [x] Foto alat tersimpan dan tampil (Storage disk public `alat/`, storage:link aktif)
- [x] Hapus berupa soft delete, tidak merusak riwayat transaksi
- [x] Admin tidak dapat membuat role pemilik (validasi Rule `in:admin,pelanggan`)
- [x] BB-15 sampai BB-26 lolos (58 tests, 171 assertions 100% pass)
- [x] Tampilan Blade responsif (kategori, alat, pelanggan, user)
