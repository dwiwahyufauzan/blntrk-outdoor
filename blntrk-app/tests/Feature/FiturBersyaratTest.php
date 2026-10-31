<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\DendaService;
use App\Services\LogService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FiturBersyaratTest extends TestCase
{
    use RefreshDatabase;

    protected User $pemilik;

    protected User $admin;

    protected User $pelangganUser;

    protected Pelanggan $pelanggan;

    protected Alat $tenda;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePemilik = Role::firstOrCreate(['nama' => 'pemilik'], ['label' => 'Pemilik Toko']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);

        $this->pemilik = User::factory()->create([
            'role_id' => $rolePemilik->id,
            'nama' => 'Pemilik BLNTRK',
            'email' => 'pemilik@blntrk.com',
            'is_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Kasir',
            'email' => 'admin@blntrk.com',
            'is_aktif' => true,
        ]);

        $this->pelangganUser = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Rian Hidayat',
            'email' => 'rian@test.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id,
            'nama_lengkap' => 'Rian Hidayat',
            'no_hp' => '081234567890',
            'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create([
            'nama' => 'Tenda',
            'slug' => 'tenda',
            'deskripsi' => 'Tenda camping',
        ]);

        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Dome Arpenaz 4.1',
            'varian' => '4 Orang',
            'deskripsi' => 'Tenda dome double layer',
            'harga_per_hari' => 80000,
            'stok_total' => 10,
            'stok_perawatan' => 3,
            'is_aktif' => true,
        ]);
    }

    /**
     * Fitur 1: Pengaturan oleh Pemilik - Memperbarui konfigurasi toko dan mengubah
     * formula denda keterlambatan pada transaksi baru.
     */
    public function test_pengaturan_oleh_pemilik_dapat_diubah_dan_mempengaruhi_perhitungan_denda_baru(): void
    {
        // 1. Pemilik mengubah pengaturan
        $response = $this->actingAs($this->pemilik)->put(route('pemilik.pengaturan.update'), [
            'nama_toko' => 'BLNTRK Adventure Camp Subang',
            'alamat_toko' => 'Jl. Raya Ciater No. 99, Subang',
            'no_wa_toko' => '6281299998888',
            'jam_kembali' => '13:00',
            'toleransi_menit' => 30,
            'persen_denda_harian' => 150,
            'maks_durasi_hari' => 7,
            'ambang_stok_kritis' => 3,
        ]);

        $response->assertRedirect(route('pemilik.pengaturan.index'));
        $response->assertSessionHas('status');

        // Pastikan nilai tersimpan di database dan cache diperbarui
        $this->assertEquals('BLNTRK Adventure Camp Subang', Pengaturan::ambil('nama_toko'));
        $this->assertEquals(30, (int) Pengaturan::ambil('toleransi_menit'));
        $this->assertEquals(150, (float) Pengaturan::ambil('persen_denda_harian'));

        // 2. Uji dampak ke DendaService: denda harus dihitung dengan tarif 150% dan toleransi 30 menit
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-261020-9999',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->pelangganUser->id,
            'tanggal_mulai' => '2026-10-20',
            'tanggal_selesai' => '2026-10-22',
            'durasi_hari' => 2,
            'jatuh_tempo' => Carbon::parse('2026-10-22 13:00:00'),
            'batas_ambil' => Carbon::parse('2026-10-20 21:00:00'),
            'total_sewa' => 160000,
            'status' => StatusPesanan::Disewa,
        ]);

        $pesanan->detail()->create([
            'alat_id' => $this->tenda->id,
            'jumlah' => 1,
            'harga_per_hari' => 80000,
            'subtotal' => 160000,
        ]);

        $dendaService = new DendaService;

        // Kasus A: kembali tepat 25 menit setelah jatuh tempo (masih dalam toleransi 30 menit) -> denda 0
        $telatA = $dendaService->hitungKeterlambatan($pesanan, Carbon::parse('2026-10-22 13:25:00'));
        $this->assertEquals(0, $telatA['hari']);
        $this->assertEquals(0, $telatA['jumlah']);

        // Kasus B: kembali 40 menit setelah jatuh tempo (melewati toleransi 30 menit) -> 1 hari denda dengan tarif 150% (80.000 * 1.5 = 120.000)
        $telatB = $dendaService->hitungKeterlambatan($pesanan, Carbon::parse('2026-10-22 13:40:00'));
        $this->assertEquals(1, $telatB['hari']);
        $this->assertEquals(120000, $telatB['jumlah']);

        // 3. Hak akses: Admin dan Pelanggan dilarang mengubah pengaturan (403)
        $this->actingAs($this->admin)->put(route('pemilik.pengaturan.update'), ['nama_toko' => 'Ilegal'])->assertStatus(403);
        $this->actingAs($this->pelangganUser)->put(route('pemilik.pengaturan.update'), ['nama_toko' => 'Ilegal'])->assertStatus(403);
    }

    /**
     * Fitur 1: Kelola Admin oleh Pemilik - Tambah akun, toggle aktif/nonaktif, dan reset password.
     */
    public function test_pemilik_dapat_mengelola_akun_admin_tambah_toggle_dan_reset_password(): void
    {
        // 1. Tambah admin baru
        $responseCreate = $this->actingAs($this->pemilik)->post(route('pemilik.admin.store'), [
            'nama' => 'Budi Staf Baru',
            'email' => 'budi.kasir@blntrk.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_aktif' => '1',
        ]);

        $responseCreate->assertRedirect(route('pemilik.admin.index'));
        $this->assertDatabaseHas('users', ['email' => 'budi.kasir@blntrk.com', 'nama' => 'Budi Staf Baru']);

        $budi = User::where('email', 'budi.kasir@blntrk.com')->first();
        $this->assertTrue($budi->is_aktif);

        // 2. Toggle status aktif -> menjadi nonaktif
        $this->actingAs($this->pemilik)->patch(route('pemilik.admin.toggle', $budi));
        $this->assertFalse($budi->fresh()->is_aktif);

        // 3. Toggle kembali -> menjadi aktif
        $this->actingAs($this->pemilik)->patch(route('pemilik.admin.toggle', $budi));
        $this->assertTrue($budi->fresh()->is_aktif);

        // 4. Reset password ke default (password123)
        $this->actingAs($this->pemilik)->post(route('pemilik.admin.reset-password', $budi));
        $this->assertTrue(Hash::check('password123', $budi->fresh()->password));

        // 5. Hapus admin yang belum memiliki riwayat transaksi (Soft delete)
        $this->actingAs($this->pemilik)->delete(route('pemilik.admin.destroy', $budi));
        $this->assertSoftDeleted('users', ['id' => $budi->id]);

        // 6. Admin tidak boleh menghapus/mengelola sesama admin melalui rute pemilik (403)
        $this->actingAs($this->admin)->get(route('pemilik.admin.index'))->assertStatus(403);
    }

    /**
     * Fitur 2: Log Aktivitas - Mencatat aksi pengguna dan dapat difilter (read-only).
     */
    public function test_log_aktivitas_mencatat_peristiwa_penting_dan_dapat_difilter_read_only(): void
    {
        // Catat beberapa aktivitas
        LogService::catat($this->admin, 'pesanan.bayar', 'pesanan', 101, 'Menerima kas sewa.');
        LogService::catat($this->admin, 'perawatan.selesai', 'alat', $this->tenda->id, 'Selesai perbaikan tenda.');
        LogService::catat($this->pemilik, 'pengaturan.ubah', 'pengaturan', null, 'Update nama toko.');

        // 1. Pemilik melihat daftar log
        $response = $this->actingAs($this->pemilik)->get(route('pemilik.log-aktivitas.index'));
        $response->assertStatus(200);
        $response->assertSee('pesanan.bayar');
        $response->assertSee('perawatan.selesai');
        $response->assertSee('pengaturan.ubah');

        // 2. Filter berdasarkan modul
        $responseModul = $this->actingAs($this->pemilik)->get(route('pemilik.log-aktivitas.index', ['modul' => 'alat']));
        $responseModul->assertStatus(200);
        $responseModul->assertSee('perawatan.selesai');
        $responseModul->assertDontSee('pengaturan.ubah');

        // 3. Pelanggan dilarang melihat log aktivitas sistem (403)
        $this->actingAs($this->pelangganUser)->get(route('pemilik.log-aktivitas.index'))->assertStatus(403);
    }

    /**
     * Fitur 3: Perawatan Alat (T12) - Menyelesaikan servis mengembalikan stok perawatan
     * ke stok tersedia toko, dan admin dapat memindahkan unit ke perawatan.
     */
    public function test_perawatan_alat_unit_selesai_diperbaiki_mengurangi_stok_perawatan(): void
    {
        // Tenda awal: stok_total = 10, stok_perawatan = 3
        $this->assertEquals(3, $this->tenda->stok_perawatan);

        // 1. Selesai perbaikan 2 unit
        $response = $this->actingAs($this->admin)->patch(route('admin.perawatan.selesai', $this->tenda), [
            'jumlah' => 2,
            'catatan' => 'Selesai cuci dan jahit kain tenda',
        ]);

        $response->assertRedirect(route('admin.perawatan.index'));
        $this->tenda->refresh();
        $this->assertEquals(1, $this->tenda->stok_perawatan); // 3 - 2 = 1

        // 2. Memindahkan 2 unit secara manual ke perawatan (misal karena kotor)
        $this->actingAs($this->admin)->patch(route('admin.perawatan.tambah', $this->tenda), [
            'jumlah' => 2,
            'keterangan' => 'Frame bengkok saat cek rutin',
        ]);

        $this->tenda->refresh();
        $this->assertEquals(3, $this->tenda->stok_perawatan); // 1 + 2 = 3

        // 3. Validasi: menyelesaikan unit lebih banyak dari yang dirawat harus ditolak
        $responseError = $this->actingAs($this->admin)->patch(route('admin.perawatan.selesai', $this->tenda), [
            'jumlah' => 50,
        ]);
        $responseError->assertSessionHasErrors('jumlah');
    }

    /**
     * Fitur 4: WhatsApp Service (T14) - Normalisasi nomor 08xxx/628xxx dan pembuatan tautan link.
     */
    public function test_whatsapp_service_dan_helper_menormalkan_nomor_dan_membuat_tautan(): void
    {
        // 1. Normalisasi nomor ponsel
        $this->assertEquals('6281234567890', WhatsAppService::normalkanNomor('081234567890'));
        $this->assertEquals('6281234567890', WhatsAppService::normalkanNomor('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', WhatsAppService::normalkanNomor('6281234567890'));

        // 2. Helper function wa_link()
        $link = wa_link('081234567890', 'Halo BLNTRK Outdoor');
        $this->assertStringContainsString('phone=6281234567890', $link);
        $this->assertStringContainsString('Halo+BLNTRK+Outdoor', $link);

        // 3. Template Pesan
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-261020-0088',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->pelangganUser->id,
            'tanggal_mulai' => '2026-10-20',
            'tanggal_selesai' => '2026-10-22',
            'durasi_hari' => 2,
            'jatuh_tempo' => Carbon::parse('2026-10-22 12:00:00'),
            'batas_ambil' => Carbon::parse('2026-10-20 21:00:00'),
            'total_sewa' => 160000,
            'status' => StatusPesanan::Disewa,
        ]);

        $pesanIngat = WhatsAppService::pesanPengingatJatuhTempo($pesanan);
        $this->assertStringContainsString('BLN-261020-0088', $pesanIngat);
        $this->assertStringContainsString('Rian Hidayat', $pesanIngat);

        $pesanDenda = WhatsAppService::pesanTagihanDenda($pesanan, 50000, 'Frame bengkok');
        $this->assertStringContainsString('50.000', $pesanDenda);
        $this->assertStringContainsString('Frame bengkok', $pesanDenda);
    }

    /**
     * Fitur 5: Ekspor Excel/CSV (T13) - Mengunduh file spreadsheet dengan status 200 dan format CSV UTF-8.
     */
    public function test_ekspor_excel_csv_mengembalikan_stream_file_dengan_status_200(): void
    {
        $dari = now()->startOfMonth()->toDateString();
        $sampai = now()->toDateString();

        // 1. Admin mengunduh CSV Laporan Penyewaan
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.laporan.csv', [
            'jenis' => 'penyewaan',
            'dari' => $dari,
            'sampai' => $sampai,
        ]));

        $responseAdmin->assertStatus(200);
        $responseAdmin->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // 2. Pemilik mengunduh CSV Laporan Pendapatan
        $responsePemilik = $this->actingAs($this->pemilik)->get(route('pemilik.laporan.csv', [
            'jenis' => 'pendapatan',
            'dari' => $dari,
            'sampai' => $sampai,
        ]));

        $responsePemilik->assertStatus(200);
        $responsePemilik->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
