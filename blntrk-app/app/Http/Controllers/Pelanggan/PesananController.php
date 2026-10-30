<?php

namespace App\Http\Controllers\Pelanggan;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\BatalRequest;
use App\Http\Requests\PesananRequest;
use App\Models\Alat;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Services\DendaService;
use App\Services\PesananService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan pelanggan (dipisahkan antara pesanan aktif dan riwayat selesai).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $pelanggan = $user->pelanggan;

        if (! $pelanggan) {
            $pesananList = Pesanan::whereRaw('1 = 0')->paginate(10);

            return view('pesanan.index', [
                'pesananList' => $pesananList,
                'tab' => 'aktif',
                'jumlahAktif' => 0,
                'jumlahRiwayat' => 0,
                'statusCounts' => [],
            ]);
        }

        // Kategori status aktif vs riwayat
        $statusAktif = ['menunggu', 'dibayar', 'disewa', 'terlambat'];
        $statusRiwayat = ['dikembalikan', 'dibatalkan'];

        $jumlahAktif = Pesanan::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', $statusAktif)
            ->count();

        $jumlahRiwayat = Pesanan::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', $statusRiwayat)
            ->count();

        // Tentukan tab aktif (default 'aktif' jika ada pesanan aktif atau belum memilih)
        $tab = $request->query('tab');
        if (! in_array($tab, ['aktif', 'riwayat'], true)) {
            $tab = ($jumlahAktif === 0 && $jumlahRiwayat > 0) ? 'riwayat' : 'aktif';
        }

        $query = Pesanan::with(['detail.alat'])
            ->where('pelanggan_id', $pelanggan->id);

        if ($tab === 'aktif') {
            $query->whereIn('status', $statusAktif);
            if ($request->filled('status') && in_array($request->status, $statusAktif, true)) {
                $query->where('status', $request->status);
            }
        } else {
            $query->whereIn('status', $statusRiwayat);
            if ($request->filled('status') && in_array($request->status, $statusRiwayat, true)) {
                $query->where('status', $request->status);
            }
        }

        $pesananList = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $statusCounts = Pesanan::where('pelanggan_id', $pelanggan->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('pesanan.index', compact(
            'pesananList',
            'tab',
            'jumlahAktif',
            'jumlahRiwayat',
            'statusCounts'
        ));
    }

    /**
     * Menampilkan form pembuatan pesanan baru.
     */
    public function create(Request $request): View
    {
        $user = auth()->user();
        $pelanggan = $user->pelanggan;

        $alatList = Alat::with('kategori')
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();
        Alat::muatStokTersedia($alatList);

        $checkoutFromCart = session()->get('checkout_from_cart');
        $initialItems = [];
        $paketNama = null;

        if ($checkoutFromCart && ! empty($checkoutFromCart['items'])) {
            foreach ($checkoutFromCart['items'] as $alatId => $qty) {
                $initialItems[] = [
                    'alat_id' => (int) $alatId,
                    'jumlah' => (int) $qty,
                ];
            }
            $initialMulai = $checkoutFromCart['mulai'] ?? $request->query('mulai', today()->toDateString());
            $initialSelesai = $checkoutFromCart['selesai'] ?? $request->query('selesai', today()->addDay()->toDateString());
            $paketNama = $checkoutFromCart['paket_nama'] ?? null;
        } else {
            $initialAlatId = (int) $request->query('alat_id', 0);
            $initialJumlah = max(1, (int) $request->query('jumlah', 1));
            $initialMulai = $request->query('mulai', today()->toDateString());
            $initialSelesai = $request->query('selesai', today()->addDay()->toDateString());

            if ($initialAlatId > 0) {
                $initialItems[] = [
                    'alat_id' => $initialAlatId,
                    'jumlah' => $initialJumlah,
                ];
            }
        }

        $initialAlatId = ! empty($initialItems) ? $initialItems[0]['alat_id'] : (int) $request->query('alat_id', 0);
        $initialJumlah = ! empty($initialItems) ? $initialItems[0]['jumlah'] : max(1, (int) $request->query('jumlah', 1));

        return view('pesanan.create', compact(
            'pelanggan',
            'alatList',
            'initialAlatId',
            'initialJumlah',
            'initialItems',
            'initialMulai',
            'initialSelesai',
            'paketNama'
        ));
    }

    /**
     * Memproses penyimpanan pesanan baru.
     */
    public function store(PesananRequest $request, PesananService $service, \App\Services\KeranjangService $keranjangService): RedirectResponse
    {
        $user = auth()->user();
        $pelanggan = $user->pelanggan;

        if (! $pelanggan) {
            $pelanggan = Pelanggan::create([
                'user_id' => $user->id,
                'nama_lengkap' => $user->nama,
                'no_hp' => $request->validated('no_hp'),
                'alamat' => $request->validated('alamat'),
            ]);
        }

        $pesanan = $service->buat($pelanggan, $user, Arr::except($request->validated(), ['no_hp', 'alamat']));

        // Bersihkan snapshot checkout dan keranjang belanja
        session()->forget('checkout_from_cart');
        $keranjangService->kosongkan();

        return redirect()->route('pesanan.show', $pesanan->no_resi)
            ->with('status', 'Pesanan berhasil dibuat! Silakan simpan nomor resi Anda.');
    }

    /**
     * Menampilkan halaman detail pesanan pelanggan.
     */
    public function show(string $resi, PesananService $pesananService, DendaService $dendaService): View
    {
        $pesananService->sinkronStatus();

        $pesanan = Pesanan::with([
            'pelanggan',
            'pembuat',
            'pemroses',
            'detail.alat.kategori',
            'pembayaran.pencatat',
            'jaminan.penerima',
            'pengembalian.penerima',
            'pengembalian.detail.pesananDetail.alat',
            'denda.kategoriDenda',
        ])
            ->where('no_resi', $resi)
            ->tampilkanUntuk(auth()->user())
            ->firstOrFail();

        $this->authorize('view', $pesanan);

        $infoKeterlambatan = null;
        if ($pesanan->status === StatusPesanan::Terlambat || ($pesanan->status === StatusPesanan::Disewa && now()->gt($pesanan->jatuh_tempo))) {
            $infoKeterlambatan = $dendaService->hitungKeterlambatan($pesanan, now());
        }

        return view('pesanan.show', compact('pesanan', 'infoKeterlambatan'));
    }

    /**
     * Menampilkan lembar bukti resi pesanan resmi (siap cetak / simpan).
     */
    public function resi(string $resi): View
    {
        $pesanan = Pesanan::with([
            'pelanggan',
            'pembuat',
            'pemroses',
            'detail.alat.kategori',
            'pembayaran.pencatat',
            'jaminan.penerima',
            'pengembalian.penerima',
            'pengembalian.detail.pesananDetail.alat',
            'denda.kategoriDenda',
        ])
            ->where('no_resi', $resi)
            ->tampilkanUntuk(auth()->user())
            ->firstOrFail();

        $this->authorize('view', $pesanan);

        $toko = Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor');
        $alamat = Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang');
        $noWa = Pengaturan::ambil('no_wa_toko', '6281234567890');
        $petugas = $pesanan->pemroses?->nama ?? (auth()->user()?->nama ?? 'Kasir BLNTRK Outdoor');
        $tanggalCetak = now()->translatedFormat('d F Y H:i');

        return view('pesanan.resi', compact('pesanan', 'toko', 'alamat', 'noWa', 'petugas', 'tanggalCetak'));
    }

    /**
     * Mengunduh file PDF lembar bukti resi pesanan resmi.
     */
    public function cetakPdf(string $resi): Response
    {
        $pesanan = Pesanan::with([
            'pelanggan',
            'pembuat',
            'pemroses',
            'detail.alat.kategori',
            'pembayaran.pencatat',
            'jaminan.penerima',
            'pengembalian.penerima',
            'pengembalian.detail.pesananDetail.alat',
            'denda.kategoriDenda',
        ])
            ->where('no_resi', $resi)
            ->tampilkanUntuk(auth()->user())
            ->firstOrFail();

        $this->authorize('view', $pesanan);

        $toko = Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor');
        $alamat = Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang');
        $petugas = $pesanan->pemroses?->nama ?? (auth()->user()?->nama ?? 'Kasir BLNTRK Outdoor');
        $tanggalCetak = now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('pdf.resi', compact('pesanan', 'toko', 'alamat', 'petugas', 'tanggalCetak'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("resi-{$pesanan->no_resi}.pdf");
    }

    /**
     * Membatalkan pesanan oleh pelanggan.
     */
    public function batal(BatalRequest $request, string $resi, PesananService $service): RedirectResponse
    {
        $pesanan = Pesanan::where('no_resi', $resi)->tampilkanUntuk(auth()->user())->firstOrFail();

        $this->authorize('batal', $pesanan);

        $service->batal($pesanan, auth()->user(), $request->validated()['alasan_batal']);

        return redirect()->route('pesanan.show', $pesanan->no_resi)
            ->with('status', 'Pesanan berhasil dibatalkan.');
    }
}
