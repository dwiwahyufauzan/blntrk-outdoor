# 4. Perancangan Laravel

Versi: Laravel 11 atau 12, PHP 8.2+, MySQL (XAMPP). Autentikasi: Laravel Breeze (Blade).

## 4a. Arsitektur dan Struktur Folder (MVC + Service)

```
app/
├── Console/Commands/
│   └── PerbaruiStatusPesanan.php     # scheduler: terlambat & batal otomatis
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                      # dari Breeze (+ Register pelanggan)
│   │   ├── Pelanggan/  KatalogController, PesananController, ProfilController
│   │   ├── Admin/      DashboardController, AlatController, KategoriController,
│   │   │               PerawatanController, PelangganController, UserController,
│   │   │               PesananController, PengembalianController, DendaController,
│   │   │               LaporanController, LogController
│   │   └── Pemilik/    DashboardController, LaporanController, AdminController,
│   │                   PengaturanController, LogController
│   ├── Middleware/     CekRole.php
│   └── Requests/       (Form Request, lihat 4d)
├── Models/             (lihat tabel di bawah)
├── Services/           KetersediaanService, PesananService, PengembalianService,
│                       DendaService, LaporanService, LogService
├── Enums/              StatusPesanan, JenisDenda, ...
└── Policies/           PesananPolicy
database/{migrations,seeders}
resources/views/{layouts,components,pelanggan,admin,pemilik,pdf}
routes/web.php
```

**Alasan Service layer:** aturan bisnis (ketersediaan, denda, transisi status) kompleks. Menempatkannya di Controller membuat kode gemuk dan sulit diuji. Controller hanya menerima request, memanggil Service, dan mengembalikan view. Tetap MVC dan mudah dijelaskan pada sidang.

### Model dan Relasi Eloquent

| Model | Relasi |
|---|---|
| Role | `hasMany(User)` |
| User | `belongsTo(Role)`, `hasOne(Pelanggan)`, `hasMany(LogAktivitas)` |
| Pelanggan | `belongsTo(User)`, `hasMany(Pesanan)` — SoftDeletes |
| KategoriAlat | `hasMany(Alat)` |
| Alat | `belongsTo(KategoriAlat)`, `hasMany(PesananDetail)` — SoftDeletes |
| Pesanan | `belongsTo(Pelanggan)`, `belongsTo(User, 'dibuat_oleh')`, `belongsTo(User, 'diproses_oleh')`, `hasMany(PesananDetail)`, `belongsToMany(Alat, 'pesanan_detail')`, `hasMany(Pembayaran)`, `hasOne(Jaminan)`, `hasOne(Pengembalian)`, `hasMany(Denda)` — SoftDeletes |
| PesananDetail | `belongsTo(Pesanan)`, `belongsTo(Alat)`, `hasOne(PengembalianDetail)` |
| Pembayaran | `belongsTo(Pesanan)`, `belongsTo(User, 'dicatat_oleh')` |
| Jaminan | `belongsTo(Pesanan)`, `belongsTo(User, 'diterima_oleh')` |
| Pengembalian | `belongsTo(Pesanan)`, `hasMany(PengembalianDetail)`, `hasMany(Denda)` |
| PengembalianDetail | `belongsTo(Pengembalian)`, `belongsTo(PesananDetail)` |
| Denda | `belongsTo(Pesanan)`, `belongsTo(Pengembalian)`, `belongsTo(PengembalianDetail)` |
| Pengaturan | key-value (PK `kunci`), helper `Pengaturan::get('jam_kembali')` |
| LogAktivitas | `belongsTo(User)` |

## 4b. Migration (urutan pembuatan)

Urutan mengikuti ketergantungan FK:

| No | Migration | Catatan |
|---|---|---|
| 1 | `create_roles_table` | Sebelum users |
| 2 | `create_users_table` (modifikasi bawaan: tambah `role_id`, `is_aktif`, `softDeletes`) | |
| 3 | `create_password_reset_tokens`, `sessions`, `cache`, `jobs` | Bawaan Laravel |
| 4 | `create_pelanggan_table` | |
| 5 | `create_kategori_alat_table` | |
| 6 | `create_alat_table` | Tambah CHECK via `DB::statement` |
| 7 | `create_pesanan_table` | |
| 8 | `create_pesanan_detail_table` | |
| 9 | `create_pembayaran_table` | |
| 10 | `create_jaminan_table` | |
| 11 | `create_pengembalian_table` | |
| 12 | `create_pengembalian_detail_table` | |
| 13 | `create_denda_table` | |
| 14 | `create_pengaturan_table` | |
| 15 | `create_log_aktivitas_table` | |

