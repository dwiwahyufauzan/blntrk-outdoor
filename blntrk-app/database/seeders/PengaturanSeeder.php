<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use App\Support\PengaturanToko;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        // Create-only: nilai yang sudah diubah pemilik di halaman Pengaturan tidak boleh tertimpa saat restart/deploy.
        foreach (PengaturanToko::bawaan() as $kunci => $item) {
            Pengaturan::firstOrCreate(
                ['kunci' => $kunci],
                ['nilai' => $item['nilai'], 'keterangan' => $item['keterangan']]
            );
        }
    }
}
