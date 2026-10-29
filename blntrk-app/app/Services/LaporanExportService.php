<?php

namespace App\Services;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportService
{
    /**
     * Menghasilkan file spreadsheet CSV terformat dengan UTF-8 BOM untuk Microsoft Excel.
     */
    public function download(LaporanService $laporanService, string $jenis, Carbon $dari, Carbon $sampai, ?string $status = null): StreamedResponse
    {
        $filename = "laporan-{$jenis}-{$dari->format('Ymd')}-{$sampai->format('Ymd')}.csv";

        return response()->streamDownload(function () use ($laporanService, $jenis, $dari, $sampai, $status) {
            $handle = fopen('php://output', 'w');
            // Tulis BOM UTF-8 agar karakter Indonesia dan aksen tampil sempurna di MS Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            match ($jenis) {
                'pendapatan' => $this->tulisPendapatan($handle, $laporanService, $dari, $sampai),
                'denda' => $this->tulisDenda($handle, $laporanService, $dari, $sampai, $status),
                'terlaris' => $this->tulisTerlaris($handle, $laporanService, $dari, $sampai),
                default => $this->tulisPenyewaan($handle, $laporanService, $dari, $sampai, $status),
            };

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function tulisPenyewaan($handle, LaporanService $service, Carbon $dari, Carbon $sampai, ?string $status): void
    {
        $data = $service->laporanPenyewaan($dari, $sampai, $status);

        fputcsv($handle, ['LAPORAN PENYEWAAN ALAT OUTDOOR']);
        fputcsv($handle, ['Periode:', $dari->format('d/m/Y').' s/d '.$sampai->format('d/m/Y')]);
        fputcsv($handle, ['Total Pesanan:', $data['total_pesanan']]);
        fputcsv($handle, ['Total Nilai Sewa:', 'Rp '.number_format($data['total_sewa'], 0, ',', '.')]);
        fputcsv($handle, []);

        fputcsv($handle, ['No', 'No Resi', 'Nama Pelanggan', 'No HP', 'Tgl Mulai', 'Tgl Selesai', 'Durasi (Hari)', 'Status Pesanan', 'Total Sewa (Rp)']);

        foreach ($data['items'] as $index => $item) {
            fputcsv($handle, [
                $index + 1,
                $item->no_resi,
                $item->pelanggan?->nama_lengkap ?? '-',
                $item->pelanggan?->no_hp ?? '-',
                $item->tanggal_mulai?->format('d/m/Y'),
                $item->tanggal_selesai?->format('d/m/Y'),
                $item->durasi_hari,
                $item->status->label(),
                (int) $item->total_sewa,
            ]);
        }
    }

    private function tulisPendapatan($handle, LaporanService $service, Carbon $dari, Carbon $sampai): void
    {
        $data = $service->laporanPendapatan($dari, $sampai);

        fputcsv($handle, ['LAPORAN PENDAPATAN FINANSIAL (KAS MASUK RIIL)']);
        fputcsv($handle, ['Periode:', $dari->format('d/m/Y').' s/d '.$sampai->format('d/m/Y')]);
        fputcsv($handle, ['Total Pendapatan Sewa:', 'Rp '.number_format($data['total_pendapatan_sewa'], 0, ',', '.')]);
        fputcsv($handle, ['Total Denda Lunas:', 'Rp '.number_format($data['total_pendapatan_denda'], 0, ',', '.')]);
        fputcsv($handle, ['Total Kas Masuk Bersih:', 'Rp '.number_format($data['total_pendapatan_bersih'], 0, ',', '.')]);
        fputcsv($handle, []);

        fputcsv($handle, ['--- PEMBAYARAN SEWA MASUK ---']);
        fputcsv($handle, ['No', 'Waktu Bayar', 'No Resi', 'Nama Pelanggan', 'Metode Bayar', 'Petugas Kasir', 'Nominal (Rp)']);
        foreach ($data['pembayaran_sewa'] as $idx => $bayar) {
            fputcsv($handle, [
                $idx + 1,
                $bayar->dibayar_pada?->format('d/m/Y H:i'),
                $bayar->pesanan?->no_resi,
                $bayar->pesanan?->pelanggan?->nama_lengkap ?? '-',
                $bayar->metode->label(),
                $bayar->pencatat?->nama ?? 'Admin',
                (int) $bayar->jumlah,
            ]);
        }

        fputcsv($handle, []);
        fputcsv($handle, ['--- DENDA KETERLAMBATAN & KERUSAKAN LUNAS ---']);
        fputcsv($handle, ['No', 'Waktu Pelunasan', 'No Resi', 'Nama Pelanggan', 'Jenis Denda', 'Petugas Kasir', 'Nominal (Rp)']);
        foreach ($data['denda_lunas'] as $idx => $denda) {
            fputcsv($handle, [
                $idx + 1,
                $denda->dilunasi_pada?->format('d/m/Y H:i') ?? $denda->created_at?->format('d/m/Y H:i'),
                $denda->pesanan?->no_resi,
                $denda->pesanan?->pelanggan?->nama_lengkap ?? '-',
                $denda->jenis->label(),
                $denda->petugasLunas?->nama ?? 'Admin',
                (int) $denda->jumlah,
            ]);
        }
    }

    private function tulisDenda($handle, LaporanService $service, Carbon $dari, Carbon $sampai, ?string $status): void
    {
        $data = $service->laporanDenda($dari, $sampai, $status);

        fputcsv($handle, ['LAPORAN DENDA DAN GANTI RUGI']);
        fputcsv($handle, ['Periode:', $dari->format('d/m/Y').' s/d '.$sampai->format('d/m/Y')]);
        fputcsv($handle, ['Total Seluruh Denda:', 'Rp '.number_format($data['total_denda'], 0, ',', '.')]);
        fputcsv($handle, ['Total Denda Lunas:', 'Rp '.number_format($data['total_lunas'], 0, ',', '.')]);
        fputcsv($handle, ['Total Belum Lunas:', 'Rp '.number_format($data['total_belum_lunas'], 0, ',', '.')]);
        fputcsv($handle, []);

        fputcsv($handle, ['No', 'Waktu Kejadian', 'No Resi', 'Nama Pelanggan', 'Jenis Denda', 'Keterangan', 'Status Bayar', 'Waktu Pelunasan', 'Nominal (Rp)']);
        foreach ($data['items'] as $idx => $denda) {
            fputcsv($handle, [
                $idx + 1,
                $denda->created_at->format('d/m/Y H:i'),
                $denda->pesanan?->no_resi,
                $denda->pesanan?->pelanggan?->nama_lengkap ?? '-',
                $denda->jenis->label(),
                $denda->keterangan ?? '-',
                strtoupper($denda->status_bayar),
                $denda->dilunasi_pada?->format('d/m/Y H:i') ?? '-',
                (int) $denda->jumlah,
            ]);
        }
    }

    private function tulisTerlaris($handle, LaporanService $service, Carbon $dari, Carbon $sampai): void
    {
        $data = $service->laporanAlatTerlaris($dari, $sampai, 50);

        fputcsv($handle, ['LAPORAN ALAT OUTDOOR TERLARIS']);
        fputcsv($handle, ['Periode:', $dari->format('d/m/Y').' s/d '.$sampai->format('d/m/Y')]);
        fputcsv($handle, ['Total Unit Disewa:', $data['total_unit_disewa']]);
        fputcsv($handle, ['Total Omzet Sewa:', 'Rp '.number_format($data['total_omzet_alat'], 0, ',', '.')]);
        fputcsv($handle, []);

        fputcsv($handle, ['Peringkat', 'Kode Alat', 'Nama Perlengkapan Outdoor', 'Kategori', 'Total Unit Disewa', 'Total Frekuensi Transaksi', 'Total Omzet (Rp)']);
        foreach ($data['items'] as $idx => $row) {
            fputcsv($handle, [
                $idx + 1,
                $row->alat?->kode_alat ?? '-',
                $row->alat?->nama ?? 'Alat Dihapus',
                $row->alat?->kategori?->nama ?? '-',
                (int) $row->total_disewa,
                (int) $row->total_transaksi,
                (int) $row->total_pendapatan,
            ]);
        }
    }
}