### Seeder

| Seeder | Isi |
|---|---|
| `RoleSeeder` | pemilik, admin, pelanggan |
| `UserSeeder` | 1 Pemilik, 2 Admin, 3 Pelanggan contoh (password di-hash, ganti setelah uji) |
| `PengaturanSeeder` | persen denda 100, jam kembali 12:00, toleransi 60, maks durasi 14 |
| `KategoriAlatSeeder` | Tenda, Carrier & Tas, Sleeping Gear, Alas Tidur, Alas Kaki, Alat Masak, Penerangan, Aksesoris |
| `AlatSeeder` | Data di bawah (harga dan stok contoh, ganti dengan data toko) |
| `TransaksiDemoSeeder` (opsional) | Beberapa pesanan dengan berbagai status untuk demo dan uji laporan |

Data `AlatSeeder`:

| Kode | Kategori | Nama | Varian | Harga/hari | Stok |
|---|---|---|---|---|---|
| TND-D2 | Tenda | Tenda Dome | Kapasitas 2 | 35.000 | 10 |
| TND-D4 | Tenda | Tenda Dome | Kapasitas 4 | 50.000 | 8 |
| TND-D6 | Tenda | Tenda Dome | Kapasitas 6 | 75.000 | 4 |
| TND-FS | Tenda | Flysheet | 3×4 m | 15.000 | 6 |
| CRR-40 | Carrier & Tas | Carrier | 40 L | 25.000 | 8 |
| CRR-60 | Carrier & Tas | Carrier | 60 L | 30.000 | 12 |
| CRR-80 | Carrier & Tas | Carrier | 80 L | 40.000 | 5 |
| TAS-DP | Carrier & Tas | Daypack | 25 L | 15.000 | 6 |
| SLB-STD | Sleeping Gear | Sleeping Bag | Standar | 15.000 | 20 |
| SLB-POL | Sleeping Gear | Sleeping Bag | Polar | 20.000 | 10 |
| MTR-FM | Alas Tidur | Matras Gulung | – | 10.000 | 20 |
| HMK-01 | Alas Tidur | Hammock | – | 15.000 | 6 |
| SPT-38 | Alas Kaki | Sepatu Gunung | No. 38 | 30.000 | 3 |
| SPT-40 | Alas Kaki | Sepatu Gunung | No. 40 | 30.000 | 4 |
| SPT-42 | Alas Kaki | Sepatu Gunung | No. 42 | 30.000 | 4 |
| SPT-43 | Alas Kaki | Sepatu Gunung | No. 43 | 30.000 | 3 |
| KMP-PT | Alat Masak | Kompor Portable | – | 15.000 | 10 |
| NST-01 | Alat Masak | Nesting | – | 10.000 | 10 |
| HDL-01 | Penerangan | Headlamp | – | 8.000 | 15 |
| LNT-01 | Penerangan | Lampu Tenda | – | 8.000 | 10 |
| TRK-01 | Aksesoris | Trekking Pole | Sepasang | 10.000 | 10 |
| JKT-01 | Aksesoris | Jaket Gunung | M–XL | 20.000 | 10 |
| RNC-01 | Aksesoris | Jas Hujan / Ponco | – | 5.000 | 20 |

## 4c. Route, Controller, dan Middleware

### Middleware
`CekRole` (alias `role`): `Route::middleware(['auth','role:admin'])`. Memeriksa `auth()->user()->role->nama` dan mengembalikan 403 bila tidak sesuai. Admin yang tidak aktif (`is_aktif = 0`) otomatis keluar. Pemilik dapat mengakses fitur Admin dengan `role:admin,pemilik` bila diinginkan, tetapi MVP memisahkan tegas.

### Route Publik dan Pelanggan

