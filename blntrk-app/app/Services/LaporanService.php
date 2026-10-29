<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LaporanService
{
    /**
     * Laporan 1: Penyewaan
     * Berisi daftar pesanan rental dalam rentang tanggal sewa.
     *
     * @return array{items: Collection, total_pesanan: int, total_sewa: float, per_status: array}
     */
    public function laporanPenyewaan(Carbon $dari, Carbon $sampai, ?string $status = null): array
    {
        $query = Pesanan::with(['pelanggan', 'pembuat', 'detail.alat'])
            ->whereDate('tanggal_mulai', '>=', $dari->toDateString())
            ->whereDate('tanggal_mulai', '<=', $sampai->toDateString())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('tanggal_mulai')
            ->orderBy('id');

        $items = $query->get();

        $totalSewa = $items->sum('total_sewa');
        $perStatus = $items->groupBy(fn ($p) => $p->status->value)
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total_sewa'),
            ])->toArray();

        return [
            'items' => $items,
            'total_pesanan' => $items->count(),
            'total_sewa' => (float) $totalSewa,
            'per_status' => $perStatus,
        ];
    }

    /**
     * Laporan 2: Pendapatan Finansial Riil (Kas Masuk)
     * Dihitung berdasarkan pembayaran.dibayar_pada yang jatuh tempo pada periode,
     * ditambah denda yang berstatus lunas pada periode tersebut.
     * Pesanan dibatalkan tidak dihitung.
     *
     * @return array{
     *     pembayaran_sewa: Collection,
     *     denda_lunas: Collection,
     *     total_pendapatan_sewa: float,
     *     total_pendapatan_denda: float,
     *     total_pendapatan_bersih: float
     * }
     */
    public function laporanPendapatan(Carbon $dari, Carbon $sampai): array
    {
        $start = $dari->copy()->startOfDay();
        $end = $sampai->copy()->endOfDay();

        // 1. Kas masuk dari sewa alat (Pembayaran riil)
        $pembayaranSewa = Pembayaran::with(['pesanan.pelanggan', 'pencatat'])
            ->whereBetween('dibayar_pada', [$start, $end])
            ->whereHas('pesanan', fn ($q) => $q->where('status', '!=', StatusPesanan::Dibatalkan->value))
            ->orderBy('dibayar_pada')
            ->get();

        // 2. Kas masuk dari denda keterlambatan/kerusakan yang sudah dilunasi
        $dendaLunas = Denda::with(['pesanan.pelanggan', 'petugasLunas'])
            ->where('status_bayar', 'lunas')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('dilunasi_pada', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('dilunasi_pada')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderBy('dilunasi_pada')
            ->get();

        $totalSewa = (float) $pembayaranSewa->sum('jumlah');
        $totalDenda = (float) $dendaLunas->sum('jumlah');
        $totalBersih = $totalSewa + $totalDenda;

        return [
            'pembayaran_sewa' => $pembayaranSewa,
            'denda_lunas' => $dendaLunas,
            'total_pendapatan_sewa' => $totalSewa,
            'total_pendapatan_denda' => $totalDenda,
            'total_pendapatan_bersih' => $totalBersih,
        ];
    }

    /**
     * Laporan 3: Denda dan Ganti Rugi
     * Berisi catatan seluruh denda keterlambatan dan kerusakan alat.
     *
     * @return array{
     *     items: Collection,
     *     total_denda: float,
     *     total_lunas: float,
     *     total_belum_lunas: float,
     *     jumlah_kasus: int
     * }
     */
    public function laporanDenda(Carbon $dari, Carbon $sampai, ?string $statusBayar = null): array
    {
        $start = $dari->copy()->startOfDay();
        $end = $sampai->copy()->endOfDay();

        $query = Denda::with(['pesanan.pelanggan', 'petugasLunas'])
            ->whereBetween('created_at', [$start, $end])
            ->when($statusBayar, fn ($q) => $q->where('status_bayar', $statusBayar))
            ->orderBy('created_at', 'desc');

        $items = $query->get();

        $totalDenda = (float) $items->sum('jumlah');
        $totalLunas = (float) $items->where('status_bayar', 'lunas')->sum('jumlah');
        $totalBelumLunas = (float) $items->where('status_bayar', 'belum_lunas')->sum('jumlah');

        return [
            'items' => $items,
            'total_denda' => $totalDenda,
            'total_lunas' => $totalLunas,
            'total_belum_lunas' => $totalBelumLunas,
            'jumlah_kasus' => $items->count(),
        ];
    }

    /**
     * Laporan 4: Alat Terlaris (Most Rented Equipment)
     * Mengukur kuantitas penyewaan dan pendapatan yang dihasilkan tiap alat.
     *
     * @return array{items: Collection, total_unit_disewa: int, total_omzet_alat: float}
     */
    public function laporanAlatTerlaris(Carbon $dari, Carbon $sampai, int $limit = 10): array
    {
        $items = PesananDetail::query()
            ->selectRaw('alat_id, SUM(jumlah) as total_disewa, SUM(subtotal) as total_pendapatan, COUNT(DISTINCT pesanan_id) as total_transaksi')
            ->whereHas('pesanan', function ($q) use ($dari, $sampai) {
                $q->where('status', '!=', StatusPesanan::Dibatalkan->value)
                    ->whereDate('tanggal_mulai', '>=', $dari->toDateString())
                    ->whereDate('tanggal_mulai', '<=', $sampai->toDateString());
            })
            ->groupBy('alat_id')
            ->orderByDesc('total_disewa')
            ->limit($limit)
            ->with('alat.kategori')
            ->get();

        return [
            'items' => $items,
            'total_unit_disewa' => (int) $items->sum('total_disewa'),
            'total_omzet_alat' => (float) $items->sum('total_pendapatan'),
        ];
    }

    /**
     * Laporan 5: Stok Kritis (Stok fisik tersedia <= batas)
     */
    public function laporanStokKritis(int $ambangBatas = 2): Collection
    {
        return Alat::with('kategori')
            ->where('is_aktif', true)
            ->whereRaw('(stok_total - stok_perawatan) <= ?', [$ambangBatas])
            ->orderByRaw('(stok_total - stok_perawatan) ASC')
            ->get();
    }
}
