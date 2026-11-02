<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\RiwayatPerawatan;
use App\Services\KetersediaanService;
use App\Services\LogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerawatanController extends Controller
{
    public function __construct(protected KetersediaanService $ketersediaan) {}

    /**
     * Menampilkan daftar alat outdoor yang sedang dalam masa perawatan/servis (T12).
     */
    public function index(): View
    {
        // 1. Alat yang sedang berada dalam perawatan
        $alatPerawatan = Alat::with('kategori')
            ->where('stok_perawatan', '>', 0)
            ->orderByDesc('stok_perawatan')
            ->get();

        // 2. Seluruh alat aktif untuk opsi pindah ke perawatan manual
        $semuaAlat = Alat::with('kategori')
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        $totalUnitPerawatan = (int) $alatPerawatan->sum('stok_perawatan');

        // 3. Jejak keluar-masuk perawatan (siapa, kapan, berapa unit, mengapa)
        $riwayat = RiwayatPerawatan::with(['alat', 'user', 'pesanan'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.perawatan.index', compact('alatPerawatan', 'semuaAlat', 'totalUnitPerawatan', 'riwayat'));
    }

    /**
     * Konfirmasi unit alat yang telah selesai diperbaiki/dibersihkan.
     * Mengurangi stok_perawatan dan mengembalikannya ke kapasitas tersedia toko.
     */
    public function selesai(Request $request, Alat $alat): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:'.max(1, $alat->stok_perawatan)],
            'catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Jumlah unit selesai perbaikan wajib diisi.',
            'jumlah.min' => 'Jumlah unit minimal 1.',
            'jumlah.max' => "Jumlah unit tidak boleh melebihi unit yang sedang dirawat ({$alat->stok_perawatan} unit).",
        ]);

        $jumlah = (int) $validated['jumlah'];

        $alat->stok_perawatan = max(0, $alat->stok_perawatan - $jumlah);
        $alat->save();

        RiwayatPerawatan::catat($alat, RiwayatPerawatan::SELESAI, $jumlah, $validated['catatan'] ?? 'Selesai diperbaiki', auth()->user());

        $catatanInfo = ! empty($validated['catatan']) ? " (Catatan: {$validated['catatan']})" : '';

        LogService::catat(
            auth()->user(),
            'perawatan.selesai',
            'alat',
            $alat->id,
            "Selesai perbaikan {$jumlah} unit alat {$alat->nama}. Unit telah kembali siap disewa{$catatanInfo}."
        );

        return redirect()->route('admin.perawatan.index')
            ->with('status', "Berhasil! {$jumlah} unit {$alat->nama} telah selesai dirawat dan kembali siap disewa.");
    }

    /**
     * Memindahkan unit alat secara manual ke masa perawatan/servis.
     */
    public function tambah(Request $request, Alat $alat): RedirectResponse
    {
        // Unit yang masih dibutuhkan pesanan aktif tidak boleh dipindah ke perawatan
        $dibutuhkan = $this->ketersediaan->puncakTerpakai($alat->id);
        $maxTersedia = max(0, $alat->stok_total - $alat->stok_perawatan - $dibutuhkan);

        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:'.$maxTersedia],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Jumlah unit wajib diisi.',
            'jumlah.min' => 'Jumlah unit minimal 1.',
            'jumlah.max' => "Jumlah unit melebihi unit yang bebas dipindah ({$maxTersedia} unit); sisanya masih dibutuhkan pesanan aktif atau sudah dirawat.",
        ]);

        $jumlah = (int) $validated['jumlah'];

        $alat->stok_perawatan += $jumlah;
        $alat->save();

        RiwayatPerawatan::catat($alat, RiwayatPerawatan::MASUK, $jumlah, $validated['keterangan'] ?? 'Dipindahkan manual ke perawatan', auth()->user());

        $ket = ! empty($validated['keterangan']) ? " Alasan: {$validated['keterangan']}" : '';

        LogService::catat(
            auth()->user(),
            'perawatan.masuk',
            'alat',
            $alat->id,
            "Memindahkan {$jumlah} unit alat {$alat->nama} ke status perawatan.{$ket}"
        );

        return redirect()->route('admin.perawatan.index')
            ->with('status', "{$jumlah} unit {$alat->nama} berhasil dipindahkan ke status perawatan.");
    }
}
