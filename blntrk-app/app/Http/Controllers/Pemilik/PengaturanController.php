<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\LogService;
use App\Support\PengaturanToko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    /**
     * Menampilkan formulir konfigurasi operasional dan aturan denda toko.
     */
    public function index(): View
    {
        $pengaturan = PengaturanToko::semua();

        return view('pemilik.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Menyimpan pembaruan konfigurasi toko dan memperbarui cache.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_toko' => ['required', 'string', 'max:100'],
            'alamat_toko' => ['required', 'string', 'max:255'],
            'no_wa_toko' => ['required', 'string', 'max:20'],
            'jam_kembali' => ['required', 'string', 'max:5'],
            'toleransi_menit' => ['required', 'integer', 'min:0', 'max:1440'],
            'persen_denda_harian' => ['required', 'numeric', 'min:0', 'max:500'],
            'maks_durasi_hari' => ['required', 'integer', 'min:1', 'max:90'],
            'ambang_stok_kritis' => ['required', 'integer', 'min:0', 'max:50'],
            'noshow_batal_otomatis' => ['sometimes', 'required', 'in:0,1'],
            'noshow_toleransi_hari' => ['sometimes', 'required', 'integer', 'min:0', 'max:30'],
            'batas_bayar_jam' => ['sometimes', 'required', 'integer', 'min:0', 'max:720'],
            'maks_pesanan_menunggu' => ['sometimes', 'required', 'integer', 'min:0', 'max:50'],
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi.',
            'alamat_toko.required' => 'Alamat toko wajib diisi.',
            'no_wa_toko.required' => 'Nomor WhatsApp wajib diisi.',
            'jam_kembali.required' => 'Jam batas pengembalian wajib diisi.',
            'toleransi_menit.required' => 'Toleransi keterlambatan wajib diisi.',
            'persen_denda_harian.required' => 'Persentase denda harian wajib diisi.',
            'maks_durasi_hari.required' => 'Maksimum durasi peminjaman wajib diisi.',
            'ambang_stok_kritis.required' => 'Ambang batas stok kritis wajib diisi.',
            'noshow_batal_otomatis.required' => 'Pilihan pembatalan otomatis no-show wajib dipilih.',
            'noshow_toleransi_hari.required' => 'Toleransi hari no-show wajib diisi.',
            'batas_bayar_jam.required' => 'Batas waktu pembayaran wajib diisi (0 = hanya dibatasi batas pengambilan).',
            'maks_pesanan_menunggu.required' => 'Batas pesanan menunggu per pelanggan wajib diisi (0 = tanpa batas).',
        ]);

        $bawaan = PengaturanToko::bawaan();

        foreach ($validated as $kunci => $nilai) {
            Pengaturan::setel($kunci, (string) $nilai, $bawaan[$kunci]['keterangan'] ?? null);
        }

        LogService::catat(
            auth()->user(),
            'pengaturan.ubah',
            'pengaturan',
            null,
            'Memperbarui parameter konfigurasi operasional toko dan formula denda keterlambatan.'
        );

        return redirect()->route('pemilik.pengaturan.index')
            ->with('status', 'Konfigurasi pengaturan toko dan aturan rental berhasil diperbarui.');
    }
}
