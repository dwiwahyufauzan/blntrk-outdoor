---
name: foresta-outdoor-design
description: Panduan design system dan visual UI Foresta Adventure untuk seluruh halaman BLNTRK Outdoor. Gunakan skill ini setiap kali mengimplementasikan, merombak, atau memodifikasi tampilan halaman publik maupun interface pengguna agar konsisten dengan forestaadventure.com (warna, tipografi, navbar, floating schedule picker, cards, rasio produk, badge).
---

# Foresta Adventure Design System & UI Guidelines

Panduan ini mengatur standar visual antarmuka (UI/UX) untuk proyek **BLNTRK Outdoor**, yang mengadopsi bahasa desain modern dari **Foresta Adventure** (`forestaadventure.com`). Seluruh halaman (Beranda, Katalog, Detail Produk, Keranjang, Konfirmasi Pesanan, Riwayat Sewa, dan Interface Pengguna) **WAJIB** mematuhi aturan dan token desain dalam dokumen ini.

---

## 1. Brand Identity & Design Tokens

### 1.1 Palet Warna Utama (Color Palette)
- **Primary Action (Foresta Neon Lime):**
  - Hex: `#5edd5b` / `#70f46d`
  - Tailwind helper: `bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-bold`
  - Digunakan untuk: Tombol aksi utama (CTA), tombol "Cari", tombol "Lihat Lainnya", badge aktif, bullet checkmark tick, radio/checkbox aktif.
- **Deep Forest Dark (Header, Hero & Dark Accents):**
  - Hex: `#0e1b15` / `#14251e`
  - Digunakan untuk: Latar hero banner, top bar, dark cards, footer, teks judul kontras tinggi.
- **Neutral Background Canvas:**
  - Hex: `#f4f5f6` / `#f8fafc`
  - Digunakan untuk latar belakang seluruh halaman publik (`bg-[#f4f5f6]`).
- **Surface & Cards:**
  - Hex: `#ffffff` (Pure White) dengan `border border-stone-200/80` dan `shadow-xs` hingga `shadow-lg`.
- **Text Hierarchy:**
  - Headings / Heavy: `#111827` (stone-900)
  - Body: `#374151` (stone-700)
  - Muted / Labels: `#6b7280` (stone-500)
  - Inverted on Dark: `#ffffff` dan `#ffffff/80`

### 1.2 Tipografi
- **Primary Sans:** `Plus Jakarta Sans` atau `Urbanist`, sans-serif.
- **Display Headings:** Font tebal modern (`font-extrabold` / `font-bold`), tracking ketat (`tracking-tight`), line height proporsional.

---

## 2. Struktur Komponen Standar

### 2.1 Top Bar Info
- Letak: Di paling atas sebelum navbar.
- Tampilan: `bg-[#0e1b15] text-stone-300 text-xs py-2 px-4`.
- Konten:
  - Kiri: Kontak telepon (`📞 0812-3456-7890`) & Lokasi/Status Toko (`Buka 24 Jam • Subang`).
  - Kanan: Teks promo / informasi + tombol pill neon green (`bg-[#5edd5b] text-black text-xs font-bold px-3 py-1 rounded-full`).

### 2.2 Navigation Bar (Navbar)
- Tampilan: Transparan atau sticky white `bg-white/95 backdrop-blur-md border-b border-stone-200`.
- Elemen:
  - **Logo:** Siluet gunung outdoor putih atau hijau + nama merek "BLNTRK Outdoor".
  - **Menu Links:** `Beranda`, `Katalog`, `Cara Sewa`, `Kontak`, `Riwayat Sewa` (jika login).
  - **Tombol Masuk / Akun:**
    - Jika tamu: Teks `Masuk` (dengan icon user `👤`) + tombol `Daftar`.
    - Jika login: Nama user + badge role + tombol `Keluar`.
  - **Tombol Keranjang (Cart Button):**
    - Tombol putih rounded: `bg-white border border-stone-300 text-stone-900 rounded-xl px-3.5 py-2 flex items-center gap-1.5 shadow-sm`.
    - Icon shopping cart 🛒 + badge merah bulat jumlah item.

### 2.3 Hero Section
- Gaya: Latar belakang pemandangan outdoor / perkemahan dengan overlay gelap nuansa deep forest (`#0e1b15`).
- Konten:
  - Subtitle lime kecil: `Siap Menjelajah, Tanpa Ribet.` (`text-[#5edd5b] font-bold text-sm sm:text-base`).
  - Heading 1 raksasa: `Sewa Gear Outdoor Kini Lebih Mudah!` (`text-white font-extrabold text-4xl sm:text-6xl`).
  - Feature checklist ticks (3 pill items dengan icon checklist bulat hijau):
    - `Stok Real-Time & Lengkap`
    - `Praktis & Cepat`
    - `Buka 24 Jam di Subang (Bayar di Toko)`

