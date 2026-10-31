<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\PaketBundling;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use App\Services\PesananService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi untuk revisi klien: tombol modal admin, form pengembalian, dan paket bundling.
 */
class RevisiKlienTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Pelanggan $pelanggan;

    protected Alat $alat;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);

        $this->admin = User::factory()->create(['role_id' => $roleAdmin->id, 'is_aktif' => true]);
        $userPelanggan = User::factory()->create(['role_id' => $rolePelanggan->id, 'is_aktif' => true]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $userPelanggan->id,
            'nama_lengkap' => 'Pelanggan Uji',
            'no_hp' => '08123456789',
            'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Camping', 'slug' => 'camping']);
        $this->alat = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-01',
            'nama' => 'Tenda Dome',
            'harga_per_hari' => 35000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);
    }

    private function buatPesanan()
    {
        return app(PesananService::class)->buat($this->pelanggan, $this->admin, [
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->addDays(2)->toDateString(),
            'items' => [['alat_id' => $this->alat->id, 'jumlah' => 1]],
        ]);
    }

    /**
     * Revisi 2: modal Catat Pembayaran/Batalkan harus berada di dalam scope Alpine (x-data).
     * Div penutup berlebih sebelumnya menutup root x-data sebelum modal.
     */
    public function test_modal_pembayaran_dan_batal_berada_di_dalam_scope_alpine(): void
    {
        $pesanan = $this->buatPesanan();

        $html = $this->actingAs($this->admin)
            ->get(route('admin.pesanan.show', $pesanan->id))
            ->assertOk()
            ->getContent();

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8"?>'.$html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        foreach (['showModalBayar', 'showModalBatal'] as $state) {
            $dalamRoot = $xpath->query(
                "//div[@x-show='{$state}'][ancestor::div[contains(@x-data, 'showModalBayar:')]]"
            );
            $this->assertSame(1, $dalamRoot->length, "Modal {$state} berada di luar root x-data Alpine.");
        }
    }

    /**
     * Revisi 3: script Alpine form pengembalian harus dirender oleh layout (@stack).
     */
    public function test_form_pengembalian_memuat_script_alpine_dan_dapat_disimpan(): void
    {
        $pesanan = $this->buatPesanan();
        $service = app(PesananService::class);
        $service->bayar($pesanan, $this->admin, ['jumlah' => $pesanan->total_sewa, 'metode' => 'tunai']);
        $service->ambil($pesanan->fresh(), $this->admin, ['jenis_jaminan' => 'ktp', 'nomor_identitas' => '3204123456780001']);

        $this->actingAs($this->admin)
            ->get(route('admin.pesanan.pengembalian.create', $pesanan->id))
            ->assertOk()
            ->assertSee('function pengembalianForm', false);

        $detail = $pesanan->detail()->first();

        $this->actingAs($this->admin)
            ->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
                'dikembalikan_pada' => now()->format('Y-m-d\TH:i'),
                'catatan' => 'uji',
                'baris' => [$detail->id => [
                    'baik' => 1, 'rusak_ringan' => 0, 'rusak_berat' => 0, 'hilang' => 0,
                    'kategori_denda_id' => '', 'biaya' => 0, 'catatan' => '',
                ]],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.pesanan.show', $pesanan->id));

        $this->assertSame('dikembalikan', $pesanan->fresh()->status->value);
    }

    /**
     * Revisi 5: halaman tambah/edit paket bundling dapat dikompilasi dan simpan berhasil.
     */
    public function test_paket_bundling_create_edit_dan_simpan_berhasil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.paket-bundling.create'))
            ->assertOk()
            ->assertSee('function bundlingForm', false);

        $this->actingAs($this->admin)
            ->post(route('admin.paket-bundling.store'), [
                'nomor_paket' => 1,
                'kode_paket' => 'pkt-t1',
                'nama' => 'Paket Uji',
                'kapasitas_orang' => '2 Orang',
                'harga_paket_per_hari' => 30000,
                'is_aktif' => 1,
                'items' => [['alat_id' => $this->alat->id, 'jumlah' => 2]],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.paket-bundling.index'));

        $paket = PaketBundling::where('kode_paket', 'PKT-T1')->firstOrFail();
        $this->assertEquals(70000, $paket->harga_asli_per_hari);

        $this->actingAs($this->admin)
            ->get(route('admin.paket-bundling.edit', $paket->id))
            ->assertOk()
            ->assertSee('function bundlingEditForm', false);
    }
}
