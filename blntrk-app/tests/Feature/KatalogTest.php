<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    protected KategoriAlat $kategori;
    protected Alat $alat;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['nama' => 'admin', 'label' => 'Administrator']);
        Role::create(['nama' => 'pemilik', 'label' => 'Pemilik Toko']);
        Role::create(['nama' => 'pelanggan', 'label' => 'Pelanggan']);

        $this->user = User::create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Test',
            'email' => 'admin@blntrk.test',
            'password' => bcrypt('password'),
            'is_aktif' => true,
        ]);

        $this->kategori = KategoriAlat::create([
            'nama' => 'Tenda Dome',
            'slug' => 'tenda-dome',
        ]);

        $this->alat = Alat::create([
            'kategori_id' => $this->kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Eiger Guardian 4P',
            'varian' => 'Kapasitas 4 Orang Waterproof',
            'deskripsi' => 'Tenda dome double layer kokoh.',
            'harga_per_hari' => 45000,
            'stok_total' => 5,
            'stok_perawatan' => 1, // Stok siap = 4
            'is_aktif' => true,
        ]);
    }

    public function test_halaman_katalog_dapat_diakses_publik(): void
    {
        $response = $this->get(route('katalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Tenda Eiger Guardian 4P');
        $response->assertSee('Tenda Dome');
        $response->assertSee('Rp 45.000');
    }

    public function test_filter_kategori_dan_pencarian_alat(): void
    {
        // Buat alat kedua dengan kategori berbeda
        $katCarrier = KategoriAlat::create([
            'nama' => 'Tas Carrier',
            'slug' => 'tas-carrier',
        ]);

        $carrier = Alat::create([
            'kategori_id' => $katCarrier->id,
            'kode_alat' => 'CAR-001',
            'nama' => 'Carrier Osprey 60L',
            'harga_per_hari' => 50000,
            'stok_total' => 3,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        // Filter kategori tenda
        $respTenda = $this->get(route('katalog.index', ['kategori' => 'tenda-dome']));
        $respTenda->assertStatus(200);
        $respTenda->assertSee('Tenda Eiger Guardian 4P');
        $respTenda->assertDontSee('Carrier Osprey 60L');

        // Pencarian kata kunci Osprey
        $respSearch = $this->get(route('katalog.index', ['q' => 'Osprey']));
        $respSearch->assertStatus(200);
        $respSearch->assertSee('Carrier Osprey 60L');
        $respSearch->assertDontSee('Tenda Eiger Guardian 4P');
    }

    public function test_halaman_detail_alat_dapat_diakses(): void
    {
        $response = $this->get(route('katalog.show', $this->alat->id));

        $response->assertStatus(200);
        $response->assertSee('Tenda Eiger Guardian 4P');
        $response->assertSee('TND-001');
        $response->assertSee('Cek Jadwal Ketersediaan');
    }

    public function test_alat_nonaktif_menghasilkan_404(): void
    {
        $alatNonaktif = Alat::create([
            'kategori_id' => $this->kategori->id,
            'kode_alat' => 'TND-999',
            'nama' => 'Tenda Rusak Pensiun',
            'harga_per_hari' => 30000,
            'stok_total' => 2,
            'stok_perawatan' => 0,
            'is_aktif' => false,
        ]);

        $response = $this->get(route('katalog.show', $alatNonaktif->id));
        $response->assertStatus(404);
    }

    public function test_endpoint_ketersediaan_memberikan_json_akurat(): void
    {
        $mulai = Carbon::today()->addDays(2)->toDateString();
        $selesai = Carbon::today()->addDays(5)->toDateString();

        $response = $this->getJson(route('katalog.ketersediaan', [
            'alat' => $this->alat->id,
            'mulai' => $mulai,
            'selesai' => $selesai,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'sukses' => true,
            'alat_id' => $this->alat->id,
            'stok_total' => 5,
            'stok_perawatan' => 1,
            'tersedia' => 4, // 5 - 1 = 4
            'durasi_hari' => 3,
            'harga_per_hari' => 45000,
            'estimasi_per_unit' => 135000, // 45000 * 3
            'bisa_disewa' => true,
        ]);
    }

    public function test_endpoint_ketersediaan_validasi_tanggal_salah(): void
    {
        // Tanggal selesai sebelum tanggal mulai
        $mulai = Carbon::today()->addDays(5)->toDateString();
        $selesai = Carbon::today()->addDays(2)->toDateString();

        $response = $this->getJson(route('katalog.ketersediaan', [
            'alat' => $this->alat->id,
            'mulai' => $mulai,
            'selesai' => $selesai,
        ]));

        $response->assertStatus(422);
    }

    public function test_endpoint_ketersediaan_maksimal_14_hari(): void
    {
        $mulai = Carbon::today()->addDays(1)->toDateString();
        $selesai = Carbon::today()->addDays(18)->toDateString(); // 17 hari (>14 hari)

        $response = $this->getJson(route('katalog.ketersediaan', [
            'alat' => $this->alat->id,
            'mulai' => $mulai,
            'selesai' => $selesai,
        ]));

        $response->assertStatus(422);
        $response->assertJson([
            'sukses' => false,
            'pesan' => 'Maksimal durasi penyewaan adalah 14 hari.',
        ]);
    }
}
