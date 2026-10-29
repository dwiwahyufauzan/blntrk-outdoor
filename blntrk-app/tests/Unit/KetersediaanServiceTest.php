<?php

namespace Tests\Unit;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Role;
use App\Models\User;
use App\Services\KetersediaanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KetersediaanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KetersediaanService $service;

    protected KategoriAlat $kategori;

    protected Pelanggan $pelanggan;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new KetersediaanService;

        // Siapkan role & user admin
        $roleAdmin = Role::create(['nama' => 'admin', 'label' => 'Administrator']);
        Role::create(['nama' => 'pemilik', 'label' => 'Pemilik Toko']);
        Role::create(['nama' => 'pelanggan', 'label' => 'Pelanggan']);

        $this->user = User::create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Test',
            'email' => 'admin.test@blntrk.test',
            'password' => bcrypt('password'),
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->user->id,
            'nama_lengkap' => 'Pelanggan Test',
            'no_hp' => '081234567890',
            'alamat' => 'Subang',
        ]);

        $this->kategori = KategoriAlat::create([
            'nama' => 'Tenda Dome',
            'slug' => 'tenda-dome',
        ]);
    }

    private function buatAlat(int $stokTotal = 10, int $stokPerawatan = 0): Alat
    {
        return Alat::create([
            'kategori_id' => $this->kategori->id,
            'kode_alat' => 'TND-'.uniqid(),
            'nama' => 'Tenda Eiger 4P',
            'harga_per_hari' => 45000,
            'stok_total' => $stokTotal,
            'stok_perawatan' => $stokPerawatan,
            'is_aktif' => true,
        ]);
    }

    private function buatPesanan(Carbon $mulai, Carbon $selesai, string $status = 'dibayar'): Pesanan
    {
        return Pesanan::create([
            'no_resi' => 'BLN-'.uniqid(),
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->user->id,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'durasi_hari' => $mulai->diffInDays($selesai),
            'jatuh_tempo' => $selesai->copy()->setTime(21, 0, 0),
            'batas_ambil' => $mulai->copy()->setTime(18, 0, 0),
            'status' => $status,
            'total_sewa' => 100000,
            'total_denda' => 0,
        ]);
    }

    /** Kasus 1: Tanpa pesanan -> stok penuh */
    public function test_tanpa_pesanan_stok_tersedia_penuh(): void
    {
        $alat = $this->buatAlat(10, 0);
        $mulai = Carbon::today()->addDays(1);
        $selesai = Carbon::today()->addDays(4);

        $tersedia = $this->service->tersedia($alat, $mulai, $selesai);
        $this->assertEquals(10, $tersedia);
        $this->assertTrue($this->service->cukup($alat, 10, $mulai, $selesai));
        $this->assertFalse($this->service->cukup($alat, 11, $mulai, $selesai));
    }

    /** Kasus 2: Pesanan aktif beririsan -> stok berkurang */
    public function test_pesanan_aktif_beririsan_mengurangi_stok(): void
    {
        $alat = $this->buatAlat(5, 0);

        // Pesanan tgl +1 s.d. +4 sejumlah 3 unit
        $mulaiPesanan = Carbon::today()->addDays(1);
        $selesaiPesanan = Carbon::today()->addDays(4);
        $pesanan = $this->buatPesanan($mulaiPesanan, $selesaiPesanan, 'dibayar');

        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $alat->id,
            'jumlah' => 3,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 3 * $alat->harga_per_hari * 3,
        ]);

        // Cek pada rentang beririsan (+2 s.d. +3) -> sisa 5 - 3 = 2 unit
        $cekMulai = Carbon::today()->addDays(2);
        $cekSelesai = Carbon::today()->addDays(3);
        $tersedia = $this->service->tersedia($alat, $cekMulai, $cekSelesai);
        $this->assertEquals(2, $tersedia);
    }

    /** Kasus 3: Tidak beririsan (tgl selesai pesanan = tgl mulai cek) -> stok penuh */
    public function test_pesanan_tidak_beririsan_stok_tetap_penuh(): void
    {
        $alat = $this->buatAlat(5, 0);

        // Pesanan tgl +1 s.d. +3 sejumlah 5 unit
        $mulaiPesanan = Carbon::today()->addDays(1);
        $selesaiPesanan = Carbon::today()->addDays(3);
        $pesanan = $this->buatPesanan($mulaiPesanan, $selesaiPesanan, 'dibayar');

        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $alat->id,
            'jumlah' => 5,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 5 * $alat->harga_per_hari * 2,
        ]);

        // Cek tgl +3 s.d. +5 (hari pengembalian pesanan lama adalah hari mulai sewa baru)
        $cekMulai = Carbon::today()->addDays(3);
        $cekSelesai = Carbon::today()->addDays(5);
        $tersedia = $this->service->tersedia($alat, $cekMulai, $cekSelesai);
        $this->assertEquals(5, $tersedia);
    }

    /** Kasus 4: Pesanan dibatalkan / selesai -> tidak menahan stok */
    public function test_pesanan_dibatalkan_atau_selesai_tidak_menahan_stok(): void
    {
        $alat = $this->buatAlat(5, 0);

        $mulai = Carbon::today()->addDays(1);
        $selesai = Carbon::today()->addDays(4);

        $pesananBatal = $this->buatPesanan($mulai, $selesai, 'dibatalkan');
        PesananDetail::create([
            'pesanan_id' => $pesananBatal->id,
            'alat_id' => $alat->id,
            'jumlah' => 5,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 50000,
        ]);

        $tersedia = $this->service->tersedia($alat, $mulai, $selesai);
        $this->assertEquals(5, $tersedia);
    }

    /** Kasus 5: Stok perawatan mengurangi kapasitas efektif */
    public function test_stok_perawatan_mengurangi_kapasitas(): void
    {
        // Total 5, rusak/perawatan 2 -> kapasitas efektif 3
        $alat = $this->buatAlat(5, 2);

        $mulai = Carbon::today()->addDays(1);
        $selesai = Carbon::today()->addDays(3);

        $tersedia = $this->service->tersedia($alat, $mulai, $selesai);
        $this->assertEquals(3, $tersedia);
    }

    /** Kasus 6: Pesanan terlambat tetap menahan stok hari ini */
    public function test_pesanan_terlambat_tetap_menahan_stok_hari_ini(): void
    {
        $alat = $this->buatAlat(5, 0);

        // Pesanan 3 hari lalu s.d. kemarin, status 'terlambat'
        $mulaiKemarin = Carbon::today()->subDays(3);
        $selesaiKemarin = Carbon::today()->subDays(1);
        $pesananTerlambat = $this->buatPesanan($mulaiKemarin, $selesaiKemarin, 'terlambat');

        PesananDetail::create([
            'pesanan_id' => $pesananTerlambat->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 90000,
        ]);

        // Cek ketersediaan hari ini s.d. besok -> 2 unit masih tertahan
        $mulaiCek = Carbon::today();
        $selesaiCek = Carbon::today()->addDays(1);

        $tersedia = $this->service->tersedia($alat, $mulaiCek, $selesaiCek);
        $this->assertEquals(3, $tersedia);
    }

    /** Kasus 7: Puncak harian menghitung bottleneck minimum */
    public function test_puncak_harian_menghitung_ketersediaan_minimum(): void
    {
        // Stok total 3
        $alat = $this->buatAlat(3, 0);

        // Pesanan A: tgl +1 s.d. +3 (jumlah 2)
        $pA = $this->buatPesanan(Carbon::today()->addDays(1), Carbon::today()->addDays(3), 'dibayar');
        PesananDetail::create([
            'pesanan_id' => $pA->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 100000,
        ]);

        // Pesanan B: tgl +3 s.d. +5 (jumlah 2)
        $pB = $this->buatPesanan(Carbon::today()->addDays(3), Carbon::today()->addDays(5), 'dibayar');
        PesananDetail::create([
            'pesanan_id' => $pB->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 100000,
        ]);

        // Cek sepanjang rentang tgl +1 s.d. +5:
        // Setiap hari max pemakaian adalah 2, sehingga sisa tersedia adalah 3 - 2 = 1 unit.
        $tersedia = $this->service->tersedia($alat, Carbon::today()->addDays(1), Carbon::today()->addDays(5));
        $this->assertEquals(1, $tersedia);
    }

    /** Kasus 8: Abaikan pesanan tertentu (misal saat edit pesanan sendiri) */
    public function test_abaikan_pesanan_saat_kalkulasi(): void
    {
        $alat = $this->buatAlat(5, 0);
        $mulai = Carbon::today()->addDays(1);
        $selesai = Carbon::today()->addDays(3);

        $pesanan = $this->buatPesanan($mulai, $selesai, 'dibayar');
        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $alat->id,
            'jumlah' => 5,
            'harga_per_hari' => $alat->harga_per_hari,
            'subtotal' => 100000,
        ]);

        // Tanpa abaikan -> 0
        $this->assertEquals(0, $this->service->tersedia($alat, $mulai, $selesai));

        // Dengan abaikan ID pesanan tersebut -> 5 (tersedia penuh)
        $this->assertEquals(5, $this->service->tersedia($alat, $mulai, $selesai, $pesanan->id));
    }
}
