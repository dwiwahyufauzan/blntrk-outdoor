# Plan 5 – Katalog dan Ketersediaan Alat

**Tujuan:** pelanggan melihat katalog dan mengetahui ketersediaan alat per rentang tanggal. **Fitur:** F04a (T03). **Estimasi:** 4–5 hari. Ini bagian teknis paling berisiko, jadi kerjakan dengan uji unit.

## 1. KetersediaanService
```powershell
php artisan make:class Services/KetersediaanService
```
```php
namespace App\Services;

class KetersediaanService
{
    public const STATUS_AKTIF = ['menunggu','dibayar','disewa','terlambat'];

    /** Jumlah terpakai per alat per hari dalam rentang [mulai, selesai). */
    public function terpakaiPerHari(int $alatId, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): array
    {
        $baris = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.alat_id', $alatId)
            ->whereIn('pesanan.status', self::STATUS_AKTIF)
            ->whereNull('pesanan.deleted_at')
            ->when($abaikanPesananId, fn ($q) => $q->where('pesanan.id', '!=', $abaikanPesananId))
            ->where('pesanan.tanggal_mulai', '<', $selesai)
            ->where(function ($q) use ($mulai) {
                // Terlambat: tetap menahan stok sampai hari ini
                $q->where('pesanan.tanggal_selesai', '>', $mulai)
                  ->orWhere('pesanan.status', 'terlambat');
            })
            ->get(['pesanan.tanggal_mulai','pesanan.tanggal_selesai','pesanan.status','pesanan_detail.jumlah']);

        $hasil = [];
        for ($d = $mulai->copy(); $d->lt($selesai); $d->addDay()) {
            $hasil[$d->toDateString()] = $baris->filter(function ($b) use ($d) {
                $akhir = $b->status === 'terlambat'
                    ? Carbon::parse($b->tanggal_selesai)->max(today()->addDay())
                    : Carbon::parse($b->tanggal_selesai);
                return $d->gte($b->tanggal_mulai) && $d->lt($akhir);
            })->sum('jumlah');
        }
        return $hasil;
    }

    /** Ketersediaan minimum sepanjang rentang. */
    public function tersedia(Alat $alat, Carbon $mulai, Carbon $selesai, ?int $abaikan = null): int
    {
        $kapasitas = $alat->stok_total - $alat->stok_perawatan;
        $terpakai = $this->terpakaiPerHari($alat->id, $mulai, $selesai, $abaikan);
        return max(0, $kapasitas - (empty($terpakai) ? 0 : max($terpakai)));
    }

    public function cukup(Alat $alat, int $jumlah, Carbon $mulai, Carbon $selesai): bool
    {
        return $this->tersedia($alat, $mulai, $selesai) >= $jumlah;
    }
}
```
Catatan: satu query per alat dan perulangan hari di PHP. Dengan durasi maksimum 14 hari dan puluhan alat ini sudah cepat.

## 2. Katalog Publik
```powershell
php artisan make:controller KatalogController
```
- `index`: query `Alat::where('is_aktif', true)->with('kategori')`, filter `kategori` dan `q`, `paginate(12)`.
- `show`: detail alat plus form tanggal.
- `ketersediaan` (JSON): terima `mulai`, `selesai`; kembalikan `{ "tersedia": n, "harga_per_hari": x }`. Validasi tanggal.
- Kartu alat menampilkan stok total sebagai informasi umum, bukan jaminan ketersediaan.

## 3. Interaksi di Halaman Detail (JavaScript sederhana)
- Dua input tanggal (`min` = hari ini). Setelah keduanya terisi, panggil `fetch('/alat/{id}/ketersediaan?mulai=..&selesai=..')`.
- Tampilkan "Tersedia: n unit" (hijau) atau "Tidak tersedia pada tanggal ini" (merah). Nonaktifkan tombol bila nol.
- Hitung durasi dan estimasi harga di sisi klien (hanya tampilan; angka final dihitung ulang di server).

## 4. Uji Unit (wajib)
```powershell
php artisan make:test KetersediaanServiceTest --unit
```
Kasus minimum (BB-28 sampai BB-32):
| Kasus | Setup | Harapan |
|---|---|---|
| Tanpa pesanan | stok 10 | 10 |
| Pesanan aktif beririsan | stok 2, pesanan jumlah 2 pada 1–3 | cek 2–3 → 0 |
| Tidak beririsan | pesanan 1–3, cek 3–5 | stok penuh |
| Pesanan batal | status dibatalkan | stok penuh |
| Perawatan | stok 5, perawatan 2 | 3 |
| Terlambat | selesai kemarin, belum kembali | menahan stok hari ini |
| Puncak harian | A 1–3 jumlah 2, B 3–5 jumlah 2, stok 3, cek 1–5 | 1 |

## 5. Risiko
| Risiko | Penanganan |
|---|---|
| Logika batas tanggal salah satu hari | Uji unit dengan kasus batas (tanggal selesai = tanggal mulai pesanan lain) |
| Zona waktu | `APP_TIMEZONE` sudah diset di Plan 1 |
| Lambat | Index `(status, tanggal_mulai, tanggal_selesai)` sudah ada; baru dioptimasi bila terukur lambat |

## 6. Definition of Done
- [ ] Katalog dengan filter dan pencarian tampil di ponsel dan desktop
- [ ] Endpoint ketersediaan memberi hasil benar
- [ ] Seluruh uji unit lolos
- [ ] Indikator ketersediaan berubah tanpa memuat ulang halaman
- [ ] Tangkapan layar katalog dan detail alat
