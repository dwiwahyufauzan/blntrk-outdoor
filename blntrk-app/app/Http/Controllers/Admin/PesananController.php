<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPesananRequest;
use App\Http\Requests\BatalRequest;
use App\Http\Requests\PembayaranRequest;
use App\Http\Requests\PengambilanRequest;
use App\Models\Alat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Services\PengembalianService;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
                    $sub->where('no_resi', 'like', "%{$q}%")
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama_lengkap', 'like', "%{$q}%")->orWhere('no_hp', 'like', "%{$q}%"));
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
    public function create(): View
    {
        $pelangganList = Pelanggan::orderBy('nama_lengkap')->get();
        $alatList = Alat::with('kategori')
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        return view('admin.pesanan.create', compact('pelangganList', 'alatList'));
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

        $pesanan = $service->buat($pelanggan, auth()->user(), $request->validated());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Pesanan walk-in berhasil dibuat dengan no resi {$pesanan->no_resi}.");
    }

    /**
     * Menampilkan detail pesanan di panel admin.
     */
    public function show(Pesanan $pesanan): View
    {
        $pesanan->load([
            'pelanggan',
            'pembuat',
            'pemroses',
            'detail.alat.kategori',
            'pembayaran.pencatat',
            'jaminan.penerima',
            'pengembalian.penerima',
            'pengembalian.detail.pesananDetail.alat',
            'denda',
        ]);

        // Format nomor WhatsApp pelanggan (konversi 08xx ke 628xx)
        $noHp = preg_replace('/\D/', '', $pesanan->pelanggan->no_hp ?? '');
        if (str_starts_with($noHp, '0')) {
            $noHp = '62'.substr($noHp, 1);
        }

        $namaPelanggan = $pesanan->pelanggan->nama_lengkap ?? 'Pelanggan';
        $pesanWa = urlencode("Halo Kak {$namaPelanggan}, kami dari BLNTRK Outdoor ingin menginformasikan perihal pesanan sewa Anda dengan no resi {$pesanan->no_resi}. Status saat ini: {$pesanan->status->label()}.");

        return view('admin.pesanan.show', compact('pesanan', 'noHp', 'pesanWa'));
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
            ->with('status', "Pembayaran pesanan {$pesanan->no_resi} sebesar Rp ".number_format($pesanan->total_sewa, 0, ',', '.').' berhasil dikonfirmasi. Status pesanan: Sudah Dibayar.');
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
        $service->kembalikanJaminan($pesanan, auth()->user());

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Jaminan kartu identitas untuk pesanan {$pesanan->no_resi} telah berhasil dikembalikan kepada penyewa.");
    }

    /**
     * Pembatalan pesanan oleh Admin dengan alasan wajib (F05c).
     * Berlaku untuk pesanan berstatus Menunggu atau Dibayar.
     */
    public function batal(BatalRequest $request, Pesanan $pesanan, PesananService $service): RedirectResponse
    {
        $this->authorize('batal', $pesanan);

        $service->batal($pesanan, auth()->user(), $request->validated()['alasan_batal']);

        return redirect()->route('admin.pesanan.show', $pesanan->id)
            ->with('status', "Pesanan {$pesanan->no_resi} berhasil dibatalkan.");
    }
}
