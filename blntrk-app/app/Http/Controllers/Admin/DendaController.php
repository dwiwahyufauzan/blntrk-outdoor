<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Pesanan;
use App\Services\DendaService;
use App\Services\PengembalianService;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DendaController extends Controller
{
    /**
     * Menampilkan daftar denda sewa (keterlambatan, kehilangan, & kerusakan).
     */
    public function index(Request $request, DendaService $dendaService, PesananService $pesananService): View
    {
        // 1. Sinkronisasi status pesanan realtime
        $pesananService->sinkronStatus();

        // 2. Ambil seluruh pesanan yang saat ini sedang terlambat (denda berjalan aktif)
        $pesananTerlambatList = Pesanan::with(['pelanggan', 'detail.alat'])
            ->where('status', StatusPesanan::Terlambat)
            ->latest('jatuh_tempo')
            ->get()
            ->map(function ($p) use ($dendaService) {
                $p->info_telat = $dendaService->hitungKeterlambatan($p, now());

                $noHp = preg_replace('/\D/', '', $p->pelanggan->no_hp ?? '');
                if (str_starts_with($noHp, '0')) {
                    $noHp = '62'.substr($noHp, 1);
                }
                $p->no_wa = $noHp;
                $p->pesan_wa = urlencode("Halo Kak {$p->pelanggan->nama_lengkap}, kami dari BLNTRK Outdoor menginformasikan bahwa sewa perlengkapan No Resi {$p->no_resi} telah melewati batas waktu pengembalian (Terlambat {$p->info_telat['hari']} Hari). Estimasi denda keterlambatan saat ini adalah Rp ".number_format($p->info_telat['jumlah'], 0, ',', '.').". Mohon segera kembalikan unit ke toko. Terima kasih!");

                return $p;
            });

        $totalEstimasiDendaBerjalan = (float) $pesananTerlambatList->sum(fn ($p) => $p->info_telat['jumlah']);

        // 3. Query denda tercatat di tabel denda
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

        // Total akumulasi denda tertunggak = denda tercatat belum lunas + estimasi denda berjalan penyewa terlambat
        $grandTotalTertunggak = $totalBelumLunas + $totalEstimasiDendaBerjalan;

        return view('admin.denda.index', compact(
            'pesananTerlambatList',
            'totalEstimasiDendaBerjalan',
            'grandTotalTertunggak',
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
