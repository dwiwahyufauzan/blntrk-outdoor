<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Http\Requests\BatalRequest;
use App\Http\Requests\PesananRequest;
use App\Models\Alat;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Services\PesananService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar riwayat pesanan milik pelanggan.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $pelanggan = $user->pelanggan;

        $pesananList = $pelanggan
            ? Pesanan::with(['detail.alat'])
                ->where('pelanggan_id', $pelanggan->id)
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
                ->latest()
                ->paginate(10)
                ->withQueryString()
            : Pesanan::whereRaw('1 = 0')->paginate(10);

        return view('pesanan.index', compact('pesananList'));
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

        $initialAlatId = (int) $request->query('alat_id', 0);
        $initialJumlah = max(1, (int) $request->query('jumlah', 1));
        $initialMulai = $request->query('mulai', today()->toDateString());
        $initialSelesai = $request->query('selesai', today()->addDay()->toDateString());

        return view('pesanan.create', compact(
            'pelanggan',
            'alatList',
            'initialAlatId',
            'initialJumlah',
            'initialMulai',
            'initialSelesai'
        ));
    }

    /**
     * Memproses penyimpanan pesanan baru.
     */
    public function store(PesananRequest $request, PesananService $service): RedirectResponse
    {
        $user = auth()->user();
        $pelanggan = $user->pelanggan;

        if (! $pelanggan) {
            $pelanggan = Pelanggan::create([
                'user_id' => $user->id,
                'nama_lengkap' => $user->nama,
                'no_hp' => $request->input('no_hp', '-'),
                'alamat' => $request->input('alamat', '-'),
            ]);
        }

        $pesanan = $service->buat($pelanggan, $user, $request->validated());

        return redirect()->route('pesanan.show', $pesanan->no_resi)
            ->with('status', 'Pesanan berhasil dibuat! Silakan simpan nomor resi Anda.');
    }

    /**
     * Menampilkan detail atau lembar resi pesanan.
     */
    public function show(string $resi): View
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
            'denda',
        ])
            ->where('no_resi', $resi)
            ->firstOrFail();

        $this->authorize('view', $pesanan);

        return view('pesanan.show', compact('pesanan'));
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
            'denda',
        ])
            ->where('no_resi', $resi)
            ->firstOrFail();

        $this->authorize('view', $pesanan);

        $toko = Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor');
        $alamat = Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang');
        $petugas = $pesanan->pemroses?->nama ?? 'Sistem BLNTRK';
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
        $pesanan = Pesanan::where('no_resi', $resi)->firstOrFail();

        $this->authorize('batal', $pesanan);

        $service->batal($pesanan, auth()->user(), $request->validated()['alasan_batal']);

        return redirect()->route('pesanan.show', $pesanan->no_resi)
            ->with('status', 'Pesanan berhasil dibatalkan.');
    }
}
