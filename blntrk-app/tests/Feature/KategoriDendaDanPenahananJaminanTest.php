<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Jaminan;
use App\Models\KategoriAlat;
use App\Models\KategoriDenda;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Role;
use App\Models\User;
use App\Services\PengembalianService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KategoriDendaDanPenahananJaminanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $userPelanggan;
    private Pelanggan $pelanggan;
    private Alat $alat;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'is_aktif' => true,
        ]);

        $this->userPelanggan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->userPelanggan->id,
            'nama_lengkap' => 'Penyewa Uji Denda',
            'no_hp' => '081234567890',
            'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create([
            'nama' => 'Peralatan Camping',
            'slug' => 'peralatan-camping',
            'deskripsi' => 'Alat camping outdoor',
        ]);

        $this->alat = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Dome 4P',
            'slug' => 'tenda-dome-4p',
            'harga_per_hari' => 50000,
            'harga_beli' => 450000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        KategoriDenda::seedDefault();
    }

    public function test_admin_dapat_melihat_daftar_kategori_denda(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.kategori-denda.index'));

        $response->assertOk();
        $response->assertSee('Kelola Kategori Denda');
        $response->assertSee('Keterlambatan Pengembalian');
        $response->assertSee('Kehilangan Alat');
    }

    public function test_admin_dapat_membuat_kategori_denda_baru(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.kategori-denda.store'), [
                'kode' => 'KOTOR_EKSTREM',
                'nama' => 'Pembersihan Lumpur Ekstrem',
                'jenis' => 'kerusakan',
                'tarif_standar' => 25000,
                'satuan' => 'unit',
                'deskripsi' => 'Dikenakan bila tenda dikembalikan berlumpur tebal dan butuh deep wash.',
                'is_aktif' => '1',
            ]);

        $response->assertRedirect(route('admin.kategori-denda.index'));
        $this->assertDatabaseHas('kategori_denda', [
            'kode' => 'KOTOR_EKSTREM',
            'nama' => 'Pembersihan Lumpur Ekstrem',
            'tarif_standar' => 25000,
        ]);
    }

    protected function bikinPesananDisewa(string $catatan = 'Uji denda'): Pesanan
    {
        $pesananService = app(\App\Services\PesananService::class);
        $pesanan = $pesananService->buat($this->pelanggan, $this->userPelanggan, [
            'tanggal_mulai' => Carbon::today()->subDays(2)->toDateString(),
            'tanggal_selesai' => Carbon::yesterday()->toDateString(),
            'items' => [
                ['alat_id' => $this->alat->id, 'jumlah' => 1],
            ],
            'catatan' => $catatan,
        ]);

        $pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $pesananService->ambil($pesanan, $this->admin, [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '3204123456780001',
        ]);

        return $pesanan->fresh(['detail.alat', 'jaminan']);
    }

    public function test_admin_dapat_menambahkan_denda_manual_ke_pesanan(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan 001');

        $kategoriHilang = KategoriDenda::where('kode', 'HILANG')->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pesanan.denda.tambah', $pesanan->id), [
                'kategori_denda_id' => $kategoriHilang->id,
                'jumlah' => 75000,
                'keterangan' => '2 unit frame aluminium patah/hilang',
            ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));
        $this->assertDatabaseHas('denda', [
            'pesanan_id' => $pesanan->id,
            'kategori_denda_id' => $kategoriHilang->id,
            'jumlah' => 75000,
            'status_bayar' => 'belum_lunas',
            'keterangan' => '2 unit frame aluminium patah/hilang',
        ]);
    }

    public function test_jaminan_ditahan_jika_ada_denda_yang_belum_lunas(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan 002');
        $jaminan = $pesanan->jaminan;

        // Tambah denda belum lunas
        $kategori = KategoriDenda::first();
        Denda::create([
            'pesanan_id' => $pesanan->id,
            'kategori_denda_id' => $kategori->id,
            'jenis' => 'kerusakan',
            'jumlah' => 50000,
            'keterangan' => 'Frame bengkok parah',
            'status_bayar' => 'belum_lunas',
        ]);

        // Coba kembalikan jaminan lewat endpoint admin
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pesanan.jaminan.kembali', $pesanan->id));

        $response->assertSessionHasErrors(['jaminan']);
        $this->assertDatabaseHas('jaminan', [
            'id' => $jaminan->id,
            'status' => 'dititipkan',
            'dikembalikan_pada' => null,
        ]);
    }

    public function test_jaminan_berhasil_dikembalikan_setelah_denda_dilunasi(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan 003');
        $jaminan = $pesanan->jaminan;

        $kategori = KategoriDenda::first();
        $denda = Denda::create([
            'pesanan_id' => $pesanan->id,
            'kategori_denda_id' => $kategori->id,
            'jenis' => 'kehilangan',
            'jumlah' => 30000,
            'keterangan' => 'Pasak hilang',
            'status_bayar' => 'belum_lunas',
        ]);

        // Lunasi denda
        $this->actingAs($this->admin)
            ->patch(route('admin.denda.lunas', $denda->id));

        $this->assertEquals('lunas', $denda->fresh()->status_bayar);

        // Sekarang coba kembalikan jaminan
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pesanan.jaminan.kembali', $pesanan->id));

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));
        $this->assertEquals('dikembalikan', $jaminan->fresh()->status);
        $this->assertNotNull($jaminan->fresh()->dikembalikan_pada);
    }

    public function test_pelanggan_dapat_melihat_informasi_denda_dan_peringatan_jaminan_ditahan(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan 004');

        $kategori = KategoriDenda::where('kode', 'HILANG')->first();
        Denda::create([
            'pesanan_id' => $pesanan->id,
            'kategori_denda_id' => $kategori->id,
            'jenis' => 'kehilangan',
            'jumlah' => 45000,
            'keterangan' => 'Kehilangan pasak aluminium 3 pcs',
            'status_bayar' => 'belum_lunas',
        ]);

        $response = $this->actingAs($this->userPelanggan)
            ->get(route('pesanan.show', $pesanan->no_resi));

        $response->assertOk();
        $response->assertSee('Kartu Identitas Jaminan Anda Ditahan Sementara');
        $response->assertSee('Kehilangan pasak aluminium 3 pcs');
        $response->assertSee('DITAHAN (DENDA)');
        $response->assertSee('Rp45.000');
    }

    public function test_proses_pengembalian_dengan_barang_hilang_mencatat_denda_dan_menahan_jaminan(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan Cek Kembali');
        $detail = $pesanan->detail->first();
        $kategoriHilang = KategoriDenda::where('kode', 'HILANG')->first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
                'dikembalikan_pada' => Carbon::yesterday()->setTime(14, 0)->format('Y-m-d\TH:i'),
                'baris' => [
                    $detail->id => [
                        'baik' => 0,
                        'rusak_ringan' => 0,
                        'rusak_berat' => 0,
                        'hilang' => 1,
                        'kategori_denda_id' => $kategoriHilang->id,
                        'biaya' => 60000,
                        'catatan' => 'Unit tenda hilang tidak kembali',
                    ],
                ],
                'catatan' => 'Pengecekan fisik saat kembali',
            ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));

        // Pastikan denda kehilangan tercatat
        $this->assertDatabaseHas('denda', [
            'pesanan_id' => $pesanan->id,
            'kategori_denda_id' => $kategoriHilang->id,
            'jenis' => 'kehilangan',
            'jumlah' => 60000,
            'status_bayar' => 'belum_lunas',
        ]);

        // Pastikan jaminan ditolak untuk dikembalikan karena ada denda kehilangan yang belum lunas
        $responseJaminan = $this->actingAs($this->admin)
            ->patch(route('admin.pesanan.jaminan.kembali', $pesanan->id));

        $responseJaminan->assertSessionHasErrors(['jaminan']);
    }

    public function test_proses_pengembalian_dengan_barang_hilang_otomatis_hitung_denda_sesuai_harga_beli(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan Auto Harga Beli');
        $detail = $pesanan->detail->first();

        // Admin mencatat 1 unit hilang tanpa mengisi nominal biaya manual (0)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
                'dikembalikan_pada' => Carbon::yesterday()->setTime(14, 0)->format('Y-m-d\TH:i'),
                'baris' => [
                    $detail->id => [
                        'baik' => 0,
                        'rusak_ringan' => 0,
                        'rusak_berat' => 0,
                        'hilang' => 1,
                        'kategori_denda_id' => '',
                        'biaya' => 0,
                        'catatan' => 'Unit hilang tidak kembali',
                    ],
                ],
                'catatan' => 'Pengecekan alat hilang',
            ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));

        // Denda otomatis dihitung sebesar harga_beli alat (450.000)
        $this->assertDatabaseHas('denda', [
            'pesanan_id' => $pesanan->id,
            'jenis' => 'kehilangan',
            'jumlah' => 450000,
            'status_bayar' => 'belum_lunas',
        ]);
    }

    public function test_admin_dapat_menyimpan_dan_mengubah_harga_beli_alat(): void
    {
        $kategori = KategoriAlat::first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.alat.store'), [
                'kategori_id' => $kategori->id,
                'kode_alat' => 'TEST-BELI-01',
                'nama' => 'Kompor Portable Ultralight',
                'harga_per_hari' => 15000,
                'harga_beli' => 175000,
                'stok_total' => 10,
                'stok_perawatan' => 0,
                'is_aktif' => '1',
            ]);

        $response->assertRedirect(route('admin.alat.index'));
        $this->assertDatabaseHas('alat', [
            'kode_alat' => 'TEST-BELI-01',
            'harga_per_hari' => 15000,
            'harga_beli' => 175000,
        ]);
    }

    public function test_beranda_menampilkan_informasi_ketentuan_kategori_denda(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertOk();
        $response->assertSee('Transparansi &amp; Kategori Denda', false);
        $response->assertSee('Telat Pengembalian');
        $response->assertSee('Ganti Rugi Kehilangan Alat');
        $response->assertSee('Sesuai Harga Beli Alat');
        $response->assertSee('Kerusakan Ringan / Berat');
        $response->assertSee('Ketentuan Penahanan Kartu Identitas Jaminan');
    }

    public function test_halaman_denda_admin_menampilkan_penyewa_terlambat_dan_estimasi_denda_berjalan(): void
    {
        // Buat pesanan yang terlambat 2 hari lalu
        $pesanan = $this->bikinPesananDisewa('Pesanan Terlambat Uji');
        $pesanan->update([
            'status' => StatusPesanan::Terlambat,
            'tanggal_mulai' => Carbon::now()->subDays(4)->toDateString(),
            'tanggal_selesai' => Carbon::now()->subDays(2)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.denda.index'));

        $response->assertOk();
        $response->assertSee('Penyewa Sedang Terlambat');
        $response->assertSee($pesanan->no_resi);
        $response->assertSee('Penyewa Uji Denda');
        $response->assertSee('Terlambat');
        $response->assertSee('Proses Kembali');
        $response->assertSee('Total Denda Tertunggak');
    }

    public function test_halaman_pesanan_pelanggan_otomatis_menampilkan_rincian_denda_saat_terlambat(): void
    {
        // Buat pesanan berstatus terlambat milik pelanggan
        $pesanan = $this->bikinPesananDisewa('Pesanan Telat Pelanggan');
        $pesanan->update([
            'status' => StatusPesanan::Terlambat,
            'tanggal_mulai' => Carbon::now()->subDays(3)->toDateString(),
            'tanggal_selesai' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        $response = $this->actingAs($this->userPelanggan)
            ->get(route('pesanan.show', $pesanan->no_resi));

        $response->assertOk();
        $response->assertSee('Peringatan: Masa Sewa Melewati Batas Waktu');
        $response->assertSee('Denda Berjalan');
        $response->assertSee('Durasi Keterlambatan');
        $response->assertSee('Denda Keterlambatan Berjalan (Otomatis)');
        $response->assertSee('Kartu Identitas Jaminan Anda Ditahan Sementara');
    }

    public function test_halaman_pesanan_admin_menampilkan_denda_berjalan_saat_terlambat(): void
    {
        $pesanan = $this->bikinPesananDisewa('Pesanan Telat Panel Admin');
        $pesanan->update([
            'status' => StatusPesanan::Terlambat,
            'tanggal_mulai' => Carbon::now()->subDays(3)->toDateString(),
            'tanggal_selesai' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pesanan.show', $pesanan->id));

        $response->assertOk();
        $response->assertSee('Denda Keterlambatan Sedang Berjalan (Otomatis)');
        $response->assertSee('Peringatan Terlambat WA');
        $response->assertSee('Jaminan Terkunci (Denda Belum Lunas)');
    }
}
