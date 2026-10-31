<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\PaketBundling;
use App\Models\PaketBundlingDetail;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\DendaService;
use App\Services\KeranjangService;
use App\Services\PaketBundlingService;
use App\Services\PesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiskonPaketBundlingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pelangganUser;

    protected Pelanggan $pelanggan;

    protected Alat $tenda;

    protected Alat $sleepingBag;

    protected PaketBundling $paket;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['nama' => 'admin', 'label' => 'Administrator']);
        $rolePelanggan = Role::create(['nama' => 'pelanggan', 'label' => 'Pelanggan']);

        $this->admin = User::create([
            'role_id' => $roleAdmin->id, 'nama' => 'Admin', 'email' => 'admin@blntrk.test',
            'password' => bcrypt('password'), 'is_aktif' => true,
        ]);
        $this->pelangganUser = User::create([
            'role_id' => $rolePelanggan->id, 'nama' => 'Pelanggan', 'email' => 'p@blntrk.test',
            'password' => bcrypt('password'), 'is_aktif' => true,
        ]);
        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id, 'nama_lengkap' => 'Pelanggan',
            'no_hp' => '081234567890', 'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Umum', 'slug' => 'umum']);
        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'varian' => 'D2',
            'harga_per_hari' => 40000, 'stok_total' => 10, 'stok_perawatan' => 0, 'is_aktif' => true,
        ]);
        $this->sleepingBag = Alat::create([
            'kategori_id' => $kategori->id, 'kode_alat' => 'SLB', 'nama' => 'Sleeping Bag', 'varian' => 'Polar',
            'harga_per_hari' => 10000, 'stok_total' => 10, 'stok_perawatan' => 0, 'is_aktif' => true,
        ]);

        // Eceran: 1 tenda + 2 sleeping bag = 60.000/hari; harga paket 50.000/hari -> hemat 10.000/hari
        $this->paket = PaketBundling::create([
            'nomor_paket' => 1, 'kode_paket' => 'PKT-01', 'nama' => 'Paket Duo', 'slug' => 'paket-duo',
            'kapasitas_orang' => '2 Orang', 'harga_paket_per_hari' => 50000, 'harga_asli_per_hari' => 60000,
            'is_aktif' => true, 'urutan' => 1,
        ]);
        PaketBundlingDetail::create(['paket_bundling_id' => $this->paket->id, 'alat_id' => $this->tenda->id, 'jumlah' => 1]);
        PaketBundlingDetail::create(['paket_bundling_id' => $this->paket->id, 'alat_id' => $this->sleepingBag->id, 'jumlah' => 2]);
    }

    private function buatPesanan(array $items, int $hari = 2): Pesanan
    {
        return app(PesananService::class)->buat($this->pelanggan, $this->pelangganUser, [
            'tanggal_mulai' => today()->toDateString(),
            'tanggal_selesai' => today()->addDays($hari)->toDateString(),
            'items' => $items,
        ]);
    }

    public function test_pesanan_yang_memenuhi_paket_mendapat_diskon_otomatis(): void
    {
        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 2],
        ], 3);

        // Eceran 60.000 x 3 hari = 180.000, diskon 10.000 x 3 = 30.000
        $this->assertSame($this->paket->id, $pesanan->paket_bundling_id);
        $this->assertSame(1, $pesanan->jumlah_paket);
        $this->assertEquals(30000, $pesanan->diskon_paket);
        $this->assertEquals(150000, $pesanan->total_sewa);
    }

    public function test_pesanan_eceran_tanpa_memenuhi_paket_tidak_mendapat_diskon(): void
    {
        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 1],
        ]);

        $this->assertNull($pesanan->paket_bundling_id);
        $this->assertEquals(0, $pesanan->diskon_paket);
        $this->assertEquals(100000, $pesanan->total_sewa);
    }

    public function test_diskon_berlipat_untuk_dua_set_paket_dan_item_ekstra_tetap_eceran(): void
    {
        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 2],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 5],
        ], 1);

        // Eceran: 2x40.000 + 5x10.000 = 130.000; 2 set paket -> hemat 20.000
        $this->assertSame(2, $pesanan->jumlah_paket);
        $this->assertEquals(20000, $pesanan->diskon_paket);
        $this->assertEquals(110000, $pesanan->total_sewa);
    }

    public function test_paket_nonaktif_tidak_memberi_diskon(): void
    {
        $this->paket->update(['is_aktif' => false]);

        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 2],
        ]);

        $this->assertEquals(0, $pesanan->diskon_paket);
        $this->assertEquals(120000, $pesanan->total_sewa);
    }

    public function test_denda_keterlambatan_memakai_tarif_harian_setelah_diskon(): void
    {
        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 2],
        ], 2);

        $pesanan->load('detail');
        $this->assertEquals(50000, $pesanan->tarif_harian);

        $telat = app(DendaService::class)->hitungKeterlambatan($pesanan, $pesanan->jatuh_tempo->copy()->addDay());
        $this->assertSame(1, $telat['hari']);
        $this->assertEquals(50000, $telat['jumlah']);
    }

    public function test_hitung_diskon_service_mengabaikan_kuantitas_nol(): void
    {
        $hasil = app(PaketBundlingService::class)->hitungDiskon([$this->tenda->id => 0], 2);

        $this->assertNull($hasil['paket']);
        $this->assertEquals(0, $hasil['diskon']);
    }

    public function test_ringkasan_keranjang_dan_form_menampilkan_diskon(): void
    {
        $this->actingAs($this->pelangganUser)->post(route('keranjang.tambah-paket', $this->paket))
            ->assertRedirect(route('keranjang.index'));

        $this->actingAs($this->pelangganUser)
            ->get(route('keranjang.index', ['mulai' => today()->toDateString(), 'selesai' => today()->addDays(2)->toDateString()]))
            ->assertOk()
            ->assertSee('Diskon Paket Duo')
            ->assertSee('Rp100.000');

        $this->assertEquals(100000, app(KeranjangService::class)->grandTotal());

        $this->actingAs($this->pelangganUser)->get(route('pesanan.create'))
            ->assertOk()
            ->assertSee('daftarPaketBundling', false);
    }

    public function test_detail_dan_resi_pesanan_menampilkan_baris_diskon(): void
    {
        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 2],
        ]);

        $this->actingAs($this->pelangganUser)->get(route('pesanan.show', $pesanan->no_resi))
            ->assertOk()->assertSee('Diskon Paket Duo');
        $this->actingAs($this->pelangganUser)->get(route('pesanan.resi', $pesanan->no_resi))
            ->assertOk()->assertSee('Diskon Paket Duo');
        $this->actingAs($this->admin)->get(route('admin.pesanan.show', $pesanan->id))
            ->assertOk()->assertSee('Diskon Paket Duo');
    }

    public function test_pembayaran_harus_sesuai_total_setelah_diskon(): void
    {
        $pesanan = $this->buatPesanan([
            ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ['alat_id' => $this->sleepingBag->id, 'jumlah' => 2],
        ], 1);

        $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan), ['jumlah' => 60000, 'metode' => 'tunai'])
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan), ['jumlah' => 50000, 'metode' => 'tunai'])
            ->assertSessionHasNoErrors();
        $this->assertSame(StatusPesanan::Dibayar, $pesanan->fresh()->status);
    }

    public function test_paket_dengan_stok_kurang_dilaporkan_sebagai_paket_tidak_lengkap(): void
    {
        $this->sleepingBag->update(['stok_total' => 1]);

        $this->actingAs($this->pelangganUser)->post(route('keranjang.tambah-paket', $this->paket))
            ->assertRedirect(route('keranjang.index'))
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Sleeping Bag') && str_contains($m, 'Diskon paket tidak berlaku'));

        // Hanya tenda yang masuk; diskon paket tidak berlaku
        $this->assertSame([$this->tenda->id => 1], app(KeranjangService::class)->getRaw());
        $this->assertEquals(0, app(KeranjangService::class)->diskonPaket()['diskon']);
    }

    public function test_paket_dengan_semua_item_habis_ditolak(): void
    {
        $this->tenda->update(['stok_total' => 0]);
        $this->sleepingBag->update(['stok_total' => 0]);

        $this->actingAs($this->pelangganUser)->from('/paket-bundling')
            ->post(route('keranjang.tambah-paket', $this->paket))
            ->assertRedirect('/paket-bundling')
            ->assertSessionHas('error');
        $this->assertSame([], app(KeranjangService::class)->getRaw());
    }
}
