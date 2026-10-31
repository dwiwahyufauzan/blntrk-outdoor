<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\PaketBundling;
use App\Services\KeranjangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja sewa perlengkapan outdoor.
     */
    public function index(Request $request, KeranjangService $keranjang): View
    {
        if ($request->filled('mulai') && $request->filled('selesai')) {
            $keranjang->setJadwal($request->mulai, $request->selesai);
        }

        $items = $keranjang->ambil();
        $jadwal = $keranjang->getJadwal();
        $totalUnit = $keranjang->totalUnit();
        $totalPerHari = $keranjang->totalHargaPerHari();
        $subtotalKotor = $keranjang->subtotalKotor();
        $diskonPaket = $keranjang->diskonPaket();
        $grandTotal = max(0.0, $subtotalKotor - $diskonPaket['diskon']);

        // Rekomendasi Paket Bundling Hemat
        $paketBundling = PaketBundling::aktif()
            ->urut()
            ->with(['items.alat'])
            ->get();

        return view('keranjang.index', compact(
            'items',
            'jadwal',
            'totalUnit',
            'totalPerHari',
            'grandTotal',
            'subtotalKotor',
            'diskonPaket',
            'paketBundling'
        ));
    }

    /**
     * Menambahkan alat eceran ke keranjang belanja.
     */
    public function tambah(Request $request, KeranjangService $keranjang): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'alat_id' => ['required', 'integer', 'exists:alat,id'],
            'jumlah' => ['nullable', 'integer', 'min:1', 'max:50'],
            'mulai' => ['nullable', 'date'],
            'selesai' => ['nullable', 'date'],
        ]);

        $alat = Alat::findOrFail($validated['alat_id']);
        $jumlah = (int) ($validated['jumlah'] ?? 1);

        if (! empty($validated['mulai']) && ! empty($validated['selesai'])) {
            $keranjang->setJadwal($validated['mulai'], $validated['selesai']);
        }

        $berhasil = $keranjang->tambah($alat->id, $jumlah);

        if (! $berhasil) {
            $pesanError = $alat->stok_tersedia <= 0
                ? "Maaf, stok alat {$alat->nama} sedang habis dan tidak dapat dipesan."
                : "Jumlah yang diminta melebihi sisa stok yang tersedia ({$alat->stok_tersedia} unit).";

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $pesanError,
                ], 422);
            }

            return back()->with('error', $pesanError);
        }

        $pesan = "{$alat->nama} ({$jumlah} unit) berhasil ditambahkan ke keranjang belanja.";

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesan,
                'cartCount' => $keranjang->totalUnit(),
            ]);
        }

        if ($request->input('aksi') === 'langsung_keranjang') {
            return redirect()->route('keranjang.index')->with('status', $pesan);
        }

        return back()->with('status', $pesan);
    }

    /**
     * Menambahkan seluruh isi Paket Bundling ke dalam keranjang belanja sekaligus.
     */
    public function tambahPaket(PaketBundling $paket, KeranjangService $keranjang): RedirectResponse
    {
        $hasil = $keranjang->tambahPaket($paket);

        if ($hasil['ditambahkan'] === 0) {
            return back()->with('error', "Paket {$paket->nama} tidak dapat ditambahkan karena stok perlengkapannya sedang tidak mencukupi.");
        }

        $redirect = redirect()->route('keranjang.index')->with(
            'status',
            "Perlengkapan {$paket->nama} ({$hasil['ditambahkan']} jenis alat) berhasil dimasukkan ke keranjang belanja!"
        );

        if (! empty($hasil['dilewati'])) {
            $redirect->with('error', 'Paket belum lengkap, item berikut dilewati karena stok tidak mencukupi: '.implode(', ', $hasil['dilewati']).'. Diskon paket tidak berlaku sampai semua item terpenuhi.');
        }

        return $redirect;
    }

    /**
     * Memperbarui kuantitas alat di keranjang.
     */
    public function update(Request $request, int $alatId, KeranjangService $keranjang): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        $keranjang->update($alatId, (int) $validated['jumlah']);

        return back()->with('status', 'Jumlah unit sewa berhasil diperbarui.');
    }

    /**
     * Menghapus alat tertentu dari keranjang.
     */
    public function hapus(int $alatId, KeranjangService $keranjang): RedirectResponse
    {
        $keranjang->hapus($alatId);

        return back()->with('status', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Mengosongkan keranjang belanja.
     */
    public function kosongkan(KeranjangService $keranjang): RedirectResponse
    {
        $keranjang->kosongkan();

        return redirect()->route('keranjang.index')->with('status', 'Keranjang belanja berhasil dikosongkan.');
    }

    /**
     * Memproses keranjang menuju form pembuatan pesanan resmi (checkout).
     */
    public function checkout(Request $request, KeranjangService $keranjang): RedirectResponse
    {
        $cart = $keranjang->getRaw();
        if (empty($cart)) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $itemsKeranjang = $keranjang->ambil();
        foreach ($itemsKeranjang as $it) {
            if (! $it->cukup || $it->stok_tersedia <= 0) {
                return redirect()->route('keranjang.index')->with('error', "Stok alat {$it->nama} sedang habis atau tidak mencukupi untuk disewa pada jadwal tersebut.");
            }
        }

        $jadwal = $keranjang->getJadwal();

        // Simpan snapshot keranjang ke session untuk dimuat otomatis oleh form pemesanan
        session()->put('checkout_from_cart', [
            'items' => $cart,
            'mulai' => $jadwal['mulai'],
            'selesai' => $jadwal['selesai'],
        ]);

        return redirect()->route('pesanan.create', [
            'from_cart' => 1,
            'mulai' => $jadwal['mulai'],
            'selesai' => $jadwal['selesai'],
        ]);
    }
}
