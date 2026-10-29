<?php

namespace App\Console\Commands;

use App\Services\PesananService;
use Illuminate\Console\Command;

class PerbaruiStatusPesanan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesanan:perbarui-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbarui status pesanan sewa secara berkala (menandai terlambat dan membatalkan kadaluarsa)';

    /**
     * Execute the console command.
     */
    public function handle(PesananService $service): int
    {
        $jumlah = $service->sinkronStatus();
        $this->info("Berhasil memperbarui status {$jumlah} pesanan.");

        return self::SUCCESS;
    }
}
