<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\KategoriDenda;
use App\Models\PaketBundling;
use App\Support\PengaturanToko;
use Illuminate\View\View;

class BerandaController extends Controller
{
    /**
     * Tampilkan halaman utama / beranda BLNTRK Outdoor.
     */
    public function index(): View
    {
        $kategoriList = KategoriAlat::withCount('alat')->get();

        $alatUnggulan = Alat::aktif()
            ->with('kategori')
            ->orderBy('id', 'asc')
            ->take(8)
            ->get();
        Alat::muatStokTersedia($alatUnggulan);

        $paketBundlingList = PaketBundling::aktif()
            ->urut()
            ->with(['items.alat'])
            ->get();

        $kategoriDendaList = KategoriDenda::aktif()->get();

        $toko = [
            'nama' => PengaturanToko::namaToko(),
            'alamat' => PengaturanToko::alamatToko(),
            'wa' => PengaturanToko::noWa(),
        ];

        return view('beranda', compact('kategoriList', 'alatUnggulan', 'paketBundlingList', 'kategoriDendaList', 'toko'));
    }
}
