<?php

namespace App\Services;

use App\Models\Pesanan;

class WhatsAppService
{
    /**
     * Menormalkan nomor ponsel ke format internasional Indonesia (628xxxxxxxx).
     */
    public static function normalkanNomor(?string $noHp): string
    {
        if (empty($noHp)) {
            return '';
        }

        // Hapus semua karakter non-digit
        $nomor = preg_replace('/\D/', '', $noHp);

        // Jika diawali 0, ganti dengan 62
        if (str_starts_with($nomor, '0')) {
            $nomor = '62'.substr($nomor, 1);
        }

        // Jika diawali +62 (sudah dibersihkan jadi 62), tetap 62
        return $nomor;
    }

    /**
     * Menghasilkan tautan langsung ke WhatsApp Web / App dengan pesan terenkripsi URL.
     */
    public static function link(?string $noHp, string $pesan): string
    {
        $nomorBersih = self::normalkanNomor($noHp);
        $pesanEnkripsi = urlencode($pesan);

        if (empty($nomorBersih)) {
            return "https://api.whatsapp.com/send?text={$pesanEnkripsi}";
        }

        return "https://api.whatsapp.com/send?phone={$nomorBersih}&text={$pesanEnkripsi}";
    }

    /**
     * Template pesan WhatsApp untuk pengingat batas waktu pengembalian alat (jatuh tempo).
     */
    public static function pesanPengingatJatuhTempo(Pesanan $pesanan): string
    {
        $nama = $pesanan->pelanggan?->nama_lengkap ?? 'Pelanggan';
        $resi = $pesanan->no_resi;
        $tgl = $pesanan->tanggal_selesai?->translatedFormat('d F Y') ?? '-';
        $jam = $pesanan->jatuh_tempo ? $pesanan->jatuh_tempo->format('H:i').' WIB' : '12:00 WIB';

        return "Halo Kak {$nama}, kami dari BLNTRK Outdoor ingin mengingatkan bahwa masa sewa peralatan dengan nomor resi {$resi} akan jatuh tempo pada {$tgl} pukul {$jam}. Mohon pastikan peralatan dikembalikan tepat waktu dalam kondisi bersih dan lengkap. Terima kasih!";
    }

    /**
     * Template pesan WhatsApp untuk tagihan denda keterlambatan / kerusakan.
     */
    public static function pesanTagihanDenda(Pesanan $pesanan, float $nominalDenda, ?string $alasan = null): string
    {
        $nama = $pesanan->pelanggan?->nama_lengkap ?? 'Pelanggan';
        $resi = $pesanan->no_resi;
        $rupiah = number_format($nominalDenda, 0, ',', '.');
        $rincian = $alasan ? " ({$alasan})" : '';

        return "Halo Kak {$nama}, kami dari BLNTRK Outdoor menginformasikan bahwa terdapat denda sebesar Rp {$rupiah}{$rincian} untuk pesanan sewa {$resi}. Silakan melakukan pelunasan di kasir toko atau melalui transfer. Terima kasih!";
    }

    /**
     * Template pesan WhatsApp untuk penagihan/peringatan penyewa yang terlambat pengembalian (denda berjalan).
     */
    public static function pesanPeringatanTerlambat(Pesanan $pesanan, int $hariTerlambat, float $estimasiDenda): string
    {
        $nama = $pesanan->pelanggan?->nama_lengkap ?? 'Pelanggan';
        $resi = $pesanan->no_resi;
        $rupiah = number_format($estimasiDenda, 0, ',', '.');
        $tglSelesai = $pesanan->tanggal_selesai?->translatedFormat('d F Y') ?? '-';

        return "Halo Kak {$nama}, kami dari BLNTRK Outdoor menginformasikan bahwa sewa perlengkapan dengan No Resi {$resi} telah melewati batas pengembalian ({$tglSelesai} pukul 12:00 WIB) dan saat ini terlambat {$hariTerlambat} hari. Estimasi denda keterlambatan berjalan sebesar Rp {$rupiah}. Mohon segera kembalikan perlengkapan ke toko hari ini untuk menghindari penambahan denda harian dan agar kartu identitas jaminan Anda dapat segera diserahkan kembali. Terima kasih!";
    }

    /**
     * Template konfirmasi reservasi booking sewa.
     */
    public static function pesanKonfirmasiBooking(Pesanan $pesanan): string
    {
        $nama = $pesanan->pelanggan?->nama_lengkap ?? 'Pelanggan';
        $resi = $pesanan->no_resi;
        $tglMulai = $pesanan->tanggal_mulai?->translatedFormat('d F Y') ?? '-';
        $total = number_format($pesanan->total_sewa, 0, ',', '.');

        return "Halo Kak {$nama}, reservasi booking perlengkapan outdoor Anda di BLNTRK telah terdaftar dengan No Resi {$resi}. Tanggal mulai sewa: {$tglMulai}, Total sewa: Rp {$total}. Silakan lakukan pembayaran dan pengambilan barang di toko. Terima kasih!";
    }
}
