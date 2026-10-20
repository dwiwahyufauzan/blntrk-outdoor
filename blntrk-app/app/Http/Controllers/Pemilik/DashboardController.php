<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $ringkasan = [
            'pendapatan_sewa' => Pembayaran::sum('jumlah'),
            'pendapatan_denda' => Denda::where('status_bayar', 'lunas')->sum('jumlah'),
            'total_transaksi' => Pesanan::count(),
            'total_alat' => Alat::count(),
            'alat_dalam_perawatan' => Alat::where('stok_perawatan', '>', 0)->sum('stok_perawatan'),
        ];

        $transaksiTerbaru = Pesanan::with(['pelanggan', 'pemroses'])
            ->latest()
            ->take(6)
            ->get();

        return view('pemilik.dashboard', compact('ringkasan', 'transaksiTerbaru'));
    }
}
