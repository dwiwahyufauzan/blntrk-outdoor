# Audit Aksesibilitas Dasar

Pemeriksaan ini dilakukan secara statis (kode dan perhitungan kontras), **bukan** pengujian dengan pembaca layar atau Lighthouse. Hasil pengujian dengan alat tersebut perlu dilampirkan terpisah bila dibutuhkan skripsi.

## Kontras warna (WCAG 2.1 AA: 4,5:1 untuk teks normal)

| Pasangan | Rasio | Keterangan |
|---|:---:|---|
| `#14201a` di atas `#f4f5f6` (teks utama) | 15,4 | Lolos |
| `#5edd5b` di atas `#0e1b15` (aksen di panel gelap) | 10,1 | Lolos |
| `#000` di atas `#5edd5b` (tombol utama) | 12,0 | Lolos |
| `stone-500` `#78716c` di atas putih | 4,8 | Lolos |
| `stone-400` `#a8a29e` di atas putih | 2,5 | **Gagal** (dipakai 340 tempat) |
| `#5edd5b` di atas putih | 1,75 | **Gagal** bila dipakai sebagai warna teks pada latar terang |
| `stone-400` di atas `#0e1b15` | 7,0 | Lolos (panel gelap) |

Perbaikan: `resources/css/app.css` menggelapkan `.text-stone-400` ke `#78716c` pada latar terang dan mempertahankan warna asli di dalam kontainer gelap.

Belum: `text-[#5edd5b]` (196 pemakaian) perlu ditinjau satu per satu; sebagian berada di latar gelap (lolos), sebagian di latar terang (gagal).

## Keyboard dan fokus

- Tautan "Lewati ke konten utama" ditambahkan di kelima layout; `<main id="konten-utama">`.
- Aturan global `:focus-visible` pada tautan, tombol, `summary`, dan elemen `role="button"` agar fokus tetap terlihat walau komponen memakai `focus:outline-none`.
- Modal baru (refund) memakai `aria-label` pada tombol tutup dan label `sr-only` pada pilihan penanganan dana serta metode pelunasan denda.

## Belum diperiksa

- Tombol ikon tanpa teks pada modal lama belum seluruhnya memiliki `aria-label`.
- Perangkap fokus (focus trap) dan penutupan modal dengan tombol Esc pada modal berbasis Alpine.
- Urutan judul (`h1`–`h3`) dan landmark per halaman.
- Gambar katalog memakai `alt` nama alat; foto bawaan Unsplash belum diaudit.
