<?php

namespace Database\Seeders;

use App\Models\KategoriAlat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriAlatSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Tenda', 'deskripsi' => 'Tenda dome kapasitas 2-6 orang, flysheet, dan pasak tenda.'],
            ['nama' => 'Carrier & Tas', 'deskripsi' => 'Tas gunung carrier kapasitas 40L hingga 80L, dan daypack.'],
            ['nama' => 'Sleeping Gear', 'deskripsi' => 'Sleeping bag standar, polar, dan selimut hangat.'],
            ['nama' => 'Alas Tidur', 'deskripsi' => 'Matras gulung aluminium/busa dan hammock santai.'],
            ['nama' => 'Alas Kaki', 'deskripsi' => 'Sepatu gunung tahan air berbagai ukuran dan gaiter.'],
            ['nama' => 'Alat Masak', 'deskripsi' => 'Kompor portable, nesting set, gas kaleng, dan cangkir lipat.'],
            ['nama' => 'Penerangan', 'deskripsi' => 'Headlamp LED, senter, dan lampu tenda gantung.'],
            ['nama' => 'Aksesoris & Perlengkapan', 'deskripsi' => 'Trekking pole, jas hujan ponco, jaket gunung, dan sarung tangan.'],
        ];

        foreach ($kategori as $item) {
            KategoriAlat::firstOrCreate(
                ['nama' => $item['nama']],
                [
                    'slug' => Str::slug($item['nama']),
                    'deskripsi' => $item['deskripsi'],
                ]
            );
        }
    }
}
