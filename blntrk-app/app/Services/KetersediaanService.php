<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\PesananDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KetersediaanService
{
    /**
     * Status pesanan yang dianggap aktif dan menahan stok alat.
     */
    public const STATUS_AKTIF = ['menunggu', 'dibayar', 'disewa', 'terlambat'];

    /**
     * Menghitung jumlah unit alat yang terpakai per hari dalam rentang [mulai, selesai).
     *
     * @return array<string, int> Format: ['YYYY-MM-DD' => jumlah_terpakai]
     */
    public function terpakaiPerHari(int $alatId, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): array
    {
        return $this->terpakaiPerHariBanyak([$alatId], $mulai, $selesai, $abaikanPesananId)[$alatId];
    }

    /**
     * Versi massal dari terpakaiPerHari: satu query untuk banyak alat sekaligus (menghindari N+1 di katalog).
     *
     * @param  array<int, int>  $alatIds
     * @return array<int, array<string, int>> Format: [alat_id => ['YYYY-MM-DD' => jumlah_terpakai]]
     */
    public function terpakaiPerHariBanyak(array $alatIds, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): array
    {
        $mulai = $mulai->copy()->startOfDay();
        $selesai = $selesai->copy()->startOfDay();
        $alatIds = array_values(array_unique(array_map('intval', $alatIds)));

        if (empty($alatIds)) {
            return [];
        }

        $baris = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->whereIn('pesanan_detail.alat_id', $alatIds)
            ->whereIn('pesanan.status', self::STATUS_AKTIF)
            ->whereNull('pesanan.deleted_at')
            ->when($abaikanPesananId, fn ($q) => $q->where('pesanan.id', '!=', $abaikanPesananId))
            ->where('pesanan.tanggal_mulai', '<', $selesai)
            ->where(function ($q) use ($mulai) {
                // Terlambat: tetap menahan stok hingga hari ini/besok jika belum kembali
                $q->where('pesanan.tanggal_selesai', '>', $mulai)
                    ->orWhere('pesanan.status', 'terlambat');
            })
            ->get([
                'pesanan_detail.alat_id',
                'pesanan.tanggal_mulai',
                'pesanan.tanggal_selesai',
                'pesanan.status',
                'pesanan_detail.jumlah',
            ]);

        $besok = today()->addDay()->startOfDay();

        // Parse tanggal sekali per baris, bukan sekali per baris per hari
        $perAlat = $baris->groupBy('alat_id')->map(fn (Collection $rows) => $rows->map(function ($b) use ($besok) {
            $akhir = Carbon::parse($b->tanggal_selesai)->startOfDay();

            return [
                'mulai' => Carbon::parse($b->tanggal_mulai)->startOfDay(),
                'akhir' => $b->status === 'terlambat' ? $akhir->max($besok) : $akhir,
                'jumlah' => (int) $b->jumlah,
            ];
        }));

        $hasil = [];
        foreach ($alatIds as $alatId) {
            $rows = $perAlat->get($alatId, collect());
            $hasil[$alatId] = [];

            for ($d = $mulai->copy(); $d->lt($selesai); $d->addDay()) {
                $hasil[$alatId][$d->toDateString()] = (int) $rows
                    ->filter(fn ($r) => $d->gte($r['mulai']) && $d->lt($r['akhir']))
                    ->sum('jumlah');
            }
        }

        return $hasil;
    }

    /**
     * Ketersediaan unit alat minimum sepanjang rentang waktu [mulai, selesai).
     */
    public function tersedia(Alat $alat, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): int
    {
        // Kapasitas efektif = stok fisik total dikurangi unit yang sedang rusak/perawatan
        $kapasitas = max(0, (int) $alat->stok_total - (int) $alat->stok_perawatan);
        $terpakai = $this->terpakaiPerHari($alat->id, $mulai, $selesai, $abaikanPesananId);

        $maxTerpakai = empty($terpakai) ? 0 : max($terpakai);

        return max(0, $kapasitas - $maxTerpakai);
    }

    /**
     * Ketersediaan banyak alat sekaligus dengan satu query pemakaian.
     *
     * @param  iterable<Alat>  $alatList
     * @return array<int, int> Format: [alat_id => unit_tersedia]
     */
    public function tersediaBanyak(iterable $alatList, Carbon $mulai, Carbon $selesai): array
    {
        $alatList = collect($alatList)->keyBy('id');
        $terpakai = $this->terpakaiPerHariBanyak($alatList->keys()->all(), $mulai, $selesai);

        return $alatList->map(function (Alat $alat) use ($terpakai) {
            $kapasitas = max(0, (int) $alat->stok_total - (int) $alat->stok_perawatan);
            $perHari = $terpakai[$alat->id] ?? [];

            return max(0, $kapasitas - (empty($perHari) ? 0 : max($perHari)));
        })->all();
    }

    /**
     * Memeriksa apakah stok alat mencukupi untuk jumlah yang diminta sepanjang rentang waktu.
     */
    public function cukup(Alat $alat, int $jumlah, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): bool
    {
        return $this->tersedia($alat, $mulai, $selesai, $abaikanPesananId) >= $jumlah;
    }

    /**
     * Pemakaian unit tertinggi pada satu hari mana pun dari $dari hingga akhir seluruh pesanan aktif alat tersebut.
     * Dipakai untuk menolak pengurangan stok/penghapusan alat yang masih dibutuhkan pesanan aktif.
     */
    public function puncakTerpakai(int $alatId, ?Carbon $dari = null): int
    {
        $dari = ($dari ?? today())->copy()->startOfDay();

        $akhirTerjauh = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.alat_id', $alatId)
            ->whereIn('pesanan.status', self::STATUS_AKTIF)
            ->whereNull('pesanan.deleted_at')
            ->max('pesanan.tanggal_selesai');

        if (! $akhirTerjauh) {
            return 0;
        }

        // +1 hari agar pesanan terlambat (yang menahan stok sampai besok) ikut terhitung
        $sampai = Carbon::parse($akhirTerjauh)->startOfDay()->max($dari)->addDay()->addDay();
        $terpakai = $this->terpakaiPerHari($alatId, $dari, $sampai);

        return empty($terpakai) ? 0 : max($terpakai);
    }

    /**
     * Ringkasan ketersediaan harian satu alat untuk kalender (heatmap) di halaman detail.
     *
     * @return array<int, array{tanggal: string, terpakai: int, tersedia: int}>
     */
    public function kalender(Alat $alat, Carbon $mulai, int $jumlahHari = 42): array
    {
        $mulai = $mulai->copy()->startOfDay();
        $selesai = $mulai->copy()->addDays($jumlahHari);
        $kapasitas = max(0, (int) $alat->stok_total - (int) $alat->stok_perawatan);

        $hasil = [];
        foreach ($this->terpakaiPerHari($alat->id, $mulai, $selesai) as $tanggal => $terpakai) {
            $hasil[] = [
                'tanggal' => $tanggal,
                'terpakai' => $terpakai,
                'tersedia' => max(0, $kapasitas - $terpakai),
            ];
        }

        return $hasil;
    }
}
