# Plan 6 – Pemesanan (Pelanggan dan Walk-in)

**Tujuan:** pelanggan (dan Admin untuk walk-in) membuat pesanan lengkap dengan total dan nomor resi. **Fitur:** F04b, F04c. **Estimasi:** 5–6 hari.

## 1. Alur Form
Dua pendekatan; pilih **A** (lebih sederhana):
- **A. Form tunggal**: halaman `/pesanan/buat` berisi tanggal mulai dan selesai, serta daftar alat (dropdown + jumlah, tombol "Tambah Baris"). Dari halaman detail alat, tombol "Pesan" membawa `alat_id` dan tanggal sebagai nilai awal.
- B. Keranjang di session: lebih kaya, tetapi menambah kompleksitas, tunda sebagai pengembangan.

## 2. PesananService::buat
```powershell
php artisan make:class Services/PesananService
```
```php
public function buat(Pelanggan $pelanggan, User $pembuat, array $data): Pesanan
{
    return DB::transaction(function () use ($pelanggan, $pembuat, $data) {
        $mulai = Carbon::parse($data['tanggal_mulai'])->startOfDay();
        $selesai = Carbon::parse($data['tanggal_selesai'])->startOfDay();
        $durasi = max(1, $mulai->diffInDays($selesai));

        // Kunci baris alat agar pemesanan paralel berurutan
        $alatIds = collect($data['items'])->pluck('alat_id')->sort()->values();
        $alatList = Alat::whereIn('id', $alatIds)->where('is_aktif', true)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        $total = 0; $detail = [];
        foreach ($data['items'] as $item) {
            $alat = $alatList[$item['alat_id']] ?? throw ValidationException::withMessages(['items' => 'Alat tidak tersedia.']);
            if (! $this->ketersediaan->cukup($alat, $item['jumlah'], $mulai, $selesai)) {
                throw ValidationException::withMessages(['items' => "Stok {$alat->nama} tidak mencukupi pada tanggal tersebut."]);
            }
            $subtotal = $alat->harga_per_hari * $item['jumlah'] * $durasi;
            $total += $subtotal;
            $detail[] = ['alat_id'=>$alat->id,'jumlah'=>$item['jumlah'],'harga_per_hari'=>$alat->harga_per_hari,'subtotal'=>$subtotal];
        }

        $jamKembali = Pengaturan::ambil('jam_kembali', '12:00');
        $pesanan = Pesanan::create([
            'no_resi' => $this->buatResi(),
            'pelanggan_id' => $pelanggan->id,
            'dibuat_oleh' => $pembuat->id,
            'tanggal_mulai' => $mulai, 'tanggal_selesai' => $selesai, 'durasi_hari' => $durasi,
            'jatuh_tempo' => $selesai->copy()->setTimeFromTimeString($jamKembali),
            'batas_ambil' => $mulai->copy()->setTime(23, 59),
            'status' => StatusPesanan::Menunggu, 'total_sewa' => $total,
        ]);
        $pesanan->detail()->createMany($detail);
        return $pesanan;
    });
}
```
`buatResi()`: format `BLN-YYMMDD-####`, urutan harian dari hitungan pesanan hari itu (dalam transaksi) atau gunakan `lockForUpdate` pada pengambilan nomor terakhir; unique index sebagai pengaman, ulangi bila bentrok.

Catatan: ketersediaan dihitung **di dalam transaksi setelah `lockForUpdate`** (NF07). Hindari menghitung total dari input klien.

## 3. Form Request
`PesananRequest` (lihat dokumen 04, 4d). Tambahan:
- `items.*.alat_id` tidak boleh ganda dalam satu pesanan (`distinct`).
- Lapisan kedua pengecekan ketersediaan berada di Service (sumber kebenaran).

## 4. Controller
`Pelanggan\PesananController`: `create`, `store`, `index`, `show`, `batal`.
`Admin\PesananController@create/store`: Admin memilih pelanggan (cari atau tambah cepat walk-in) lalu memakai `PesananService::buat` yang sama (`dibuat_oleh` = Admin).

## 5. Halaman Resi
- Tampilkan no resi besar, status, daftar alat, total, batas pengambilan, instruksi (bawa identitas, bayar di toko).
- Tombol **Hubungi Toko via WhatsApp**: `https://wa.me/{no_wa_toko}?text=urlencode("Halo BLNTRK, saya ingin konfirmasi pesanan {resi}")`.
- Tombol cetak resi (PDF di Plan 9, sementara `window.print()`).

## 6. Pembatalan oleh Pelanggan
`PATCH /pesanan/{pesanan}/batal`: hanya status `menunggu` dan milik sendiri (Policy). Set status `dibatalkan`, `dibatalkan_pada`, `alasan_batal` (opsional "Dibatalkan pelanggan").

## 7. Policy
```powershell
php artisan make:policy PesananPolicy --model=Pesanan
```
`view`: pelanggan hanya pesanan miliknya (`$pesanan->pelanggan->user_id === $user->id`). Admin dan Pemilik boleh lihat. `batal`: pelanggan hanya status menunggu.

## 8. Pengujian (BB-33 sampai BB-41, BB-47)
Satu alat, banyak alat, jumlah berlebih, tanggal tidak valid, durasi lebih dari 14 hari, resi unik, **pesanan paralel** (jalankan dua tab/`curl` serentak pada stok 1), walk-in, batal pelanggan.

## 9. Definition of Done
- [x] Pesanan tersimpan dengan header, detail, snapshot harga, total benar
- [x] Stok tidak pernah terlampaui (uji paralel)
- [x] Resi unik dan tampil dengan tautan WhatsApp
- [x] Admin dapat membuat pesanan walk-in
- [x] Pelanggan hanya melihat pesanan sendiri
- [x] Uji unit total harga (contoh Rp330.000) lolos
