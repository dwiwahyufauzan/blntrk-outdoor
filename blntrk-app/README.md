# BLNTRK Outdoor – Aplikasi Laravel

Source code utama sistem informasi persewaan alat outdoor BLNTRK Outdoor (Laravel 13, PHP ≥ 8.3, Foresta Adventure Design System).
Dokumentasi lengkap, instalasi, akun bawaan, dan alur penggunaan ada di [`../README.md`](../README.md).

### Perintah cepat

```bash
php artisan serve                 # jalankan server lokal
php artisan migrate --seed        # migrasi + data awal
php artisan schedule:work         # scheduler (status terlambat / kedaluwarsa)
php artisan test                  # 173 test
./vendor/bin/pint                 # format kode
npm run dev                       # hot reload aset (atau npm run build)

# Data transaksi demo
php artisan db:seed --class=TransaksiDemoSeeder
```
