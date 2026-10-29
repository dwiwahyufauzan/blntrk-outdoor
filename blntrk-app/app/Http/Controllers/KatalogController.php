<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Services\KetersediaanService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function __construct(
        protected KetersediaanService $ketersediaanService
    ) {}

    /**
     * Menampilkan katalog publik alat outdoor dengan filter kategori, pencarian, dan rentang tanggal.
     */
    public function index(Request $request): View
    {
        $kategoriList = KategoriAlat::withCount(['alat' => fn ($q) => $q->where('is_aktif', true)])
            ->orderBy('nama')
            ->get();

        $query = Alat::query()
            ->where('is_aktif', true)
            ->with('kategori');

        // Filter berdasarkan kategori (slug atau id)
        if ($request->filled('kategori')) {
            $katVal = $request->input('kategori');
            $query->whereHas('kategori', function ($q) use ($katVal) {
                $q->where('slug', $katVal)->orWhere('id', $katVal);
            });
        }

        // Pencarian kata kunci nama/varian/deskripsi/kode
        if ($request->filled('q')) {
            $search = '%'.trim($request->input('q')).'%';
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', $search)
                    ->orWhere('varian', 'like', $search)
                    ->orWhere('deskripsi', 'like', $search)
                    ->orWhere('kode_alat', 'like', $search);
            });
        }

        // Urutan alat
        $sort = $request->input('sort', 'terbaru');
        match ($sort) {
            'termurah' => $query->orderBy('harga_per_hari', 'asc'),
            'termahal' => $query->orderBy('harga_per_hari', 'desc'),
            'nama_asc' => $query->orderBy('nama', 'asc'),
            default => $query->latest(),
        };

        $alatList = $query->paginate(12)->withQueryString();

        // Cek apakah ada filter tanggal dari form hero atau filter tanggal
        $tanggalMulai = null;
        $tanggalSelesai = null;
        $durasiHari = 0;

        if ($request->filled('mulai') && $request->filled('selesai')) {
            try {
                $tMulai = Carbon::parse($request->input('mulai'))->startOfDay();
                $tSelesai = Carbon::parse($request->input('selesai'))->startOfDay();

                if ($tSelesai->gt($tMulai)) {
                    $tanggalMulai = $tMulai;
                    $tanggalSelesai = $tSelesai;
                    $durasiHari = (int) $tMulai->diffInDays($tSelesai);

                    // Hitung ketersediaan per alat untuk rentang tanggal ini
                    foreach ($alatList as $alat) {
                        $alat->stok_tersedia_rentang = $this->ketersediaanService->tersedia($alat, $tMulai, $tSelesai);
                    }
                }
            } catch (\Exception $e) {
                // Abaikan parsing error
            }
        }

        return view('katalog.index', [
            'alatList' => $alatList,
            'kategoriList' => $kategoriList,
            'kategoriAktif' => $request->input('kategori'),
            'keyword' => $request->input('q'),
            'sortAktif' => $sort,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'durasiHari' => $durasiHari,
        ]);
    }

    /**
     * Menampilkan detail alat outdoor publik beserta form cek ketersediaan.
     */
    public function show(Request $request, Alat $alat): View
    {
        abort_unless($alat->is_aktif, 404);

        $alat->load('kategori');

        // Parameter tanggal awal jika datang dari halaman katalog atau beranda
        $tMulaiDef = $request->input('mulai', date('Y-m-d'));
        $tSelesaiDef = $request->input('selesai', date('Y-m-d', strtotime('+1 day')));

        // Alat terkait di kategori yang sama
        $alatTerkait = Alat::where('is_aktif', true)
            ->where('kategori_id', $alat->kategori_id)
            ->where('id', '!=', $alat->id)
            ->take(4)
            ->get();

        return view('katalog.show', [
            'alat' => $alat,
            'alatTerkait' => $alatTerkait,
            'tMulaiDef' => $tMulaiDef,
            'tSelesaiDef' => $tSelesaiDef,
        ]);
    }

    /**
     * Endpoint API JSON untuk pengecekan ketersediaan stok alat per rentang tanggal.
     * Mengembalikan jumlah unit tersedia dan estimasi biaya sewa.
     */
    public function ketersediaan(Request $request, Alat $alat): JsonResponse
    {
        abort_unless($alat->is_aktif, 404);

        $validated = $request->validate([
            'mulai' => ['required', 'date', 'after_or_equal:today'],
            'selesai' => ['required', 'date', 'after:mulai'],
        ], [
            'mulai.required' => 'Tanggal mulai sewa wajib dipilih.',
            'mulai.after_or_equal' => 'Tanggal mulai sewa tidak boleh sebelum hari ini.',
            'selesai.required' => 'Tanggal selesai sewa wajib dipilih.',
            'selesai.after' => 'Tanggal selesai sewa harus setelah tanggal mulai sewa.',
        ]);

        $mulai = Carbon::parse($validated['mulai'])->startOfDay();
        $selesai = Carbon::parse($validated['selesai'])->startOfDay();

        $durasiHari = (int) $mulai->diffInDays($selesai);

        if ($durasiHari > 14) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Maksimal durasi penyewaan adalah 14 hari.',
                'tersedia' => 0,
            ], 422);
        }

        $tersedia = $this->ketersediaanService->tersedia($alat, $mulai, $selesai);
        $hargaPerHari = (float) $alat->harga_per_hari;
        $estimasiPerUnit = $hargaPerHari * $durasiHari;

        return response()->json([
            'sukses' => true,
            'alat_id' => $alat->id,
            'alat_nama' => $alat->nama,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'durasi_hari' => $durasiHari,
            'stok_total' => (int) $alat->stok_total,
            'stok_perawatan' => (int) $alat->stok_perawatan,
            'tersedia' => $tersedia,
            'bisa_disewa' => $tersedia > 0,
            'harga_per_hari' => $hargaPerHari,
            'estimasi_per_unit' => $estimasiPerUnit,
            'pesan' => $tersedia > 0
                ? "Tersedia {$tersedia} unit untuk periode tanggal tersebut."
                : 'Maaf, seluruh stok unit sudah terpesan pada rentang tanggal tersebut.',
        ]);
    }
}
