<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Pengaturan;
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
        $sewaHariIni = $laporanService->pendapatanSewaBersih($today->copy()->startOfDay(), $today->copy()->endOfDay());

        $dendaHariIni = Denda::where('status_bayar', 'lunas')
            ->whereDate('dilunasi_pada', $today)
            ->sum('jumlah');

        $pendapatanHariIni = (float) ($sewaHariIni + $dendaHariIni);

        // 2. Pendapatan Bulan Ini
        $sewaBulanIni = $laporanService->pendapatanSewaBersih($startOfMonth, $endOfMonth);

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
        $stokKritis = $laporanService->laporanStokKritis((int) Pengaturan::ambil('ambang_stok_kritis', 2));

        // 5. Grafik Pendapatan 6 Bulan Terakhir
        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $bln = now()->subMonths($i);
            $start = $bln->copy()->startOfMonth();
            $end = $bln->copy()->endOfMonth();

            $chartLabels[] = $bln->translatedFormat('M Y');

            $revSewa = $laporanService->pendapatanSewaBersih($start, $end);

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

        // 6. Analitik tambahan: perbandingan bulan lalu, rasio denda terhadap pendapatan, okupansi per alat
        $awalBulanLalu = now()->subMonthNoOverflow()->startOfMonth();
        $akhirBulanLalu = now()->subMonthNoOverflow()->endOfMonth();
        $sewaBulanLalu = $laporanService->pendapatanSewaBersih($awalBulanLalu, $akhirBulanLalu);
        $dendaBulanLalu = (float) Denda::where('status_bayar', 'lunas')->whereBetween('dilunasi_pada', [$awalBulanLalu, $akhirBulanLalu])->sum('jumlah');
        $pendapatanBulanLalu = $sewaBulanLalu + $dendaBulanLalu;

        $perbandingan = [
            'bulan_lalu' => $pendapatanBulanLalu,
            // null bila bulan lalu nol agar tidak menampilkan persen tak terhingga
            'persen' => $pendapatanBulanLalu > 0 ? round(($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu * 100, 1) : null,
            'nama_bulan_lalu' => $awalBulanLalu->translatedFormat('F Y'),
        ];

        $rasioDenda = [
            'denda' => (float) $dendaBulanIni,
            'persen' => $pendapatanBulanIni > 0 ? round($dendaBulanIni / $pendapatanBulanIni * 100, 1) : null,
        ];

        $okupansi = $laporanService->okupansiPerAlat($startOfMonth, $endOfMonth, 8);

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
            'perbandingan',
            'rasioDenda',
            'okupansi',
            'transaksiTerbaru'
        ));
    }
}
