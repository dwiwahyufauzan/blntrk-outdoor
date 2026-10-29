<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Services\LaporanService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(LaporanService $laporanService): View
    {
        $today = today();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // 1. Pendapatan Hari Ini
        $sewaHariIni = Pembayaran::whereDate('dibayar_pada', $today)
            ->whereHas('pesanan', fn ($q) => $q->where('status', '!=', StatusPesanan::Dibatalkan->value))
            ->sum('jumlah');

        $dendaHariIni = Denda::where('status_bayar', 'lunas')
            ->whereDate('dilunasi_pada', $today)
            ->sum('jumlah');

        $pendapatanHariIni = (float) ($sewaHariIni + $dendaHariIni);

        // 2. Pendapatan Bulan Ini
        $sewaBulanIni = Pembayaran::whereBetween('dibayar_pada', [$startOfMonth, $endOfMonth])
            ->whereHas('pesanan', fn ($q) => $q->where('status', '!=', StatusPesanan::Dibatalkan->value))
            ->sum('jumlah');

        $dendaBulanIni = Denda::where('status_bayar', 'lunas')
            ->whereBetween('dilunasi_pada', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $pendapatanBulanIni = (float) ($sewaBulanIni + $dendaBulanIni);

        // 3. Ringkasan Metrik
        $ringkasan = [
            'pendapatan_hari_ini' => $pendapatanHariIni,
            'pendapatan_bulan_ini' => $pendapatanBulanIni,
            'total_transaksi' => Pesanan::where('status', '!=', StatusPesanan::Dibatalkan->value)->count(),
            'total_alat' => Alat::count(),
            'alat_dalam_perawatan' => Alat::where('stok_perawatan', '>', 0)->sum('stok_perawatan'),
            'jumlah_terlambat' => Pesanan::where('status', StatusPesanan::Terlambat)->count(),
        ];

        // 4. Alat Terlaris (Top 5) & Stok Kritis
        $alatTerlaris = $laporanService->laporanAlatTerlaris(now()->subMonths(6), now(), 5)['items'];
        $stokKritis = $laporanService->laporanStokKritis(2);

        // 5. Grafik Pendapatan 6 Bulan Terakhir
        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $bln = now()->subMonths($i);
            $start = $bln->copy()->startOfMonth();
            $end = $bln->copy()->endOfMonth();

            $chartLabels[] = $bln->translatedFormat('M Y');

            $revSewa = Pembayaran::whereBetween('dibayar_pada', [$start, $end])
                ->whereHas('pesanan', fn ($q) => $q->where('status', '!=', StatusPesanan::Dibatalkan->value))
                ->sum('jumlah');

            $revDenda = Denda::where('status_bayar', 'lunas')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('dilunasi_pada', [$start, $end])
                        ->orWhere(function ($sub) use ($start, $end) {
                            $sub->whereNull('dilunasi_pada')
                                ->whereBetween('created_at', [$start, $end]);
                        });
                })
                ->sum('jumlah');

            $chartData[] = (float) ($revSewa + $revDenda);
        }

        $transaksiTerbaru = Pesanan::with(['pelanggan', 'pemroses'])
            ->latest()
            ->take(6)
            ->get();

        return view('pemilik.dashboard', compact(
            'ringkasan',
            'alatTerlaris',
            'stokKritis',
            'chartLabels',
            'chartData',
            'transaksiTerbaru'
        ));
    }
}
