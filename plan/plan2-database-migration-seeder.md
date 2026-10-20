# Plan 2 – Database: Migration, Model, Seeder

**Tujuan:** skema database sesuai ERD terbentuk lewat migration, model dengan relasi, dan data awal siap.
**Acuan:** `docs/perencanaan/03-perancangan-database.md`, `erd.dbml`, `schema.sql`.
**Estimasi:** 3–4 hari.

## 1. Membuat Migration (urutan penting)

```powershell
php artisan make:migration create_roles_table
php artisan make:migration add_role_to_users_table --table=users
php artisan make:migration create_pelanggan_table
php artisan make:migration create_kategori_alat_table
php artisan make:migration create_alat_table
php artisan make:migration create_pesanan_table
php artisan make:migration create_pesanan_detail_table
php artisan make:migration create_pembayaran_table
php artisan make:migration create_jaminan_table
php artisan make:migration create_pengembalian_table
php artisan make:migration create_pengembalian_detail_table
php artisan make:migration create_denda_table
php artisan make:migration create_pengaturan_table
php artisan make:migration create_log_aktivitas_table
```

> Nama file migration berstempel waktu, jadi buat **berurutan** agar `roles` terbit sebelum `users` yang merujuknya. Karena `users` bawaan Laravel dibuat lebih awal, ubah migration `create_users_table` bawaan: ganti nama berkas agar stempel waktunya setelah `create_roles_table`, atau tambahkan `role_id` lewat migration terpisah (lebih aman, dengan `nullable` lalu diisi seeder). **Rekomendasi:** edit migration users bawaan langsung (proyek baru) dan ubah stempel waktu roles menjadi lebih awal.

### Contoh migration kunci

`alat`:
```php
Schema::create('alat', function (Blueprint $t) {
    $t->id();
    $t->unsignedSmallInteger('kategori_id');
    $t->string('kode_alat', 20)->unique();
    $t->string('nama', 100);
    $t->string('varian', 50)->nullable();
    $t->text('deskripsi')->nullable();
    $t->string('foto')->nullable();
    $t->decimal('harga_per_hari', 10, 2);
    $t->unsignedInteger('stok_total')->default(0);
    $t->unsignedInteger('stok_perawatan')->default(0);
    $t->boolean('is_aktif')->default(true);
    $t->timestamps();
    $t->softDeletes();
    $t->foreign('kategori_id')->references('id')->on('kategori_alat')->restrictOnDelete();
});
DB::statement('ALTER TABLE alat ADD CONSTRAINT ck_alat_stok CHECK (stok_perawatan <= stok_total)');
```

`pesanan` (inti):
```php
$t->id();
$t->string('no_resi', 20)->unique();
$t->foreignId('pelanggan_id')->constrained('pelanggan')->restrictOnDelete();
$t->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
$t->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
$t->date('tanggal_mulai');
$t->date('tanggal_selesai');
$t->unsignedSmallInteger('durasi_hari');
$t->dateTime('jatuh_tempo');
$t->dateTime('batas_ambil');
$t->enum('status', ['menunggu','dibayar','disewa','terlambat','dikembalikan','dibatalkan'])->default('menunggu');
$t->decimal('total_sewa', 12, 2);
$t->dateTime('diambil_pada')->nullable();
$t->dateTime('dibatalkan_pada')->nullable();
$t->string('alasan_batal')->nullable();
$t->string('catatan')->nullable();
$t->timestamps();
$t->softDeletes();
$t->index(['status','tanggal_mulai','tanggal_selesai']);
```
Tipe FK `constrained` memakai `unsignedBigInteger`. Untuk `roles` dan `kategori_alat` yang saya rancang dengan `tinyint`/`smallint`, sederhanakan dengan `id()` (bigint) jika ingin memakai `constrained()`. **Keputusan praktis:** pakai `id()` biasa di semua tabel agar kode lebih singkat. Perbedaan tipe tidak berpengaruh pada skala toko ini. Catat dan sesuaikan kamus data.

## 2. Model

```powershell
php artisan make:model Role
php artisan make:model Pelanggan
php artisan make:model KategoriAlat
php artisan make:model Alat
php artisan make:model Pesanan
php artisan make:model PesananDetail
php artisan make:model Pembayaran
php artisan make:model Jaminan
php artisan make:model Pengembalian
php artisan make:model PengembalianDetail
php artisan make:model Denda
php artisan make:model Pengaturan
php artisan make:model LogAktivitas
```

