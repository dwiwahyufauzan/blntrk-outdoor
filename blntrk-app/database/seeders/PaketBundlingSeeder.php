<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\PaketBundling;
use App\Models\PaketBundlingDetail;
use Illuminate\Database\Seeder;

class PaketBundlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // PAKET 1: Paket Solo Explorer / Backpacker (1 Orang)
        // -------------------------------------------------------------
        $p1 = PaketBundling::updateOrCreate(
            ['kode_paket' => 'PKT-01'],
            [
                'nomor_paket' => 1,
                'nama' => 'Paket 1: Solo Backpacker Explorer',
                'slug' => 'paket-1-solo-backpacker-explorer',
                'kapasitas_orang' => '1 Orang',
                'deskripsi' => 'Paket hemat super praktis untuk petualang mandiri atau solo hiker. Dilengkapi tenda dome 2P, sleeping bag polar hangat, matras foil, kompor portable, nesting masak, serta headlamp LED untuk mendaki Tangkuban Parahu, Ciremai, atau camping mandiri di Subang.',
                'harga_paket_per_hari' => 79000,
                'harga_asli_per_hari' => 98000,
                'is_aktif' => true,
                'urutan' => 1,
            ]
        );

        $alatP1 = [
            'TND-D2' => 1,  // Tenda Dome 2P (35.000)
            'SLB-POL' => 1, // Sleeping Bag Polar (20.000)
            'MTR-FM' => 1,  // Matras Foil (10.000)
            'KMP-PT' => 1,  // Kompor Portable Windproof (15.000)
            'NST-01' => 1,  // Cooking Set Nesting (10.000)
            'HDL-01' => 1,  // Headlamp LED (8.000)
        ];
        $this->attachItems($p1, $alatP1);

        // -------------------------------------------------------------
        // PAKET 2: Paket Duo Mountain Adventure (2-3 Orang)
        // -------------------------------------------------------------
        $p2 = PaketBundling::updateOrCreate(
            ['kode_paket' => 'PKT-02'],
            [
                'nomor_paket' => 2,
                'nama' => 'Paket 2: Duo Mountain Adventure',
                'slug' => 'paket-2-duo-mountain-adventure',
                'kapasitas_orang' => '2-3 Orang',
                'deskripsi' => 'Pilihan terfavorit bagi sepasang sahabat atau pasangan camper yang ingin menikmati sejuknya alam Ciater, Bukit Pamoyanan, dan perkebunan teh Subang. Perlengkapan lengkap tenda dome 4P yang lapang, 2 sleeping bag polar, 2 matras, kompor + nesting, lampu tenda gantung, dan flysheet kanopi pelindung hujan.',
                'harga_paket_per_hari' => 129000,
                'harga_asli_per_hari' => 158000,
                'is_aktif' => true,
                'urutan' => 2,
            ]
        );

        $alatP2 = [
            'TND-D4' => 1,  // Tenda Dome 4P (50.000)
            'SLB-POL' => 2, // 2x Sleeping Bag Polar (40.000)
            'MTR-FM' => 2,  // 2x Matras Foil (20.000)
            'KMP-PT' => 1,  // Kompor Portable (15.000)
            'NST-01' => 1,  // Cooking Set (10.000)
            'LNT-01' => 1,  // Lampu Tenda (8.000)
            'TND-FS' => 1,  // Flysheet Waterproof (15.000)
        ];
        $this->attachItems($p2, $alatP2);

        // -------------------------------------------------------------
        // PAKET 3: Paket Family & Regu Summit (4-6 Orang)
        // -------------------------------------------------------------
        $p3 = PaketBundling::updateOrCreate(
            ['kode_paket' => 'PKT-03'],
            [
                'nomor_paket' => 3,
                'nama' => 'Paket 3: Family & Regu Summit Camp',
                'slug' => 'paket-3-family-regu-summit-camp',
                'kapasitas_orang' => '4-6 Orang',
                'deskripsi' => 'Paket komplit rombongan keluarga atau regu pendaki tanpa repot mencatat alat satu per satu. Sudah termasuk tenda jumbo 6P, 4 sleeping bag, 4 matras alas tidur, flysheet besar, perlengkapan masak lengkap, 2 lentera malam, serta 2 hammock santai untuk liburan camping seru dan nyaman.',
                'harga_paket_per_hari' => 210000,
                'harga_asli_per_hari' => 261000,
                'is_aktif' => true,
                'urutan' => 3,
            ]
        );

        $alatP3 = [
            'TND-D6' => 1,  // Tenda Dome Family 6P (75.000)
            'SLB-STD' => 4, // 4x Sleeping Bag (60.000)
            'MTR-FM' => 4,  // 4x Matras Foil (40.000)
            'TND-FS' => 1,  // Flysheet Waterproof (15.000)
            'KMP-PT' => 1,  // Kompor Portable (15.000)
            'NST-01' => 1,  // Cooking Set (10.000)
            'LNT-01' => 2,  // 2x Lampu Tenda (16.000)
            'HMK-01' => 2,  // 2x Hammock Single (30.000)
        ];
        $this->attachItems($p3, $alatP3);
    }

    private function attachItems(PaketBundling $paket, array $alatQuantities): void
    {
        PaketBundlingDetail::where('paket_bundling_id', $paket->id)->delete();

        foreach ($alatQuantities as $kode => $qty) {
            $alat = Alat::where('kode_alat', $kode)->first();
            if ($alat) {
                PaketBundlingDetail::create([
                    'paket_bundling_id' => $paket->id,
                    'alat_id' => $alat->id,
                    'jumlah' => $qty,
                ]);
            }
        }

        // Hitung ulang harga asli per hari dari total alat
        $totalAsli = 0;
        foreach ($paket->fresh()->items as $item) {
            if ($item->alat) {
                $totalAsli += ($item->alat->harga_per_hari * $item->jumlah);
            }
        }

        if ($totalAsli > 0) {
            $paket->update(['harga_asli_per_hari' => $totalAsli]);
        }
    }
}

