<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananAlurTest extends TestCase
{
    use RefreshDatabase;

    protected User $pelangganUser;

    protected Pelanggan $pelanggan;

    protected User $admin;

    protected Alat $tenda;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);

        $this->pelangganUser = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Rian EndToEnd',
            'email' => 'rian.e2e@test.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id,
            'nama_lengkap' => 'Rian EndToEnd',
            'no_hp' => '081234567891',
            'alamat' => 'Subang',
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Kasir Utama',
            'email' => 'kasir@blntrk.com',
            'is_aktif' => true,
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Tenda', 'slug' => 'tenda']);
        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-01',
            'nama' => 'Tenda Dome Arpenaz',
            'harga_per_hari' => 80000,
            'stok_total' => 5,
            'is_aktif' => true,
        ]);
    }

    /**
     * Alur Lengkap End-to-End Siklus Transaksi Rental:
     * Buat Pesanan (Menunggu) -> Bayar (Dibayar) -> Ambil & Titip Jaminan (Disewa) ->
     * Kembalikan Alat Tepat Waktu (Dikembalikan) -> Kembalikan Kartu Jaminan.
     */
    public function test_alur_lengkap_siklus_pesanan_dari_reservasi_hingga_jaminan_dikembalikan(): void
    {
        $mulai = now()->toDateString();
        $selesai = now()->addDays(2)->toDateString();

        // 1. Pelanggan membuat reservasi sewa daring
        $responseBuat = $this->actingAs($this->pelangganUser)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'catatan' => 'Camping akhir pekan',
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ],
        ]);

        $responseBuat->assertSessionHasNoErrors();
        $pesanan = Pesanan::latest('id')->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals(StatusPesanan::Menunggu, $pesanan->status);
        $this->assertEquals(160000, $pesanan->total_sewa); // 80.000 * 2 hari = 160.000

        // 2. Admin mencatat pembayaran penuh di kasir
        $responseBayar = $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan), [
            'jumlah' => 160000,
            'metode' => MetodePembayaran::Tunai->value,
            'catatan' => 'Bayar tunai di toko',
        ]);

        $responseBayar->assertSessionHasNoErrors();
        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dibayar, $pesanan->status);
        $this->assertCount(1, $pesanan->pembayaran);

        // 3. Pelanggan datang mengambil alat dan menitipkan kartu identitas fisik
        $responseAmbil = $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $pesanan), [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '3213012345678901',
            'keterangan' => 'KTP Asli atas nama penyewa',
        ]);

        $responseAmbil->assertSessionHasNoErrors();
        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Disewa, $pesanan->status);
        $this->assertNotNull($pesanan->jaminan);
        $this->assertEquals('dititipkan', $pesanan->jaminan->status);
        $this->assertEquals('****8901', $pesanan->jaminan->nomor_identitas_samar);

        // 4. Pelanggan mengembalikan alat dalam kondisi baik dan tepat waktu
        $det = $pesanan->detail->first();
        $responseKembali = $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan), [
            'dikembalikan_pada' => now()->format('Y-m-d H:i:s'),
            'catatan' => 'Semua alat lengkap dan bersih',
            'baris' => [
                $det->id => [
                    'baik' => 1,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                    'hilang' => 0,
                    'biaya' => 0,
                ],
            ],
        ]);

        $responseKembali->assertSessionHasNoErrors();
        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dikembalikan, $pesanan->status);
        $this->assertNotNull($pesanan->pengembalian);
        $this->assertCount(0, $pesanan->denda); // Tidak ada denda karena tepat waktu dan kondisi baik

        // 5. Admin mengembalikan kartu jaminan fisik kepada penyewa
        $responseJaminan = $this->actingAs($this->admin)->patch(route('admin.pesanan.jaminan.kembali', $pesanan));
        $responseJaminan->assertSessionHas('status');

        $pesanan->jaminan->refresh();
        $this->assertEquals('dikembalikan', $pesanan->jaminan->status);
        $this->assertNotNull($pesanan->jaminan->dikembalikan_pada);
        $this->assertEquals($this->admin->id, $pesanan->jaminan->dikembalikan_oleh);
    }
}