| Method | URI | Controller@aksi | Middleware |
|---|---|---|---|
| GET | `/` | `BerandaController@index` | – |
| GET | `/alat` | `KatalogController@index` | – |
| GET | `/alat/{alat}` | `KatalogController@show` | – |
| GET | `/alat/{alat}/ketersediaan` | `KatalogController@ketersediaan` (JSON, query `mulai`, `selesai`) | – |
| GET/POST | `/register` | `RegisteredUserController` | guest |
| GET/POST | `/login`, POST `/logout` | Breeze | guest / auth |
| GET | `/pesanan/buat` | `Pelanggan\PesananController@create` | auth, role:pelanggan |
| POST | `/pesanan` | `@store` | auth, role:pelanggan |
| GET | `/pesanan` | `@index` | auth, role:pelanggan |
| GET | `/pesanan/{pesanan}` | `@show` (Policy: milik sendiri) | auth, role:pelanggan |
| PATCH | `/pesanan/{pesanan}/batal` | `@batal` | auth, role:pelanggan |
| GET/PUT | `/profil` | `ProfilController@edit/update` | auth |

### Route Admin (prefix `/admin`, middleware `auth`, `role:admin`)

| Method | URI | Controller@aksi |
|---|---|---|
| GET | `/admin` | `DashboardController@index` |
| resource | `/admin/alat` | `AlatController` (+ `PATCH /{alat}/toggle`) |
| resource | `/admin/kategori` | `KategoriController` (kecuali show) |
| GET, PATCH | `/admin/perawatan`, `/admin/perawatan/{alat}/selesai` | `PerawatanController@index/selesai` |
| resource | `/admin/pelanggan` | `PelangganController` |
| resource | `/admin/user` | `UserController` (hanya role pelanggan dan admin; pemilik tidak dapat dibuat) |
| GET | `/admin/pesanan` | `PesananController@index` (filter status) |
| GET, POST | `/admin/pesanan/buat`, `/admin/pesanan` | `@create`, `@store` (walk-in) |
| GET | `/admin/pesanan/{pesanan}` | `@show` |
| POST | `/admin/pesanan/{pesanan}/pembayaran` | `@bayar` |
| POST | `/admin/pesanan/{pesanan}/pengambilan` | `@ambil` (jaminan + status disewa) |
| PATCH | `/admin/pesanan/{pesanan}/batal` | `@batal` |
| GET | `/admin/pesanan/{pesanan}/resi` | `@resi` (PDF/print) |
| GET, POST | `/admin/pengembalian`, `/admin/pesanan/{pesanan}/pengembalian` | `PengembalianController@index/store` |
| PATCH | `/admin/pesanan/{pesanan}/jaminan/kembali` | `PengembalianController@kembalikanJaminan` |
| GET, PATCH | `/admin/denda`, `/admin/denda/{denda}/lunas` | `DendaController` |
| GET | `/admin/laporan`, `/admin/laporan/{jenis}/pdf`, `/excel` | `LaporanController` |
| GET | `/admin/log` | `LogController@index` |

### Route Pemilik (prefix `/pemilik`, middleware `auth`, `role:pemilik`)

| Method | URI | Controller@aksi |
|---|---|---|
| GET | `/pemilik` | `DashboardController@index` |
| GET | `/pemilik/laporan`, `/pemilik/laporan/{jenis}/pdf` | `LaporanController` |
| resource | `/pemilik/admin` | `AdminController` (kelola akun Admin) |
| GET, PUT | `/pemilik/pengaturan` | `PengaturanController` |
| GET | `/pemilik/log` | `LogController@index` |

## 4d. Validasi (Form Request) dan Keamanan

| Form Request | Aturan utama |
|---|---|
| `RegisterPelangganRequest` | nama required max:100; email required email unique:users; no_hp required regex digit 10–15; password min:8 confirmed |
| `AlatRequest` | kode_alat unique (abaikan diri sendiri); kategori_id exists; harga numeric min:0; stok_total integer min:0; stok_perawatan ≤ stok_total; foto image mimes:jpg,jpeg,png,webp max:2048 |
| `KategoriRequest` | nama unique max:50 |
| `UserRequest` | nama, email unique, role in:admin,pelanggan; password min:8 (wajib saat buat) |
| `PesananRequest` | tanggal_mulai date after_or_equal:today; tanggal_selesai after:tanggal_mulai; selisih ≤ maks_durasi; items array min:1; items.*.alat_id exists; items.*.jumlah integer min:1; **withValidator**: cek ketersediaan via `KetersediaanService` |
| `PembayaranRequest` | jumlah numeric sama dengan total sewa; metode in:tunai,transfer |
| `PengambilanRequest` | jenis_jaminan in:ktp,sim,kartu_pelajar,lainnya; nomor_identitas regex digit (disimpan 4 digit terakhir); status pesanan harus `dibayar` |
| `PengembalianRequest` | untuk tiap baris: jml_baik + jml_rusak_ringan + jml_rusak_berat + jml_hilang = jumlah disewa; biaya kerusakan numeric min:0; dikembalikan_pada date ≥ tanggal_mulai |
| `BatalRequest` | alasan required max:255 (Admin) |
| `PengaturanRequest` | persen 0–500; jam format H:i; toleransi 0–240 |

