<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Services\PengembalianService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DendaController extends Controller
{
    /**
     * Menampilkan daftar denda sewa (keterlambatan & kerusakan).
     */
    public function index(Request $request): View
    {
        $statusBayar = $request->query('status_bayar');
        $jenis = $request->query('jenis');
        $q = $request->query('q');

        $dendaList = Denda::with(['pesanan.pelanggan', 'pengembalian', 'petugasLunas'])
            ->when($statusBayar, fn ($query, $sb) => $query->where('status_bayar', $sb))
            ->when($jenis, fn ($query, $j) => $query->where('jenis', $j))
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('keterangan', 'like', "%{$q}%")
                        ->orWhereHas('pesanan', fn ($p) => $p->where('no_resi', 'like', "%{$q}%")
                            ->orWhereHas('pelanggan', fn ($pel) => $pel->where('nama_lengkap', 'like', "%{$q}%")));
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $countSemua = Denda::count();
        $countBelumLunas = Denda::where('status_bayar', 'belum_lunas')->count();
        $countLunas = Denda::where('status_bayar', 'lunas')->count();

        $totalBelumLunas = (float) Denda::where('status_bayar', 'belum_lunas')->sum('jumlah');
        $totalLunas = (float) Denda::where('status_bayar', 'lunas')->sum('jumlah');

        return view('admin.denda.index', compact(
            'dendaList',
            'statusBayar',
            'jenis',
            'q',
            'countSemua',
            'countBelumLunas',
            'countLunas',
            'totalBelumLunas',
            'totalLunas'
        ));
    }

    /**
     * Menandai denda telah dilunasi di kasir toko.
     */
    public function lunas(Denda $denda, PengembalianService $service): RedirectResponse
    {
        $service->lunaskanDenda($denda, auth()->user());

        return back()->with('status', 'Denda sebesar Rp '.number_format($denda->jumlah, 0, ',', '.').' berhasil ditandai lunas.');
    }
}
