<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        if (!User::where('email', 'admin@blntrk.test')->exists()) {
            $this->seed();
        }
        $this->admin = User::where('email', 'admin@blntrk.test')->firstOrFail();
    }

    /* -------------------------------------------------------------------------- */
    /* UJI CRUD KATEGORI ALAT                                                     */
    /* -------------------------------------------------------------------------- */

    public function test_admin_bisa_menambah_kategori_baru(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama' => 'Peralatan Masak Gunung',
            'deskripsi' => 'Nesting, kompor gas portable, dan alat makan outdoor',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('kategori_alat', [
            'nama' => 'Peralatan Masak Gunung',
            'slug' => 'peralatan-masak-gunung',
        ]);
    }

    public function test_kategori_duplikat_ditolak(): void
    {
        // Kategori 'Tenda' sudah ada di Seeder
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'nama' => 'Tenda',
            'deskripsi' => 'Duplikat tenda',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_admin_bisa_mengubah_kategori(): void
    {
        $kategori = KategoriAlat::where('nama', 'Tenda')->firstOrFail();

        $response = $this->actingAs($this->admin)->put(route('admin.kategori.update', $kategori->id), [
            'nama' => 'Tenda & Shelter',
            'deskripsi' => 'Tenda dome, flysheet, dan bivak shelter',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('kategori_alat', [
            'id' => $kategori->id,
            'nama' => 'Tenda & Shelter',
            'slug' => 'tenda-shelter',
        ]);
    }

    public function test_kategori_yang_memiliki_alat_ditolak_saat_dihapus(): void
    {
        $kategoriTenda = KategoriAlat::where('nama', 'Tenda')->firstOrFail();
        $this->assertTrue($kategoriTenda->alat()->exists());

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategoriTenda->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kategori_alat', ['id' => $kategoriTenda->id]);
    }

    public function test_kategori_kosong_berhasil_dihapus(): void
    {
        $kategoriKosong = KategoriAlat::create([
            'nama' => 'Kategori Sementara',
            'slug' => 'kategori-sementara',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategoriKosong->id));

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseMissing('kategori_alat', ['id' => $kategoriKosong->id]);
    }

    /* -------------------------------------------------------------------------- */
    /* UJI CRUD ALAT OUTDOOR                                                      */
    /* -------------------------------------------------------------------------- */

    public function test_admin_bisa_menambah_alat_baru_dengan_upload_foto(): void
    {
        Storage::fake('public');
        $kategori = KategoriAlat::firstOrFail();
        $file = UploadedFile::fake()->image('tenda_arpenaz.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.alat.store'), [
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-999',
            'nama' => 'Tenda Quechua Arpenaz 3',
            'deskripsi' => 'Tenda waterproof 3 orang',
            'harga_per_hari' => 35000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => 1,
            'foto' => $file,
        ]);

        $response->assertRedirect(route('admin.alat.index'));

        $this->assertDatabaseHas('alat', [
            'kode_alat' => 'TND-999',
            'nama' => 'Tenda Quechua Arpenaz 3',
            'harga_per_hari' => 35000,
            'stok_total' => 5,
        ]);

        $alat = Alat::where('kode_alat', 'TND-999')->first();
        $this->assertNotNull($alat->foto);
        Storage::disk('public')->assertExists($alat->foto);
    }

    public function test_tambah_alat_gagal_jika_kode_duplikat(): void
    {
        $alatLama = Alat::firstOrFail();
        $kategori = KategoriAlat::firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.alat.store'), [
            'kategori_id' => $kategori->id,
            'kode_alat' => $alatLama->kode_alat, // duplikat
            'nama' => 'Alat Duplikat',
            'harga_per_hari' => 20000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
        ]);

        $response->assertSessionHasErrors('kode_alat');
    }

    public function test_tambah_alat_gagal_jika_harga_negatif(): void
    {
        $kategori = KategoriAlat::firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.alat.store'), [
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TEST-NEG',
            'nama' => 'Alat Harga Negatif',
            'harga_per_hari' => -5000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
        ]);

        $response->assertSessionHasErrors('harga_per_hari');
    }

    public function test_tambah_alat_gagal_jika_stok_perawatan_melebihi_stok_total(): void
    {
        $kategori = KategoriAlat::firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.alat.store'), [
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TEST-STOK',
            'nama' => 'Alat Stok Rusak Berlebih',
            'harga_per_hari' => 20000,
            'stok_total' => 5,
            'stok_perawatan' => 8, // 8 > 5 (tidak logis)
        ]);

        $response->assertSessionHasErrors('stok_perawatan');
    }

    public function test_tambah_alat_gagal_jika_file_bukan_gambar_atau_terlalu_besar(): void
    {
        $kategori = KategoriAlat::firstOrFail();

        // 1. File berupa teks/PDF
        $pdfFile = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');
        $responsePdf = $this->actingAs($this->admin)->post(route('admin.alat.store'), [
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TEST-PDF',
            'nama' => 'Alat File PDF',
            'harga_per_hari' => 20000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'foto' => $pdfFile,
        ]);
        $responsePdf->assertSessionHasErrors('foto');

        // 2. File gambar ukuran > 2048 KB (misal 3000 KB)
        $largeImage = UploadedFile::fake()->image('gambar_besar.jpg')->size(3000);
        $responseLarge = $this->actingAs($this->admin)->post(route('admin.alat.store'), [
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TEST-LARGE',
            'nama' => 'Alat Gambar Besar',
            'harga_per_hari' => 20000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'foto' => $largeImage,
        ]);
        $responseLarge->assertSessionHasErrors('foto');
    }

    public function test_admin_bisa_mengubah_alat_dan_toggle_status(): void
    {
        $alat = Alat::firstOrFail();

        $response = $this->actingAs($this->admin)->put(route('admin.alat.update', $alat->id), [
            'kategori_id' => $alat->kategori_id,
            'kode_alat' => $alat->kode_alat,
            'nama' => $alat->nama . ' Updated',
            'harga_per_hari' => 45000,
            'stok_total' => 10,
            'stok_perawatan' => 2,
            'is_aktif' => 1,
        ]);

        $response->assertRedirect(route('admin.alat.index'));
        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'nama' => $alat->nama . ' Updated',
            'harga_per_hari' => 45000,
        ]);

        // Toggle status
        $statusAwal = $alat->fresh()->is_aktif;
        $this->actingAs($this->admin)->patch(route('admin.alat.toggle', $alat->id));
        $this->assertEquals(! $statusAwal, $alat->fresh()->is_aktif);
    }

    public function test_hapus_alat_berupa_soft_delete(): void
    {
        $alat = Alat::firstOrFail();

        $response = $this->actingAs($this->admin)->delete(route('admin.alat.destroy', $alat->id));

        $response->assertRedirect(route('admin.alat.index'));
        $this->assertSoftDeleted('alat', ['id' => $alat->id]);
    }

    /* -------------------------------------------------------------------------- */
    /* UJI CRUD PELANGGAN                                                         */
    /* -------------------------------------------------------------------------- */

    public function test_admin_bisa_menambah_pelanggan_walk_in(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pelanggan.store'), [
            'nama_lengkap' => 'Bambang Sudirman',
            'no_hp' => '081233445566',
            'alamat' => 'Desa Tambakmekar, Jalancagak',
        ]);

        $response->assertRedirect(route('admin.pelanggan.index'));
        $this->assertDatabaseHas('pelanggan', [
            'nama_lengkap' => 'Bambang Sudirman',
            'user_id' => null, // Pelanggan walk-in
        ]);
    }

    public function test_hapus_pelanggan_ditolak_jika_memiliki_pesanan_aktif(): void
    {
        $pelanggan = Pelanggan::whereNotNull('user_id')->firstOrFail();

        // Buat pesanan aktif untuk pelanggan ini
        Pesanan::create([
            'no_resi' => 'BLN-TEST-AKTIF',
            'pelanggan_id' => $pelanggan->id,
            'dibuat_oleh' => $this->admin->id,
            'status' => StatusPesanan::Disewa,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => now()->addDays(2)->setTime(12, 0),
            'batas_ambil' => now()->addHours(6),
            'total_sewa' => 50000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.pelanggan.destroy', $pelanggan->id));

        $response->assertSessionHas('error');
        $this->assertNotSoftDeleted('pelanggan', ['id' => $pelanggan->id]);
    }

    /* -------------------------------------------------------------------------- */
    /* UJI CRUD PENGGUNA (USER) & KEAMANAN ROLE                                  */
    /* -------------------------------------------------------------------------- */

    public function test_admin_bisa_membuat_user_admin_dan_pelanggan(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->post(route('admin.user.store'), [
            'nama' => 'Staf Baru',
            'email' => 'staf@blntrk.test',
            'role' => 'admin',
            'password' => 'password123',
            'is_aktif' => 1,
        ]);

        $responseAdmin->assertRedirect(route('admin.user.index'));
        $this->assertDatabaseHas('users', ['email' => 'staf@blntrk.test']);
        $userAdmin = User::where('email', 'staf@blntrk.test')->first();
        $this->assertTrue($userAdmin->punyaRole('admin'));

        $responsePelanggan = $this->actingAs($this->admin)->post(route('admin.user.store'), [
            'nama' => 'Pelanggan Baru',
            'email' => 'pelanggan.baru@blntrk.test',
            'role' => 'pelanggan',
            'password' => 'password123',
            'no_hp' => '081299887766',
            'alamat' => 'Subang Kota',
            'is_aktif' => 1,
        ]);

        $responsePelanggan->assertRedirect(route('admin.user.index'));
        $userPelanggan = User::where('email', 'pelanggan.baru@blntrk.test')->first();
        $this->assertTrue($userPelanggan->punyaRole('pelanggan'));
        $this->assertNotNull($userPelanggan->pelanggan);
    }

    public function test_admin_dilarang_membuat_user_berperan_pemilik(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.user.store'), [
            'nama' => 'Hacker Owner',
            'email' => 'hacker@blntrk.test',
            'role' => 'pemilik', // Harus DITOLAK
            'password' => 'password123',
            'is_aktif' => 1,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'hacker@blntrk.test']);
    }

    public function test_admin_dilarang_menghapus_akun_dirinya_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.user.destroy', $this->admin->id));

        $response->assertSessionHas('error');
        $this->assertNotSoftDeleted('users', ['id' => $this->admin->id]);
    }

    public function test_reset_password_user_menghasilkan_password_sementara(): void
    {
        $budi = User::where('email', 'budi@blntrk.test')->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.user.reset-password', $budi->id));

        $response->assertSessionHas('password_baru');
        $passwordBaru = session('password_baru')['password'];
        $this->assertNotEmpty($passwordBaru);

        // Verifikasi password baru dapat digunakan untuk login
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($passwordBaru, $budi->fresh()->password));
    }
}
