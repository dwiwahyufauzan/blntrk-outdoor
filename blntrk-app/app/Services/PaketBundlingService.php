<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\PaketBundling;

class PaketBundlingService
{
    /**
     * Mendeteksi paket bundling aktif yang terpenuhi oleh isi pesanan/keranjang dan
     * menghitung diskonnya. Bila beberapa paket terpenuhi, dipilih yang memberi hemat terbesar.
     * Harga eceran dihitung dari harga alat saat ini, bukan dari kolom harga_asli_per_hari.
     *
     * @param  array<int, int>  $kuantitas  Format: [alat_id => jumlah]
     * @return array{paket: ?PaketBundling, jumlah_paket: int, hemat_per_hari: float, diskon: float}
     */
    public function hitungDiskon(array $kuantitas, int $durasi): array
    {
        $kosong = ['paket' => null, 'jumlah_paket' => 0, 'hemat_per_hari' => 0.0, 'diskon' => 0.0];

        $kuantitas = array_filter($kuantitas, fn ($q) => $q > 0);
        if (empty($kuantitas)) {
            return $kosong;
        }

        $paketList = PaketBundling::aktif()->with('items')->get()->filter(fn ($p) => $p->items->isNotEmpty());
        if ($paketList->isEmpty()) {
            return $kosong;
        }

        $hargaAlat = Alat::withTrashed()
            ->whereIn('id', $paketList->flatMap(fn ($p) => $p->items->pluck('alat_id'))->unique())
            ->pluck('harga_per_hari', 'id');

        $terbaik = $kosong;

        foreach ($paketList as $paket) {
            $set = PHP_INT_MAX;
            $hargaEceran = 0.0;

            foreach ($paket->items as $item) {
                $dimiliki = (int) ($kuantitas[$item->alat_id] ?? 0);
                $set = min($set, intdiv($dimiliki, max(1, $item->jumlah)));
                $hargaEceran += (float) ($hargaAlat[$item->alat_id] ?? 0) * $item->jumlah;
            }

            if ($set < 1) {
                continue;
            }

            $hematPerHari = max(0.0, $hargaEceran - (float) $paket->harga_paket_per_hari) * $set;
            if ($hematPerHari > $terbaik['hemat_per_hari']) {
                $terbaik = [
                    'paket' => $paket,
                    'jumlah_paket' => $set,
                    'hemat_per_hari' => $hematPerHari,
                    'diskon' => $hematPerHari * max(1, $durasi),
                ];
            }
        }

        return $terbaik;
    }

    /**
     * Data paket ringkas untuk perhitungan diskon sisi klien (formulir pemesanan).
     *
     * @return array<int, array{id: int, nama: string, harga: float, items: array<int, int>}>
     */
    public function dataUntukKlien(): array
    {
        return PaketBundling::aktif()->with('items')->get()
            ->filter(fn ($p) => $p->items->isNotEmpty())
            ->map(fn ($p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'harga' => (float) $p->harga_paket_per_hari,
                'items' => $p->items->mapWithKeys(fn ($i) => [$i->alat_id => $i->jumlah])->all(),
            ])
            ->values()
            ->all();
    }
}
