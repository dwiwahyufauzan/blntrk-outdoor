<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statistik = [
            'pesanan_menunggu' => Pesanan::where('status', StatusPesanan::Menunggu)->count(),
            'perlu_diambil_hari_ini' => Pesanan::where('status', StatusPesanan::Dibayar)->whereDate('tanggal_mulai', today())->count(),
            'pesanan_disewa' => Pesanan::where('status', StatusPesanan::Disewa)->count(),
            'pesanan_terlambat' => Pesanan::where('status', StatusPesanan::Terlambat)->count(),
            'denda_belum_lunas' => (float) Denda::where('status_bayar', 'belum_lunas')->sum('jumlah'),
            'total_alat' => Alat::count(),
            'total_pelanggan' => Pelanggan::count(),
        ];

        // Daftar Pesanan "Perlu Tindakan": Terlambat dan Jadwal Ambil Hari Ini
        $pesananTerlambat = Pesanan::with(['pelanggan', 'detail.alat'])
            ->where('status', StatusPesanan::Terlambat)
            ->latest()
            ->take(5)
            ->get();

        $pesananAmbilHariIni = Pesanan::with(['pelanggan', 'detail.alat'])
            ->where('status', StatusPesanan::Dibayar)
            ->whereDate('tanggal_mulai', today())
            ->latest()
            ->take(5)
            ->get();

        // Tren Penyewaan 7 Hari Terakhir untuk Chart.js
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = today()->subDays($i);
            $chartLabels[] = $tgl->translatedFormat('d M');
            $chartData[] = Pesanan::where('status', '!=', StatusPesanan::Dibatalkan->value)
                ->whereDate('tanggal_mulai', $tgl)
                ->count();
        }

        $pesananTerbaru = Pesanan::with(['pelanggan', 'pembuat'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'statistik',
            'pesananTerlambat',
            'pesananAmbilHariIni',
            'chartLabels',
            'chartData',
            'pesananTerbaru'
        ));
    }
}