### 2.4 Floating Schedule & Search Picker Card
Komponen khas Foresta Adventure yang melayang di batas bawah Hero:
- Tampilan Card: `bg-white rounded-2xl sm:rounded-3xl border border-stone-200/90 shadow-xl p-4 sm:p-5 -mt-16 sm:-mt-20 relative z-20`.
- 4 Kolom Input Bersebelahan (Grid / Flexbox):
  1. **Tgl Ambil:** Label `Tgl Ambil`, input date dengan icon kalender 📅.
  2. **Durasi:** Label `Durasi`, dropdown durasi sewa (1 Hari, 2 Hari, 3 Hari, dst.).
  3. **Maks. Pengembalian:** Label `Maks. Pengembalian`, input readonly otomatis terhitung (Tgl Ambil + Durasi).
  4. **Cari:** Label `Cari`, input text `Cari peralatan ...`.
  5. **Tombol CTA Cari:** Tombol neon green `bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-bold px-6 py-3 rounded-xl flex items-center justify-center gap-2`.

### 2.5 Section "Pilihan Brand"
- Heading: `Pilihan Brand` dengan subtitle `Dipilih dari merek terbaik untuk pengalaman terbaik.`.
- Tampilan: Barisan card logo brand outdoor (Eiger, Consina, Tendaki, Merapi Mountain, Arei, Quechua, Mokzhaware, Deuter, Rubtrack, Great Outdoor) dengan card putih ber-radius halus (`rounded-2xl border border-stone-200/80 bg-white p-4 shadow-xs hover:shadow-md transition`).

### 2.6 Section "Pilihan Terbaik Minggu Ini!" (Featured Product Grid)
- Heading & CTA:
  - Kiri: Judul `Pilihan Terbaik Minggu Ini!` + subtitle `Gear Pilihan Pendaki, Siap Temani Petualanganmu.`.
  - Kanan: Tombol neon green `Lihat Lainnya →`.
- Grid Produk:
  - 4 kolom pada desktop (`grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6`).
  - Card Produk Foresta:
    - Wadah foto square 1:1 (`aspect-square bg-stone-50 rounded-xl p-3 flex items-center justify-center overflow-hidden`).
    - Foto produk: `object-contain w-full h-full group-hover:scale-105 transition duration-300`.
    - Judul produk: 2 baris (`line-clamp-2 text-sm font-bold text-stone-900 text-center min-h-[40px]`).
    - Harga sewa: `text-sm font-extrabold text-stone-900 text-center mt-1` (contoh: `Rp 25.000 /hari`).
    - Status stok: Badge pill halus (misal `Tersedia 5 unit` atau `Stok habis`).
    - Tombol aksi: Link ke detail alat untuk cek ketersediaan.

### 2.7 Alur Sewa & FAQ
- **4 Langkah Alur Sewa:** Kartu bernomor bulat hijau/lime dengan teks ringkas.
- **FAQ:** Accordion bersih berlatar putih dengan expand icon halus.

### 2.8 Footer Foresta
- Latar belakang Deep Forest (`bg-[#0e1b15] text-stone-400`).
- Kolom:
  - Tentang Toko & Alamat Lengkap.
  - Ketentuan & Alur Sewa.
  - Layanan Bantuan & WhatsApp Toko (nomor besar hijau).
  - Copyright & Social Media Links.

---

## 3. Checklist Konsistensi Desain Halaman Lainnya
Saat mengimplementasikan halaman Katalog, Detail Produk, Keranjang, Form Pesanan, dan Admin:
1. **Latar Kanvas:** Gunakan `bg-[#f4f5f6]` untuk halaman publik.
2. **Tombol Primer:** Selalu gunakan warna Foresta Neon Lime (`bg-[#5edd5b] text-black font-bold hover:bg-[#4ed04b]`).
3. **Kartu (Cards):** Selalu gunakan putih murni `bg-white`, border `border-stone-200/80`, dan radius `rounded-2xl` atau `rounded-3xl`.
4. **Foto Produk:** Pertahankan rasio 1:1 (square) atau centering rapi di wadah netral.
5. **Badge:** Gunakan `rounded-full text-xs font-semibold px-3 py-1`.
6. **Form Inputs:** Gunakan border halus `border-stone-300`, rounded `rounded-xl`, focus ring `#5edd5b`.
