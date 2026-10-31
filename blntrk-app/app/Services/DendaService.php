<?php

namespace App\Services;

use App\Models\Pengaturan;
use App\Models\Pesanan;
use Carbon\Carbon;

class DendaService
{
    /**
     * Menghitung hari keterlambatan dan nominal denda keterlambatan.
     *
     * @return array{hari: int, jumlah: float, tarif_harian: float}
     */
    public function hitungKeterlambatan(Pesanan $pesanan, Carbon $waktuKembali): array
    {
        $toleransi = (int) Pengaturan::ambil('toleransi_menit', 60);
        $persen = (float) Pengaturan::ambil('persen_denda_harian', 100) / 100;

        $batasToleransi = $pesanan->jatuh_tempo->copy()->addMinutes($toleransi);

        // Jika waktu kembali masih dalam rentang jatuh tempo + toleransi, tidak ada denda
        if ($waktuKembali->lte($batasToleransi)) {
            return [
                'hari' => 0,
                'jumlah' => 0.0,
                'tarif_harian' => $pesanan->tarif_harian,
            ];
        }

        // Hitung selisih waktu dari jatuh tempo
        $selisihMenit = $pesanan->jatuh_tempo->diffInMinutes($waktuKembali);
        $hari = (int) ceil($selisihMenit / 1440);

        // Tarif harian total dari seluruh item yang disewa
        $tarifHarian = $pesanan->tarif_harian;
        $jumlahDenda = $hari * $tarifHarian * $persen;

        return [
            'hari' => $hari,
            'jumlah' => $jumlahDenda,
            'tarif_harian' => $tarifHarian,
        ];
    }
}
