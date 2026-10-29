<?php

namespace Tests\Unit;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\DendaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DendaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DendaService $dendaService;

    protected Pesanan $pesanan;

    protected Alat $alat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dendaService = new DendaService;

        // Konfigurasi default: toleransi 60 menit, denda 100% per hari
        Pengaturan::setel('toleransi_menit', '60');
        Pengaturan::setel('persen_denda_harian', '100');

        $role = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Customer Denda',
            'no_hp' => '081234567890',
            'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Tenda', 'slug' => 'tenda']);
        $this->alat = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-99',
            'nama' => 'Tenda Dome 4P',
            'harga_per_hari' => 80000,
            'stok_total' => 5,
            'is_aktif' => true,
        ]);

        // Pesanan dengan jatuh tempo 22 Oktober 2026 pukul 12:00:00
        $this->pesanan = Pesanan::create([
            'no_resi' => 'BLN-261020-0099',
            'pelanggan_id' => $pelanggan->id,
            'dibuat_oleh' => $user->id,
            'tanggal_mulai' => '2026-10-20',
            'tanggal_selesai' => '2026-10-22',
            'durasi_hari' => 2,
            'jatuh_tempo' => Carbon::parse('2026-10-22 12:00:00'),
            'batas_ambil' => Carbon::parse('2026-10-20 21:00:00'),
            'total_sewa' => 160000,
        ]);

        $this->pesanan->detail()->create([
            'alat_id' => $this->alat->id,
            'jumlah' => 1,
            'harga_per_hari' => 80000,
            'subtotal' => 160000,
        ]);
    }

    /**
     * BB-53: Pengembalian dalam batas toleransi (misal 45 menit dari jatuh tempo)
     * tidak dikenakan denda (hari = 0, denda = Rp 0).
     */
    public function test_bb_53_pengembalian_dalam_batas_toleransi_denda_nol(): void
    {
        // Jatuh tempo: 12:00:00, Toleransi: 60 menit (batas toleransi: 13:00:00)
        // Waktu kembali: 12:45:00 (masih aman)
        $waktuKembali = Carbon::parse('2026-10-22 12:45:00');

        $hasil = $this->dendaService->hitungKeterlambatan($this->pesanan, $waktuKembali);

        $this->assertEquals(0, $hasil['hari']);
        $this->assertEquals(0.0, $hasil['jumlah']);
        $this->assertEquals(80000.0, $hasil['tarif_harian']);
    }

    /**
     * BB-55: Keterlambatan 1 hari dan 2 hari dihitung berdasarkan tarif sewa harian.
     */
    public function test_bb_55_keterlambatan_1_hari_dan_2_hari(): void
    {
        // 1. Terlambat 1 hari (kembali 23 Oktober 2026 pukul 10:00:00)
        $waktuKembali1 = Carbon::parse('2026-10-23 10:00:00');
        $hasil1 = $this->dendaService->hitungKeterlambatan($this->pesanan, $waktuKembali1);

        $this->assertEquals(1, $hasil1['hari']);
        $this->assertEquals(80000.0, $hasil1['jumlah']); // 1 hari x 80.000 = 80.000

        // 2. Terlambat 2 hari (kembali 24 Oktober 2026 pukul 10:00:00, 46 jam dari jatuh tempo)
        $waktuKembali2 = Carbon::parse('2026-10-24 10:00:00');
        $hasil2 = $this->dendaService->hitungKeterlambatan($this->pesanan, $waktuKembali2);

        $this->assertEquals(2, $hasil2['hari']);
        $this->assertEquals(160000.0, $hasil2['jumlah']); // 2 hari x 80.000 = 160.000
    }

    /**
     * BB-71: Parameter denda berubah (misal menjadi 150%)
     * Perhitungan denda baru mengalikan tarif harian dengan persentase 150%.
     */
    public function test_bb_71_persen_denda_berubah_mempengaruhi_perhitungan(): void
    {
        // Ubah persentase denda menjadi 150%
        Pengaturan::setel('persen_denda_harian', '150');

        // Terlambat 2 hari
        $waktuKembali = Carbon::parse('2026-10-24 10:00:00');
        $hasil = $this->dendaService->hitungKeterlambatan($this->pesanan, $waktuKembali);

        $this->assertEquals(2, $hasil['hari']);
        // 2 hari x Rp 80.000 x 150% = Rp 240.000
        $this->assertEquals(240000.0, $hasil['jumlah']);
    }
}
