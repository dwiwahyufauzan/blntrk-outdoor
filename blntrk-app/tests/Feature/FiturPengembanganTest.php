<?php

namespace Tests\Feature;

use App\Data\PesananBaru;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Notifications\PengingatPesanan;
use App\Rules\NomorHpIndonesia;
use App\Services\KeranjangService;
use App\Services\LaporanService;
use App\Services\PengingatService;
use App\Services\PesananService;
use App\Services\WhatsAppService;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FiturPengembanganTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pelangganUser;

    protected User $orangLain;

    protected Pelanggan $pelanggan;

    protected Alat $alat;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['nama' => 'admin', 'label' => 'Administrator']);
        $rolePelanggan = Role::create(['nama' => 'pelanggan', 'label' => 'Pelanggan']);

        $this->admin = User::create([
            'role_id' => $roleAdmin->id, 'nama' => 'Admin', 'email' => 'admin@blntrk.test',
            'password' => bcrypt('password'), 'is_aktif' => true,
        ]);
        $this->pelangganUser = User::create([
            'role_id' => $rolePelanggan->id, 'nama' => 'Pelanggan', 'email' => 'p@blntrk.test',
            'password' => bcrypt('password'), 'is_aktif' => true,
        ]);
        $this->orangLain = User::create([
            'role_id' => $rolePelanggan->id, 'nama' => 'Orang Lain', 'email' => 'lain@blntrk.test',
            'password' => bcrypt('password'), 'is_aktif' => true,
        ]);
        Pelanggan::create(['user_id' => $this->orangLain->id, 'nama_lengkap' => 'Orang Lain', 'no_hp' => '081200000000', 'alamat' => 'Bandung']);
        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id, 'nama_lengkap' => 'Pelanggan',
            'no_hp' => '081234567890', 'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Umum', 'slug' => 'umum']);
        $this->alat = Alat::create([
            'kategori_id' => $kategori->id, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'varian' => 'D2',
            'harga_per_hari' => 40000, 'stok_total' => 3, 'stok_perawatan' => 0, 'is_aktif' => true,
        ]);
    }

    /** Membuat pesanan lewat service (jalur nyata) lalu memaksa status/jadwal sesuai skenario. */
    private function pesanan(StatusPesanan $status, int $mulaiOffset = 1, int $durasi = 2, int $jumlah = 1, ?Pelanggan $pelanggan = null): Pesanan
    {
        $mulai = today()->addDays($mulaiOffset);
        $p = app(PesananService::class)->buat($pelanggan ?? $this->pelanggan, $this->admin, PesananBaru::dari([
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays($durasi)->toDateString(),
            'items' => [['alat_id' => $this->alat->id, 'jumlah' => $jumlah]],
        ]));

        if ($status !== StatusPesanan::Menunggu) {
            $p->update(['status' => $status]);
            $p->pembayaran()->create([
                'jumlah' => $p->total_sewa, 'metode' => 'tunai', 'dibayar_pada' => now(), 'dicatat_oleh' => $this->admin->id,
            ]);
        }

        return $p->fresh();
    }

    // ---------------------------------------------------------------- operasional

    public function test_health_check_memastikan_database_terhubung(): void
    {
        $this->getJson('/health')->assertOk()->assertJson(['status' => 'ok', 'database' => 'terhubung']);
    }

    public function test_endpoint_cron_ditolak_tanpa_token_valid(): void
    {
        config(['services.cron.token' => 'rahasia-cron']);

        $this->get('/cron/jadwal')->assertNotFound();
        $this->get('/cron/jadwal?token=salah')->assertNotFound();
        $this->withHeader('Authorization', 'Bearer rahasia-cron')->get('/cron/jadwal')->assertOk()->assertJson(['status' => 'ok']);
        $this->withHeader('X-Cron-Token', 'rahasia-cron')->get('/cron/jadwal')->assertOk();
    }

    public function test_endpoint_cron_mati_bila_token_tidak_dikonfigurasi(): void
    {
        config(['services.cron.token' => null]);

        $this->get('/cron/jadwal')->assertNotFound();
        $this->get('/cron/jadwal?token=')->assertNotFound();
    }

    // ---------------------------------------------------------------- no-show

    public function test_pesanan_dibayar_tidak_diambil_dibatalkan_otomatis(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, -3, 2);
        // batas_ambil 3 hari lalu, toleransi bawaan 1 hari
        $this->artisan('pesanan:perbarui-status')->assertSuccessful();

        $p->refresh();
        $this->assertSame(StatusPesanan::Dibatalkan, $p->status);
        $this->assertStringContainsString('no-show', $p->alasan_batal);

        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'pesanan.batal', 'user_id' => null]);
    }

    public function test_noshow_menghormati_toleransi_hari(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, -2, 2);

        // Masih dalam toleransi 3 hari: tidak dibatalkan
        Pengaturan::setel('noshow_toleransi_hari', '3');
        $this->artisan('pesanan:perbarui-status');
        $this->assertSame(StatusPesanan::Dibayar, $p->fresh()->status);

        // Toleransi 0 hari
        Pengaturan::setel('noshow_toleransi_hari', '0');
        Cache::flush();
        $this->artisan('pesanan:perbarui-status');
        $this->assertSame(StatusPesanan::Dibatalkan, $p->fresh()->status);
    }

    public function test_noshow_tidak_berjalan_bila_dimatikan(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, -3, 2);
        Pengaturan::setel('noshow_batal_otomatis', '0');
        Cache::flush();

        $this->artisan('pesanan:perbarui-status');

        $this->assertSame(StatusPesanan::Dibayar, $p->fresh()->status);
    }

    public function test_pesanan_noshow_tidak_menahan_stok_setelah_dibatalkan(): void
    {
        $this->pesanan(StatusPesanan::Dibayar, -3, 5, 3);
        // stok_total 3 terpakai penuh pada hari ini; pesanan baru di masa depan tetap boleh,
        // tetapi yang tumpang tindih hari ini harus bebas setelah no-show dibatalkan
        $this->artisan('pesanan:perbarui-status');

        $baru = app(PesananService::class)->buat($this->pelanggan, $this->admin, PesananBaru::dari([
            'tanggal_mulai' => today()->toDateString(),
            'tanggal_selesai' => today()->addDay()->toDateString(),
            'items' => [['alat_id' => $this->alat->id, 'jumlah' => 3]],
        ]));
        $this->assertSame(StatusPesanan::Menunggu, $baru->status);
    }

    public function test_admin_membatalkan_pesanan_dibayar_dengan_dana_hangus(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, 2, 2);

        $this->actingAs($this->admin)
            ->patch(route('admin.pesanan.batal', $p->id), ['alasan_batal' => 'Cuaca buruk', 'penanganan_dana' => 'hangus'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame(StatusPesanan::Dibatalkan, $p->fresh()->status);
        $this->assertDatabaseCount('refund', 0);
    }

    public function test_dashboard_admin_menandai_pesanan_dibayar_lewat_batas_ambil(): void
    {
        $this->pesanan(StatusPesanan::Dibayar, -1, 2);

        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()->assertSee('sudah dibayar tetapi tidak diambil');
    }

    // ---------------------------------------------------------------- bukti transfer

    public function test_pelanggan_mengunggah_bukti_transfer_dan_admin_dapat_melihatnya(): void
    {
        Storage::fake('local');
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 2);

        $this->actingAs($this->pelangganUser)
            ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image('bukti.jpg', 400, 400)])
            ->assertRedirect(route('pesanan.show', $p->no_resi));

        $p->refresh();
        $this->assertNotNull($p->bukti_transfer);
        Storage::disk('local')->assertExists($p->bukti_transfer);

        $this->actingAs($this->pelangganUser)->get(route('pesanan.bukti', $p->no_resi))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.pesanan.bukti', $p->id))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.pesanan.show', $p->id))->assertOk()->assertSee('Lihat Bukti');
    }

    public function test_bukti_transfer_tidak_dapat_diakses_orang_lain_atau_diunggah_ke_pesanan_orang_lain(): void
    {
        Storage::fake('local');
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 2);

        $this->actingAs($this->orangLain)
            ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image('bukti.jpg')])
            ->assertNotFound();
        $this->assertNull($p->fresh()->bukti_transfer);

        $this->actingAs($this->pelangganUser)->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image('b.jpg')]);
        $this->actingAs($this->orangLain)->get(route('pesanan.bukti', $p->no_resi))->assertNotFound();
        $this->actingAs($this->orangLain)->get(route('admin.pesanan.bukti', $p->id))->assertForbidden();
        auth()->logout();
        $this->get(route('pesanan.bukti', $p->no_resi))->assertRedirect('/login');
    }

    public function test_bukti_transfer_hanya_menerima_gambar_atau_pdf_dan_hanya_saat_menunggu(): void
    {
        Storage::fake('local');
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 2);

        $this->actingAs($this->pelangganUser)
            ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])
            ->assertSessionHasErrors('bukti');
        $this->actingAs($this->pelangganUser)
            ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->create('besar.pdf', 5000, 'application/pdf')])
            ->assertSessionHasErrors('bukti');
        $this->actingAs($this->pelangganUser)
            ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->create('ok.pdf', 100, 'application/pdf')])
            ->assertSessionHasNoErrors();

        $dibayar = $this->pesanan(StatusPesanan::Dibayar, 10, 2);
        $this->actingAs($this->pelangganUser)
            ->post(route('pesanan.bukti.upload', $dibayar->no_resi), ['bukti' => UploadedFile::fake()->image('b.jpg')])
            ->assertForbidden();
    }

    public function test_mengganti_bukti_menghapus_berkas_lama(): void
    {
        Storage::fake('local');
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 2);

        $this->actingAs($this->pelangganUser)->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image('a.jpg')]);
        $lama = $p->fresh()->bukti_transfer;
        $this->actingAs($this->pelangganUser)->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image('b.jpg')]);
        $baru = $p->fresh()->bukti_transfer;

        $this->assertNotSame($lama, $baru);
        Storage::disk('local')->assertMissing($lama);
        Storage::disk('local')->assertExists($baru);
    }

    // ---------------------------------------------------------------- perpanjangan

    public function test_admin_memperpanjang_sewa_dengan_pembayaran_tambahan(): void
    {
        $p = $this->pesanan(StatusPesanan::Disewa, 0, 2);
        $p->update(['tanggal_mulai' => today(), 'jatuh_tempo' => now()->addHours(5)]);
        $totalLama = (float) $p->total_sewa;
        $selesaiBaru = $p->tanggal_selesai->copy()->addDays(2);

        $this->actingAs($this->admin)
            ->post(route('admin.pesanan.perpanjang', $p->id), ['tanggal_selesai' => $selesaiBaru->toDateString(), 'metode' => 'tunai'])
            ->assertSessionHasNoErrors()->assertRedirect();

        $p->refresh();
        $this->assertSame(4, $p->durasi_hari);
        $this->assertTrue($p->tanggal_selesai->isSameDay($selesaiBaru));
        $this->assertEquals($totalLama + 2 * 40000, (float) $p->total_sewa);
        $tambahan = $p->pembayaran()->where('jenis', 'perpanjangan')->first();
        $this->assertNotNull($tambahan);
        $this->assertEquals(80000, (float) $tambahan->jumlah);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'pesanan.perpanjang']);
    }

    public function test_perpanjangan_ditolak_bila_stok_hari_tambahan_tidak_cukup(): void
    {
        $p = $this->pesanan(StatusPesanan::Disewa, 0, 2, 2);
        $p->update(['jatuh_tempo' => now()->addHours(5)]);
        // Pesanan lain memakai 2 dari 3 unit pada hari tambahan -> hanya 1 sisa, butuh 2
        $this->pesanan(StatusPesanan::Dibayar, 2, 3, 2);
        $totalLama = (float) $p->total_sewa;

        $this->actingAs($this->admin)
            ->post(route('admin.pesanan.perpanjang', $p->id), [
                'tanggal_selesai' => $p->tanggal_selesai->copy()->addDays(2)->toDateString(), 'metode' => 'tunai',
            ])->assertSessionHasErrors('tanggal_selesai');

        $this->assertEquals($totalLama, (float) $p->fresh()->total_sewa);
        $this->assertSame(0, $p->pembayaran()->where('jenis', 'perpanjangan')->count());
    }

    public function test_perpanjangan_hanya_untuk_pesanan_disewa_dan_oleh_admin(): void
    {
        $dibayar = $this->pesanan(StatusPesanan::Dibayar, 2, 2);
        $this->actingAs($this->admin)
            ->post(route('admin.pesanan.perpanjang', $dibayar->id), ['tanggal_selesai' => today()->addDays(9)->toDateString(), 'metode' => 'tunai'])
            ->assertForbidden();

        $disewa = $this->pesanan(StatusPesanan::Disewa, 20, 2);
        $this->actingAs($this->pelangganUser)
            ->post(route('admin.pesanan.perpanjang', $disewa->id), ['tanggal_selesai' => today()->addDays(30)->toDateString(), 'metode' => 'tunai'])
            ->assertForbidden();
    }

    public function test_perpanjangan_tidak_boleh_melewati_maksimal_durasi(): void
    {
        $p = $this->pesanan(StatusPesanan::Disewa, 0, 12);
        $p->update(['jatuh_tempo' => now()->addHours(5)]);

        $this->actingAs($this->admin)
            ->post(route('admin.pesanan.perpanjang', $p->id), [
                'tanggal_selesai' => $p->tanggal_selesai->copy()->addDays(5)->toDateString(), 'metode' => 'tunai',
            ])->assertSessionHasErrors('tanggal_selesai');
    }

    // ---------------------------------------------------------------- ubah jadwal

    public function test_pelanggan_mengubah_jadwal_pesanan_menunggu_dan_total_dihitung_ulang(): void
    {
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 2);
        $this->assertEquals(80000, (float) $p->total_sewa);

        $mulai = today()->addDays(10);
        $this->actingAs($this->pelangganUser)
            ->patch(route('pesanan.jadwal', $p->no_resi), [
                'tanggal_mulai' => $mulai->toDateString(), 'tanggal_selesai' => $mulai->copy()->addDays(3)->toDateString(),
            ])->assertSessionHasNoErrors();

        $p->refresh();
        $this->assertTrue($p->tanggal_mulai->isSameDay($mulai));
        $this->assertSame(3, $p->durasi_hari);
        $this->assertEquals(120000, (float) $p->total_sewa);
        $this->assertEquals(120000, (float) $p->detail->first()->subtotal);
        $this->assertTrue($p->batas_ambil->isSameDay($mulai));
    }

    public function test_ubah_jadwal_ditolak_bila_stok_penuh_pada_tanggal_baru(): void
    {
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 2, 2);
        $this->pesanan(StatusPesanan::Dibayar, 10, 3, 2, Pelanggan::where('user_id', $this->orangLain->id)->first());

        $mulai = today()->addDays(11);
        $this->actingAs($this->pelangganUser)
            ->patch(route('pesanan.jadwal', $p->no_resi), [
                'tanggal_mulai' => $mulai->toDateString(), 'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
            ])->assertSessionHasErrors('tanggal_mulai');

        $this->assertTrue($p->fresh()->tanggal_mulai->isSameDay(today()->addDays(2)));
    }

    public function test_ubah_jadwal_ke_tanggal_sendiri_yang_tumpang_tindih_tidak_menghitung_dirinya_dua_kali(): void
    {
        $p = $this->pesanan(StatusPesanan::Menunggu, 2, 3, 3); // memakai seluruh 3 unit

        $this->actingAs($this->pelangganUser)
            ->patch(route('pesanan.jadwal', $p->no_resi), [
                'tanggal_mulai' => today()->addDays(3)->toDateString(), 'tanggal_selesai' => today()->addDays(6)->toDateString(),
            ])->assertSessionHasNoErrors();

        $this->assertTrue($p->fresh()->tanggal_mulai->isSameDay(today()->addDays(3)));
    }

    public function test_pelanggan_tidak_dapat_ubah_jadwal_pesanan_dibayar_atau_milik_orang_lain(): void
    {
        $dibayar = $this->pesanan(StatusPesanan::Dibayar, 2, 2);
        $payload = ['tanggal_mulai' => today()->addDays(8)->toDateString(), 'tanggal_selesai' => today()->addDays(10)->toDateString()];

        $this->actingAs($this->pelangganUser)->patch(route('pesanan.jadwal', $dibayar->no_resi), $payload)->assertForbidden();

        $menunggu = $this->pesanan(StatusPesanan::Menunggu, 20, 2);
        $this->actingAs($this->orangLain)->patch(route('pesanan.jadwal', $menunggu->no_resi), $payload)->assertNotFound();
    }

    public function test_admin_ubah_jadwal_pesanan_dibayar_hanya_dengan_durasi_tetap(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, 2, 2);
        $mulai = today()->addDays(8);

        $this->actingAs($this->admin)->patch(route('admin.pesanan.jadwal', $p->id), [
            'tanggal_mulai' => $mulai->toDateString(), 'tanggal_selesai' => $mulai->copy()->addDays(4)->toDateString(),
        ])->assertSessionHasErrors('tanggal_selesai');

        $this->actingAs($this->admin)->patch(route('admin.pesanan.jadwal', $p->id), [
            'tanggal_mulai' => $mulai->toDateString(), 'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
        ])->assertSessionHasNoErrors();

        $this->assertTrue($p->fresh()->tanggal_mulai->isSameDay($mulai));
        $this->assertEquals(80000, (float) $p->fresh()->total_sewa);
    }

    // ---------------------------------------------------------------- deposit uang

    public function test_serah_terima_dengan_deposit_uang_menggantikan_kartu_identitas(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, 0, 2);

        $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $p->id), [
            'jenis_jaminan' => 'deposit', 'nominal_deposit' => 150000,
        ])->assertSessionHasNoErrors();

        $p->refresh();
        $this->assertSame(StatusPesanan::Disewa, $p->status);
        $this->assertTrue($p->jaminan->is_deposit);
        $this->assertEquals(150000, (float) $p->jaminan->nominal);
        $this->assertSame('Deposit Uang Rp150.000', $p->jaminan->label_jenis);
        $this->actingAs($this->admin)->get(route('admin.pesanan.show', $p->id))->assertOk()->assertSee('Deposit Uang Rp150.000');
        $this->actingAs($this->pelangganUser)->get(route('pesanan.show', $p->no_resi))->assertOk()->assertSee('Deposit Uang Rp150.000');
    }

    public function test_deposit_wajib_bernominal_dan_kartu_wajib_bernomor(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, 0, 2);

        $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $p->id), ['jenis_jaminan' => 'deposit'])
            ->assertSessionHasErrors('nominal_deposit');
        $this->actingAs($this->admin)->post(route('admin.pesanan.ambil', $p->id), ['jenis_jaminan' => 'ktp'])
            ->assertSessionHasErrors('nomor_identitas');
        $this->assertSame(StatusPesanan::Dibayar, $p->fresh()->status);
    }

    // ---------------------------------------------------------------- perawatan

    public function test_perawatan_masuk_dan_selesai_tercatat_di_riwayat(): void
    {
        $this->actingAs($this->admin)->patch(route('admin.perawatan.tambah', $this->alat->id), ['jumlah' => 2, 'keterangan' => 'Sobek'])
            ->assertSessionHasNoErrors();
        $this->actingAs($this->admin)->patch(route('admin.perawatan.selesai', $this->alat->id), ['jumlah' => 1, 'catatan' => 'Dijahit'])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $this->alat->fresh()->stok_perawatan);
        $this->assertDatabaseHas('riwayat_perawatan', ['alat_id' => $this->alat->id, 'jenis' => 'masuk', 'jumlah' => 2, 'keterangan' => 'Sobek', 'user_id' => $this->admin->id]);
        $this->assertDatabaseHas('riwayat_perawatan', ['alat_id' => $this->alat->id, 'jenis' => 'selesai', 'jumlah' => 1, 'keterangan' => 'Dijahit']);

        $this->actingAs($this->admin)->get(route('admin.perawatan.index'))->assertOk()->assertSee('Riwayat Perawatan')->assertSee('Dijahit');
    }

    public function test_unit_yang_dibutuhkan_pesanan_aktif_tidak_bisa_dipindah_ke_perawatan(): void
    {
        $this->pesanan(StatusPesanan::Dibayar, 1, 2, 3); // seluruh 3 unit dibutuhkan

        $this->actingAs($this->admin)->patch(route('admin.perawatan.tambah', $this->alat->id), ['jumlah' => 1])
            ->assertSessionHasErrors('jumlah');
        $this->assertSame(0, $this->alat->fresh()->stok_perawatan);
    }

    // ---------------------------------------------------------------- guard alat & pencarian

    public function test_alat_dengan_pesanan_aktif_tidak_dapat_dihapus_atau_dikurangi_stoknya(): void
    {
        $this->pesanan(StatusPesanan::Dibayar, 1, 2, 2);

        $this->actingAs($this->admin)->delete(route('admin.alat.destroy', $this->alat->id))
            ->assertSessionHas('error');
        $this->assertNotSoftDeleted($this->alat);

        $this->actingAs($this->admin)->put(route('admin.alat.update', $this->alat->id), [
            'kategori_id' => $this->alat->kategori_id, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'harga_per_hari' => 40000,
            'stok_total' => 1, 'stok_perawatan' => 0,
        ])->assertSessionHasErrors('stok_total');
        $this->assertSame(3, $this->alat->fresh()->stok_total);
    }

    public function test_menonaktifkan_alat_terpakai_memberi_peringatan_dan_alat_bebas_dapat_dihapus(): void
    {
        $p = $this->pesanan(StatusPesanan::Dibayar, 1, 2, 1);

        $this->actingAs($this->admin)->patch(route('admin.alat.toggle', $this->alat->id))->assertSessionHas('peringatan');

        $p->update(['status' => StatusPesanan::Dikembalikan]);
        $this->actingAs($this->admin)->delete(route('admin.alat.destroy', $this->alat->id))->assertSessionHas('sukses');
        $this->assertSoftDeleted($this->alat);
    }

    public function test_varian_alat_dapat_diisi_dan_divalidasi(): void
    {
        $payload = [
            'kategori_id' => $this->alat->kategori_id, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'harga_per_hari' => 40000,
            'stok_total' => 3, 'stok_perawatan' => 0,
        ];

        $this->actingAs($this->admin)->put(route('admin.alat.update', $this->alat->id), $payload + ['varian' => 'Kapasitas 4 orang'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Kapasitas 4 orang', $this->alat->fresh()->varian);

        $this->actingAs($this->admin)->put(route('admin.alat.update', $this->alat->id), $payload + ['varian' => str_repeat('x', 60)])
            ->assertSessionHasErrors('varian');
    }

    public function test_pola_pencarian_membuang_wildcard_buatan_pengguna(): void
    {
        $this->assertSame('%tenda%', like_pattern('  te%n_da\\ '));
        $this->assertSame('%%', like_pattern(null));

        // Mencari "%" tidak boleh cocok dengan semua alat
        $this->actingAs($this->admin)->get(route('admin.alat.index', ['q' => '%']))->assertOk();
        $this->get(route('katalog.index', ['q' => '_']))->assertOk();
    }

    public function test_pengaturan_ambil_tidak_menyimpan_nilai_default_pemanggil(): void
    {
        $this->assertSame('A', Pengaturan::ambil('kunci_baru', 'A'));
        $this->assertSame('B', Pengaturan::ambil('kunci_baru', 'B'));

        Pengaturan::setel('kunci_baru', 'nyata');
        $this->assertSame('nyata', Pengaturan::ambil('kunci_baru', 'A'));
    }

    // ---------------------------------------------------------------- keranjang vs stok per tanggal

    public function test_keranjang_memakai_stok_per_tanggal_bukan_stok_global(): void
    {
        // Seluruh unit terpakai pada minggu depan, tetapi hari ini kosong
        $this->pesanan(StatusPesanan::Dibayar, 20, 3, 3);

        $this->post(route('keranjang.tambah'), ['alat_id' => $this->alat->id, 'jumlah' => 3])->assertSessionDoesntHaveErrors();
        $this->assertSame([$this->alat->id => 3], app(KeranjangService::class)->getRaw());
    }

    // ---------------------------------------------------------------- pengingat email

    public function test_pengingat_jatuh_tempo_dikirim_sekali_ke_pelanggan_terdaftar(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']);
        $p = $this->pesanan(StatusPesanan::Disewa, 0, 1);
        $p->update(['jatuh_tempo' => now()->addHours(6)]);

        $hasil = app(PengingatService::class)->kirimSemua();
        $this->assertSame(1, $hasil['kembali']);
        Notification::assertSentTo($this->pelangganUser, PengingatPesanan::class, fn ($n) => $n->jenis === PengingatPesanan::KEMBALI);

        // Putaran kedua tidak mengirim ulang
        $this->assertSame(0, app(PengingatService::class)->kirimSemua()['kembali']);
        Notification::assertSentToTimes($this->pelangganUser, PengingatPesanan::class, 1);
    }

    public function test_pengingat_terlambat_memuat_estimasi_denda_dan_hanya_sekali(): void
    {
        Notification::fake();
        config(['mail.default' => 'smtp']);
        $p = $this->pesanan(StatusPesanan::Terlambat, 0, 1);
        $p->update(['tanggal_mulai' => today()->subDays(4), 'tanggal_selesai' => today()->subDays(2), 'jatuh_tempo' => now()->subDays(2)]);

        $this->assertSame(1, app(PengingatService::class)->kirimSemua()['terlambat']);
        Notification::assertSentTo($this->pelangganUser, PengingatPesanan::class, fn ($n) => $n->jenis === PengingatPesanan::TERLAMBAT && $n->estimasiDenda > 0);
        $this->assertSame(0, app(PengingatService::class)->kirimSemua()['terlambat']);
    }

    public function test_pengingat_dilewati_untuk_walk_in_dan_saat_mailer_log(): void
    {
        Notification::fake();
        $walkIn = Pelanggan::create(['user_id' => null, 'nama_lengkap' => 'Walk In', 'no_hp' => '081111111111', 'alamat' => '-']);
        $p = $this->pesanan(StatusPesanan::Disewa, 0, 1, 1, $walkIn);
        $p->update(['jatuh_tempo' => now()->addHours(3)]);

        config(['mail.default' => 'smtp']);
        $this->assertSame(0, app(PengingatService::class)->kirimSemua()['kembali']);
        $this->assertNull($p->fresh()->pengingat_kembali_pada);

        $p2 = $this->pesanan(StatusPesanan::Disewa, 0, 1);
        $p2->update(['jatuh_tempo' => now()->addHours(3)]);
        config(['mail.default' => 'log']);
        $this->assertSame(0, app(PengingatService::class)->kirimSemua()['kembali']);
        Notification::assertNothingSent();
    }

    public function test_pengingat_terdaftar_di_scheduler(): void
    {
        $terdaftar = collect(app(Schedule::class)->events())
            ->contains(fn ($e) => str_contains($e->command, 'pesanan:kirim-pengingat'));

        $this->assertTrue($terdaftar);
    }

    // ---------------------------------------------------------------- kalender katalog & pesan WA

    public function test_halaman_detail_alat_menampilkan_kalender_ketersediaan(): void
    {
        $this->pesanan(StatusPesanan::Dibayar, 1, 2, 3);

        $this->get(route('katalog.show', $this->alat->id))->assertOk()->assertSee('Kalender Ketersediaan');
    }

    public function test_pesan_wa_terlambat_memakai_jam_kembali_pesanan(): void
    {
        $p = $this->pesanan(StatusPesanan::Terlambat, 0, 1);
        $p->update(['jatuh_tempo' => today()->subDay()->setTime(15, 30)]);

        $pesan = WhatsAppService::pesanPeringatanTerlambat($p->fresh(), 1, 40000);

        $this->assertStringContainsString('pukul 15:30 WIB', $pesan);
        $this->assertStringNotContainsString('12:00', $pesan);
    }

    // ---------------------------------------------------------------- pemilik: analitik & pendapatan bersih

    private function pemilik(): User
    {
        return User::create([
            'role_id' => Role::create(['nama' => 'pemilik', 'label' => 'Pemilik'])->id, 'nama' => 'Pemilik',
            'email' => 'pemilik@blntrk.test', 'password' => bcrypt('password'), 'is_aktif' => true,
        ]);
    }

    public function test_pendapatan_pesanan_dibatalkan_mengikuti_kebijakan_dana_refund_atau_hangus(): void
    {
        $laporan = app(LaporanService::class);
        $awal = now()->startOfDay();
        $akhir = now()->endOfDay();

        $direfund = $this->pesanan(StatusPesanan::Dibayar, 3, 2); // 80.000, dikembalikan penuh
        $hangus = $this->pesanan(StatusPesanan::Dibayar, 4, 2); // 80.000, hangus
        $this->pesanan(StatusPesanan::Dibayar, 30, 2); // 80.000, tetap aktif

        $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $direfund->id), [
            'alasan_batal' => 'x', 'penanganan_dana' => 'refund', 'jumlah_refund' => 80000, 'metode_refund' => 'tunai',
        ])->assertSessionHasNoErrors();
        $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $hangus->id), [
            'alasan_batal' => 'x', 'penanganan_dana' => 'hangus',
        ])->assertSessionHasNoErrors();

        // Kas: 3 pembayaran masuk (240.000) dikurangi 1 refund (80.000); dana hangus tetap menjadi pendapatan
        $this->assertEqualsWithDelta(160000, $laporan->pendapatanSewaBersih($awal, $akhir), 0.01);
        $hasil = $laporan->laporanPendapatan(today(), today());
        $this->assertEqualsWithDelta(240000, $hasil['total_pendapatan_sewa'], 0.01);
        $this->assertEqualsWithDelta(80000, $hasil['total_refund'], 0.01);
        $this->assertEqualsWithDelta(160000, $hasil['total_pendapatan_bersih'], 0.01);
    }

    public function test_okupansi_per_alat_menghitung_unit_hari_pada_rentang(): void
    {
        // 2 unit x 3 hari = 6 unit-hari dari kapasitas 3 unit x 10 hari = 30 -> 20%
        $this->pesanan(StatusPesanan::Dibayar, 1, 3, 2);

        $hasil = app(LaporanService::class)->okupansiPerAlat(today(), today()->addDays(9));

        $baris = $hasil->firstWhere(fn ($r) => $r['alat']->id === $this->alat->id);
        $this->assertSame(6, $baris['unit_hari']);
        $this->assertSame(30, $baris['kapasitas_unit_hari']);
        $this->assertEquals(20.0, $baris['persen']);
    }

    public function test_dashboard_pemilik_menampilkan_perbandingan_rasio_denda_dan_okupansi(): void
    {
        $this->pesanan(StatusPesanan::Dibayar, 1, 2);

        $this->actingAs($this->pemilik())->get(route('pemilik.dashboard'))
            ->assertOk()
            ->assertSee('Rasio Denda terhadap Pendapatan')
            ->assertSee('Okupansi Alat')
            ->assertSee('Tenda');
    }

    public function test_pemilik_mengatur_kebijakan_noshow(): void
    {
        $pemilik = $this->pemilik();
        $payload = [
            'nama_toko' => 'BLNTRK', 'alamat_toko' => 'Subang', 'no_wa_toko' => '6281234567890', 'jam_kembali' => '12:00',
            'toleransi_menit' => 60, 'persen_denda_harian' => 100, 'maks_durasi_hari' => 14, 'ambang_stok_kritis' => 2,
            'noshow_batal_otomatis' => 1, 'noshow_toleransi_hari' => 2,
        ];

        $this->actingAs($pemilik)->put(route('pemilik.pengaturan.update'), $payload)->assertSessionHasNoErrors();
        $this->assertSame('2', Pengaturan::ambil('noshow_toleransi_hari'));

        $this->actingAs($pemilik)->put(route('pemilik.pengaturan.update'), ['noshow_toleransi_hari' => 99] + $payload)
            ->assertSessionHasErrors('noshow_toleransi_hari');
    }

    // ---------------------------------------------------------------- validasi nomor HP

    public function test_nomor_hp_indonesia_valid_dan_tidak_valid(): void
    {
        foreach (['081234567890', '+62 812-3456-7890', '6281234567890', '(0812) 3456 7890'] as $ok) {
            $this->assertTrue(NomorHpIndonesia::valid($ok), $ok);
        }
        foreach (['12345678', '0212345678', '08123', 'abcdefghijk', '', '0812345678901234', null] as $bad) {
            $this->assertFalse(NomorHpIndonesia::valid($bad), (string) $bad);
        }
    }

    public function test_registrasi_menolak_nomor_hp_palsu(): void
    {
        $data = ['nama' => 'Budi', 'email' => 'budi@example.com', 'alamat' => 'Subang', 'password' => 'Password-123', 'password_confirmation' => 'Password-123'];

        $this->post('/register', $data + ['no_hp' => '00000000'])->assertSessionHasErrors('no_hp');
        $this->assertGuest();
    }

    // ---------------------------------------------------------------- seeder produksi

    private function jalankanUserSeederProduksi(array $env): void
    {
        $this->app['env'] = 'production';
        foreach ($env as $k => $v) {
            putenv("{$k}={$v}");
            $_ENV[$k] = $_SERVER[$k] = $v;
        }

        try {
            app(UserSeeder::class)->setContainer($this->app)->run();
        } finally {
            foreach (array_keys($env) as $k) {
                putenv($k);
                unset($_ENV[$k], $_SERVER[$k]);
            }
            $this->app['env'] = 'testing';
        }
    }

    public function test_seeder_produksi_tidak_menghidupkan_kembali_akun_awal_setelah_instalasi_pertama(): void
    {
        Role::create(['nama' => 'pemilik', 'label' => 'Pemilik']);
        // Pemilik sudah mengganti email akunnya; tabel users tidak kosong
        User::where('id', $this->admin->id)->update(['email' => 'pemilik-asli@tokoku.id']);

        $this->jalankanUserSeederProduksi(['SEED_DEFAULT_PASSWORD' => 'Rahasia-Prod-123', 'SEED_DEMO' => 'false']);

        $this->assertDatabaseMissing('users', ['email' => 'pemilik@blntrk.test']);
        $this->assertDatabaseMissing('users', ['email' => 'admin@blntrk.test']);
    }

    public function test_seeder_produksi_instalasi_pertama_membuat_pemilik_dan_admin_tanpa_pelanggan_demo(): void
    {
        Role::create(['nama' => 'pemilik', 'label' => 'Pemilik']);
        DB::table('pelanggan')->delete();
        User::query()->delete();

        $this->jalankanUserSeederProduksi([
            'SEED_DEFAULT_PASSWORD' => 'Rahasia-Prod-123', 'SEED_DEMO' => 'false',
            'SEED_OWNER_EMAIL' => 'pemilik@tokoku.id', 'SEED_ADMIN_EMAIL' => 'admin@tokoku.id',
        ]);

        $this->assertSame(['admin@tokoku.id', 'pemilik@tokoku.id'], User::orderBy('email')->pluck('email')->all());
        $this->assertDatabaseMissing('users', ['email' => 'budi@blntrk.test']);
    }
}
