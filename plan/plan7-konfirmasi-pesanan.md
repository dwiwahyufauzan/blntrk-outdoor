# Plan 7 – Konfirmasi Pesanan: Pembayaran, Pengambilan, Jaminan, Pembatalan

**Tujuan:** Admin memproses pesanan dari Menunggu hingga Disewa, dengan aturan status yang ketat. **Fitur:** F05. **Estimasi:** 4–5 hari.

## 1. Aturan Transisi (state machine)
Semua perubahan status melalui `PesananService`. Controller tidak boleh mengisi `status` langsung.

| Aksi | Dari | Ke | Syarat |
|---|---|---|---|
| bayar | menunggu | dibayar | jumlah = total_sewa, belum ada pembayaran |
| ambil | dibayar | disewa | jaminan valid |
| batal (Admin) | menunggu, dibayar | dibatalkan | alasan wajib |
| batal (Pelanggan) | menunggu | dibatalkan | milik sendiri |
| (scheduler) | disewa | terlambat | lewat jatuh tempo + toleransi |
| (scheduler) | menunggu | dibatalkan | lewat batas_ambil |

```php
private function pastikan(Pesanan $p, array $dari): void {
    if (! in_array($p->status, $dari, true)) {
        throw ValidationException::withMessages(['status' => "Aksi tidak valid untuk status {$p->status->value}."]);
    }
}
```

## 2. Implementasi
```php
public function bayar(Pesanan $p, User $admin, array $d): void {
    DB::transaction(function () use ($p, $admin, $d) {
        $p = Pesanan::lockForUpdate()->findOrFail($p->id);
        $this->pastikan($p, [StatusPesanan::Menunggu]);
        if ((float) $d['jumlah'] !== (float) $p->total_sewa) throw ValidationException::withMessages(['jumlah' => 'Jumlah harus sama dengan total sewa.']);
        $p->pembayaran()->create([...$d, 'dibayar_pada' => now(), 'dicatat_oleh' => $admin->id]);
        $p->update(['status' => StatusPesanan::Dibayar, 'diproses_oleh' => $admin->id]);
        LogService::catat($admin, 'konfirmasi_bayar', 'pesanan', $p->id, "Pembayaran {$p->no_resi}");
    });
}

public function ambil(Pesanan $p, User $admin, array $d): void {
    DB::transaction(function () use ($p, $admin, $d) {
        $p = Pesanan::lockForUpdate()->findOrFail($p->id);
        $this->pastikan($p, [StatusPesanan::Dibayar]);
        $p->jaminan()->create([
            'jenis' => $d['jenis'],
            'nomor_identitas_samar' => '****'.substr(preg_replace('/\D/', '', $d['nomor_identitas']), -4),
            'keterangan' => $d['keterangan'] ?? null,
            'diterima_pada' => now(), 'diterima_oleh' => $admin->id,
        ]);
        $p->update(['status' => StatusPesanan::Disewa, 'diambil_pada' => now(), 'diproses_oleh' => $admin->id]);
    });
}

public function batal(Pesanan $p, User $pelaku, string $alasan): void { /* sesuai tabel */ }
```
Catatan: nomor identitas penuh **tidak disimpan** (NF09). Hanya empat digit terakhir.

## 3. Admin: Daftar dan Detail Pesanan
- `index`: tab status (Semua, Menunggu, Dibayar, Disewa, Terlambat, Dikembalikan, Dibatalkan), pencarian resi atau nama, urut terbaru, paginate.
- `show`: panel aksi sesuai status (lihat A10 di dokumen 05):
  - Menunggu: tombol **Catat Pembayaran** (modal), **Batalkan** (modal alasan).
  - Dibayar: form **Konfirmasi Pengambilan** (jaminan), **Batalkan**.
  - Disewa/Terlambat: tombol **Proses Pengembalian** (Plan 8).
- Timeline status dari data (`created_at`, pembayaran, `diambil_pada`, dst.).
- Tautan WhatsApp ke pelanggan: `https://wa.me/62...` (normalkan nomor `08…` menjadi `628…`).

## 4. Form Request
`PembayaranRequest`, `PengambilanRequest`, `BatalRequest` (dokumen 04, 4d).

## 5. Pengujian (BB-42 sampai BB-50)
Bayar sesuai/kurang, ambil tanpa bayar, jaminan kosong, batal oleh pelanggan pada status Dibayar (ditolak), batal Admin, batal pada status Disewa (ditolak). Coba **manipulasi request** (kirim POST langsung ke URL aksi pada status salah) untuk memastikan state machine menolak.

## 6. Definition of Done
- [x] Alur Menunggu → Dibayar → Disewa berjalan
- [x] Transisi tidak valid ditolak walau request dimanipulasi
- [x] Jaminan tersimpan (empat digit)
- [x] Pembatalan dengan alasan dan stok terbebas
- [x] BB-42 sampai BB-50 lolos