**Wajib:** nama tabel bahasa Indonesia tidak sesuai pluralisasi Laravel. Tetapkan manual:

```php
class Pelanggan extends Model { protected $table = 'pelanggan'; }
class Alat extends Model { protected $table = 'alat'; }
class Pengaturan extends Model {
    protected $table = 'pengaturan'; protected $primaryKey = 'kunci';
    public $incrementing = false; protected $keyType = 'string'; public $timestamps = false;
    public static function ambil(string $kunci, $default = null) {
        return cache()->remember("pengaturan.$kunci", 300, fn () => static::find($kunci)?->nilai ?? $default);
    }
}
```
Dan sejenisnya untuk `KategoriAlat` (`kategori_alat`), `PesananDetail` (`pesanan_detail`), `PengembalianDetail` (`pengembalian_detail`), `LogAktivitas` (`log_aktivitas`). Relasi lengkap lihat dokumen 04 bagian 4a.

Enum status:
```php
// app/Enums/StatusPesanan.php
enum StatusPesanan: string {
    case Menunggu = 'menunggu'; case Dibayar = 'dibayar'; case Disewa = 'disewa';
    case Terlambat = 'terlambat'; case Dikembalikan = 'dikembalikan'; case Dibatalkan = 'dibatalkan';
    public function aktif(): bool { return in_array($this, [self::Menunggu, self::Dibayar, self::Disewa, self::Terlambat]); }
    public function warna(): string { return match($this){ self::Menunggu=>'warning', self::Dibayar=>'info', self::Disewa=>'success', self::Terlambat=>'danger', default=>'secondary' }; }
}
```
Tambahkan `protected $casts = ['status' => StatusPesanan::class, 'tanggal_mulai' => 'date', 'tanggal_selesai' => 'date', 'jatuh_tempo' => 'datetime', 'batas_ambil' => 'datetime'];` pada `Pesanan`.

## 3. Seeder

```powershell
php artisan make:seeder RoleSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder PengaturanSeeder
php artisan make:seeder KategoriAlatSeeder
php artisan make:seeder AlatSeeder
```
Isi data: lihat dokumen 04 bagian 4b (23 alat). `DatabaseSeeder::run()` memanggil berurutan: Role → User → Pengaturan → KategoriAlat → Alat.

Akun contoh (ganti password sebelum demo publik):

| Role | Email | Password (contoh) |
|---|---|---|
| Pemilik | pemilik@blntrk.test | Pemilik#2026 |
| Admin | admin@blntrk.test | Admin#2026 |
| Pelanggan | budi@blntrk.test | Pelanggan#2026 |

Gunakan `Hash::make()` atau cast `hashed`. Pelanggan contoh juga membuat baris di tabel `pelanggan`.

## 4. Verifikasi

```powershell
php artisan migrate:fresh --seed
php artisan tinker
>>> App\Models\Alat::with('kategori')->count()
>>> App\Models\User::with('role')->get()->pluck('role.nama','email')
```
Bandingkan struktur dengan `erd.dbml` (gambar ulang dari database lewat phpMyAdmin → Designer sebagai bukti implementasi untuk Bab IV).

## 5. Risiko
| Risiko | Penanganan |
|---|---|
| FK error karena urutan migration | Cek stempel waktu file, urutkan ulang, `migrate:fresh` |
| `CHECK` tidak didukung (MariaDB lama) | Lewati CHECK dan validasi di aplikasi |
| Nama tabel tidak ditemukan | Pastikan `$table` diatur di model |

## 6. Definition of Done
- [x] `migrate:fresh --seed` sukses tanpa galat di MySQL `blntrk_outdoor`
- [x] 14 tabel bisnis + tabel sistem Laravel terbuat dengan rapi dan konsisten
- [x] Seluruh Model Eloquent dibuat dengan relasi, casts, dan soft deletes
- [x] Enum PHP untuk status pesanan, jenis jaminan, metode pembayaran, jenis denda dibuat
- [x] Seeder menghasilkan 3 role, 4 user (Pemilik, Admin, 2 Pelanggan), pelanggan walk-in, 8 kategori, 23 alat outdoor, dan 7 pengaturan
- [x] Automated test `DatabaseSeedTest` lulus 100% (4 tests, 18 assertions)
