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
            'pesanan_dibayar' => Pesanan::where('status', StatusPesanan::Dibayar)->count(),
            'pesanan_disewa' => Pesanan::where('status', StatusPesanan::Disewa)->count(),
            'pesanan_terlambat' => Pesanan::where('status', StatusPesanan::Terlambat)->count(),
            'denda_belum_lunas' => Denda::where('status_bayar', 'belum_lunas')->sum('jumlah'),
            'total_alat' => Alat::count(),
            'total_pelanggan' => Pelanggan::count(),
        ];

        $pesananTerbaru = Pesanan::with(['pelanggan', 'pembuat'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('statistik', 'pesananTerbaru'));
    }
}
