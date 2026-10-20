# Plan 10 – Fitur Bersyarat (P3)

**Syarat mulai:** Plan 1–9 selesai dan berstatus DoD. Jika belum, **lewati** plan ini dan tulis fiturnya sebagai saran pengembangan lanjutan. Titik evaluasi: akhir minggu 14.
**Estimasi:** 1 minggu (pilih sebagian saja).

| Urutan | Fitur | Kode | Usaha | Nilai untuk skripsi |
|---|---|---|---|---|
| 1 | Pengaturan oleh Pemilik dan kelola akun Admin | – | R | Tinggi (parameter denda bisa diubah) |
| 2 | Log aktivitas | T15 | S | Sedang (jejak audit) |
| 3 | Perawatan alat | T12 | S | Sedang |
| 4 | Ekspor Excel | T13 | R | Rendah |
| 5 | Tautan WhatsApp lanjutan | T14 | R | Rendah (dasar sudah ada di Plan 6 dan 7) |

> Pengaturan dan Kelola Admin sebenarnya dibutuhkan agar aturan denda tidak di-hardcode. Sebaiknya dikerjakan walau fitur P3 lain dilewati.

## 1. Pengaturan dan Kelola Admin (Pemilik)
- `Pemilik\PengaturanController`: form persen denda, jam kembali, toleransi, durasi maksimum, nama toko, nomor WhatsApp. Simpan ke `pengaturan` dan `cache()->forget("pengaturan.$kunci")`.
- `Pemilik\AdminController`: CRUD akun Admin (nonaktifkan, bukan hapus, bila punya riwayat).
- Perubahan pengaturan **tidak** memengaruhi pesanan lama (jatuh tempo sudah tersimpan di pesanan).

## 2. Log Aktivitas
```php
// app/Services/LogService.php
public static function catat(?User $u, string $aksi, string $modul, ?int $ref = null, ?string $desk = null): void {
    LogAktivitas::create(['user_id'=>$u?->id,'aksi'=>$aksi,'modul'=>$modul,'ref_id'=>$ref,'deskripsi'=>$desk,'ip_address'=>request()->ip(),'created_at'=>now()]);
}
```
Titik pencatatan: login, bayar, ambil, batal, proses pengembalian, lunas denda, ubah harga alat, ubah pengaturan, tambah/hapus user. Tampilan: tabel dengan filter tanggal, user, modul; paginate 25. Tidak boleh diubah atau dihapus dari UI.

## 3. Perawatan Alat
- Halaman daftar alat dengan `stok_perawatan > 0`.
- Aksi "Selesai diperbaiki" (jumlah n): `stok_perawatan -= n` (validasi ≤ nilai saat ini). Log aktivitas.
- Tambahan opsional: "Pindahkan ke perawatan" manual dari form alat.

## 4. Ekspor Excel
```powershell
composer require maatwebsite/excel
php artisan make:export LaporanExport
```
Satu kelas export menerima collection dari `LaporanService`. Tambahkan tombol Excel di halaman laporan.

## 5. Tautan WhatsApp
Helper `wa_link($noHp, $pesan)` yang menormalkan `08xxx` menjadi `628xxx`. Template pesan:
- Pengingat jatuh tempo: "Halo {nama}, sewa {resi} jatuh tempo {tanggal} pukul {jam}."
- Tagihan denda: "Halo {nama}, terdapat denda Rp{jumlah} untuk pesanan {resi}."
Tombol hanya membuka WhatsApp (tanpa API), sehingga tidak ada biaya dan tidak ada dependensi.

## 6. Definition of Done (per fitur yang dikerjakan)
- [ ] Pengaturan mengubah perilaku perhitungan denda pada pesanan baru
- [ ] Log mencatat semua aksi penting
- [ ] Alat selesai diperbaiki kembali tersedia
- [ ] Ekspor Excel berisi data sama dengan layar
- [ ] Fitur yang dilewati dicatat sebagai saran pengembangan lanjutan di Bab V
