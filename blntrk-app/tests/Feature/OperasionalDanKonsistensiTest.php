<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\KategoriDenda;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TransaksiDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class OperasionalDanKonsistensiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pelangganUser;

    protected Pelanggan $pelanggan;

    protected Alat $alat;

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
        $this->alat = Alat::create([
            'kategori_id' => $kategori->id, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'varian' => 'D2',
            'harga_per_hari' => 40000, 'stok_total' => 10, 'stok_perawatan' => 0, 'is_aktif' => true,
        ]);
    }

    private function pesananTerlambatBelumDisinkron(): Pesanan
    {
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-000000-0001',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->pelangganUser->id,
            'tanggal_mulai' => today()->subDays(4),
            'tanggal_selesai' => today()->subDays(2),
            'durasi_hari' => 2,
            'jatuh_tempo' => now()->subDays(2),
            'batas_ambil' => now()->subDays(4),
            'status' => StatusPesanan::Disewa,
            'total_sewa' => 80000,
        ]);
        $pesanan->detail()->create([
            'alat_id' => $this->alat->id, 'jumlah' => 1, 'harga_per_hari' => 40000, 'subtotal' => 80000,
        ]);

        return $pesanan;
    }

    public function test_membuka_halaman_tidak_mengubah_status_pesanan(): void
    {
        $pesanan = $this->pesananTerlambatBelumDisinkron();

        $this->actingAs($this->pelangganUser)->get(route('pesanan.show', $pesanan->no_resi))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.pesanan.show', $pesanan->id))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.denda.index'))->assertOk();

        $this->assertSame(StatusPesanan::Disewa, $pesanan->fresh()->status);
    }

    public function test_perintah_terjadwal_menandai_pesanan_terlambat(): void
    {
        $pesanan = $this->pesananTerlambatBelumDisinkron();

        $this->artisan('pesanan:perbarui-status')->assertSuccessful();

        $this->assertSame(StatusPesanan::Terlambat, $pesanan->fresh()->status);
    }

    public function test_sinkronisasi_status_terdaftar_di_scheduler(): void
    {
        $terdaftar = collect(Schedule::events())
            ->contains(fn ($e) => str_contains($e->command, 'pesanan:perbarui-status'));

        $this->assertTrue($terdaftar);
    }

    public function test_alias_url_lama_dialihkan_ke_rute_kanonis(): void
    {
        $this->get('/alat')->assertRedirect('/katalog')->assertStatus(301);
        $this->get('/alat/'.$this->alat->id)->assertRedirect(route('katalog.show', $this->alat->id));
        $this->get('/alat/'.$this->alat->id.'/ketersediaan?mulai=2030-01-01')
            ->assertRedirect(route('katalog.ketersediaan', ['alat' => $this->alat->id, 'mulai' => '2030-01-01']));
        $this->actingAs($this->pelangganUser)->get('/profil')->assertRedirect('/profile');
        $this->actingAs($this->pelangganUser)->get('/sesi')->assertRedirect('/sesi-login');
    }

    public function test_denda_manual_melebihi_batas_wajar_ditolak(): void
    {
        $pesanan = $this->pesananTerlambatBelumDisinkron();

        $this->actingAs($this->admin)->post(route('admin.pesanan.denda.tambah', $pesanan), [
            'jumlah' => 99999999, 'keterangan' => 'Salah ketik',
        ])->assertSessionHasErrors('jumlah');

        $this->actingAs($this->admin)->post(route('admin.pesanan.denda.tambah', $pesanan), [
            'jumlah' => 150000, 'keterangan' => 'Tenda sobek',
        ])->assertSessionHasNoErrors();
        $this->assertSame(1, $pesanan->denda()->count());
    }

    public function test_seed_default_tidak_menimpa_tarif_yang_diubah_pemilik(): void
    {
        KategoriDenda::seedDefault();
        KategoriDenda::where('kode', 'RUSAK_BERAT')->update(['tarif_standar' => 123000]);

        KategoriDenda::seedDefault();

        $this->assertEquals(123000, KategoriDenda::where('kode', 'RUSAK_BERAT')->value('tarif_standar'));
    }

    public function test_seeder_demo_idempoten_dan_produksi_tanpa_seed_demo_tidak_memuat_katalog(): void
    {
        $this->seed();
        $this->seed(TransaksiDemoSeeder::class);
        $jumlah = Pesanan::count();
        $this->assertGreaterThan(0, $jumlah);

        $this->seed(TransaksiDemoSeeder::class);
        $this->assertSame($jumlah, Pesanan::count());
    }

    public function test_seeder_produksi_hanya_memuat_data_dasar_kecuali_seed_demo_aktif(): void
    {
        $this->app['env'] = 'production';
        putenv('SEED_DEFAULT_PASSWORD=Rahasia-Prod-123');
        putenv('SEED_DEMO=false');
        $_ENV['SEED_DEFAULT_PASSWORD'] = $_SERVER['SEED_DEFAULT_PASSWORD'] = 'Rahasia-Prod-123';
        $_ENV['SEED_DEMO'] = $_SERVER['SEED_DEMO'] = 'false';
        Alat::query()->delete();

        try {
            // Dipanggil langsung karena `db:seed` meminta --force di lingkungan production
            app(DatabaseSeeder::class)->setContainer($this->app)->run();
            $this->assertSame(0, Alat::count());
            $this->assertGreaterThan(0, KategoriDenda::count());
        } finally {
            putenv('SEED_DEFAULT_PASSWORD');
            putenv('SEED_DEMO');
            unset($_ENV['SEED_DEFAULT_PASSWORD'], $_SERVER['SEED_DEFAULT_PASSWORD'], $_ENV['SEED_DEMO'], $_SERVER['SEED_DEMO']);
            $this->app['env'] = 'testing';
        }
    }
}
