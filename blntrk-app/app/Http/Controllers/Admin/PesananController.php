<?php

namespace App\Http\Controllers\Admin;

use App\Data\PesananBaru;
use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPesananRequest;
use App\Http\Requests\BatalRequest;
use App\Http\Requests\PembayaranRequest;
use App\Http\Requests\PengambilanRequest;
use App\Http\Requests\PengembalianRequest;
use App\Http\Requests\PerpanjanganRequest;
use App\Http\Requests\RefundRequest;
use App\Http\Requests\UbahJadwalRequest;
use App\Models\Alat;
use App\Models\KategoriDenda;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Services\BuktiTransferService;
use App\Services\DendaService;
use App\Services\PaketBundlingService;
use App\Services\PengembalianService;
use App\Services\PerubahanPesananService;
use App\Services\PesananService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan sewa di panel admin dengan tab status dan pencarian.
     */
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $status = $request->query('status');
        $dari = $request->query('dari');
        $sampai = $request->query('sampai');

        $pesananList = Pesanan::with(['pelanggan', 'pembuat', 'detail.alat'])
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_resi', 'like', like_pattern($q))
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama_lengkap', 'like', like_pattern($q))->orWhere('no_hp', 'like', like_pattern($q)));
                });
            })
            ->when($status, fn ($query, $s) => $query->where('status', $s))
            ->when($dari, fn ($query, $d) => $query->whereDate('tanggal_mulai', '>=', $d))
            ->when($sampai, fn ($query, $s) => $query->whereDate('tanggal_mulai', '<=', $s))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = StatusPesanan::cases();

        // Hitung jumlah pesanan per status untuk indikator badge pada tab
        $statusCounts = Pesanan::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalCount = Pesanan::count();

        return view('admin.pesanan.index', compact('pesananList', 'statuses', 'statusCounts', 'totalCount', 'status', 'q', 'dari', 'sampai'));
    }

    /**
     * Menampilkan form buat pesanan walk-in di toko.
     */
    public function create(PaketBundlingService $paketBundling): View
    {
        $pelangganList = Pelanggan::orderBy('nama_lengkap')->get();
        $alatList = Alat::with('kategori')
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();
        Alat::muatStokTersedia($alatList);

        $paketData = $paketBundling->dataUntukKlien();

        return view('admin.pesanan.create', compact('pelangganList', 'alatList', 'paketData'));
    }

    /**
     * Menyimpan pesanan walk-in baru.
     */
    public function store(AdminPesananRequest $request, PesananService $service): RedirectResponse
    {
        if ($request->input('tipe_pelanggan') === 'terdaftar') {
            $pelanggan = Pelanggan::findOrFail($request->input('pelanggan_id'));
        } else {
            $pelanggan = Pelanggan::create([
                'user_id' => null,
                'nama_lengkap' => $request->input('nama_lengkap'),
                'no_hp' => $request->input('no_hp'),
                'alamat' => $request->input('alamat'),
            ]);
        }

        $pesanan = $service->buat($pelanggan, auth()->user(), PesananBaru::dari($request->validated()));

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Pesanan walk-in berhasil dibuat dengan no resi {$pesanan->no_resi}.");
    }

    /**
     * Menampilkan detail pesanan di panel admin.
     */
    public function show(Pesanan $pesanan, DendaService $dendaService): View
    {
        $pesanan->load(Pesanan::RELASI_RINCIAN);

        // Format nomor WhatsApp pelanggan (konversi 08xx ke 628xx)
        $noHp = preg_replace('/\D/', '', $pesanan->pelanggan->no_hp ?? '');
        if (str_starts_with($noHp, '0')) {
            $noHp = '62'.substr($noHp, 1);
        }

        $namaPelanggan = $pesanan->pelanggan->nama_lengkap ?? 'Pelanggan';

        $infoKeterlambatan = null;
        if ($pesanan->status === StatusPesanan::Terlambat || ($pesanan->status === StatusPesanan::Disewa && now()->gt($pesanan->jatuh_tempo))) {
            $infoKeterlambatan = $dendaService->hitungKeterlambatan($pesanan, now());
        }

        if ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0) {
            $pesanWa = urlencode(WhatsAppService::pesanPeringatanTerlambat($pesanan, $infoKeterlambatan['hari'], $infoKeterlambatan['jumlah']));
        } else {
            $pesanWa = urlencode("Halo Kak {$namaPelanggan}, kami dari BLNTRK Outdoor ingin menginformasikan perihal pesanan sewa Anda dengan no resi {$pesanan->no_resi}. Status saat ini: {$pesanan->status->label()}.");
        }

        $kategoriDendaList = KategoriDenda::aktif()->get();

        return view('admin.pesanan.show', compact('pesanan', 'noHp', 'pesanWa', 'kategoriDendaList', 'infoKeterlambatan'));
    }

    /**
     * Menambahkan denda manual ke pesanan (kategori denda, biaya, dan keterangan tambahan).
     */
    public function tambahDenda(Request $request, Pesanan $pesanan, PengembalianService $service): RedirectResponse
    {
        $this->authorize('tambahDenda', $pesanan);

        $validated = $request->validate([
            'kategori_denda_id' => ['nullable', 'exists:kategori_denda,id'],
            'jumlah' => ['required', 'numeric', 'min:1000', 'max:'.PengembalianRequest::BIAYA_MAKS],
            'keterangan' => ['required', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Nominal denda wajib diisi.',
            'jumlah.min' => 'Nominal denda minimal Rp 1.000.',
            'jumlah.max' => 'Nominal denda melebihi batas wajar Rp 10.000.000, periksa kembali angkanya.',
            'keterangan.required' => 'Keterangan atau informasi tambahan denda wajib diisi.',
        ]);

        $denda = $service->tambahDendaManual($pesanan, auth()->user(), $validated);

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', 'Denda baru sebesar Rp '.number_format((float) $denda->jumlah, 0, ',', '.')." berhasil ditambahkan ke pesanan {$pesanan->no_resi}. Jaminan fisik ditahan sampai seluruh denda dilunasi.");
    }

    /**
     * Mencatat dan mengonfirmasi pembayaran pesanan sewa (F05a).
     * Transisi: Menunggu -> Dibayar
     */
    public function bayar(PembayaranRequest $request, Pesanan $pesanan, PesananService $service): RedirectResponse
    {
        $this->authorize('bayar', $pesanan);

        $service->bayar($pesanan, auth()->user(), $request->validated());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Pembayaran pesanan {$pesanan->no_resi} sebesar Rp ".number_format((float) $pesanan->total_sewa, 0, ',', '.').' berhasil dikonfirmasi. Status pesanan: Sudah Dibayar.');
    }

    /**
     * Mengonfirmasi pengambilan barang / serah terima dan pencatatan jaminan fisik (F05b).
     * Transisi: Dibayar -> Disewa
     */
    public function ambil(PengambilanRequest $request, Pesanan $pesanan, PesananService $service): RedirectResponse
    {
        $this->authorize('ambil', $pesanan);

        $service->ambil($pesanan, auth()->user(), $request->validated());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Serah terima barang pesanan {$pesanan->no_resi} berhasil. Jaminan kartu identitas telah dicatat dan status pesanan menjadi Sedang Disewa.");
    }

    /**
     * Mengembalikan jaminan fisik kartu identitas kepada penyewa.
     */
    public function kembalikanJaminan(Pesanan $pesanan, PengembalianService $service): RedirectResponse
    {
        $this->authorize('kembalikanJaminan', $pesanan);

        $service->kembalikanJaminan($pesanan, auth()->user());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Jaminan kartu identitas untuk pesanan {$pesanan->no_resi} telah berhasil dikembalikan kepada penyewa.");
    }

    /**
     * Pembatalan pesanan oleh Admin dengan alasan wajib (F05c).
     * Berlaku untuk pesanan berstatus Menunggu atau Dibayar; pesanan yang sudah dibayar wajib menentukan
     * apakah dananya dikembalikan (refund) atau hangus.
     */
    public function batal(BatalRequest $request, Pesanan $pesanan, PesananService $service): RedirectResponse
    {
        $this->authorize('batal', $pesanan);

        $service->batal($pesanan, auth()->user(), $request->validated()['alasan_batal'], $request->refund());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Pesanan {$pesanan->no_resi} berhasil dibatalkan.");
    }

    /**
     * Mencatat pengembalian dana (refund) pada pesanan yang sudah dibatalkan.
     */
    public function catatRefund(RefundRequest $request, Pesanan $pesanan, PesananService $service): RedirectResponse
    {
        $this->authorize('catatRefund', $pesanan);

        $refund = $service->catatRefund($pesanan, auth()->user(), $request->refund());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', 'Refund sebesar Rp '.number_format((float) $refund->jumlah, 0, ',', '.')." untuk pesanan {$pesanan->no_resi} berhasil dicatat.");
    }

    /**
     * Mengubah jadwal sewa sebelum pengambilan.
     */
    public function ubahJadwal(UbahJadwalRequest $request, Pesanan $pesanan, PerubahanPesananService $service): RedirectResponse
    {
        $this->authorize('ubahJadwal', $pesanan);

        $service->ubahJadwal($pesanan, auth()->user(), Carbon::parse($request->input('tanggal_mulai')), Carbon::parse($request->input('tanggal_selesai')));

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Jadwal pesanan {$pesanan->no_resi} berhasil diubah.");
    }

    /**
     * Memperpanjang sewa pesanan yang sedang disewa disertai pembayaran tambahan.
     */
    public function perpanjang(PerpanjanganRequest $request, Pesanan $pesanan, PerubahanPesananService $service): RedirectResponse
    {
        $this->authorize('perpanjang', $pesanan);

        $tambahan = $service->perpanjang(
            $pesanan,
            auth()->user(),
            Carbon::parse($request->input('tanggal_selesai')),
            $request->safe()->only(['metode', 'catatan'])
        );

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Pesanan {$pesanan->no_resi} berhasil diperpanjang. Tambahan biaya sewa Rp ".number_format($tambahan, 0, ',', '.').' dicatat.');
    }

    /**
     * Menampilkan bukti transfer yang diunggah pelanggan (disk privat, hanya lewat rute ini).
     */
    public function bukti(Pesanan $pesanan, BuktiTransferService $bukti): StreamedResponse
    {
        $this->authorize('lihatBukti', $pesanan);

        return $bukti->kirim($pesanan);
    }
}