### Keamanan Dasar
| Aspek | Penerapan |
|---|---|
| CSRF | `@csrf` pada semua form, `@method` untuk PATCH/DELETE |
| Hash password | Cast `'password' => 'hashed'` (bcrypt) |
| Otorisasi | Middleware `CekRole` + `PesananPolicy` (pelanggan hanya pesanan sendiri) |
| Transisi status | Hanya via `PesananService` yang memeriksa status sebelumnya (tidak dapat dilompati dengan manipulasi request) |
| Mass assignment | `$fillable` eksplisit, `status` dan `total_sewa` tidak boleh diisi dari input pengguna |
| Upload gambar | Validasi MIME dan ukuran, simpan ke `storage/app/public/alat` dengan nama hash, jalankan `php artisan storage:link` |
| SQL injection | Eloquent atau Query Builder (prepared statement) |
| XSS | `{{ }}` pada Blade (escape otomatis), hindari `{!! !!}` |
| Brute force | Rate limiting login bawaan Breeze |
| Konsistensi data | `DB::transaction` + `lockForUpdate` |
| Privasi | Nomor identitas disimpan dalam bentuk disamarkan |
| Konfigurasi | `.env` tidak masuk git, `APP_DEBUG=false` saat demo |

### Inti Logika Ketersediaan (acuan implementasi)

```php
public function tersedia(Alat $alat, Carbon $mulai, Carbon $selesai): int
{
    $min = PHP_INT_MAX;
    for ($d = $mulai->copy(); $d->lt($selesai); $d->addDay()) {
        $terpakai = PesananDetail::where('alat_id', $alat->id)
            ->whereHas('pesanan', fn ($q) => $q
                ->whereIn('status', ['menunggu','dibayar','disewa','terlambat'])
                ->whereDate('tanggal_mulai', '<=', $d)
                ->whereDate('tanggal_selesai', '>', $d))
            ->sum('jumlah');
        $min = min($min, $alat->stok_total - $alat->stok_perawatan - $terpakai);
    }
    return max(0, $min);
}
```

Optimasi (jika diperlukan): satu query yang mengambil pesanan beririsan, lalu dihitung per hari di PHP.

## 4e. Rekomendasi Package

| Kebutuhan | Package | Alasan |
|---|---|---|
| Autentikasi | `laravel/breeze` (stack Blade) | Ringan, sesuai Blade, tidak membawa SPA |
| PDF laporan dan resi | `barryvdh/laravel-dompdf` | Paling umum, tanpa binary eksternal (cocok di XAMPP) |
| Excel (bersyarat) | `maatwebsite/excel` | Standar de facto, ekspor dari collection |
| Role | **Tidak memakai** `spatie/laravel-permission` | Tiga role tetap cukup dengan `roles` dan middleware sendiri |
| Gambar | Bawaan Laravel `Storage` | Tanpa dependensi untuk upload dan simpan |
| Tanggal | Carbon (bawaan), `Carbon::setLocale('id')` | Format tanggal Indonesia |
| Grafik dashboard | Chart.js (CDN) | Ringan, hanya JavaScript |
| UI | CSS sendiri atau Bootstrap 5 (CDN) | Dipilih sesuai kemampuan |
| Pengujian otomatis (opsional) | Pest atau PHPUnit (bawaan) | Uji aturan bisnis ketersediaan dan denda |

### Scheduler

`php artisan pesanan:perbarui-status` dijalankan tiap jam (`routes/console.php`: `Schedule::command(...)->hourly()`). Tugasnya:
1. `disewa` dengan jatuh tempo + toleransi terlewati → `terlambat`.
2. `menunggu` dengan `batas_ambil` terlewati → `dibatalkan` (alasan "Melewati batas pengambilan", log sistem).

Di XAMPP: jalankan manual `php artisan schedule:work` saat demo, atau Windows Task Scheduler memanggil `schedule:run` tiap menit. Pengecekan juga dilakukan saat halaman daftar pesanan dibuka.
