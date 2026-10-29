<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Services\LaporanExportService;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Menampilkan antarmuka laporan dengan 4 tab jenis dan filter tanggal periode.
     */
    public function index(Request $request, LaporanService $laporanService): View
    {
        $jenis = $request->query('jenis', 'penyewaan');
        $dariInput = $request->query('dari', now()->startOfMonth()->toDateString());
        $sampaiInput = $request->query('sampai', now()->toDateString());
        $status = $request->query('status');

        $dari = Carbon::parse($dariInput);
        $sampai = Carbon::parse($sampaiInput);

        $data = match ($jenis) {
            'pendapatan' => $laporanService->laporanPendapatan($dari, $sampai),
            'denda' => $laporanService->laporanDenda($dari, $sampai, $status),
            'terlaris' => $laporanService->laporanAlatTerlaris($dari, $sampai),
            default => $laporanService->laporanPenyewaan($dari, $sampai, $status),
        };

        return view('admin.laporan.index', compact('jenis', 'dari', 'sampai', 'status', 'data'));
    }

    /**
     * Mengunduh dokumen laporan berperiode dalam format PDF resmi DomPDF.
     */
    public function cetakPdf(Request $request, LaporanService $laporanService): Response
    {
        $jenis = $request->query('jenis', 'penyewaan');
        $dariInput = $request->query('dari', now()->startOfMonth()->toDateString());
        $sampaiInput = $request->query('sampai', now()->toDateString());
        $status = $request->query('status');

        $dari = Carbon::parse($dariInput);
        $sampai = Carbon::parse($sampaiInput);

        $data = match ($jenis) {
            'pendapatan' => $laporanService->laporanPendapatan($dari, $sampai),
            'denda' => $laporanService->laporanDenda($dari, $sampai, $status),
            'terlaris' => $laporanService->laporanAlatTerlaris($dari, $sampai),
            default => $laporanService->laporanPenyewaan($dari, $sampai, $status),
        };

        $toko = Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor');
        $alamat = Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang');
        $petugas = auth()->user()->nama;
        $tanggalCetak = now()->translatedFormat('d F Y H:i');

        $viewName = match ($jenis) {
            'pendapatan' => 'pdf.laporan-pendapatan',
            'denda' => 'pdf.laporan-denda',
            'terlaris' => 'pdf.laporan-alat-terlaris',
            default => 'pdf.laporan-penyewaan',
        };

        $pdf = Pdf::loadView($viewName, compact('data', 'dari', 'sampai', 'status', 'toko', 'alamat', 'petugas', 'tanggalCetak', 'jenis'))
            ->setPaper('a4', 'portrait');

        $filename = "laporan-{$jenis}-{$dari->format('Ymd')}-{$sampai->format('Ymd')}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Mengunduh lembar bukti resi pemesanan resmi format PDF.
     */
    public function cetakResi(Pesanan $pesanan): Response
    {
        $pesanan->load([
            'pelanggan',
            'pembuat',
            'pemroses',
            'detail.alat.kategori',
            'pembayaran.pencatat',
            'jaminan.penerima',
            'pengembalian.penerima',
            'denda',
        ]);

        $toko = Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor');
        $alamat = Pengaturan::ambil('alamat_toko', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang');
        $petugas = auth()->user()->nama;
        $tanggalCetak = now()->translatedFormat('d F Y H:i');

        $pdf = Pdf::loadView('pdf.resi', compact('pesanan', 'toko', 'alamat', 'petugas', 'tanggalCetak'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("resi-{$pesanan->no_resi}.pdf");
    }

    /**
     * Mengunduh data laporan dalam format spreadsheet Excel/CSV (T13).
     */
    public function eksporExcel(Request $request, LaporanService $laporanService, LaporanExportService $exportService): StreamedResponse
    {
        $jenis = $request->query('jenis', 'penyewaan');
        $dariInput = $request->query('dari', now()->startOfMonth()->toDateString());
        $sampaiInput = $request->query('sampai', now()->toDateString());
        $status = $request->query('status');

        $dari = Carbon::parse($dariInput);
        $sampai = Carbon::parse($sampaiInput);

        return $exportService->download($laporanService, $jenis, $dari, $sampai, $status);
    }
}
