<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeedTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        if (! Role::exists()) {
            $this->seed();
        }
    }

    public function test_roles_terbentuk_dan_sesuai(): void
    {
        $this->assertEquals(3, Role::count());
        $this->assertDatabaseHas('roles', ['nama' => 'pemilik']);
        $this->assertDatabaseHas('roles', ['nama' => 'admin']);
        $this->assertDatabaseHas('roles', ['nama' => 'pelanggan']);
    }

    public function test_users_dan_pelanggan_terhubung(): void
    {
        $pemilik = User::where('email', 'pemilik@blntrk.test')->first();
        $this->assertNotNull($pemilik);
        $this->assertTrue($pemilik->punyaRole('pemilik'));

        $budi = User::where('email', 'budi@blntrk.test')->first();
        $this->assertNotNull($budi);
        $this->assertNotNull($budi->pelanggan);
        $this->assertEquals('Budi Setiawan', $budi->pelanggan->nama_lengkap);

        // Pelanggan walk-in
        $walkIn = Pelanggan::whereNull('user_id')->first();
        $this->assertNotNull($walkIn);
        $this->assertTrue($walkIn->is_walk_in);
    }

    public function test_kategori_dan_alat_terhubung(): void
    {
        $this->assertEquals(8, KategoriAlat::count());
        $this->assertEquals(23, Alat::count());

        $tenda = KategoriAlat::where('nama', 'Tenda')->first();
        $this->assertNotNull($tenda);
        $this->assertGreaterThan(0, $tenda->alat()->count());
    }

    public function test_pengaturan_bisa_diakses(): void
    {
        $this->assertEquals('12:00', Pengaturan::ambil('jam_kembali'));
        $this->assertEquals('100', Pengaturan::ambil('persen_denda_harian'));
        $this->assertEquals('BLNTRK Outdoor', Pengaturan::ambil('nama_toko'));
    }
}
