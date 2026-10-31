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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananTest extends TestCase
{
    use RefreshDatabase;

    protected User $userPelanggan;

    protected Pelanggan $pelanggan;

    protected User $admin;

    protected Alat $tenda;

    protected Alat $carrier;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);

        $this->userPelanggan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Rian Hidayat',
            'email' => 'rian@test.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->userPelanggan->id,
            'nama_lengkap' => 'Rian Hidayat',
            'no_hp' => '08123456789',
            'alamat' => 'Subang Kota',
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Toko',
            'email' => 'admin@test.com',
            'is_aktif' => true,
        ]);

        $kategori = KategoriAlat::create([
            'nama' => 'Tenda',
            'slug' => 'tenda',
            'deskripsi' => 'Perlengkapan tenda camping',
        ]);

        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Dome Kapasitas 4',
            'harga_per_hari' => 50000,
            'stok_total' => 10,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->carrier = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'CAR-001',
            'nama' => 'Carrier 60 Liter',
            'harga_per_hari' => 30000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);
    }

    public function test_pelanggan_dapat_melihat_halaman_formulir_pemesanan(): void
    {
        $response = $this->actingAs($this->userPelanggan)->get(route('pesanan.create', [
            'alat_id' => $this->tenda->id,
            'jumlah' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('Reservasi Perlengkapan Outdoor');
        $response->assertSee($this->tenda->nama);
    }

    public function test_pelanggan_dapat_membuat_pesanan_satu_alat(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(3)->toDateString(); // durasi 2 hari

        $response = $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 2],
            ],
            'catatan' => 'Mohon cek kondisi pasak sebelum diambil',
        ]);

        // 2 hari * 2 unit * 50.000 = 200.000
        $this->assertDatabaseHas('pesanan', [
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->userPelanggan->id,
            'durasi_hari' => 2,
            'status' => StatusPesanan::Menunggu->value,
            'total_sewa' => 200000,
        ]);

        $this->assertDatabaseHas('pesanan_detail', [
            'alat_id' => $this->tenda->id,
            'jumlah' => 2,
            'harga_per_hari' => 50000,
            'subtotal' => 200000,
        ]);

        $response->assertSessionHas('status');
        $response->assertRedirect();
    }

    public function test_pelanggan_dapat_membuat_pesanan_banyak_alat(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(4)->toDateString(); // durasi 3 hari

        // Tenda: 3 hari * 1 unit * 50.000 = 150.000
        // Carrier: 3 hari * 2 unit * 30.000 = 180.000
        // Total = 330.000 (sesuai contoh di plan6: Rp330.000)
        $response = $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 1],
                ['alat_id' => $this->carrier->id, 'jumlah' => 2],
            ],
        ]);

        $this->assertDatabaseHas('pesanan', [
            'pelanggan_id' => $this->pelanggan->id,
            'total_sewa' => 330000,
            'durasi_hari' => 3,
        ]);

        $response->assertRedirect();
    }

    public function test_pesanan_gagal_jika_stok_tidak_cukup(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();

        // Carrier stok total 5, diminta 10
        $response = $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [
                ['alat_id' => $this->carrier->id, 'jumlah' => 10],
            ],
        ]);

        $response->assertSessionHasErrors(['items.0.jumlah']);
        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_pesanan_gagal_jika_durasi_lebih_dari_14_hari(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(16)->toDateString(); // 15 hari sewa

        $response = $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertSessionHasErrors(['tanggal_selesai']);
        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_pesanan_menghasilkan_nomor_resi_unik_harian(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();

        $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);

        $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->carrier->id, 'jumlah' => 1]],
        ]);

        $prefix = 'BLN-'.now()->format('ymd').'-';
        $this->assertDatabaseHas('pesanan', ['no_resi' => $prefix.'0001']);
        $this->assertDatabaseHas('pesanan', ['no_resi' => $prefix.'0002']);
    }

    public function test_admin_dapat_membuat_pesanan_walk_in(): void
    {
        $mulai = today()->toDateString();
        $selesai = today()->addDay()->toDateString();

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.store'), [
            'tipe_pelanggan' => 'baru',
            'nama_lengkap' => 'Ahmad Walk-in',
            'no_hp' => '08999999999',
            'alamat' => 'Jalancagak Subang',
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 1],
            ],
            'catatan' => 'Pelanggan langsung datang ke toko',
        ]);

        $this->assertDatabaseHas('pelanggan', [
            'user_id' => null,
            'nama_lengkap' => 'Ahmad Walk-in',
            'no_hp' => '08999999999',
        ]);

        $this->assertDatabaseHas('pesanan', [
            'dibuat_oleh' => $this->admin->id,
            'durasi_hari' => 1,
            'total_sewa' => 50000,
        ]);

        $response->assertRedirect();
    }

    public function test_pelanggan_dapat_membatalkan_pesanan_yang_masih_menunggu(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();

        $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);

        $prefix = 'BLN-'.now()->format('ymd').'-0001';

        $response = $this->actingAs($this->userPelanggan)->patch(route('pesanan.batal', $prefix), [
            'alasan_batal' => 'Acara camping dibatalkan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pesanan', [
            'no_resi' => $prefix,
            'status' => StatusPesanan::Dibatalkan->value,
            'alasan_batal' => 'Acara camping dibatalkan',
        ]);
    }

    public function test_pelanggan_tidak_dapat_melihat_resi_milik_pelanggan_lain(): void
    {
        $rolePelanggan = Role::where('nama', 'pelanggan')->first();
        $userLain = User::factory()->create(['role_id' => $rolePelanggan->id, 'is_aktif' => true]);
        $pelangganLain = Pelanggan::create([
            'user_id' => $userLain->id,
            'nama_lengkap' => 'Pelanggan Lain',
            'no_hp' => '0812999999',
            'alamat' => 'Bandung',
        ]);

        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();

        $this->actingAs($userLain)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);

        $prefix = 'BLN-'.now()->format('ymd').'-0001';

        // UserPelanggan mencoba melihat detail & resi milik userLain -> 404 Not Found
        $responseShow = $this->actingAs($this->userPelanggan)->get(route('pesanan.show', $prefix));
        $responseShow->assertNotFound();

        $responseResi = $this->actingAs($this->userPelanggan)->get(route('pesanan.resi', $prefix));
        $responseResi->assertNotFound();
    }

    public function test_pelanggan_dapat_melihat_detail_pesanan_dan_lembar_resi(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();

        $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);

        $prefix = 'BLN-'.now()->format('ymd').'-0001';

        // 1. Uji Halaman Detail Pesanan (pesanan.show)
        $responseDetail = $this->actingAs($this->userPelanggan)->get(route('pesanan.show', $prefix));
        $responseDetail->assertOk();
        $responseDetail->assertSee($prefix);
        $responseDetail->assertSee('Detail Reservasi Sewa');
        $responseDetail->assertSee('Lihat Lembar Resi');
        $responseDetail->assertSee('Batalkan Pesanan'); // Tersedia tombol batal di halaman detail
        $responseDetail->assertSee($this->tenda->nama);

        // 2. Uji Halaman Lembar Resi Resmi (pesanan.resi)
        $responseResi = $this->actingAs($this->userPelanggan)->get(route('pesanan.resi', $prefix));
        $responseResi->assertOk();
        $responseResi->assertSee($prefix);
        $responseResi->assertSee('Bukti Reservasi Sewa');
        $responseResi->assertSee('Cetak Bukti Resi');
        $responseResi->assertSee('Kembali ke Detail Pesanan');
        $responseResi->assertSee($this->tenda->nama);
    }

    public function test_pelanggan_dapat_melihat_detail_dan_resi_pesanan_yang_sudah_dibayar(): void
    {
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();

        $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);

        $prefix = 'BLN-'.now()->format('ymd').'-0001';
        $pesanan = Pesanan::where('no_resi', $prefix)->first();

        // Admin bayar pesanan
        $this->actingAs($this->admin)->post(route('admin.pesanan.bayar', $pesanan), [
            'jumlah' => $pesanan->total_sewa,
            'metode' => MetodePembayaran::Tunai->value,
            'catatan' => 'Lunas tunai di toko',
        ]);

        // Detail Pesanan
        $responseDetail = $this->actingAs($this->userPelanggan)->get(route('pesanan.show', $prefix));
        $responseDetail->assertOk();
        $responseDetail->assertSee($prefix);
        $responseDetail->assertSee('Pembayaran Dikonfirmasi Lunas');
        $responseDetail->assertSee('Pembayaran Sewa');
        $responseDetail->assertSee('LUNAS');

        // Lembar Resi
        $responseResi = $this->actingAs($this->userPelanggan)->get(route('pesanan.resi', $prefix));
        $responseResi->assertOk();
        $responseResi->assertSee($prefix);
        $responseResi->assertSee('LUNAS');
    }

    public function test_pelanggan_dapat_melihat_daftar_pesanan_aktif_dan_riwayat_terpisah(): void
    {
        // 1. Buat pesanan aktif (status Menunggu)
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(2)->toDateString();
        $this->actingAs($this->userPelanggan)->post(route('pesanan.store'), [
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);
        $resiAktif = 'BLN-'.now()->format('ymd').'-0001';

        // 2. Buat pesanan selesai (status Dikembalikan) secara manual
        $pesananSelesai = Pesanan::create([
            'no_resi' => 'BLN-'.now()->format('ymd').'-0099',
            'pelanggan_id' => $this->pelanggan->id,
            'dibuat_oleh' => $this->userPelanggan->id,
            'tanggal_mulai' => today()->subDays(5)->toDateString(),
            'tanggal_selesai' => today()->subDays(3)->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => today()->subDays(3)->setTime(12, 0, 0),
            'batas_ambil' => today()->subDays(5)->setTime(23, 59, 59),
            'total_sewa' => 100000,
            'status' => StatusPesanan::Dikembalikan,
        ]);

        // Cek tab aktif -> hanya pesanan aktif yang muncul dengan tombol ke Detail dan Resi
        $responseAktif = $this->actingAs($this->userPelanggan)->get(route('pesanan.index', ['tab' => 'aktif']));
        $responseAktif->assertOk();
        $responseAktif->assertSee('Pesanan Aktif');
        $responseAktif->assertSee($resiAktif);
        $responseAktif->assertDontSee($pesananSelesai->no_resi);
        $responseAktif->assertSee('Detail Pesanan');
        $responseAktif->assertSee('Lihat Resi');

        // Pastikan tombol batal dapat diakses di halaman detail pesanan aktif tersebut
        $responseDetailAktif = $this->actingAs($this->userPelanggan)->get(route('pesanan.show', $resiAktif));
        $responseDetailAktif->assertSee('Batalkan Pesanan');

        // Cek tab riwayat -> hanya pesanan selesai yang muncul
        $responseRiwayat = $this->actingAs($this->userPelanggan)->get(route('pesanan.index', ['tab' => 'riwayat']));
        $responseRiwayat->assertOk();
        $responseRiwayat->assertSee('Riwayat Selesai');
        $responseRiwayat->assertSee($pesananSelesai->no_resi);
        $responseRiwayat->assertDontSee($resiAktif);
    }
}
