<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransisiStatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $userPelanggan;

    protected Pelanggan $pelanggan;

    protected Alat $tenda;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Penguji',
            'email' => 'admin.test@blntrk.com',
            'is_aktif' => true,
        ]);

        $this->userPelanggan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Pelanggan Penguji',
            'email' => 'pelanggan.test@blntrk.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->userPelanggan->id,
            'nama_lengkap' => 'Pelanggan Penguji',
            'no_hp' => '081234567899',
            'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Tenda', 'slug' => 'tenda']);
        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-TEST',
            'nama' => 'Tenda Dome Uji',
            'harga_per_hari' => 50000,
            'stok_total' => 5,
            'is_aktif' => true,
        ]);
    }

    /**
     * Helper untuk membuat pesanan dengan status tertentu.
     */
    protected function buatPesanan(StatusPesanan $status): Pesanan
    {
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-'.now()->format('ymd').'-'.rand(1000, 9999),
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->userPelanggan->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'jatuh_tempo' => now()->addDays(2)->setTime(21, 0, 0),
            'durasi_hari' => 2,
            'total_sewa' => 100000,
            'status' => $status,
            'batas_ambil' => now()->addDay(),
        ]);

        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'alat_id' => $this->tenda->id,
            'jumlah' => 1,
            'harga_per_hari' => 50000,
            'subtotal' => 100000,
        ]);

        return $pesanan;
    }

    /**
     * BB-45: Pengambilan tanpa pembayaran ditolak.
     * Status pesanan masih Menunggu, dicoba langsung pengambilan.
     */
    public function test_bb45_pengambilan_ditolak_jika_pesanan_masih_menunggu_pembayaran(): void
    {
        $pesanan = $this->buatPesanan(StatusPesanan::Menunggu);

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $pesanan), [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '3213012345678901',
        ]);

        // Ditolak via validasi status PengambilanRequest
        $response->assertSessionHasErrors('status');

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Menunggu, $pesanan->status);
    }

    /**
     * BB-48: Pelanggan membatalkan pesanan Dibayar ditolak.
     * Pelanggan hanya boleh membatalkan pesanan berstatus Menunggu secara mandiri.
     */
    public function test_bb48_pelanggan_ditolak_membatalkan_pesanan_yang_sudah_dibayar(): void
    {
        $pesanan = $this->buatPesanan(StatusPesanan::Dibayar);

        $response = $this->actingAs($this->userPelanggan)->patch(route('pesanan.batal', $pesanan->no_resi), [
            'alasan_batal' => 'Membatalkan sepihak setelah bayar',
        ]);

        // Ditolak via policy (403 Forbidden)
        $response->assertForbidden();
        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dibayar, $pesanan->status);
    }

    /**
     * BB-48: Pembayaran ulang pada pesanan yang sudah Dibayar ditolak.
     */
    public function test_bb48_admin_ditolak_mencatat_pembayaran_ulang_pada_pesanan_yang_sudah_dibayar(): void
    {
        $pesanan = $this->buatPesanan(StatusPesanan::Dibayar);

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan), [
            'jumlah' => 100000,
            'metode' => MetodePembayaran::Tunai->value,
        ]);

        // Ditolak via validasi status PembayaranRequest
        $response->assertSessionHasErrors('status');

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dibayar, $pesanan->status);
    }

    /**
     * BB-50: Pembatalan pesanan yang berstatus Disewa ditolak.
     */
    public function test_bb50_pembatalan_ditolak_jika_pesanan_sedang_disewa(): void
    {
        $pesanan = $this->buatPesanan(StatusPesanan::Disewa);

        $response = $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $pesanan), [
            'alasan_batal' => 'Dibatalkan saat barang sudah dibawa pelanggan',
        ]);

        // Policy hanya mengizinkan Menunggu dan Dibayar untuk pembatalan
        $response->assertForbidden();

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Disewa, $pesanan->status);
    }

    /**
     * BB-59: Pengembalian ulang ditolak pada pesanan yang sudah Dikembalikan.
     */
    public function test_bb59_pengembalian_ulang_ditolak_pada_pesanan_yang_sudah_dikembalikan(): void
    {
        $pesanan = $this->buatPesanan(StatusPesanan::Dikembalikan);
        $det = $pesanan->detail->first();

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan), [
            'dikembalikan_pada' => now()->format('Y-m-d H:i:s'),
            'baris' => [
                $det->id => [
                    'baik' => 1,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                    'hilang' => 0,
                    'biaya' => 0,
                ],
            ],
        ]);

        // Ditolak karena PengembalianService hanya memproses status Disewa atau Terlambat
        $response->assertSessionHasErrors('status');

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dikembalikan, $pesanan->status);
    }
}
