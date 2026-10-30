<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\PaketBundling;
use App\Models\PaketBundlingDetail;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeranjangDanPaketBundlingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pelangganUser;
    protected Pelanggan $pelanggan;
    protected KategoriAlat $kategoriTenda;
    protected KategoriAlat $kategoriTidur;
    protected Alat $alatTenda;
    protected Alat $alatSleepingBag;
    protected PaketBundling $paketSolo;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['nama' => 'admin', 'label' => 'Administrator']);
        Role::create(['nama' => 'pemilik', 'label' => 'Pemilik Toko']);
        $rolePelanggan = Role::create(['nama' => 'pelanggan', 'label' => 'Pelanggan']);

        $this->admin = User::create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin BLNTRK',
            'email' => 'admin@blntrk.test',
            'password' => bcrypt('password'),
            'is_aktif' => true,
        ]);

        $this->pelangganUser = User::create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Fauzan Adventurer',
            'email' => 'fauzan@blntrk.test',
            'password' => bcrypt('password'),
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id,
            'nama_lengkap' => 'Fauzan Adventurer',
            'no_hp' => '081234567890',
            'alamat' => 'Subang, Jawa Barat',
        ]);

        $this->kategoriTenda = KategoriAlat::create([
            'nama' => 'Tenda Camping',
            'slug' => 'tenda-camping',
        ]);

        $this->kategoriTidur = KategoriAlat::create([
            'nama' => 'Perlengkapan Tidur',
            'slug' => 'perlengkapan-tidur',
        ]);

        $this->alatTenda = Alat::create([
            'kategori_id' => $this->kategoriTenda->id,
            'kode_alat' => 'TND-D2',
            'nama' => 'Tenda Dome 2 Person',
            'varian' => 'Kapasitas 2 Orang Double Layer Waterproof',
            'deskripsi' => 'Tenda kapasitas 2 orang.',
            'harga_per_hari' => 35000,
            'stok_total' => 10,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->alatSleepingBag = Alat::create([
            'kategori_id' => $this->kategoriTidur->id,
            'kode_alat' => 'SLB-POL',
            'nama' => 'Sleeping Bag Polar Bulu Hangat',
            'varian' => 'Bahan Polar Tebal',
            'deskripsi' => 'Kenyamanan tidur hangat di gunung.',
            'harga_per_hari' => 15000,
            'stok_total' => 15,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        // Buat Paket 1
        $this->paketSolo = PaketBundling::create([
            'nomor_paket' => 1,
            'kode_paket' => 'PKT-01',
            'nama' => 'Solo Backpacker Explorer',
            'slug' => 'solo-backpacker-explorer',
            'kapasitas_orang' => '1 Orang',
            'deskripsi' => 'Paket hemat solo camping lengkap dan ringkas.',
            'harga_paket_per_hari' => 45000,
            'harga_asli_per_hari' => 50000,
            'is_aktif' => true,
            'urutan' => 1,
        ]);

        PaketBundlingDetail::create([
            'paket_bundling_id' => $this->paketSolo->id,
            'alat_id' => $this->alatTenda->id,
            'jumlah' => 1,
        ]);

        PaketBundlingDetail::create([
            'paket_bundling_id' => $this->paketSolo->id,
            'alat_id' => $this->alatSleepingBag->id,
            'jumlah' => 1,
        ]);
    }

    public function test_halaman_paket_bundling_publik_dapat_diakses(): void
    {
        $response = $this->get(route('paket-bundling.index'));

        $response->assertOk();
        $response->assertSee('Paket 1');
        $response->assertSee('Solo Backpacker Explorer');
        $response->assertSee('Rp45.000');
    }

    public function test_detail_paket_bundling_menampilkan_komposisi_alat_dan_hemat(): void
    {
        $response = $this->get(route('paket-bundling.show', $this->paketSolo->slug));

        $response->assertOk();
        $response->assertSee('Tenda Dome 2 Person');
        $response->assertSee('Sleeping Bag Polar Bulu Hangat');
        $response->assertSee('Rp45.000');
        $response->assertSee('Rp50.000');
        $response->assertSee('Diskon 10%');
    }

    public function test_tambah_alat_eceran_ke_keranjang(): void
    {
        $response = $this->post(route('keranjang.tambah'), [
            'alat_id' => $this->alatTenda->id,
            'jumlah' => 2,
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals([$this->alatTenda->id => 2], session('keranjang_sewa'));
    }

    public function test_tambah_paket_bundling_ke_keranjang(): void
    {
        $response = $this->post(route('keranjang.tambah-paket', $this->paketSolo));

        $response->assertSessionHas('status');
        $cart = session('keranjang_sewa');

        $this->assertArrayHasKey($this->alatTenda->id, $cart);
        $this->assertArrayHasKey($this->alatSleepingBag->id, $cart);
        $this->assertEquals(1, $cart[$this->alatTenda->id]);
        $this->assertEquals(1, $cart[$this->alatSleepingBag->id]);
    }

    public function test_update_kuantitas_dan_hapus_item_keranjang(): void
    {
        // Isi keranjang
        session()->put('keranjang_sewa', [
            $this->alatTenda->id => 2,
            $this->alatSleepingBag->id => 1,
        ]);

        // Update kuantitas tenda menjadi 3
        $responseUpdate = $this->from(route('keranjang.index'))->patch(route('keranjang.update', $this->alatTenda->id), [
            'jumlah' => 3,
        ]);
        $responseUpdate->assertRedirect(route('keranjang.index'));
        $this->assertEquals(3, session('keranjang_sewa')[$this->alatTenda->id]);

        // Hapus sleeping bag
        $responseDelete = $this->from(route('keranjang.index'))->delete(route('keranjang.hapus', $this->alatSleepingBag->id));
        $responseDelete->assertRedirect(route('keranjang.index'));
        $this->assertArrayNotHasKey($this->alatSleepingBag->id, session('keranjang_sewa'));
    }

    public function test_kosongkan_keranjang(): void
    {
        session()->put('keranjang_sewa', [
            $this->alatTenda->id => 2,
        ]);

        $response = $this->delete(route('keranjang.kosongkan'));
        $response->assertRedirect(route('keranjang.index'));
        $this->assertEmpty(session('keranjang_sewa'));
    }

    public function test_checkout_dari_keranjang_mengarahkan_ke_form_pemesanan(): void
    {
        session()->put('keranjang_sewa', [
            $this->alatTenda->id => 2,
        ]);

        $response = $this->actingAs($this->pelangganUser)->post(route('keranjang.checkout'), [
            'tgl_sewa_mulai' => Carbon::tomorrow()->format('Y-m-d'),
            'tgl_sewa_kembali' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertRedirectContains(route('pesanan.create', ['from_cart' => 1]));
        $this->assertTrue(session()->has('checkout_from_cart'));
        $this->assertEquals(2, session('checkout_from_cart.items')[$this->alatTenda->id]);
    }

    public function test_sewa_langsung_paket_bundling_membawa_item_ke_form_pemesanan(): void
    {
        $response = $this->actingAs($this->pelangganUser)->post(route('paket-bundling.sewa-langsung', $this->paketSolo->slug), [
            'mulai' => Carbon::tomorrow()->format('Y-m-d'),
            'selesai' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'),
        ]);

        $response->assertRedirectContains(route('pesanan.create', ['from_cart' => 1]));
        $this->assertTrue(session()->has('checkout_from_cart'));
        $items = session('checkout_from_cart.items');
        $this->assertCount(2, $items);
    }

    public function test_admin_dapat_melihat_daftar_paket_bundling(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.paket-bundling.index'));

        $response->assertOk();
        $response->assertSee('Solo Backpacker Explorer');
        $response->assertSee('PKT-01');
    }

    public function test_admin_dapat_membuat_paket_bundling_baru(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.paket-bundling.store'), [
            'nomor_paket' => 4,
            'kode_paket' => 'PKT-04',
            'nama' => 'Paket Ultra Light Hiking',
            'kapasitas_orang' => '1 Orang',
            'deskripsi' => 'Paket ultra ringan untuk pendaki cepat.',
            'harga_paket_per_hari' => 40000,
            'is_aktif' => 1,
            'items' => [
                [
                    'alat_id' => $this->alatTenda->id,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.paket-bundling.index'));
        $this->assertDatabaseHas('paket_bundling', [
            'kode_paket' => 'PKT-04',
            'nama' => 'Paket Ultra Light Hiking',
            'harga_paket_per_hari' => 40000,
            'harga_asli_per_hari' => 35000,
        ]);
    }

    public function test_admin_dapat_mengubah_status_aktif_paket(): void
    {
        $this->assertTrue($this->paketSolo->is_aktif);

        $response = $this->actingAs($this->admin)->patch(route('admin.paket-bundling.toggle', $this->paketSolo));

        $response->assertSessionHas('status');
        $this->assertFalse($this->paketSolo->fresh()->is_aktif);
    }

    public function test_admin_dapat_mengupdate_paket_bundling(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.paket-bundling.update', $this->paketSolo), [
            'nomor_paket' => 1,
            'kode_paket' => 'PKT-01-REV',
            'nama' => 'Solo Backpacker Pro Updated',
            'kapasitas_orang' => '1 Orang',
            'deskripsi' => 'Deskripsi paket yang telah diupdate.',
            'harga_paket_per_hari' => 42000,
            'is_aktif' => 1,
            'items' => [
                [
                    'alat_id' => $this->alatTenda->id,
                    'jumlah' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.paket-bundling.index'));
        $this->assertDatabaseHas('paket_bundling', [
            'id' => $this->paketSolo->id,
            'kode_paket' => 'PKT-01-REV',
            'nama' => 'Solo Backpacker Pro Updated',
            'harga_paket_per_hari' => 42000,
            'harga_asli_per_hari' => 70000, // 2x 35.000
        ]);
    }

    public function test_admin_dapat_menghapus_paket_bundling(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.paket-bundling.destroy', $this->paketSolo));

        $response->assertRedirect(route('admin.paket-bundling.index'));
        $this->assertDatabaseMissing('paket_bundling', [
            'id' => $this->paketSolo->id,
        ]);
    }

    public function test_beranda_menampilkan_seksi_paket_bundling_dan_tombol_keranjang(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertOk();
        $response->assertSee('Paket Bundling Outdoor');
        $response->assertSee('Solo Backpacker Explorer');
        $response->assertSee(route('keranjang.index'));
    }
}
