<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $pengaturan = [
            [
                'kunci' => 'persen_denda_harian',
                'nilai' => '100',
                'keterangan' => 'Persentase tarif harian alat per hari keterlambatan (%)',
            ],
            [
                'kunci' => 'jam_kembali',
                'nilai' => '12:00',
                'keterangan' => 'Jam jatuh tempo pengembalian alat sewa',
            ],
            [
                'kunci' => 'toleransi_menit',
                'nilai' => '60',
                'keterangan' => 'Toleransi keterlambatan pengembalian sebelum denda dihitung (menit)',
            ],
            [
                'kunci' => 'maks_durasi_hari',
                'nilai' => '14',
                'keterangan' => 'Maksimum durasi peminjaman sewa alat (hari)',
            ],
            [
                'kunci' => 'nama_toko',
                'nilai' => 'BLNTRK Outdoor',
                'keterangan' => 'Nama toko outdoor yang dicetak pada resi dan laporan',
            ],
            [
                'kunci' => 'alamat_toko',
                'nilai' => 'Desa Kumpay, Kec. Jalancagak, Kab. Subang, Jawa Barat',
                'keterangan' => 'Alamat operasional toko',
            ],
            [
                'kunci' => 'no_wa_toko',
                'nilai' => '6281234567890',
                'keterangan' => 'Nomor WhatsApp konfirmasi pesanan toko (format internasional 62...)',
            ],
        ];

        foreach ($pengaturan as $item) {
            Pengaturan::updateOrCreate(
                ['kunci' => $item['kunci']],
                ['nilai' => $item['nilai'], 'keterangan' => $item['keterangan']]
            );
        }
    }
}
