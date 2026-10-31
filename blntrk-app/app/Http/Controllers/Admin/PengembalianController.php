<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\PengembalianRequest;
use App\Models\KategoriDenda;
use App\Models\Pesanan;
use App\Services\DendaService;
use App\Services\PengembalianService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    /**
     * Menampilkan form proses pengembalian alat (A13).
     */
    public function create(Pesanan $pesanan, DendaService $dendaService): View|RedirectResponse
    {
        if (! in_array($pesanan->status, [StatusPesanan::Disewa, StatusPesanan::Terlambat], true)) {
            return redirect()->route('admin.pesanan.show', $pesanan->id)
                ->withErrors(['status' => "Pesanan dengan status {$pesanan->status->label()} tidak dapat diproses pengembaliannya."]);
        }

        $pesanan->load(['detail.alat.kategori', 'pelanggan', 'jaminan']);

        $waktuSekarang = now();
        $telat = $dendaService->hitungKeterlambatan($pesanan, $waktuSekarang);

        $kategoriDendaList = KategoriDenda::aktif()->get();

        return view('admin.pengembalian.create', compact('pesanan', 'telat', 'waktuSekarang', 'kategoriDendaList'));
    }

    /**
     * Menyimpan data pengembalian alat, denda, dan kondisi alat.
     */
    public function store(PengembalianRequest $request, Pesanan $pesanan, PengembalianService $service): RedirectResponse
    {
        $kembali = $service->proses($pesanan, auth()->user(), $request->validated());

        $pesan = "Pengembalian pesanan {$pesanan->no_resi} berhasil diproses.";
        if ($kembali->denda()->exists()) {
            $totalDenda = $kembali->denda()->sum('jumlah');
            $pesan .= ' Tercatat total denda sebesar Rp '.number_format($totalDenda, 0, ',', '.').' yang belum dilunasi.';
        }

        return redirect()->route('admin.pesanan.show', $pesanan->id)->with('status', $pesan);
    }

    /**
     * Endpoint API untuk pratinjau kalkulasi denda keterlambatan secara realtime.
     */
    public function hitungDenda(Request $request, Pesanan $pesanan, DendaService $dendaService): JsonResponse
    {
        $pesanan->load('detail');
        $waktu = $request->query('waktu') ? Carbon::parse($request->query('waktu')) : now();

        $hasil = $dendaService->hitungKeterlambatan($pesanan, $waktu);

        return response()->json([
            'hari' => $hasil['hari'],
            'jumlah' => $hasil['jumlah'],
            'jumlah_format' => 'Rp '.number_format($hasil['jumlah'], 0, ',', '.'),
            'tarif_harian' => $hasil['tarif_harian'],
            'is_telat' => $hasil['jumlah'] > 0,
        ]);
    }
}
