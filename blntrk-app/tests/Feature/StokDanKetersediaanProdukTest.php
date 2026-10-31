<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
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

class StokDanKetersediaanProdukTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pelangganUser;

    protected Pelanggan $pelanggan;

    protected KategoriAlat $kategoriTenda;

    protected Alat $alatTenda;

    protected Alat $alatKompor;

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
            'nama' => 'Budi Pendaki',
            'email' => 'budi@blntrk.test',
            'password' => bcrypt('password'),
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id,
            'nama_lengkap' => 'Budi Pendaki',
            'no_hp' => '081234567899',
            'alamat' => 'Subang Kota',
        ]);

        $this->kategoriTenda = KategoriAlat::create([
            'nama' => 'Tenda Camping',
            'slug' => 'tenda-camping',
        ]);

        $this->alatTenda = Alat::create([
            'kategori_id' => $this->kategoriTenda->id,
            'kode_alat' => 'TND-TEST',
            'nama' => 'Tenda Dome Uji 4P',
            'varian' => 'Kapasitas 4 Orang',
            'deskripsi' => 'Tenda uji coba ketersediaan stok.',
            'harga_per_hari' => 40000,
            'harga_beli' => 450000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->alatKompor = Alat::create([
            'kategori_id' => $this->kategoriTenda->id,
            'kode_alat' => 'KMP-TEST',
            'nama' => 'Kompor Portable Uji',
            'varian' => 'Windproof',
            'deskripsi' => 'Kompor uji coba.',
            'harga_per_hari' => 15000,
            'harga_beli' => 120000,
            'stok_total' => 2,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);
    }

    public function test_katalog_menampilkan_stok_tersedia_secara_lengkap(): void
    {
        $response = $this->get(route('katalog.index'));

        $response->assertOk();
        $response->assertSee('Tenda Dome Uji 4P');
        $response->assertSee('Tersedia 5');
        $response->assertSee('Kompor Portable Uji');
        $response->assertSee('Tersedia 2');
    }

    public function test_stok_berkurang_saat_alat_disewa(): void
    {
        $this->assertEquals(5, $this->alatTenda->stok_tersedia);

        // Buat pesanan sewa 3 unit tenda
        $response = $this->actingAs($this->pelangganUser)->post(route('pesanan.store'), [
            'tanggal_mulai' => Carbon::tomorrow()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'items' => [
                [
                    'alat_id' => $this->alatTenda->id,
                    'jumlah' => 3,
                ],
            ],
            'catatan' => 'Sewa camping ceria',
        ]);

        $response->assertRedirect();

        // Stok tersedia sekarang harus berkurang menjadi 2 unit
        $this->assertEquals(2, $this->alatTenda->fresh()->stok_tersedia);

        // Di katalog publik juga harus menampilkan sisa 2 unit
        $responseKatalog = $this->get(route('katalog.index'));
        $responseKatalog->assertSee('Tersedia 2');
    }

    public function test_stok_otomatis_restock_saat_alat_dikembalikan(): void
    {
        // Buat pesanan 2 unit kompor
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-TEST-001',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->admin->id,
            'tanggal_mulai' => Carbon::now()->subDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::now()->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => Carbon::now()->setTime(12, 0, 0),
            'batas_ambil' => Carbon::now()->subDays(2)->setTime(18, 0, 0),
            'status' => StatusPesanan::Disewa,
            'total_sewa' => 30000,
        ]);

        $detail = PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $this->alatKompor->id,
            'jumlah' => 2,
            'harga_per_hari' => 15000,
            'subtotal' => 30000,
        ]);

        // Karena disewa 2 dari 2 total unit, sisa stok saat ini adalah 0
        $this->assertEquals(0, $this->alatKompor->fresh()->stok_tersedia);

        // Admin memproses pengembalian alat (semua kembali dalam kondisi baik)
        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => Carbon::now()->toDateTimeString(),
            'baris' => [
                $detail->id => [
                    'baik' => 2,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                    'hilang' => 0,
                    'biaya' => 0,
                ],
            ],
            'catatan' => 'Semua lengkap dan baik',
        ]);

        $response->assertRedirect();
        $this->assertEquals(StatusPesanan::Dikembalikan, $pesanan->fresh()->status);

        // Stok harus otomatis restock kembali penuh menjadi 2 unit!
        $this->assertEquals(2, $this->alatKompor->fresh()->stok_tersedia);

        // Di katalog publik kompor kembali muncul sebagai siap sewa
        $responseKatalog = $this->get(route('katalog.index'));
        $responseKatalog->assertSee('Tersedia 2');
    }

    public function test_alat_yang_stoknya_habis_tidak_bisa_dipesan(): void
    {
        // Habiskan stok kompor dengan pesanan aktif (2 unit)
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-HABIS-001',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->admin->id,
            'tanggal_mulai' => Carbon::tomorrow()->toDateString(),
            'tanggal_selesai' => Carbon::tomorrow()->addDay()->toDateString(),
            'durasi_hari' => 1,
            'jatuh_tempo' => Carbon::tomorrow()->addDay()->setTime(12, 0, 0),
            'batas_ambil' => Carbon::tomorrow()->setTime(18, 0, 0),
            'status' => StatusPesanan::Menunggu,
            'total_sewa' => 30000,
        ]);

        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $this->alatKompor->id,
            'jumlah' => 2,
            'harga_per_hari' => 15000,
            'subtotal' => 30000,
        ]);

        $this->assertEquals(0, $this->alatKompor->fresh()->stok_tersedia);

        // Pelanggan mencoba memesan kompor yang sudah habis
        $response = $this->actingAs($this->pelangganUser)->post(route('pesanan.store'), [
            'tanggal_mulai' => Carbon::tomorrow()->format('Y-m-d'),
            'tanggal_selesai' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'items' => [
                [
                    'alat_id' => $this->alatKompor->id,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_katalog_menampilkan_seluruh_produk_dalam_satu_halaman_dan_memisahkan_barang_out_of_stock_di_bawah(): void
    {
        // Buat 1 alat yang habis stoknya
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-HABIS-002',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->admin->id,
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::tomorrow()->toDateString(),
            'durasi_hari' => 1,
            'jatuh_tempo' => Carbon::tomorrow()->setTime(12, 0, 0),
            'batas_ambil' => Carbon::today()->setTime(18, 0, 0),
            'status' => StatusPesanan::Disewa,
            'total_sewa' => 30000,
        ]);

        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $this->alatKompor->id,
            'jumlah' => 2,
            'harga_per_hari' => 15000,
            'subtotal' => 30000,
        ]);

        $response = $this->get(route('katalog.index'));

        $response->assertOk();
        // Tenda harus ada di bagian siap sewa
        $response->assertSee('Perlengkapan Siap Sewa');
        $response->assertSee('Tenda Dome Uji 4P');
        $response->assertSee('Tersedia 5');

        // Kompor harus dipisahkan di bagian out of stock di bawah
        $response->assertSee('Perlengkapan Sedang Disewa (Out of Stock)');
        $response->assertSee('Kompor Portable Uji');
        $response->assertSee('Stok Habis (Sedang Disewa)');

        // Tidak ada pagination links dividing pages
        $response->assertDontSee('pagination');
    }
}
