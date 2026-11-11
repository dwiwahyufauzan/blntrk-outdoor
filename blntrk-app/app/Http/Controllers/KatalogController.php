<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Services\KetersediaanService;
use App\Support\PengaturanToko;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /** Jumlah alat per halaman katalog. */
    private const PER_HALAMAN = 12;

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
            // Kolom id bertipe integer: membandingkannya dengan teks non-numerik ditolak PostgreSQL
            $query->whereHas('kategori', function ($q) use ($katVal) {
                $q->where('slug', $katVal)->when(ctype_digit((string) $katVal), fn ($q) => $q->orWhere('id', (int) $katVal));
            });
        }

        // Pencarian kata kunci nama/varian/deskripsi/kode
        if ($request->filled('q')) {
            $search = like_pattern($request->input('q'));
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

        $alatList = $query->get();
        Alat::muatStokTersedia($alatList);

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

                    // Hitung ketersediaan seluruh alat untuk rentang tanggal ini dengan satu query pemakaian
                    $tersediaRentang = $this->ketersediaanService->tersediaBanyak($alatList, $tMulai, $tSelesai);
                    foreach ($alatList as $alat) {
                        $alat->stok_tersedia_rentang = $tersediaRentang[$alat->id] ?? 0;
                        $alat->stok_tersedia = $alat->stok_tersedia_rentang;
                    }
                }
            } catch (\Exception $e) {
                // Abaikan parsing error
            }
        }

        // Produk yang stoknya tersedia di atas, yang out-of-stock di bawah; lalu dipotong per halaman.
        // Stok baru diketahui setelah dihitung (hari ini atau rentang tanggal), sehingga paginasi dilakukan di memori.
        $tersedia = $alatList->filter(fn ($alat) => $alat->stok_tersedia > 0)->values();
        $habis = $alatList->filter(fn ($alat) => $alat->stok_tersedia <= 0)->values();

        $urut = $tersedia->concat($habis)->values();
        $halaman = new LengthAwarePaginator(
            $urut->forPage(Paginator::resolveCurrentPage(), self::PER_HALAMAN)->values(),
            $urut->count(),
            self::PER_HALAMAN,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('katalog.index', [
            'alatList' => $alatList,
            'halaman' => $halaman,
            'alatTersedia' => $halaman->getCollection()->filter(fn ($alat) => $alat->stok_tersedia > 0)->values(),
            'alatHabis' => $halaman->getCollection()->filter(fn ($alat) => $alat->stok_tersedia <= 0)->values(),
            'jumlahTersedia' => $tersedia->count(),
            'jumlahHabis' => $habis->count(),
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
        Alat::muatStokTersedia($alatTerkait);

        // Kalender ketersediaan 6 minggu ke depan (heatmap di halaman detail)
        $kalender = $this->ketersediaanService->kalender($alat, today(), 42);

        return view('katalog.show', [
            'alat' => $alat,
            'kalender' => $kalender,
            'alatTerkait' => $alatTerkait,
            'tMulaiDef' => $tMulaiDef,
            'tSelesaiDef' => $tSelesaiDef,
        ]);
    }

    /**
     * Endpoint API JSON ketersediaan seluruh alat aktif pada satu rentang tanggal (dipakai form pemesanan
     * agar stok yang tampil mengikuti tanggal yang dipilih, bukan stok hari ini).
     */
    public function ketersediaanSemua(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mulai' => ['required', 'date', 'after_or_equal:today'],
            'selesai' => ['required', 'date', 'after:mulai'],
        ]);

        $mulai = Carbon::parse($validated['mulai'])->startOfDay();
        $selesai = Carbon::parse($validated['selesai'])->startOfDay();

        $alatList = Alat::where('is_aktif', true)->get();

        return response()->json([
            'sukses' => true,
            'tersedia' => $this->ketersediaanService->tersediaBanyak($alatList, $mulai, $selesai),
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

        $maksDurasi = PengaturanToko::maksDurasiHari();

        if ($durasiHari > $maksDurasi) {
            return response()->json([
                'sukses' => false,
                'pesan' => "Maksimal durasi penyewaan adalah {$maksDurasi} hari.",
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
