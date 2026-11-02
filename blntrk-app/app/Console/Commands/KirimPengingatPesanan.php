<?php

namespace App\Console\Commands;

use App\Services\PengingatService;
use Illuminate\Console\Command;

class KirimPengingatPesanan extends Command
{
    protected $signature = 'pesanan:kirim-pengingat';

    protected $description = 'Mengirim email pengingat jatuh tempo dan peringatan keterlambatan kepada pelanggan terdaftar';

    public function handle(PengingatService $service): int
    {
        if (! email_aktif()) {
            $this->warn('Mailer masih "log"/"array": pengingat tidak dikirim. Atur MAIL_MAILER (mis. smtp) untuk mengaktifkan.');

            return self::SUCCESS;
        }

        $hasil = $service->kirimSemua();
        $this->info("Pengingat jatuh tempo: {$hasil['kembali']}, peringatan terlambat: {$hasil['terlambat']}.");

        return self::SUCCESS;
    }
}
