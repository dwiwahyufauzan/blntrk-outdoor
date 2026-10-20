<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pengaturan;
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

        $toko = [
            'nama' => Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor'),
            'alamat' => Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang'),
            'wa' => Pengaturan::ambil('no_wa_toko', '6281234567890'),
        ];

        return view('beranda', compact('kategoriList', 'alatUnggulan', 'toko'));
    }
}
