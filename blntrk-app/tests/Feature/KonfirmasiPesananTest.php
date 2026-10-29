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
use App\Services\KetersediaanService;
use App\Services\PesananService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KonfirmasiPesananTest extends TestCase
{
    use RefreshDatabase;

    protected User $userPelanggan;

    protected Pelanggan $pelanggan;

    protected User $admin;

    protected User $otherCustomer;

    protected Alat $tenda;

    protected PesananService $pesananService;

    protected KetersediaanService $ketersediaanService;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);

        $this->userPelanggan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Dwi Wahyu',
            'email' => 'dwi@test.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->userPelanggan->id,
            'nama_lengkap' => 'Dwi Wahyu',
            'no_hp' => '08123456789',
            'alamat' => 'Subang Kota',
        ]);

        $this->otherCustomer = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'User Lain',
            'email' => 'lain@test.com',
            'is_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Kasir',
            'email' => 'kasir@test.com',
            'is_aktif' => true,
        ]);

        $kategori = KategoriAlat::create([
            'nama' => 'Tenda',
            'slug' => 'tenda',
            'deskripsi' => 'Perlengkapan tenda',
        ]);

        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Dome 4P',
            'slug' => 'tenda-dome-4p',
            'harga_per_hari' => 50000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->pesananService = app(PesananService::class);
        $this->ketersediaanService = app(KetersediaanService::class);
    }

    protected function createPesananMenunggu(): Pesanan
    {
        return $this->pesananService->buat($this->pelanggan, $this->userPelanggan, [
            'tanggal_mulai' => Carbon::tomorrow()->toDateString(),
            'tanggal_selesai' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ],
            'catatan' => 'Uji pesanan',
        ]);
    }

    /**
     * BB-42: Catat pembayaran sesuai total sewa.
     */
    public function test_bb42_admin_dapat_mencatat_pembayaran_sesuai_total(): void
    {
        $pesanan = $this->createPesananMenunggu();
        $this->assertEquals(StatusPesanan::Menunggu, $pesanan->status);
        $totalSewa = (float) $pesanan->total_sewa;

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan->id), [
            'jumlah' => $totalSewa,
            'metode' => 'tunai',
            'catatan' => 'Dibayar tunai di kasir',
        ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Dibayar, $pesanan->status);
        $this->assertEquals($this->admin->id, $pesanan->diproses_oleh);

        $this->assertDatabaseHas('pembayaran', [
            'pesanan_id' => $pesanan->id,
            'jumlah' => $totalSewa,
            'metode' => MetodePembayaran::Tunai->value,
            'dicatat_oleh' => $this->admin->id,
        ]);
    }

    /**
     * BB-43: Pembayaran dengan jumlah tidak sesuai / kurang ditolak.
     */
    public function test_bb43_pembayaran_dengan_jumlah_kurang_ditolak(): void
    {
        $pesanan = $this->createPesananMenunggu();
        $totalSewa = (float) $pesanan->total_sewa;

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan->id), [
            'jumlah' => $totalSewa - 10000, // Jumlah kurang
            'metode' => 'tunai',
        ]);

        $response->assertSessionHasErrors('jumlah');
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Menunggu, $pesanan->status);
        $this->assertDatabaseMissing('pembayaran', [
            'pesanan_id' => $pesanan->id,
        ]);
    }

    /**
     * BB-44: Konfirmasi pengambilan valid dengan jaminan identitas fisik.
     */
    public function test_bb44_admin_dapat_konfirmasi_pengambilan_dengan_jaminan_valid(): void
    {
        $pesanan = $this->createPesananMenunggu();

        // Lakukan pembayaran terlebih dahulu
        $this->pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $pesanan->id), [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '3204123456789999',
            'keterangan' => 'KTP fisik asli diserahkan',
        ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Disewa, $pesanan->status);
        $this->assertNotNull($pesanan->diambil_pada);

        // Pastikan hanya 4 digit terakhir yang disimpan (NF09)
        $this->assertDatabaseHas('jaminan', [
            'pesanan_id' => $pesanan->id,
            'jenis' => 'ktp',
            'nomor_identitas_samar' => '****9999',
            'status' => 'dititipkan',
            'diterima_oleh' => $this->admin->id,
        ]);
    }

    /**
     * BB-45: Pengambilan tanpa pembayaran (status masih Menunggu) ditolak.
     */
    public function test_bb45_pengambilan_saat_status_menunggu_ditolak(): void
    {
        $pesanan = $this->createPesananMenunggu();

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $pesanan->id), [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '3204123456789999',
        ]);

        $response->assertSessionHasErrors('status');
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Menunggu, $pesanan->status);
        $this->assertNull($pesanan->diambil_pada);
    }

    /**
     * BB-46: Pengambilan dengan nomor identitas kosong ditolak validasi.
     */
    public function test_bb46_pengambilan_dengan_nomor_identitas_kosong_ditolak(): void
    {
        $pesanan = $this->createPesananMenunggu();

        $this->pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $pesanan->id), [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '', // Kosong
        ]);

        $response->assertSessionHasErrors('nomor_identitas');
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Dibayar, $pesanan->status);
    }

    /**
     * BB-47: Pelanggan membatalkan pesanan berstatus Menunggu dengan alasan.
     */
    public function test_bb47_pelanggan_dapat_membatalkan_pesanan_menunggu_dan_stok_terbebas(): void
    {
        $mulai = Carbon::tomorrow();
        $selesai = Carbon::tomorrow()->addDays(2);

        $stokAwal = $this->ketersediaanService->tersedia($this->tenda, $mulai, $selesai);

        $pesanan = $this->createPesananMenunggu();

        // Stok berkurang 1
        $this->assertEquals($stokAwal - 1, $this->ketersediaanService->tersedia($this->tenda, $mulai, $selesai));

        $response = $this->actingAs($this->userPelanggan)->patch(route('pesanan.batal', $pesanan->no_resi), [
            'alasan_batal' => 'Jadwal pendakian ditunda karena cuaca',
        ]);

        $response->assertRedirect(route('pesanan.show', $pesanan->no_resi));
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Dibatalkan, $pesanan->status);
        $this->assertEquals('Jadwal pendakian ditunda karena cuaca', $pesanan->alasan_batal);
        $this->assertNotNull($pesanan->dibatalkan_pada);

        // Stok kembali utuh setelah dibatalkan
        $this->assertEquals($stokAwal, $this->ketersediaanService->tersedia($this->tenda, $mulai, $selesai));
    }

    /**
     * BB-48: Pelanggan membatalkan pesanan yang sudah Dibayar ditolak (403 Forbidden).
     */
    public function test_bb48_pelanggan_tidak_dapat_membatalkan_pesanan_berstatus_dibayar(): void
    {
        $pesanan = $this->createPesananMenunggu();

        $this->pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $response = $this->actingAs($this->userPelanggan)->patch(route('pesanan.batal', $pesanan->no_resi), [
            'alasan_batal' => 'Batal sepihak',
        ]);

        $response->assertStatus(403);
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Dibayar, $pesanan->status);
    }

    /**
     * BB-49: Admin dapat membatalkan pesanan berstatus Dibayar dengan alasan wajib.
     */
    public function test_bb49_admin_dapat_membatalkan_pesanan_berstatus_dibayar(): void
    {
        $pesanan = $this->createPesananMenunggu();

        $this->pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $pesanan->id), [
            'alasan_batal' => 'Permintaan refund dari pelanggan via telepon darurat',
        ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Dibatalkan, $pesanan->status);
        $this->assertEquals('Permintaan refund dari pelanggan via telepon darurat', $pesanan->alasan_batal);
    }

    /**
     * BB-50: Pembatalan pesanan berstatus Disewa ditolak.
     */
    public function test_bb50_pembatalan_pesanan_berstatus_disewa_ditolak(): void
    {
        $pesanan = $this->createPesananMenunggu();

        $this->pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $this->pesananService->ambil($pesanan, $this->admin, [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '12345678',
        ]);

        // Coba batalkan oleh admin via HTTP request -> ditolak dengan 403 Forbidden oleh PesananPolicy
        $responseAdmin = $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $pesanan->id), [
            'alasan_batal' => 'Coba batalkan',
        ]);
        $responseAdmin->assertStatus(403);

        // Coba batalkan oleh pelanggan -> ditolak dengan 403 Forbidden
        $responsePelanggan = $this->actingAs($this->userPelanggan)->patch(route('pesanan.batal', $pesanan->no_resi), [
            'alasan_batal' => 'Coba batalkan',
        ]);
        $responsePelanggan->assertStatus(403);

        // Coba panggil service langsung -> ditolak dengan ValidationException oleh state machine
        $this->expectException(ValidationException::class);
        $this->pesananService->batal($pesanan, $this->admin, 'Coba batalkan langsung');

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Disewa, $pesanan->status);
    }

    /**
     * BB-51: Pembatalan otomatis pesanan Menunggu yang melewati batas waktu pengambilan (batas_ambil).
     */
    public function test_bb51_pembatalan_otomatis_lewat_batas_ambil_via_artisan_command(): void
    {
        $mulai = Carbon::tomorrow();
        $selesai = Carbon::tomorrow()->addDays(2);
        $stokAwal = $this->ketersediaanService->tersedia($this->tenda, $mulai, $selesai);

        $pesanan = $this->createPesananMenunggu();

        // Manipulasi batas_ambil menjadi waktu di masa lalu
        $pesanan->update([
            'batas_ambil' => now()->subHours(2),
        ]);

        // Jalankan artisan command pesanan:cek-kadaluarsa
        $this->artisan('pesanan:cek-kadaluarsa')
            ->expectsOutputToContain('Berhasil membatalkan 1 pesanan kadaluarsa.')
            ->assertSuccessful();

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dibatalkan, $pesanan->status);
        $this->assertStringContainsString('melewati batas waktu pengambilan', $pesanan->alasan_batal);

        // Stok terbebas kembali
        $this->assertEquals($stokAwal, $this->ketersediaanService->tersedia($this->tenda, $mulai, $selesai));
    }

    /**
     * Manipulasi Request: Tembak POST langsung ke endpoint bayar pada pesanan yang sudah Dibatalkan ditolak.
     */
    public function test_manipulasi_request_bayar_pada_status_dibatalkan_ditolak(): void
    {
        $pesanan = $this->createPesananMenunggu();
        $pesanan->update(['status' => StatusPesanan::Dibatalkan]);

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan->id), [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $response->assertSessionHasErrors('status');
        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dibatalkan, $pesanan->status);
    }

    /**
     * Otorisasi: Pengguna non-admin tidak dapat memanggil endpoint konfirmasi pembayaran.
     */
    public function test_pengguna_non_admin_dilarang_mengakses_konfirmasi_pembayaran(): void
    {
        $pesanan = $this->createPesananMenunggu();

        $response = $this->actingAs($this->userPelanggan)->post(route('admin.pesanan.bayar', $pesanan->id), [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        $response->assertStatus(403);
    }
}
