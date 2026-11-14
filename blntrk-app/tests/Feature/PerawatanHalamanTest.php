<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\RiwayatPerawatan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerawatanHalamanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pelanggan;
    protected Alat $alat;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Administrator']);
        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Gudang',
            'email' => 'gudang@test.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Pelanggan Biasa',
            'email' => 'user@test.com',
            'is_aktif' => true,
        ]);

        $kategori = KategoriAlat::create([
            'nama' => 'Tenda Outdoor',
            'slug' => 'tenda-outdoor',
        ]);

        $this->alat = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-TEST-01',
            'nama' => 'Tenda Arpenaz 4.1',
            'harga_per_hari' => 50000,
            'stok_total' => 6,
            'stok_perawatan' => 2,
            'is_aktif' => true,
        ]);
    }

    public function test_halaman_perawatan_dapat_diakses_oleh_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.perawatan.index'));

        $response->assertOk();
        $response->assertViewIs('admin.perawatan.index');
        $response->assertSee('Perawatan & Servis Alat', false);
        $response->assertSee('Tenda Arpenaz 4.1');
        $response->assertSee('2 Unit');
        $response->assertSee('Total Dalam Perawatan');
        $response->assertSee('Riwayat Perawatan');
    }

    public function test_halaman_perawatan_tidak_dapat_diakses_oleh_pelanggan(): void
    {
        $response = $this->actingAs($this->pelanggan)->get(route('admin.perawatan.index'));

        $response->assertForbidden();
    }

    public function test_admin_dapat_menandai_unit_perawatan_selesai(): void
    {
        $this->assertEquals(2, $this->alat->fresh()->stok_perawatan);

        $response = $this->actingAs($this->admin)->patch(route('admin.perawatan.selesai', $this->alat), [
            'jumlah' => 1,
            'catatan' => 'Selesai dicuci dan dipasangi pasak baru',
        ]);

        $response->assertRedirect(route('admin.perawatan.index'));
        $response->assertSessionHas('status');

        $this->assertEquals(1, $this->alat->fresh()->stok_perawatan);

        $this->assertDatabaseHas('riwayat_perawatan', [
            'alat_id' => $this->alat->id,
            'jenis' => RiwayatPerawatan::SELESAI,
            'jumlah' => 1,
            'keterangan' => 'Selesai dicuci dan dipasangi pasak baru',
        ]);
    }

    public function test_admin_dapat_memindahkan_unit_ke_perawatan_manual(): void
    {
        $this->assertEquals(2, $this->alat->fresh()->stok_perawatan);

        $response = $this->actingAs($this->admin)->patch(route('admin.perawatan.tambah', $this->alat), [
            'jumlah' => 2,
            'keterangan' => 'Resleting pintu depan sobek',
        ]);

        $response->assertRedirect(route('admin.perawatan.index'));
        $response->assertSessionHas('status');

        $this->assertEquals(4, $this->alat->fresh()->stok_perawatan);

        $this->assertDatabaseHas('riwayat_perawatan', [
            'alat_id' => $this->alat->id,
            'jenis' => RiwayatPerawatan::MASUK,
            'jumlah' => 2,
            'keterangan' => 'Resleting pintu depan sobek',
        ]);
    }
}
