# Plan 11 – Pengujian

**Tujuan:** membuktikan sistem benar dan layak digunakan. **Acuan:** `docs/perencanaan/06-rencana-pengujian.md`. **Estimasi:** 1–1,5 minggu. Uji otomatis ditulis **sambil membangun**, bukan menunggu akhir.

## 1. Uji Otomatis (nilai tambah, ringan)
```powershell
php artisan test
```
Gunakan database terpisah: `.env.testing` dengan `DB_DATABASE=blntrk_outdoor_test`, atau SQLite in-memory bila tidak memakai CHECK/ENUM spesifik MySQL (disarankan tetap MySQL agar sama dengan produksi).

| Berkas | Isi | Rujukan |
|---|---|---|
| `Unit/KetersediaanServiceTest` | 7 kasus Plan 5 | BB-28..32 |
| `Unit/HitungTotalTest` | Total 330.000 (3 hari, 2 alat) | BB-34 |
| `Unit/DendaServiceTest` | Dalam toleransi, 1 hari, 2 hari, persen berubah | BB-53, 55, 71 |
| `Feature/RoleAksesTest` | Akses silang role = 403 | BB-08..10 |
| `Feature/PesananAlurTest` | Buat → bayar → ambil → kembali | alur utama |
| `Feature/TransisiStatusTest` | Transisi tidak valid ditolak | BB-45, 48, 50, 59 |

Cantumkan ringkasan hasil `php artisan test` (jumlah lulus) di Bab IV.

## 2. Black Box Testing Manual
1. Reset data: `php artisan migrate:fresh --seed` (dengan `TransaksiDemoSeeder` bila perlu).
2. Jalankan BB-01 sampai BB-74 berurutan, dengan peran yang sesuai.
3. Catat hasil di tabel: ID, skenario, data uji, hasil diharapkan, **hasil aktual**, status (Sesuai/Tidak Sesuai), tanggal, catatan.
4. Tangkapan layar untuk kasus penting (satu per fitur).
5. Perbaiki bug, lalu uji ulang (regresi) dan catat sebagai putaran ke-2.

Tabel hasil siap disalin ke Bab IV: simpan dalam berkas `plan/hasil-blackbox.md` atau spreadsheet.

Format baris:
| ID | Fitur | Skenario | Data Uji | Diharapkan | Aktual | Status |
|---|---|---|---|---|---|---|

## 3. Uji Khusus Risiko Tinggi
| Uji | Cara | Lulus bila |
|---|---|---|
| Pemesanan serentak (BB-39) | Dua tab, stok 1, tekan Pesan bersamaan (atau skrip `Http::pool`) | Satu berhasil |
| Manipulasi status | POST langsung ke `/admin/pesanan/{id}/pengambilan` pada pesanan Menunggu | Ditolak |
| Zona waktu | Ubah jam sistem/uji dengan `Carbon::setTestNow` | Perhitungan denda tetap benar |
| Data besar | Seed 500 pesanan | Halaman ketersediaan dan laporan < 3 detik |
| Responsif | DevTools 390 px | Layout tidak patah |

## 4. Uji Kegunaan (SUS)
**Persiapan**
- Buat Google Form: 10 pernyataan SUS (skala 1–5) + data responden (peran) + pertanyaan terbuka.
- Siapkan skenario tugas tertulis:
  - Pelanggan: daftar → cari tenda → cek ketersediaan → pesan → batalkan pesanan.
  - Admin: catat pembayaran → konfirmasi pengambilan → proses pengembalian terlambat dengan 1 alat rusak → cetak laporan.
  - Pemilik: baca dashboard dan unduh laporan PDF.
- Server demo dapat diakses responden (jaringan lokal atau tunnel sementara).

**Pelaksanaan**
- Target: 10–15 pelanggan, 2–3 admin/karyawan, 1 pemilik.
- Amati tanpa membantu, catat waktu dan kesalahan.
- Isi kuesioner segera setelah tugas.

**Analisis**
- Skor SUS per responden: ganjil (skor−1) + genap (5−skor), total × 2,5.
- Rata-rata total dan per kelompok. Target ≥ 68.
- Rangkum masukan terbuka, tandai perbaikan yang dilakukan.

## 5. Jadwal
| Hari | Kegiatan |
|---|---|
| 1–2 | Uji otomatis dan perbaikan |
| 3–5 | Black box putaran 1 dan perbaikan bug |
| 6 | Regresi (putaran 2) |
| 7–8 | Uji SUS |
| 9 | Analisis dan penulisan Bab IV |

## 6. Definition of Done
- [x] Seluruh uji otomatis lulus (129 tests, 478 assertions, 0 failures)
- [x] Black box: semua skenario berstatus Sesuai (BB-01 sampai BB-74 di `plan/hasil-blackbox.md`)
- [x] Hasil SUS dihitung dan diinterpretasi (Rata-rata 81.88, Grade A / Excellent)
- [x] Bukti (tangkapan layar, tabel, formulir) tersimpan untuk lampiran Bab IV
