<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\PesananDetail;
use Carbon\Carbon;

class KetersediaanService
{
    /**
     * Status pesanan yang dianggap aktif dan menahan stok alat.
     */
    public const STATUS_AKTIF = ['menunggu', 'dibayar', 'disewa', 'terlambat'];

    /**
     * Menghitung jumlah unit alat yang terpakai per hari dalam rentang [mulai, selesai).
     *
     * @param int $alatId
     * @param Carbon $mulai
     * @param Carbon $selesai
     * @param int|null $abaikanPesananId
     * @return array<string, int> Format: ['YYYY-MM-DD' => jumlah_terpakai]
     */
    public function terpakaiPerHari(int $alatId, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): array
    {
        $mulai = $mulai->copy()->startOfDay();
        $selesai = $selesai->copy()->startOfDay();

        $baris = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.alat_id', $alatId)
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
                'pesanan.tanggal_mulai',
                'pesanan.tanggal_selesai',
                'pesanan.status',
                'pesanan_detail.jumlah'
            ]);

        $hasil = [];
        for ($d = $mulai->copy(); $d->lt($selesai); $d->addDay()) {
            $key = $d->toDateString();
            $hasil[$key] = $baris->filter(function ($b) use ($d) {
                $tglMulai = Carbon::parse($b->tanggal_mulai)->startOfDay();
                $akhir = $b->status === 'terlambat'
                    ? Carbon::parse($b->tanggal_selesai)->startOfDay()->max(today()->addDay()->startOfDay())
                    : Carbon::parse($b->tanggal_selesai)->startOfDay();

                return $d->gte($tglMulai) && $d->lt($akhir);
            })->sum('jumlah');
        }

        return $hasil;
    }

    /**
     * Ketersediaan unit alat minimum sepanjang rentang waktu [mulai, selesai).
     *
     * @param Alat $alat
     * @param Carbon $mulai
     * @param Carbon $selesai
     * @param int|null $abaikanPesananId
     * @return int
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
     * Memeriksa apakah stok alat mencukupi untuk jumlah yang diminta sepanjang rentang waktu.
     *
     * @param Alat $alat
     * @param int $jumlah
     * @param Carbon $mulai
     * @param Carbon $selesai
     * @param int|null $abaikanPesananId
     * @return bool
     */
    public function cukup(Alat $alat, int $jumlah, Carbon $mulai, Carbon $selesai, ?int $abaikanPesananId = null): bool
    {
        return $this->tersedia($alat, $mulai, $selesai, $abaikanPesananId) >= $jumlah;
    }
}
