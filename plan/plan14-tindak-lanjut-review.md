# Plan 14 – Tindak Lanjut Review (skor 8,5/10)

Hasil akhir: 280 test (277 lulus, 3 dilewati), Pint dan Larastan level 5 lulus tanpa baseline.

Seluruh temuan review di bawah dikerjakan **kecuali** deploy dan operasional (O1–O5, K5) serta dua butir yang butuh keputusan pemilik repo (lihat bagian akhir).

## Logika bisnis

| # | Temuan | Penyelesaian |
|---|---|---|
| B1 | Pesanan `menunggu` menahan stok tanpa batas bayar | Kolom `pesanan.batas_bayar` = *sekian jam sejak dibuat* (`batas_bayar_jam`, bawaan 24) dan tidak pernah melewati `batas_ambil` (pesanan mendadak = H-0). Scheduler membatalkan pesanan menunggu yang melewatinya, kecuali bukti transfer sudah diunggah (tinggal menunggu admin). Pelanggan dibatasi `maks_pesanan_menunggu` (bawaan 3) per akun, dengan baris pelanggan dikunci agar permintaan paralel tidak lolos; admin (walk-in) tidak dibatasi. Keduanya diatur pemilik di halaman Pengaturan; 0 = nonaktif |
| B2 | Tidak ada pencatatan refund | Tabel `refund` (jumlah, metode, waktu, petugas, catatan). Admin yang membatalkan pesanan *dibayar* **wajib** memilih: dikembalikan (penuh/sebagian) atau hangus. Refund dapat dicatat belakangan untuk pesanan dibatalkan (mis. no-show yang dibatalkan sistem dengan dana hangus). Laporan pendapatan kini berbasis kas: pembayaran (termasuk dari pesanan dibatalkan) + denda lunas − refund; dana hangus tetap menjadi pendapatan. Dashboard, PDF, dan CSV ikut. Komentar migrasi lama yang bernama menyesatkan ditambahkan (berkas tidak diganti namanya karena sudah tercatat di tabel `migrations` database berjalan) |
| B3 | Pelunasan denda tanpa metode bayar; denda manual ke pesanan sembarang status | `denda.metode_bayar` (tunai/transfer) wajib saat melunasi; `tambahDenda` hanya untuk pesanan disewa/terlambat/dikembalikan (policy + service) |
| B4 | Waktu pengembalian input bebas | Bawaan waktu sistem. Selisih > 60 menit dari waktu sistem wajib disertai catatan, ditolak bila di masa depan, dan dicatat pada log (waktu manual vs waktu sistem); detail pesanan menandai koreksi manual |
| B5 | Stok "global" menyesatkan | `Alat::muatStokTersedia` bawaan = stok **hari ini** (unit yang benar-benar tidak di toko), atau stok minimum pada rentang tanggal bila rentang diberikan. Form pesanan memuat stok untuk tanggal terpilih (`GET /ketersediaan-alat`) dan memperbaruinya saat tanggal diubah; katalog diberi label "stok hari ini / pada tanggal terpilih" |

## Keamanan

- K1: `POST /register` memakai `throttle:5,1`.
- K2: token cron hanya lewat header `Authorization: Bearer` atau `X-Cron-Token`; query string `?token=` tidak diterima lagi.
- K3: `AlatController::update` dan `destroy` berjalan dalam `DB::transaction` dengan `lockForUpdate` pada baris alat, sama dengan pemesanan.
- K4: unggah bukti transfer dibatasi 5 kali per pesanan.

## Frontend

- F1: `admin/pesanan/show` (1200 → 126 baris), `pesanan/show` pelanggan (782 → 89), dan `admin/dashboard` (549 → 32) dipecah menjadi partial. HTML hasil render diverifikasi identik dengan snapshot sebelum/sesudah.
- F2: `@tailwindcss/vite` dihapus (build memakai Tailwind v3 + PostCSS).
- F3: katalog dipaginasi 12 alat per halaman (alat tersedia didahulukan).
- F4: pemeriksaan aksesibilitas dasar, lihat [`../docs/audit-aksesibilitas.md`](../docs/audit-aksesibilitas.md): tautan "Lewati ke konten utama", fokus keyboard selalu terlihat, kontras `text-stone-400` diperbaiki.

## Kualitas kode

- Q1: baseline Larastan 499 baris (83 entri) menjadi kosong. Akar masalah: Larastan tidak membaca `casts()` (`parseModelCastsMethod: true` ditambahkan) dan relasi tanpa generik; keduanya diperbaiki beserta `?->` yang tidak perlu dan beberapa tipe kembalian. 11 pemanggilan `number_format` pada kolom `decimal:2` memakai `(float)` karena cast `decimal` bertipe string.
- Q2: `PesananBaru`, `ItemPesanan`, `PengembalianBaru`, `BarisPengembalian`, `RefundBaru` (`app/Data`) menggantikan `array $data` pada `PesananService::buat`, `PengembalianService::proses`, dan pembatalan dengan refund.
- Q3: `App\Support\PengaturanToko` menjadi satu-satunya akses bertipe ke tabel `pengaturan`; seluruh nilai bawaan ada di sana.
- Q4: identitas bawaan toko (termasuk nomor WA) berasal dari `config/toko.php` / `.env` (`TOKO_NAMA`, `TOKO_ALAMAT`, `TOKO_NO_WA`), bukan konstanta tersebar.
- Q5: `kembalikanJaminan`, `tambahDenda`, dan bukti transfer admin kini memanggil `authorize()`; ditambah `DendaPolicy::lunaskan`.
- Q7: berkas di repo sudah berakhir baris LF (diperiksa dengan `git ls-files --eol`); `.gitattributes` di root ditambahkan agar `render.yaml`, README, dan dokumen ikut dinormalkan.

## Temuan baru saat pengerjaan

- Halaman resi pelanggan dan PDF resi **error 500** untuk pesanan yang memiliki denda (`$d->status->value` pada `Denda` yang tidak punya `status`). Diperbaiki memakai `is_lunas` dan dilindungi test regresi.

## Dokumentasi

- D1: angka README dan `plan/hasil-blackbox.md` disinkronkan dengan hasil `php artisan test`; BB-10 ditulis 404.
- D2: `PULL_REQUEST.md` dipindah ke `plan/riwayat-pull-request-stok-keranjang.md`.

## Perubahan perilaku yang perlu diketahui

1. Pesanan menunggu kini dibatalkan otomatis 24 jam setelah dibuat. Bila toko tetap ingin "bayar saat ambil", set **Batas Waktu Pembayaran = 0** di Pengaturan.
2. Laporan pendapatan menghitung pembayaran dari pesanan dibatalkan (sebelumnya dikecualikan) dan mengurangkan refund.
3. Stok di katalog tanpa filter tanggal = stok hari ini.

## Belum dikerjakan

- Deploy dan operasional: O1 (masa berlaku database gratis, backup), O2, O3 (Dockerfile multi-stage), O4 (`migrate --seed` tiap boot), O5 (memastikan CI hijau), K5.
- Q6: pesan commit Conventional Commits dan memindahkan `dokumen skripsi/*.docx` keluar repo. Riwayat commit tidak bisa diubah tanpa menulis ulang sejarah, dan berkas skripsi dibiarkan sampai pemilik repo memutuskan.
- Uji concurrency (3 test) tetap butuh PostgreSQL/MySQL sungguhan; belum dijalankan lokal.
