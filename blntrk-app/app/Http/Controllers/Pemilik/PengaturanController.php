<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\LogService;
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
        $pengaturan = [
            'nama_toko' => Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor'),
            'alamat_toko' => Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang, Jawa Barat'),
            'no_wa_toko' => Pengaturan::ambil('no_wa_toko', '6281234567890'),
            'jam_kembali' => Pengaturan::ambil('jam_kembali', '12:00'),
            'toleransi_menit' => (int) Pengaturan::ambil('toleransi_menit', 60),
            'persen_denda_harian' => (float) Pengaturan::ambil('persen_denda_harian', 100),
            'maks_durasi_hari' => (int) Pengaturan::ambil('maks_durasi_hari', 14),
            'ambang_stok_kritis' => (int) Pengaturan::ambil('ambang_stok_kritis', 2),
        ];

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
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi.',
            'alamat_toko.required' => 'Alamat toko wajib diisi.',
            'no_wa_toko.required' => 'Nomor WhatsApp wajib diisi.',
            'jam_kembali.required' => 'Jam batas pengembalian wajib diisi.',
            'toleransi_menit.required' => 'Toleransi keterlambatan wajib diisi.',
            'persen_denda_harian.required' => 'Persentase denda harian wajib diisi.',
            'maks_durasi_hari.required' => 'Maksimum durasi peminjaman wajib diisi.',
            'ambang_stok_kritis.required' => 'Ambang batas stok kritis wajib diisi.',
        ]);

        $deskripsi = [
            'nama_toko' => 'Nama toko yang dicetak pada resi dan laporan resmi',
            'alamat_toko' => 'Alamat lokasi fisik operasional toko',
            'no_wa_toko' => 'Nomor WhatsApp kontak toko untuk konfirmasi pesanan',
            'jam_kembali' => 'Jam batas waktu pengembalian alat (format HH:mm WIB)',
            'toleransi_menit' => 'Waktu toleransi pengembalian sebelum denda dihitung (menit)',
            'persen_denda_harian' => 'Persentase tarif harian alat untuk denda per hari keterlambatan (%)',
            'maks_durasi_hari' => 'Batas maksimum durasi peminjaman yang diizinkan (hari)',
            'ambang_stok_kritis' => 'Batas minimum unit alat untuk peringatan stok kritis',
        ];

        foreach ($validated as $kunci => $nilai) {
            Pengaturan::setel($kunci, (string) $nilai, $deskripsi[$kunci] ?? null);
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
