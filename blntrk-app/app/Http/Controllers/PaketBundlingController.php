<?php

namespace App\Http\Controllers;

use App\Models\PaketBundling;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaketBundlingController extends Controller
{
    /**
     * Menampilkan daftar seluruh Paket Bundling Hemat (Paket 1, 2, 3).
     */
    public function index(Request $request): View
    {
        $paketList = PaketBundling::aktif()
            ->urut()
            ->with(['items.alat.kategori'])
            ->get();

        return view('paket-bundling.index', compact('paketList'));
    }

    /**
     * Menampilkan detail satu Paket Bundling.
     */
    public function show(string $slug): View
    {
        $paket = PaketBundling::where('slug', $slug)
            ->where('is_aktif', true)
            ->with(['items.alat.kategori'])
            ->firstOrFail();

        $paketLainnya = PaketBundling::where('id', '!=', $paket->id)
            ->where('is_aktif', true)
            ->urut()
            ->take(2)
            ->get();

        return view('paket-bundling.show', compact('paket', 'paketLainnya'));
    }

    /**
     * Memproses sewa langsung untuk satu paket bundling menuju form pesanan.
     */
    public function sewaLangsung(string $slug, Request $request): RedirectResponse
    {
        $paket = PaketBundling::where('slug', $slug)
            ->where('is_aktif', true)
            ->with('items')
            ->firstOrFail();

        $mulai = $request->input('mulai', today()->toDateString());
        $selesai = $request->input('selesai', today()->addDay()->toDateString());

        $items = [];
        foreach ($paket->items as $item) {
            $items[$item->alat_id] = $item->jumlah;
        }

        session()->put('checkout_from_cart', [
            'items' => $items,
            'mulai' => $mulai,
            'selesai' => $selesai,
            'paket_nama' => $paket->nama,
        ]);

        return redirect()->route('pesanan.create', [
            'from_cart' => 1,
            'mulai' => $mulai,
            'selesai' => $selesai,
        ])->with('status', "Paket {$paket->nama} telah dimuat ke formulir pemesanan.");
    }
}

