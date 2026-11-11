<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\LogAktivitas;
use App\Models\Pesanan;
use App\Support\PengaturanToko;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Perubahan status pesanan yang dikerjakan sistem (scheduler), terpisah dari aksi manual di PesananService:
 * - disewa yang melewati jatuh tempo + toleransi  -> terlambat
 * - menunggu yang melewati batas_bayar/batas_ambil -> dibatalkan
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
        $toleransi = PengaturanToko::toleransiMenit();
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
     * Membatalkan pesanan berstatus menunggu yang melewati batas_bayar atau batas_ambil, per baris dengan lock + log.
     * Pesanan yang sudah mengunggah bukti transfer tidak dibatalkan karena batas_bayar: pelanggan sudah membayar
     * dan tinggal menunggu konfirmasi admin; baru batas_ambil yang berlaku untuknya.
     */
    public function batalkanMenungguKadaluarsa(Carbon $now): int
    {
        $count = 0;
        $kandidat = Pesanan::where('status', StatusPesanan::Menunggu)
            ->where(fn ($q) => $q->where('batas_ambil', '<', $now)->orWhere(fn ($q) => $this->lewatBatasBayar($q, $now)))
            ->pluck('id');

        foreach ($kandidat as $id) {
            DB::transaction(function () use ($id, $now, &$count) {
                $p = Pesanan::lockForUpdate()->find($id);
                if (! $p || $p->status !== StatusPesanan::Menunggu) {
                    return;
                }

                $lewatAmbil = $p->batas_ambil < $now;
                $lewatBayar = $p->batas_bayar && ! $p->bukti_transfer && $p->batas_bayar < $now;
                if (! $lewatAmbil && ! $lewatBayar) {
                    return;
                }

                $sebab = $lewatBayar && ! $lewatAmbil ? 'melewati batas waktu pembayaran' : 'melewati batas waktu pengambilan';
                $p->update([
                    'status' => StatusPesanan::Dibatalkan,
                    'dibatalkan_pada' => $now,
                    'alasan_batal' => "Dibatalkan otomatis oleh sistem: {$sebab}",
                ]);
                LogAktivitas::catat(null, 'pesanan.batal', "Pesanan [{$p->no_resi}] dibatalkan otomatis oleh sistem ({$sebab})", 'pesanan', $p->id);
                $count++;
            });
        }

        return $count;
    }

    private function lewatBatasBayar(Builder $query, Carbon $now): Builder
    {
        return $query->whereNotNull('batas_bayar')
            ->whereNull('bukti_transfer')
            ->where('batas_bayar', '<', $now);
    }

    /**
     * Pesanan sudah dibayar tetapi tidak diambil hingga batas_ambil + toleransi hari dianggap no-show:
     * dibatalkan agar stok dilepas.
     * Dapat dimatikan lewat pengaturan `noshow_batal_otomatis`.
     */
    public function batalkanNoShow(Carbon $now): int
    {
        if (! PengaturanToko::noshowBatalOtomatis()) {
            return 0;
        }

        $toleransiHari = PengaturanToko::noshowToleransiHari();
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

                $dibayar = $p->total_dibayar;
                LogAktivitas::catat(
                    null,
                    'pesanan.batal',
                    "Pesanan [{$p->no_resi}] dibatalkan otomatis oleh sistem (no-show). Dana Rp".number_format($dibayar, 0, ',', '.').' dinyatakan hangus kecuali admin mencatat refund.',
                    'pesanan',
                    $p->id
                );
                $count++;
            });
        }

        return $count;
    }
}
