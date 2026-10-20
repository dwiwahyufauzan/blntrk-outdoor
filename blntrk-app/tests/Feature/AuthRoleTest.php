<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_halaman_beranda_bisa_diakses_publik(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('BLNTRK');
        $response->assertSee('Kategori Perlengkapan');
        $response->assertSee('Alur Penyewaan');
    }

    public function test_registrasi_pelanggan_berhasil_dan_membuat_data_pelanggan(): void
    {
        $payload = [
            'nama' => 'Ahmad Subang',
            'email' => 'ahmad@example.com',
            'no_hp' => '081298765432',
            'alamat' => 'Jl. Cagak No. 12, Jalancagak, Subang',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@example.com',
            'nama' => 'Ahmad Subang',
        ]);

        $user = User::where('email', 'ahmad@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->punyaRole('pelanggan'));

        $this->assertDatabaseHas('pelanggan', [
            'user_id' => $user->id,
            'nama_lengkap' => 'Ahmad Subang',
            'no_hp' => '081298765432',
            'alamat' => 'Jl. Cagak No. 12, Jalancagak, Subang',
        ]);
    }

    public function test_registrasi_gagal_jika_email_sudah_terdaftar(): void
    {
        $payload = [
            'nama' => 'Budi Duplikat',
            'email' => 'budi@blntrk.test', // Email sudah ada di seeder
            'no_hp' => '081299998888',
            'alamat' => 'Subang',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_admin_diarahkan_ke_dashboard_admin(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@blntrk.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_pemilik_diarahkan_ke_dashboard_pemilik(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'pemilik@blntrk.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pemilik.dashboard'));
    }

    public function test_login_pelanggan_diarahkan_ke_beranda(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'budi@blntrk.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('beranda'));
    }

    public function test_user_nonaktif_ditolak_login(): void
    {
        $budi = User::where('email', 'budi@blntrk.test')->first();
        $budi->update(['is_aktif' => false]);

        $response = $this->post(route('login'), [
            'email' => 'budi@blntrk.test',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_pelanggan_dilarang_akses_area_admin_dan_pemilik(): void
    {
        $budi = User::where('email', 'budi@blntrk.test')->first();

        // Akses admin
        $responseAdmin = $this->actingAs($budi)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(403);

        // Akses pemilik
        $responsePemilik = $this->actingAs($budi)->get(route('pemilik.dashboard'));
        $responsePemilik->assertStatus(403);
    }

    public function test_admin_bisa_akses_area_admin_tapi_dilarang_akses_pemilik(): void
    {
        $admin = User::where('email', 'admin@blntrk.test')->first();

        // Akses admin (diizinkan)
        $responseAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(200);

        // Akses pemilik (dilarang)
        $responsePemilik = $this->actingAs($admin)->get(route('pemilik.dashboard'));
        $responsePemilik->assertStatus(403);
    }

    public function test_pemilik_bisa_akses_area_pemilik_tapi_dilarang_akses_admin(): void
    {
        $pemilik = User::where('email', 'pemilik@blntrk.test')->first();

        // Akses pemilik (diizinkan)
        $responsePemilik = $this->actingAs($pemilik)->get(route('pemilik.dashboard'));
        $responsePemilik->assertStatus(200);

        // Akses admin (dilarang)
        $responseAdmin = $this->actingAs($pemilik)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(403);
    }

    public function test_tamu_diarahkan_ke_login_saat_akses_panel_terproteksi(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('pemilik.dashboard'))->assertRedirect(route('login'));
    }
}
