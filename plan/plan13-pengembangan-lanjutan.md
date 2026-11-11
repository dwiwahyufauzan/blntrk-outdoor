# Plan 13 – Perbaikan dan Pengembangan Lanjutan

Tindak lanjut hasil review proyek (skor 8/10). Seluruh butir di bawah sudah diimplementasikan kecuali yang ditandai **belum**.

## Prioritas tinggi

| # | Temuan | Penyelesaian |
|---|---|---|
| 1 | Akun seeder hidup kembali di produksi | `UserSeeder` di produksi hanya berjalan bila tabel `users` kosong; email dari `SEED_OWNER_EMAIL`/`SEED_ADMIN_EMAIL`; akun pelanggan contoh hanya saat `SEED_DEMO=true`; `PengaturanSeeder` kini `firstOrCreate` agar nilai yang diubah pemilik tidak tertimpa |
| 2 | Dua Dockerfile berbeda | Dockerfile dan `.dockerignore` di root dihapus; hanya `blntrk-app/Dockerfile` (dengan `pdo_pgsql`) |
| 3 | Reset sandi tidak berfungsi di produksi | Tautan/rute "Lupa kata sandi" nonaktif selama `MAIL_MAILER` = `log`/`array`; `render.yaml` + `.env.example` memuat variabel SMTP |
| 4 | Pesanan `dibayar` tidak diambil menahan stok | Aturan no-show di `PesananStatusSyncService`: batal otomatis setelah `batas_ambil` + toleransi hari; kebijakan diatur pemilik; peringatan di dashboard admin |
| 5 | Foto hilang saat redeploy | `FOTO_DISK` / `BUKTI_DISK` + `league/flysystem-aws-s3-v3` (S3 / Cloudflare R2) |

## Prioritas menengah

- Stok keranjang memakai ketersediaan per tanggal (`KeranjangService::stokUntukJadwal`).
- Teks WhatsApp terlambat memakai `jatuh_tempo` pesanan, bukan jam hardcode.
- Guard `AlatController` (hapus, nonaktifkan, kurangi stok) dan guard pemindahan unit ke perawatan terhadap pesanan aktif.
- `AlatRequest` memvalidasi `varian`.
- `Pengaturan::ambil` tidak lagi menyimpan nilai default pemanggil ke cache.
- Pencarian `LIKE` membuang wildcard buatan pengguna (`like_pattern()`).
- Scheduler: endpoint `GET /cron/jadwal` (token `CRON_TOKEN`) untuk cron eksternal selain loop di container.
- Validasi nomor HP/WhatsApp Indonesia (`NomorHpIndonesia`) menggantikan verifikasi email.

## Fitur baru

1. Bukti transfer (unggah pelanggan, disk privat, verifikasi admin).
2. Perpanjangan sewa dan ubah jadwal (`PerubahanPesananService`), dengan kunci baris dan pengecekan stok.
3. Pengingat email jatuh tempo dan keterlambatan (`PengingatService`, `pesanan:kirim-pengingat`). Integrasi WhatsApp API **belum** (butuh akun penyedia berbayar); tautan `wa.me` manual tetap ada.
4. Kalender ketersediaan (heatmap) di detail alat.
5. Riwayat perawatan alat (`riwayat_perawatan`) termasuk unit rusak dari pengembalian dan aksi "selesai diperbaiki".
6. Deposit uang sebagai alternatif kartu identitas (`jaminan.nominal`).
7. Dashboard pemilik: pendapatan vs bulan lalu, rasio denda, okupansi per alat.

## Teknis dan kualitas

- Ketersediaan massal `terpakaiPerHariBanyak` / `tersediaBanyak` (satu query untuk banyak alat) dipakai katalog dan keranjang. Cache jangka pendek **belum**.
- `PesananService` dipecah: `PesananStatusSyncService` (scheduler) dan `PerubahanPesananService`. DTO pengganti `array $data` **belum**.
- `/health` memeriksa koneksi database; Render memakainya sebagai health check.
- CI: Pint + Larastan (level 5, dengan baseline), test + coverage (pcov), seluruh test pada PostgreSQL, uji concurrency pada PostgreSQL dan MySQL.
- Uji concurrency (`tests/Concurrency`): banyak proses PHP memesan stok sama bersamaan; membuktikan tidak ada double-booking, resi unik, dan tidak ada deadlock pada pesanan multi-alat.
- Pengaturan penulisan cache dipindah ke luar transaksi pemesanan agar tidak terjadi di dalam bagian kritis.

## Belum / perlu keputusan

- Menghapus branch remote `fix/revisi-klien-modal-pengembalian-paket-bundling` (sudah ter-merge ke `main`).
- Baseline Larastan (140 temuan lama) dikurangi bertahap.
- Hasil job PostgreSQL/concurrency di CI belum teramati (tidak ada PostgreSQL/MySQL lokal saat implementasi).
