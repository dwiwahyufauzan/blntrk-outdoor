<?php

namespace Database\Seeders;

use App\Models\KategoriDenda;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Data dasar (role, akun awal, pengaturan, kategori) selalu dimuat. Katalog alat & paket contoh
     * hanya dimuat di luar produksi, atau di produksi bila SEED_DEMO=true.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PengaturanSeeder::class,
            KategoriAlatSeeder::class,
        ]);

        if (! app()->environment('production') || filter_var(env('SEED_DEMO'), FILTER_VALIDATE_BOOLEAN)) {
            $this->call([
                AlatSeeder::class,
                PaketBundlingSeeder::class,
            ]);
        }

        KategoriDenda::seedDefault();
    }
}
