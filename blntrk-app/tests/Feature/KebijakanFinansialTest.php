<?php

namespace Tests\Feature;

use App\Data\PengembalianBaru;
use App\Data\PesananBaru;
use App\Data\RefundBaru;
use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\KategoriAlat;
use App\Models\LogAktivitas;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\BuktiTransferService;
use App\Services\LaporanService;
use App\Services\PengembalianService;
use App\Services\PesananService;
use App\Support\PengaturanToko;
use Carbon\Carbon;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Batas bayar pesanan menunggu, refund, metode pelunasan denda, waktu pengembalian, stok per tanggal,
 * dan pagar keamanan kecil (throttle register, token cron, batas unggah bukti).
 */
class KebijakanFinansialTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pelangganUser;

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
        $this->pelanggan = Pelanggan::create([
            'user_id' => $this->pelangganUser->id, 'nama_lengkap' => 'Pelanggan',
            'no_hp' => '081234567890', 'alamat' => 'Subang',
        ]);

        $kategori = KategoriAlat::create(['nama' => 'Umum', 'slug' => 'umum']);
        $this->alat = Alat::create([
            'kategori_id' => $kategori->id, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'varian' => 'D2',
            'harga_per_hari' => 40000, 'harga_beli' => 300000, 'stok_total' => 20, 'stok_perawatan' => 0, 'is_aktif' => true,
        ]);
    }

    private function buat(int $mulaiOffset = 5, int $durasi = 2, ?User $pembuat = null): Pesanan
    {
        $mulai = today()->addDays($mulaiOffset);

        return app(PesananService::class)->buat($this->pelanggan, $pembuat ?? $this->pelangganUser, PesananBaru::dari([
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays($durasi)->toDateString(),
            'items' => [['alat_id' => $this->alat->id, 'jumlah' => 1]],
        ]));
    }

    private function dibayar(int $mulaiOffset = 5): Pesanan
    {
        $p = $this->buat($mulaiOffset);
        app(PesananService::class)->bayar($p, $this->admin, ['jumlah' => $p->total_sewa, 'metode' => 'tunai']);

        return $p->fresh();
    }

    // ---------------------------------------------------------------- B1: batas bayar & limit

    public function test_batas_bayar_24_jam_dan_tidak_melewati_batas_ambil(): void
    {
        Carbon::setTestNow('2026-11-11 10:00:00');

        $jauh = $this->buat(10);
        $this->assertEquals(now()->addHours(24)->toDateTimeString(), $jauh->batas_bayar->toDateTimeString());

        // Pemesanan mendadak untuk hari ini: batas bayar dipotong ke batas ambil (23:59 hari ini)
        $mendadak = $this->buat(0);
        $this->assertEquals($mendadak->batas_ambil->toDateTimeString(), $mendadak->batas_bayar->toDateTimeString());
    }

    public function test_batas_bayar_nol_jam_hanya_dibatasi_batas_ambil(): void
    {
        Pengaturan::setel('batas_bayar_jam', '0');

        $p = $this->buat(10);

        $this->assertEquals($p->batas_ambil->toDateTimeString(), $p->batas_bayar->toDateTimeString());
    }

    public function test_scheduler_membatalkan_pesanan_menunggu_yang_lewat_batas_bayar(): void
    {
        $p = $this->buat(10);

        Carbon::setTestNow(now()->addHours(25));
        $this->artisan('pesanan:perbarui-status')->assertSuccessful();

        $p->refresh();
        $this->assertSame(StatusPesanan::Dibatalkan, $p->status);
        $this->assertStringContainsString('batas waktu pembayaran', $p->alasan_batal);
    }

    public function test_pesanan_dengan_bukti_transfer_tidak_dibatalkan_karena_batas_bayar(): void
    {
        $p = $this->buat(10);
        $p->update(['bukti_transfer' => 'bukti-transfer/x.jpg', 'bukti_diunggah_pada' => now()]);

        Carbon::setTestNow(now()->addHours(25));
        $this->artisan('pesanan:perbarui-status');

        $this->assertSame(StatusPesanan::Menunggu, $p->fresh()->status);
    }

    public function test_pesanan_dalam_batas_bayar_tidak_dibatalkan(): void
    {
        $p = $this->buat(10);

        Carbon::setTestNow(now()->addHours(23));
        $this->artisan('pesanan:perbarui-status');

        $this->assertSame(StatusPesanan::Menunggu, $p->fresh()->status);
    }

    public function test_pelanggan_dibatasi_jumlah_pesanan_menunggu(): void
    {
        Pengaturan::setel('maks_pesanan_menunggu', '2');
        $this->buat(5);
        $this->buat(6);

        try {
            $this->buat(7);
            $this->fail('Pesanan ketiga seharusnya ditolak.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('pesanan', $e->errors());
        }

        $this->assertSame(2, Pesanan::where('status', StatusPesanan::Menunggu)->count());
    }

    public function test_pesanan_dibayar_tidak_dihitung_dalam_limit_dan_admin_tidak_dibatasi(): void
    {
        Pengaturan::setel('maks_pesanan_menunggu', '1');
        $this->dibayar(5);

        // Pesanan pertama sudah dibayar, jadi pelanggan boleh membuat satu pesanan menunggu
        $this->buat(6);

        // Admin (walk-in) tidak terkena limit
        $this->buat(7, 2, $this->admin);
        $this->buat(8, 2, $this->admin);

        $this->assertSame(3, Pesanan::where('status', StatusPesanan::Menunggu)->count());
    }

    public function test_limit_nol_berarti_tanpa_batas(): void
    {
        Pengaturan::setel('maks_pesanan_menunggu', '0');

        foreach (range(5, 9) as $offset) {
            $this->buat($offset);
        }

        $this->assertSame(5, Pesanan::count());
    }

    public function test_pemilik_dapat_mengubah_pengaturan_batas_bayar_dan_limit(): void
    {
        $pemilik = User::create([
            'role_id' => Role::create(['nama' => 'pemilik', 'label' => 'Pemilik'])->id, 'nama' => 'Pemilik',
            'email' => 'pemilik@blntrk.test', 'password' => bcrypt('password'), 'is_aktif' => true,
        ]);

        $payload = PengaturanToko::semua();
        $payload['batas_bayar_jam'] = '12';
        $payload['maks_pesanan_menunggu'] = '5';

        $this->actingAs($pemilik)->put(route('pemilik.pengaturan.update'), $payload)->assertSessionHasNoErrors();

        $this->assertSame(12, PengaturanToko::batasBayarJam());
        $this->assertSame(5, PengaturanToko::maksPesananMenunggu());
    }

    // ---------------------------------------------------------------- B2: refund

    public function test_admin_wajib_menentukan_penanganan_dana_saat_membatalkan_pesanan_dibayar(): void
    {
        $p = $this->dibayar();

        $this->actingAs($this->admin)
            ->patch(route('admin.pesanan.batal', $p->id), ['alasan_batal' => 'Cuaca'])
            ->assertSessionHasErrors('penanganan_dana');

        $this->assertSame(StatusPesanan::Dibayar, $p->fresh()->status);
    }

    public function test_pembatalan_dengan_refund_sebagian_mencatat_refund_dan_dana_hangus(): void
    {
        $p = $this->dibayar();

        $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $p->id), [
            'alasan_batal' => 'Cuaca', 'penanganan_dana' => 'refund',
            'jumlah_refund' => 50000, 'metode_refund' => 'transfer', 'catatan_refund' => 'Potong biaya admin',
        ])->assertSessionHasNoErrors();

        $p->refresh();
        $this->assertSame(StatusPesanan::Dibatalkan, $p->status);
        $this->assertDatabaseHas('refund', ['pesanan_id' => $p->id, 'jumlah' => 50000, 'metode' => 'transfer', 'dicatat_oleh' => $this->admin->id]);
        $this->assertEqualsWithDelta(30000, $p->dana_hangus, 0.01); // 80.000 dibayar - 50.000 refund
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'pesanan.refund']);
    }

    public function test_refund_melebihi_dana_yang_dibayar_ditolak(): void
    {
        $p = $this->dibayar();

        $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $p->id), [
            'alasan_batal' => 'x', 'penanganan_dana' => 'refund', 'jumlah_refund' => 999999, 'metode_refund' => 'tunai',
        ])->assertSessionHasErrors('jumlah_refund');

        $this->assertSame(StatusPesanan::Dibayar, $p->fresh()->status);
        $this->assertDatabaseCount('refund', 0);
    }

    public function test_refund_dapat_dicatat_belakangan_untuk_pesanan_dibatalkan(): void
    {
        $p = $this->dibayar();
        $this->actingAs($this->admin)->patch(route('admin.pesanan.batal', $p->id), ['alasan_batal' => 'x', 'penanganan_dana' => 'hangus']);
        $this->assertEqualsWithDelta(80000, $p->fresh()->dana_hangus, 0.01);

        $this->actingAs($this->admin)->post(route('admin.pesanan.refund', $p->id), [
            'jumlah_refund' => 80000, 'metode_refund' => 'tunai',
        ])->assertSessionHasNoErrors();

        $this->assertEqualsWithDelta(0, $p->fresh()->dana_hangus, 0.01);

        // Dana sudah habis dikembalikan: refund tambahan ditolak (policy: tidak ada dana tersisa)
        $this->actingAs($this->admin)->post(route('admin.pesanan.refund', $p->id), [
            'jumlah_refund' => 1000, 'metode_refund' => 'tunai',
        ])->assertForbidden();
    }

    public function test_refund_hanya_untuk_pesanan_dibatalkan_dan_oleh_admin(): void
    {
        $p = $this->dibayar();

        $this->actingAs($this->admin)->post(route('admin.pesanan.refund', $p->id), ['jumlah_refund' => 1000, 'metode_refund' => 'tunai'])
            ->assertForbidden();

        $this->expectException(ValidationException::class);
        app(PesananService::class)->catatRefund($p, $this->admin, new RefundBaru(1000, MetodePembayaran::Tunai));
    }

    public function test_pelanggan_tidak_dapat_mencatat_refund(): void
    {
        $p = $this->dibayar();
        app(PesananService::class)->batal($p, $this->admin, 'x');

        $this->actingAs($this->pelangganUser)->post(route('admin.pesanan.refund', $p->id), ['jumlah_refund' => 1000, 'metode_refund' => 'tunai'])
            ->assertForbidden();
    }

    public function test_noshow_otomatis_menyatakan_dana_hangus_pada_log_lalu_dapat_direfund(): void
    {
        $p = $this->dibayar(-3);

        $this->artisan('pesanan:perbarui-status');

        $p->refresh();
        $this->assertSame(StatusPesanan::Dibatalkan, $p->status);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'pesanan.batal', 'user_id' => null]);
        $this->assertEqualsWithDelta(80000, $p->dana_hangus, 0.01);

        app(PesananService::class)->catatRefund($p, $this->admin, new RefundBaru(40000, MetodePembayaran::Transfer, 'Kebijakan toko'));
        $this->assertEqualsWithDelta(40000, $p->fresh()->dana_hangus, 0.01);
    }

    public function test_laporan_pendapatan_memuat_refund_di_halaman_dan_csv(): void
    {
        $p = $this->dibayar();
        app(PesananService::class)->batal($p, $this->admin, 'x', new RefundBaru(30000, MetodePembayaran::Tunai));

        $hasil = app(LaporanService::class)->laporanPendapatan(today(), today());
        $this->assertEqualsWithDelta(80000, $hasil['total_pendapatan_sewa'], 0.01);
        $this->assertEqualsWithDelta(30000, $hasil['total_refund'], 0.01);
        $this->assertEqualsWithDelta(50000, $hasil['total_pendapatan_bersih'], 0.01);

        $query = ['jenis' => 'pendapatan', 'dari' => today()->toDateString(), 'sampai' => today()->toDateString()];
        $this->actingAs($this->admin)->get(route('admin.laporan.index', $query))->assertOk()->assertSee('Refund Dikembalikan');
        $this->actingAs($this->admin)->get(route('admin.laporan.pdf', $query))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.laporan.csv', $query))->assertOk();
    }

    public function test_halaman_detail_admin_dan_pelanggan_menampilkan_info_dana_pesanan_dibatalkan(): void
    {
        $p = $this->dibayar();
        app(PesananService::class)->batal($p, $this->admin, 'Cuaca buruk', new RefundBaru(30000, MetodePembayaran::Transfer));

        $this->actingAs($this->admin)->get(route('admin.pesanan.show', $p->id))
            ->assertOk()->assertSee('Refund dikembalikan')->assertSee('Dana hangus');
        $this->actingAs($this->pelangganUser)->get(route('pesanan.show', $p->no_resi))
            ->assertOk()->assertSee('dikembalikan Rp30.000');
    }

    // ---------------------------------------------------------------- B3: metode pelunasan & denda manual

    private function dendaBelumLunas(Pesanan $p): Denda
    {
        return Denda::create([
            'pesanan_id' => $p->id, 'jenis' => 'kerusakan', 'jumlah' => 25000,
            'keterangan' => 'Tali putus', 'status_bayar' => 'belum_lunas',
        ]);
    }

    public function test_pelunasan_denda_wajib_mencatat_metode_bayar(): void
    {
        $p = $this->dibayar();
        $denda = $this->dendaBelumLunas($p);

        $this->actingAs($this->admin)->patch(route('admin.denda.lunas', $denda->id))->assertSessionHasErrors('metode_bayar');
        $this->assertSame('belum_lunas', $denda->fresh()->status_bayar);

        $this->actingAs($this->admin)->patch(route('admin.denda.lunas', $denda->id), ['metode_bayar' => 'transfer'])->assertSessionHasNoErrors();

        $denda->refresh();
        $this->assertSame('lunas', $denda->status_bayar);
        $this->assertSame(MetodePembayaran::Transfer, $denda->metode_bayar);
    }

    public function test_pelanggan_tidak_dapat_melunasi_denda(): void
    {
        $denda = $this->dendaBelumLunas($this->dibayar());

        $this->actingAs($this->pelangganUser)->patch(route('admin.denda.lunas', $denda->id), ['metode_bayar' => 'tunai'])->assertForbidden();
    }

    public function test_denda_manual_ditolak_untuk_pesanan_menunggu_atau_dibatalkan(): void
    {
        $menunggu = $this->buat(5);
        $dibatalkan = $this->dibayar(6);
        app(PesananService::class)->batal($dibatalkan, $this->admin, 'x');

        foreach ([$menunggu, $dibatalkan] as $p) {
            $this->actingAs($this->admin)->post(route('admin.pesanan.denda.tambah', $p->id), [
                'jumlah' => 10000, 'keterangan' => 'Uji',
            ])->assertForbidden();
        }

        $this->assertDatabaseCount('denda', 0);

        // Service tetap menjaga aturan yang sama bagi pemanggil non-HTTP
        $this->expectException(ValidationException::class);
        app(PengembalianService::class)->tambahDendaManual($menunggu, $this->admin, ['jumlah' => 10000, 'keterangan' => 'Uji']);
    }

    public function test_denda_manual_diizinkan_untuk_pesanan_disewa(): void
    {
        $p = $this->dibayar();
        $p->update(['status' => StatusPesanan::Disewa]);

        $this->actingAs($this->admin)->post(route('admin.pesanan.denda.tambah', $p->id), [
            'jumlah' => 10000, 'keterangan' => 'Tenda kotor',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('denda', ['pesanan_id' => $p->id, 'jumlah' => 10000]);
    }

    public function test_pesanan_disewa_yang_lewat_jatuh_tempo_tampil_di_halaman_denda_walau_status_belum_terlambat(): void
    {
        $p = $this->dibayar(-5);
        $p->update([
            'status' => StatusPesanan::Disewa,
            'tanggal_mulai' => today()->subDays(5),
            'tanggal_selesai' => today()->subDays(2),
            'jatuh_tempo' => today()->subDays(2)->setTime(12, 0),
        ]);

        // Scheduler belum menandai terlambat, tetapi halaman denda dan dashboard sudah harus menampilkannya
        $this->actingAs($this->admin)->get(route('admin.denda.index'))
            ->assertOk()->assertSee($p->no_resi)->assertSee('Pelanggan');
        $this->assertSame(1, Pesanan::sedangTerlambat()->count());
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk()->assertSee($p->no_resi);
    }

    public function test_pesanan_disewa_dalam_toleransi_belum_dianggap_terlambat(): void
    {
        $p = $this->dibayar(-2);
        $p->update([
            'status' => StatusPesanan::Disewa,
            'jatuh_tempo' => now()->subMinutes(10), // toleransi bawaan 60 menit
        ]);

        $this->assertSame(0, Pesanan::sedangTerlambat()->count());
        $this->actingAs($this->admin)->get(route('admin.denda.index'))->assertOk()->assertDontSee($p->no_resi);
    }

    // ---------------------------------------------------------------- B4: waktu pengembalian

    private function pesananDisewa(): Pesanan
    {
        $p = $this->dibayar(-3);
        $p->update(['status' => StatusPesanan::Disewa, 'tanggal_mulai' => today()->subDays(3), 'tanggal_selesai' => today()->addDay(), 'jatuh_tempo' => today()->addDay()->setTime(12, 0)]);

        return $p->fresh();
    }

    private function payloadKembali(Pesanan $p, array $tambahan = []): array
    {
        $detail = $p->detail->first();

        return $tambahan + [
            'baris' => [$detail->id => ['baik' => 1, 'rusak_ringan' => 0, 'rusak_berat' => 0, 'hilang' => 0]],
        ];
    }

    public function test_waktu_pengembalian_bawaan_adalah_waktu_sistem(): void
    {
        Carbon::setTestNow('2026-11-11 10:00:00');
        $p = $this->pesananDisewa();

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $p->id), $this->payloadKembali($p))
            ->assertSessionHasNoErrors();

        $this->assertEquals('2026-11-11 10:00:00', $p->fresh()->pengembalian->dikembalikan_pada->toDateTimeString());
    }

    public function test_memundurkan_waktu_pengembalian_wajib_catatan_dan_tercatat_di_log(): void
    {
        Carbon::setTestNow('2026-11-11 10:00:00');
        $p = $this->pesananDisewa();
        $mundur = now()->subDay()->format('Y-m-d\TH:i');

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $p->id), $this->payloadKembali($p, ['dikembalikan_pada' => $mundur]))
            ->assertSessionHasErrors('catatan');
        $this->assertSame(StatusPesanan::Disewa, $p->fresh()->status);

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $p->id), $this->payloadKembali($p, ['dikembalikan_pada' => $mundur, 'catatan' => 'Dikembalikan kemarin sore, terlambat dicatat']))
            ->assertSessionHasNoErrors();

        $this->assertSame(StatusPesanan::Dikembalikan, $p->fresh()->status);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'pengembalian.proses']);
        $this->assertStringContainsString('diisi manual', LogAktivitas::where('aksi', 'pengembalian.proses')->value('deskripsi'));
    }

    public function test_selisih_kecil_dari_waktu_sistem_tidak_perlu_catatan_dan_masa_depan_ditolak(): void
    {
        Carbon::setTestNow('2026-11-11 10:00:00');
        $p = $this->pesananDisewa();

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $p->id), $this->payloadKembali($p, ['dikembalikan_pada' => now()->addDay()->format('Y-m-d\TH:i')]))
            ->assertSessionHasErrors('dikembalikan_pada');

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $p->id), $this->payloadKembali($p, ['dikembalikan_pada' => now()->subMinutes(30)->format('Y-m-d\TH:i')]))
            ->assertSessionHasNoErrors();
        $this->assertStringNotContainsString('diisi manual', LogAktivitas::where('aksi', 'pengembalian.proses')->value('deskripsi'));
    }

    public function test_service_pengembalian_memakai_dto(): void
    {
        $p = $this->pesananDisewa();
        $detail = $p->detail->first();

        $kembali = app(PengembalianService::class)->proses($p, $this->admin, PengembalianBaru::dari([
            'baris' => [$detail->id => ['baik' => 0, 'rusak_ringan' => 0, 'rusak_berat' => 0, 'hilang' => 1]],
        ]));

        // Unit hilang tanpa biaya manual: denda otomatis sebesar harga beli
        $this->assertEqualsWithDelta(300000, (float) $kembali->denda->sum('jumlah'), 0.01);
    }

    // ---------------------------------------------------------------- B5: stok per tanggal

    public function test_stok_bawaan_adalah_stok_hari_ini_dan_rentang_memperhitungkan_pesanan_mendatang(): void
    {
        $this->buat(10, 2); // 1 unit untuk 10-12 hari ke depan

        $alat = Alat::find($this->alat->id);
        $this->assertSame(20, $alat->stok_tersedia);

        $dalamRentang = Alat::muatStokTersedia([Alat::find($this->alat->id)], today()->addDays(10), today()->addDays(12))[0];
        $this->assertSame(19, $dalamRentang->stok_tersedia);
        $this->assertSame(1, $dalamRentang->stok_disewa);
    }

    public function test_unit_disewa_yang_jatuh_tempo_hari_ini_belum_dihitung_tersedia(): void
    {
        $p = $this->dibayar(-2);
        $p->update(['status' => StatusPesanan::Disewa, 'tanggal_mulai' => today()->subDays(2), 'tanggal_selesai' => today()]);

        $this->assertSame(19, Alat::find($this->alat->id)->stok_tersedia);
    }

    public function test_endpoint_ketersediaan_semua_alat_per_rentang(): void
    {
        $this->buat(10, 2);

        $this->getJson(route('katalog.ketersediaan-semua', ['mulai' => today()->addDays(10)->toDateString(), 'selesai' => today()->addDays(12)->toDateString()]))
            ->assertOk()
            ->assertJsonPath('sukses', true)
            ->assertJsonPath('tersedia.'.$this->alat->id, 19);

        $this->getJson(route('katalog.ketersediaan-semua', ['mulai' => today()->addDays(3)->toDateString(), 'selesai' => today()->addDays(2)->toDateString()]))
            ->assertStatus(422);
    }

    public function test_katalog_dipaginasi_dengan_stok_tersedia_di_depan(): void
    {
        $kategori = $this->alat->kategori;
        foreach (range(1, 14) as $i) {
            Alat::create([
                'kategori_id' => $kategori->id, 'kode_alat' => "ALT-$i", 'nama' => 'Alat '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'harga_per_hari' => 10000, 'stok_total' => $i === 14 ? 0 : 3, 'stok_perawatan' => 0, 'is_aktif' => true,
            ]);
        }

        $halaman1 = $this->get(route('katalog.index'))->assertOk();
        $halaman1->assertSee('Alat 01')->assertDontSee('Alat 14');

        // 15 alat aktif, 12 per halaman: alat berstok habis terdorong ke halaman 2
        $this->get(route('katalog.index', ['page' => 2]))->assertOk()->assertSee('Alat 14');
    }

    public function test_filter_kategori_katalog_menerima_slug_dan_id_numerik(): void
    {
        $this->get(route('katalog.index', ['kategori' => 'umum']))->assertOk()->assertSee('Tenda - D2');
        $this->get(route('katalog.index', ['kategori' => (string) $this->alat->kategori_id]))->assertOk()->assertSee('Tenda - D2');
        // Slug yang bukan angka tidak boleh dibandingkan dengan kolom id (ditolak PostgreSQL)
        $this->get(route('katalog.index', ['kategori' => 'tidak-ada']))->assertOk()->assertDontSee('Tenda - D2');
    }

    // ---------------------------------------------------------------- keamanan

    public function test_pendaftaran_dibatasi_throttle(): void
    {
        $payload = fn (int $i) => [
            'nama' => "User $i", 'email' => "user$i@contoh.test", 'password' => 'Password123!', 'password_confirmation' => 'Password123!',
            'no_hp' => '0812345678'.str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'alamat' => 'Subang',
        ];

        foreach (range(1, 5) as $i) {
            $this->post(route('register'), $payload($i))->assertStatus(302);
        }
        auth()->logout();

        $this->post(route('register'), $payload(6))->assertStatus(429);
    }

    public function test_token_cron_tidak_diterima_lewat_query_string(): void
    {
        config(['services.cron.token' => 'rahasia-cron']);

        $this->get('/cron/jadwal?token=rahasia-cron')->assertNotFound();
        $this->withHeader('Authorization', 'Bearer rahasia-cron')->get('/cron/jadwal')->assertOk();
    }

    public function test_unggah_bukti_transfer_dibatasi_per_pesanan(): void
    {
        Storage::fake('local');
        $p = $this->buat(10);

        foreach (range(1, BuktiTransferService::MAKS_UNGGAHAN) as $i) {
            $this->actingAs($this->pelangganUser)
                ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image("b$i.jpg", 200, 200)])
                ->assertSessionHasNoErrors();
        }

        $this->actingAs($this->pelangganUser)
            ->post(route('pesanan.bukti.upload', $p->no_resi), ['bukti' => UploadedFile::fake()->image('lagi.jpg', 200, 200)])
            ->assertSessionHasErrors('bukti');
    }

    public function test_pengurangan_stok_di_bawah_pemakaian_aktif_ditolak_di_dalam_transaksi(): void
    {
        $this->buat(5, 2);
        $kategoriId = $this->alat->kategori_id;

        $this->actingAs($this->admin)->put(route('admin.alat.update', $this->alat->id), [
            'kategori_id' => $kategoriId, 'kode_alat' => 'TND', 'nama' => 'Tenda', 'varian' => 'D2',
            'harga_per_hari' => 40000, 'stok_total' => 1, 'stok_perawatan' => 1, 'is_aktif' => 1,
        ])->assertSessionHasErrors('stok_total');

        $this->assertSame(20, $this->alat->fresh()->stok_total);
    }

    // ---------------------------------------------------------------- pengaturan bertipe & regresi

    public function test_pengaturan_toko_memakai_bawaan_config_dan_nilai_database(): void
    {
        config(['toko.nama' => 'Toko Uji', 'toko.no_wa' => '628111222333']);

        $this->assertSame('Toko Uji', PengaturanToko::namaToko());
        $this->assertSame('628111222333', PengaturanToko::noWaDigit());

        Pengaturan::setel('nama_toko', 'Dari Database');
        $this->assertSame('Dari Database', PengaturanToko::namaToko());
    }

    public function test_seeder_pengaturan_mengisi_seluruh_kunci_tanpa_menimpa_nilai_pemilik(): void
    {
        Pengaturan::setel('batas_bayar_jam', '6');

        $this->seed(PengaturanSeeder::class);

        foreach (array_keys(PengaturanToko::bawaan()) as $kunci) {
            $this->assertDatabaseHas('pengaturan', ['kunci' => $kunci]);
        }
        $this->assertSame(6, PengaturanToko::batasBayarJam());
    }

    public function test_resi_pelanggan_dan_pdf_untuk_pesanan_dengan_denda_tidak_error(): void
    {
        $p = $this->dibayar();
        $p->update(['status' => StatusPesanan::Dikembalikan]);
        $this->dendaBelumLunas($p);
        Denda::create(['pesanan_id' => $p->id, 'jenis' => 'kerusakan', 'jumlah' => 5000, 'keterangan' => 'Lunas', 'status_bayar' => 'lunas', 'dilunasi_pada' => now()]);

        $this->actingAs($this->pelangganUser)->get(route('pesanan.resi', $p->no_resi))->assertOk();
        $this->actingAs($this->pelangganUser)->get(route('pesanan.pdf', $p->no_resi))->assertOk();
    }
}
