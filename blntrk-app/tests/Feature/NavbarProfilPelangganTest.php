<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarProfilPelangganTest extends TestCase
{
    use RefreshDatabase;

    protected User $userPelanggan;

    protected Pelanggan $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);

        $this->userPelanggan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Dwi Wahyu Fauzan',
            'email' => 'dwi@example.com',
            'is_aktif' => true,
        ]);

        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->userPelanggan->id,
            'nama_lengkap' => 'Dwi Wahyu Fauzan',
            'no_hp' => '081234567890',
            'alamat' => 'Subang, Jawa Barat',
        ]);
    }

    public function test_tamu_melihat_tombol_masuk_dan_daftar_di_navbar(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertOk();
        $response->assertSee('Masuk');
        $response->assertSee('Daftar');
        $response->assertDontSee('Profil Saya');
        $response->assertDontSee('Pengaturan Profil');
    }

    public function test_pelanggan_melihat_dropdown_profil_ikon_user_dengan_opsi_lengkap(): void
    {
        $response = $this->actingAs($this->userPelanggan)->get(route('beranda'));

        $response->assertOk();

        // 1. Menu navbar tidak lagi memuat "Cara Sewa" dan "Kontak"
        $response->assertDontSee('>Cara Sewa<', false);
        $response->assertDontSee('>Kontak<', false);

        // 2. Trigger icon user & identitas
        $response->assertSee('Menu Profil');
        $response->assertSee($this->userPelanggan->nama);
        $response->assertSee('Pelanggan');

        // 3. Akses Halaman Profil Saya
        $response->assertSee(route('profile.edit'));
        $response->assertSee('Profil Saya');
        $response->assertSee('Akses data diri dan pengaturan akun');

        // Menu Pengaturan Profil ditiadakan karena sudah dicakup di halaman Profil Saya
        $response->assertDontSee('Pengaturan Profil');

        // 4. Akses Halaman Sesi Login (tautan ke rute sesi.index)
        $response->assertSee(route('sesi.index'));
        $response->assertSee('Sesi Login');
        $response->assertSee('AKTIF');

        // 5. Logout
        $response->assertSee('Logout');
        $response->assertSee(route('logout'));
    }

    public function test_halaman_sesi_login_dapat_diakses_oleh_pelanggan(): void
    {
        // Akses via rute sesi.index (/sesi-login)
        $response = $this->actingAs($this->userPelanggan)->get(route('sesi.index'));
        $response->assertOk();
        $response->assertSee('Sesi Login & Perangkat Aktif', false);
        $response->assertSee('Perangkat Ini');
        $response->assertSee('Sesi Aktif');
        $response->assertSee('Alamat IP');
        $response->assertSee('Durasi Inaktivitas');
        $response->assertSee('120 Menit (2 Jam)');
        $response->assertSee('Tips Keamanan Akun');
        $response->assertSee('Keluar Sesi Ini');

        // Akses via alias (/sesi)
        $responseAlias = $this->actingAs($this->userPelanggan)->get('/sesi');
        $responseAlias->assertRedirect(route('sesi.index'));

        // Tamu dialihkan ke halaman login
        auth()->logout();
        $responseTamu = $this->get(route('sesi.index'));
        $responseTamu->assertRedirect(route('login'));
    }

    public function test_rute_profile_dan_alias_profil_dapat_diakses_oleh_pelanggan(): void
    {
        $responseProfile = $this->actingAs($this->userPelanggan)->get(route('profile.edit'));
        $responseProfile->assertOk();
        $responseProfile->assertSee('Pengaturan Profil & Akun');
        $responseProfile->assertSee('informasi-profil');
        $responseProfile->assertSee('pengaturan-profil');

        $this->actingAs($this->userPelanggan)->get('/profil')->assertRedirect(route('profile.edit'));
    }

    public function test_halaman_profile_admin_mengikuti_gaya_panel_admin_dan_bukan_halaman_pelanggan(): void
    {
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin BLNTRK',
            'email' => 'admin@blntrk.com',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('profile.edit'));
        $response->assertOk();

        // Mengikuti gaya panel admin (ada topbar admin dan sidebar menu admin)
        $response->assertSee('Panel Admin Toko');
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Daftar Pesanan');
        $response->assertSee('Data Alat Outdoor');
        $response->assertSee('Sesi Login & Keamanan', false);

        // Tidak memuat elemen khas footer halaman pelanggan
        $response->assertDontSee('Sewa perlengkapan camping dan mendaki berkualitas dengan proses praktis');
    }

    public function test_halaman_sesi_login_admin_mengikuti_gaya_panel_admin_dan_bukan_halaman_pelanggan(): void
    {
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin BLNTRK',
            'email' => 'admin@blntrk.com',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('sesi.index'));
        $response->assertOk();

        // Mengikuti gaya panel admin
        $response->assertSee('Panel Admin Toko');
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Keamanan Autentikasi Admin');
        $response->assertSee('Sesi Login & Perangkat Aktif', false);
        $response->assertSee('Perangkat Ini');

        // Tidak memuat elemen khas footer halaman pelanggan
        $response->assertDontSee('Sewa perlengkapan camping dan mendaki berkualitas dengan proses praktis');
    }
}
