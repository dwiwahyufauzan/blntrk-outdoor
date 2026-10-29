<?php

namespace Tests\Unit;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use App\Services\PesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HitungTotalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Pelanggan $pelanggan;

    protected Alat $alat1;

    protected Alat $alat2;

    protected PesananService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PesananService::class);

        $role = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);
        $this->user = User::factory()->create(['role_id' => $role->id]);
        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->user->id,
            'nama_lengkap' => 'Tester Rental',
            'no_hp' => '081234567890',
            'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Tenda', 'slug' => 'tenda']);

        // Alat 1: Tarif sewa Rp 60.000 / hari
        $this->alat1 = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-01',
            'nama' => 'Carrier 60L',
            'harga_per_hari' => 60000,
            'stok_total' => 5,
            'is_aktif' => true,
        ]);

        // Alat 2: Tarif sewa Rp 50.000 / hari
        $this->alat2 = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-02',
            'nama' => 'Nesting Cooking Set',
            'harga_per_hari' => 50000,
            'stok_total' => 5,
            'is_aktif' => true,
        ]);
    }

    /**
     * BB-34: Hitung Total Sewa - Durasi 3 hari untuk 2 jenis alat
     * Alat 1 (Rp 60.000) + Alat 2 (Rp 50.000) = Rp 110.000 / hari x 3 hari = Rp 330.000.
     */
    public function test_bb_34_hitung_total_sewa_3_hari_2_alat_menghasilkan_330_ribu(): void
    {
        $tanggalMulai = '2026-10-20';
        $tanggalSelesai = '2026-10-23'; // Durasi 3 hari

        $data = [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'catatan' => 'Uji hitung sewa 3 hari',
            'items' => [
                ['alat_id' => $this->alat1->id, 'jumlah' => 1],
                ['alat_id' => $this->alat2->id, 'jumlah' => 1],
            ],
        ];

        $pesanan = $this->service->buat($this->pelanggan, $this->user, $data);

        // 1. Verifikasi durasi hari sewa
        $this->assertEquals(3, $pesanan->durasi_hari);

        // 2. Verifikasi rincian subtotal per item
        $detail1 = $pesanan->detail->firstWhere('alat_id', $this->alat1->id);
        $detail2 = $pesanan->detail->firstWhere('alat_id', $this->alat2->id);

        $this->assertEquals(180000, $detail1->subtotal); // 60.000 * 1 unit * 3 hari = 180.000
        $this->assertEquals(150000, $detail2->subtotal); // 50.000 * 1 unit * 3 hari = 150.000

        // 3. Verifikasi total keseluruhan: 180.000 + 150.000 = 330.000
        $this->assertEquals(330000, $pesanan->total_sewa);
    }
}
