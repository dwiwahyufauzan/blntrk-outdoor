<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Perubahan status pesanan yang dikerjakan sistem (scheduler), terpisah dari aksi manual di PesananService:
 * - disewa yang melewati jatuh tempo + toleransi  -> terlambat
 * - menunggu yang melewati batas_ambil            -> dibatalkan
 * - dibayar yang tidak diambil (no-show)          -> dibatalkan
 * Setiap perubahan dicatat pada log aktivitas (pelaku: sistem).
 */
class PesananStatusSyncService
{
    /**
     * Menjalankan seluruh sinkronisasi status dan mengembalikan jumlah pesanan yang berubah.
     */
    public function sinkron(): int
    {
        $now = now();

        return $this->tandaiTerlambat($now)
            + $this->batalkanMenungguKadaluarsa($now)
            + $this->batalkanNoShow($now);
    }

    /**
     * Pesanan disewa yang melewati jatuh_tempo + toleransi ditandai terlambat.
     */
    public function tandaiTerlambat(Carbon $now): int
    {
        $toleransi = (int) Pengaturan::ambil('toleransi_menit', 60);
        $batasTerlambat = $now->copy()->subMinutes($toleransi);

        $count = 0;
        $kandidat = Pesanan::where('status', StatusPesanan::Disewa)
            ->where('jatuh_tempo', '<', $batasTerlambat)
            ->pluck('id');

        foreach ($kandidat as $id) {
            DB::transaction(function () use ($id, $batasTerlambat, &$count) {
                $p = Pesanan::lockForUpdate()->find($id);
                if ($p && $p->status === StatusPesanan::Disewa && $p->jatuh_tempo < $batasTerlambat) {
                    $p->update(['status' => StatusPesanan::Terlambat]);
                    LogAktivitas::catat(null, 'pesanan.terlambat', "Pesanan [{$p->no_resi}] ditandai terlambat otomatis oleh sistem", 'pesanan', $p->id);
                    $count++;
                }
            });
        }

        return $count;
    }

    /**
     * Membatalkan pesanan berstatus menunggu yang melewati batas_ambil, per baris dengan lock + log.
     */
    public function batalkanMenungguKadaluarsa(Carbon $now): int
    {
        $count = 0;
        $kandidat = Pesanan::where('status', StatusPesanan::Menunggu)
            ->where('batas_ambil', '<', $now)
            ->pluck('id');

        foreach ($kandidat as $id) {
            DB::transaction(function () use ($id, $now, &$count) {
                $p = Pesanan::lockForUpdate()->find($id);
                if ($p && $p->status === StatusPesanan::Menunggu && $p->batas_ambil < $now) {
                    $p->update([
                        'status' => StatusPesanan::Dibatalkan,
                        'dibatalkan_pada' => $now,
                        'alasan_batal' => 'Dibatalkan otomatis oleh sistem: melewati batas waktu pengambilan',
                    ]);
                    LogAktivitas::catat(null, 'pesanan.batal', "Pesanan [{$p->no_resi}] dibatalkan otomatis oleh sistem (melewati batas pengambilan)", 'pesanan', $p->id);
                    $count++;
                }
            });
        }

        return $count;
    }

    /**
     * Pesanan sudah dibayar tetapi tidak diambil hingga batas_ambil + toleransi hari dianggap no-show:
     * dibatalkan agar stok dilepas.
     * Dapat dimatikan lewat pengaturan `noshow_batal_otomatis`.
     */
    public function batalkanNoShow(Carbon $now): int
    {
        if (! (bool) (int) Pengaturan::ambil('noshow_batal_otomatis', 1)) {
            return 0;
        }

        $toleransiHari = max(0, (int) Pengaturan::ambil('noshow_toleransi_hari', 1));
        $batas = $now->copy()->subDays($toleransiHari);

        $count = 0;
        $kandidat = Pesanan::where('status', StatusPesanan::Dibayar)
            ->where('batas_ambil', '<', $batas)
            ->pluck('id');

        foreach ($kandidat as $id) {
            DB::transaction(function () use ($id, $now, $batas, &$count) {
                $p = Pesanan::lockForUpdate()->find($id);
                if (! $p || $p->status !== StatusPesanan::Dibayar || $p->batas_ambil >= $batas) {
                    return;
                }

                $p->update([
                    'status' => StatusPesanan::Dibatalkan,
                    'dibatalkan_pada' => $now,
                    'alasan_batal' => 'Dibatalkan otomatis oleh sistem: pesanan sudah dibayar tetapi tidak diambil (no-show)',
                ]);

                LogAktivitas::catat(
                    null,
                    'pesanan.batal',
                    "Pesanan [{$p->no_resi}] dibatalkan otomatis oleh sistem (no-show)",
                    'pesanan',
                    $p->id
                );
                $count++;
            });
        }

        return $count;
    }
}
