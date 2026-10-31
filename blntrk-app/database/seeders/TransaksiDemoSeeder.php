<?php

namespace Database\Seeders;

use App\Enums\JenisDenda;
use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Jaminan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Pengembalian;
use App\Models\PengembalianDetail;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransaksiDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Idempoten: jangan menambah transaksi demo bila sudah ada pesanan (mis. saat container restart)
        if (Pesanan::query()->exists()) {
            return;
        }

        $admin = User::whereHas('role', fn ($q) => $q->where('nama', 'admin'))->first()
            ?? User::first();

        // 1. Siapkan Pelanggan Tambahan untuk keragaman data demo
        $pelangganList = [
            ['nama_lengkap' => 'Reza Pratama', 'no_hp' => '081322334455', 'alamat' => 'Jl. Otista No. 12, Subang'],
            ['nama_lengkap' => 'Nina Herlina', 'no_hp' => '085711229988', 'alamat' => 'Desa Kasomalang, Subang'],
            ['nama_lengkap' => 'Denny Kurniawan', 'no_hp' => '082199881122', 'alamat' => 'Komplek Perum Subang Jaya'],
            ['nama_lengkap' => 'Putri Wulandari', 'no_hp' => '087788990011', 'alamat' => 'Jl. Dangdeur, Subang'],
            ['nama_lengkap' => 'Fajar Nugraha', 'no_hp' => '089677889900', 'alamat' => 'Desa Sagalaherang, Subang'],
        ];

        foreach ($pelangganList as $pData) {
            Pelanggan::firstOrCreate(['no_hp' => $pData['no_hp']], $pData);
        }

        $allPelanggan = Pelanggan::all();
        $allAlat = Alat::where('is_aktif', true)->get();

        if ($allPelanggan->isEmpty() || $allAlat->isEmpty()) {
            return;
        }

        $metodeList = [MetodePembayaran::Tunai, MetodePembayaran::Transfer];
        $jenisIdentitas = ['ktp', 'sim', 'kartu_pelajar'];

        // Hasilkan 30 transaksi demo historis
        // Distribusi: 5 bulan lalu s/d hari ini
        $baseDate = today();
        $counter = 1;

        for ($i = 0; $i < 30; $i++) {
            // Rentang waktu pembuatan pesanan (dari -120 hari hingga hari ini)
            $daysAgo = match (true) {
                $i < 5 => rand(90, 120),  // 3-4 bulan lalu
                $i < 12 => rand(60, 89),   // 2-3 bulan lalu
                $i < 20 => rand(30, 59),   // 1-2 bulan lalu
                $i < 25 => rand(7, 29),    // Bulan ini (awal bulan)
                default => rand(0, 6),     // Minggu ini
            };

            $durasi = rand(2, 4);
            $tglMulai = $baseDate->copy()->subDays($daysAgo);
            $tglSelesai = $tglMulai->copy()->addDays($durasi);

            $pelanggan = $allPelanggan->random();
            $maxId = (int) (Pesanan::max('id') ?? 0);
            $resi = sprintf('BLN-%s-%04d', $tglMulai->format('ymd'), $maxId + $i + 1);

            // Tentukan status pesanan berdasarkan rentang waktu
            if ($daysAgo > 7) {
                // Pesanan masa lampau: mayoritas selesai, sebagian dibatalkan
                $status = ($i % 9 === 0) ? StatusPesanan::Dibatalkan : StatusPesanan::Dikembalikan;
            } elseif ($daysAgo >= 3 && $tglSelesai->isPast()) {
                // Sudah lewat batas pengembalian
                $status = ($i % 2 === 0) ? StatusPesanan::Terlambat : StatusPesanan::Dikembalikan;
            } else {
                // Transaksi baru minggu ini
                $statusPilihan = [
                    StatusPesanan::Menunggu,
                    StatusPesanan::Dibayar,
                    StatusPesanan::Disewa,
                    StatusPesanan::Dikembalikan,
                ];
                $status = $statusPilihan[$i % count($statusPilihan)];
            }

            // Pilih 1 hingga 3 alat secara acak
            $selectedAlat = $allAlat->random(rand(1, 3));
            $totalSewa = 0;
            $itemsDetail = [];

            foreach ($selectedAlat as $alat) {
                $qty = rand(1, 2);
                $subtotal = $alat->harga_per_hari * $qty * $durasi;
                $totalSewa += $subtotal;
                $itemsDetail[] = [
                    'alat_id' => $alat->id,
                    'jumlah' => $qty,
                    'harga_per_hari' => $alat->harga_per_hari,
                    'subtotal' => $subtotal,
                ];
            }

            $pesanan = Pesanan::create([
                'no_resi' => $resi,
                'pelanggan_id' => $pelanggan->id,
                'dibuat_oleh' => $pelanggan->user_id ?? $admin->id,
                'diproses_oleh' => in_array($status, [StatusPesanan::Menunggu], true) ? null : $admin->id,
                'tanggal_mulai' => $tglMulai->toDateString(),
                'tanggal_selesai' => $tglSelesai->toDateString(),
                'durasi_hari' => $durasi,
                'jatuh_tempo' => $tglSelesai->copy()->setTime(12, 0),
                'batas_ambil' => $tglMulai->copy()->setTime(18, 0),
                'total_sewa' => $totalSewa,
                'status' => $status,
                'catatan' => $i % 4 === 0 ? 'Perlengkapan untuk pendakian Gunung Tangkuban Parahu' : null,
                'alasan_batal' => $status === StatusPesanan::Dibatalkan ? 'Perubahan agenda mendadak dari penyewa' : null,
                'dibatalkan_pada' => $status === StatusPesanan::Dibatalkan ? $tglMulai->copy() : null,
                'diambil_pada' => in_array($status, [StatusPesanan::Disewa, StatusPesanan::Dikembalikan, StatusPesanan::Terlambat], true) ? $tglMulai->copy()->setTime(9, 30) : null,
                'created_at' => $tglMulai->copy()->subHours(rand(1, 12)),
                'updated_at' => $tglMulai->copy(),
            ]);

            foreach ($itemsDetail as $detail) {
                PesananDetail::create(array_merge($detail, ['pesanan_id' => $pesanan->id]));
            }

            // Jika status bukan Menunggu & bukan Dibatalkan, catat Pembayaran
            if ($status !== StatusPesanan::Menunggu && $status !== StatusPesanan::Dibatalkan) {
                $metode = $metodeList[array_rand($metodeList)];
                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'jumlah' => $totalSewa,
                    'metode' => $metode,
                    'dibayar_pada' => $tglMulai->copy()->setTime(9, 0),
                    'dicatat_oleh' => $admin->id,
                    'catatan' => 'Lunas di kasir toko fisik BLNTRK',
                    'created_at' => $tglMulai->copy()->setTime(9, 0),
                    'updated_at' => $tglMulai->copy()->setTime(9, 0),
                ]);
            }

            // Jika status Disewa, Dikembalikan, atau Terlambat, catat Jaminan Fisik
            if (in_array($status, [StatusPesanan::Disewa, StatusPesanan::Dikembalikan, StatusPesanan::Terlambat], true)) {
                $jenisKartu = $jenisIdentitas[array_rand($jenisIdentitas)];
                Jaminan::create([
                    'pesanan_id' => $pesanan->id,
                    'jenis' => $jenisKartu,
                    'nomor_identitas_samar' => '32120********'.rand(1000, 9999),
                    'keterangan' => 'Kartu identitas asli '.strtoupper($jenisKartu).' atas nama '.$pelanggan->nama_lengkap,
                    'status' => $status === StatusPesanan::Dikembalikan ? 'dikembalikan' : 'dititipkan',
                    'diterima_pada' => $tglMulai->copy()->setTime(9, 15),
                    'diterima_oleh' => $admin->id,
                    'dikembalikan_pada' => $status === StatusPesanan::Dikembalikan ? $tglSelesai->copy()->setTime(12, 0) : null,
                    'dikembalikan_oleh' => $status === StatusPesanan::Dikembalikan ? $admin->id : null,
                ]);
            }

            // Jika status Dikembalikan, catat Pengembalian
            if ($status === StatusPesanan::Dikembalikan) {
                $pengembalian = Pengembalian::create([
                    'pesanan_id' => $pesanan->id,
                    'dikembalikan_pada' => $tglSelesai->copy()->setTime(11, 45),
                    'hari_terlambat' => 0,
                    'diterima_oleh' => $admin->id,
                    'catatan' => 'Peralatan dicek lengkap dan bersih',
                ]);

                foreach ($pesanan->detail as $dtl) {
                    PengembalianDetail::create([
                        'pengembalian_id' => $pengembalian->id,
                        'pesanan_detail_id' => $dtl->id,
                        'jml_baik' => $dtl->jumlah,
                        'jml_rusak_ringan' => 0,
                        'jml_rusak_berat' => 0,
                        'jml_hilang' => 0,
                        'catatan' => 'Kondisi baik',
                    ]);
                }

                // 20% dari pesanan selesai memiliki denda keterlambatan atau kerusakan
                if ($i % 5 === 0) {
                    $nominalDenda = rand(1, 3) * 25000;
                    // Buat setidaknya satu denda belum lunas untuk bahan demo
                    $isLunas = ($i !== 0);
                    Denda::create([
                        'pesanan_id' => $pesanan->id,
                        'pengembalian_id' => $pengembalian->id,
                        'jenis' => ($i % 2 === 0) ? JenisDenda::Keterlambatan : JenisDenda::Kerusakan,
                        'keterangan' => ($i % 2 === 0) ? 'Terlambat 1 hari pengembalian' : 'Kain tenda kotor/robek kecil di sisi kanopi',
                        'jumlah' => $nominalDenda,
                        'status_bayar' => $isLunas ? 'lunas' : 'belum_lunas',
                        'dilunasi_pada' => $isLunas ? $tglSelesai->copy()->setTime(12, 10) : null,
                        'dilunasi_oleh' => $isLunas ? $admin->id : null,
                        'created_at' => $tglSelesai->copy()->setTime(11, 45),
                        'updated_at' => $tglSelesai->copy()->setTime(12, 10),
                    ]);
                }
            }
        }
    }
}
