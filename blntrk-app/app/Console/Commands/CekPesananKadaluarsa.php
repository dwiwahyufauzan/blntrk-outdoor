<?php

namespace App\Console\Commands;

use App\Services\PesananService;
use Illuminate\Console\Command;

class CekPesananKadaluarsa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesanan:cek-kadaluarsa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan otomatis pesanan menunggu yang telah melewati batas waktu pengambilan';

    /**
     * Execute the console command.
     */
    public function handle(PesananService $service): int
    {
        $jumlah = $service->batalkanKadaluarsa();
        $this->info("Berhasil membatalkan {$jumlah} pesanan kadaluarsa.");

        return Command::SUCCESS;
    }
}
