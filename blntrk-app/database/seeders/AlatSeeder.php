<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        $kat = KategoriAlat::pluck('id', 'nama');

        $daftarAlat = [
            // Tenda
            [
                'kategori_id' => $kat['Tenda'],
                'kode_alat' => 'TND-D2',
                'nama' => 'Tenda Dome',
                'varian' => 'Kapasitas 2 Orang',
                'deskripsi' => 'Tenda dome double layer tahan air, cocok untuk pendakian santai 2 orang.',
                'harga_per_hari' => 35000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Tenda'],
                'kode_alat' => 'TND-D4',
                'nama' => 'Tenda Dome',
                'varian' => 'Kapasitas 4 Orang',
                'deskripsi' => 'Tenda dome favorit pendaki, kokoh diterpa angin, kapasitas 4 orang leluasa.',
                'harga_per_hari' => 50000,
                'stok_total' => 8,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Tenda'],
                'kode_alat' => 'TND-D6',
                'nama' => 'Tenda Dome Family',
                'varian' => 'Kapasitas 6 Orang',
                'deskripsi' => 'Tenda kapasitas besar untuk rombongan keluarga atau kelompok pendaki.',
                'harga_per_hari' => 75000,
                'stok_total' => 4,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Tenda'],
                'kode_alat' => 'TND-FS',
                'nama' => 'Flysheet Waterproof',
                'varian' => '3x4 Meter',
                'deskripsi' => 'Penutup atap tenda tambahan dan bivak pelindung hujan angin.',
                'harga_per_hari' => 15000,
                'stok_total' => 6,
                'stok_perawatan' => 0,
            ],

            // Carrier & Tas
            [
                'kategori_id' => $kat['Carrier & Tas'],
                'kode_alat' => 'CRR-40',
                'nama' => 'Carrier Semi-Ultralight',
                'varian' => '40 Liter',
                'deskripsi' => 'Tas punggung gunung ukuran ringkas untuk pendakian 2 hari 1 malam.',
                'harga_per_hari' => 25000,
                'stok_total' => 8,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Carrier & Tas'],
                'kode_alat' => 'CRR-60',
                'nama' => 'Carrier Gunung Standar',
                'varian' => '60 Liter',
                'deskripsi' => 'Carrier terpopuler dengan busa punggung tebal dan sirkulasi udara baik.',
                'harga_per_hari' => 30000,
                'stok_total' => 12,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Carrier & Tas'],
                'kode_alat' => 'CRR-80',
                'nama' => 'Carrier Ekspedisi',
                'varian' => '80 Liter',
                'deskripsi' => 'Carrier berkapasitas besar untuk logistik ekspedisi dan perlengkapan berat.',
                'harga_per_hari' => 40000,
                'stok_total' => 5,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Carrier & Tas'],
                'kode_alat' => 'TAS-DP',
                'nama' => 'Daypack Outdoor',
                'varian' => '25 Liter',
                'deskripsi' => 'Tas harian serbaguna untuk summit attack ke puncak gunung.',
                'harga_per_hari' => 15000,
                'stok_total' => 6,
                'stok_perawatan' => 0,
            ],

            // Sleeping Gear
            [
                'kategori_id' => $kat['Sleeping Gear'],
                'kode_alat' => 'SLB-STD',
                'nama' => 'Sleeping Bag Dakron',
                'varian' => 'Standar',
                'deskripsi' => 'Kantong tidur hangat isian dakron sintetis, nyaman hingga suhu 15°C.',
                'harga_per_hari' => 15000,
                'stok_total' => 20,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Sleeping Gear'],
                'kode_alat' => 'SLB-POL',
                'nama' => 'Sleeping Bag Bulu Polar',
                'varian' => 'Polar Ekstra Hangat',
                'deskripsi' => 'Lapisan dalam polar tebal anti-dingin untuk ketinggian gunung >2500 MDPL.',
                'harga_per_hari' => 20000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],

            // Alas Tidur
            [
                'kategori_id' => $kat['Alas Tidur'],
                'kode_alat' => 'MTR-FM',
                'nama' => 'Matras Foil & Busa',
                'varian' => 'Matras Gulung',
                'deskripsi' => 'Alas tidur isolator hawa dingin tanah gunung.',
                'harga_per_hari' => 10000,
                'stok_total' => 20,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Alas Tidur'],
                'kode_alat' => 'HMK-01',
                'nama' => 'Hammock Single Parasut',
                'varian' => 'Single Beban 150kg',
                'deskripsi' => 'Tempat tidur gantung santai lengkap dengan tali webbing siap pasang.',
                'harga_per_hari' => 15000,
                'stok_total' => 6,
                'stok_perawatan' => 0,
            ],

            // Alas Kaki
            [
                'kategori_id' => $kat['Alas Kaki'],
                'kode_alat' => 'SPT-38',
                'nama' => 'Sepatu Gunung Trekking',
                'varian' => 'Ukuran 38',
                'deskripsi' => 'Sepatu sol karet anti slip, melindungi pergelangan kaki dari medan licin.',
                'harga_per_hari' => 30000,
                'stok_total' => 3,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Alas Kaki'],
                'kode_alat' => 'SPT-40',
                'nama' => 'Sepatu Gunung Trekking',
                'varian' => 'Ukuran 40',
                'deskripsi' => 'Sepatu sol karet anti slip ukuran 40, tahan air dan nyaman.',
                'harga_per_hari' => 30000,
                'stok_total' => 4,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Alas Kaki'],
                'kode_alat' => 'SPT-42',
                'nama' => 'Sepatu Gunung Trekking',
                'varian' => 'Ukuran 42',
                'deskripsi' => 'Sepatu sol karet anti slip ukuran 42 untuk medan berbatu dan tanah liat.',
                'harga_per_hari' => 30000,
                'stok_total' => 4,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Alas Kaki'],
                'kode_alat' => 'SPT-43',
                'nama' => 'Sepatu Gunung Trekking',
                'varian' => 'Ukuran 43',
                'deskripsi' => 'Sepatu sol karet anti slip ukuran 43 berdaya cengkeram kuat.',
                'harga_per_hari' => 30000,
                'stok_total' => 3,
                'stok_perawatan' => 0,
            ],

            // Alat Masak
            [
                'kategori_id' => $kat['Alat Masak'],
                'kode_alat' => 'KMP-PT',
                'nama' => 'Kompor Portable Windproof',
                'varian' => 'Model Bunga Anti-Angin',
                'deskripsi' => 'Kompor gas kaleng mini dengan pelindung terpaan angin gunung.',
                'harga_per_hari' => 15000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Alat Masak'],
                'kode_alat' => 'NST-01',
                'nama' => 'Cooking Set / Nesting',
                'varian' => 'Set Panci 3 Susun',
                'deskripsi' => 'Panci nesting aluminium anti lengket untuk masak nasi, sup, dan air hangat.',
                'harga_per_hari' => 10000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],

            // Penerangan
            [
                'kategori_id' => $kat['Penerangan'],
                'kode_alat' => 'HDL-01',
                'nama' => 'Headlamp LED Kepala',
                'varian' => 'Baterai Cas USB',
                'deskripsi' => 'Senter kepala LED terang, water-resistant untuk pendakian malam.',
                'harga_per_hari' => 8000,
                'stok_total' => 15,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Penerangan'],
                'kode_alat' => 'LNT-01',
                'nama' => 'Lampu Tenda Gantung',
                'varian' => 'Rechargeable 3 Mode',
                'deskripsi' => 'Lampu penerangan gantung di dalam tenda dengan cahaya menyebar hangat.',
                'harga_per_hari' => 8000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],

            // Aksesoris & Perlengkapan
            [
                'kategori_id' => $kat['Aksesoris & Perlengkapan'],
                'kode_alat' => 'TRK-01',
                'nama' => 'Trekking Pole Aluminium',
                'varian' => 'Sepasang (2 unit)',
                'deskripsi' => 'Tongkat pendaki penopang lutut saat menanjak dan menuruni medan curam.',
                'harga_per_hari' => 10000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Aksesoris & Perlengkapan'],
                'kode_alat' => 'JKT-01',
                'nama' => 'Jaket Gunung Windproof',
                'varian' => 'All Size L-XL',
                'deskripsi' => 'Jaket pelindung angin dan gerimis tipis saat berada di ketinggian.',
                'harga_per_hari' => 20000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
            ],
            [
                'kategori_id' => $kat['Aksesoris & Perlengkapan'],
                'kode_alat' => 'RNC-01',
                'nama' => 'Jas Hujan Ponco Kelelawar',
                'varian' => 'Waterproof Ringan',
                'deskripsi' => 'Jas hujan pelindung pendaki dan carrier dari hujan badai gunung.',
                'harga_per_hari' => 5000,
                'stok_total' => 20,
                'stok_perawatan' => 0,
            ],
        ];

        foreach ($daftarAlat as $alat) {
            if (! isset($alat['harga_beli'])) {
                $alat['harga_beli'] = $alat['harga_per_hari'] * 10;
            }
            $record = Alat::firstOrNew(['kode_alat' => $alat['kode_alat']]);
            $record->fill($alat);
            $record->save();
        }
    }
}
