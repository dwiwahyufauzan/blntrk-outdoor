<?php

namespace Tests\Feature;

use App\Enums\JenisDenda;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pengembalian;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\DendaService;
use App\Services\KetersediaanService;
use App\Services\PengembalianService;
use App\Services\PesananService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengembalianDendaTest extends TestCase
{
    use RefreshDatabase;

    protected User $userPelanggan;

    protected Pelanggan $pelanggan;

    protected User $admin;

    protected Alat $tenda;

    protected Alat $carrier;

    protected PesananService $pesananService;

    protected PengembalianService $pengembalianService;

    protected DendaService $dendaService;

    protected KetersediaanService $ketersediaanService;

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
            'alamat' => 'Subang',
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin Kasir',
            'email' => 'kasir@test.com',
            'is_aktif' => true,
        ]);

        $kategori = KategoriAlat::create([
            'nama' => 'Peralatan Camping',
            'slug' => 'peralatan-camping',
            'deskripsi' => 'Alat camping outdoor',
        ]);

        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Dome 4P',
            'slug' => 'tenda-dome-4p',
            'harga_per_hari' => 60000,
            'stok_total' => 5,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->carrier = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'CAR-001',
            'nama' => 'Carrier 60L',
            'slug' => 'carrier-60l',
            'harga_per_hari' => 50000,
            'stok_total' => 4,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->pesananService = app(PesananService::class);
        $this->pengembalianService = app(PengembalianService::class);
        $this->dendaService = app(DendaService::class);
        $this->ketersediaanService = app(KetersediaanService::class);
    }

    /**
     * Helper untuk membuat pesanan yang sudah berstatus Disewa lengkap dengan jaminan.
     */
    protected function createPesananDisewa(int $durasiHari = 2): Pesanan
    {
        $pesanan = $this->pesananService->buat($this->pelanggan, $this->userPelanggan, [
            'tanggal_mulai' => Carbon::yesterday()->subDays($durasiHari)->toDateString(),
            'tanggal_selesai' => Carbon::yesterday()->toDateString(),
            'items' => [
                ['alat_id' => $this->tenda->id, 'jumlah' => 1],
                ['alat_id' => $this->carrier->id, 'jumlah' => 1],
            ],
            'catatan' => 'Pesanan uji pengembalian',
        ]);

        // Bayar
        $this->pesananService->bayar($pesanan, $this->admin, [
            'jumlah' => $pesanan->total_sewa,
            'metode' => 'tunai',
        ]);

        // Ambil
        $this->pesananService->ambil($pesanan, $this->admin, [
            'jenis_jaminan' => 'ktp',
            'nomor_identitas' => '3204123456781234',
        ]);

        return $pesanan->fresh(['detail.alat', 'jaminan']);
    }

    /**
     * BB-52: Keterlambatan - Sistem menandai pesanan terlambat jika melewati jatuh tempo + toleransi.
     */
    public function test_bb52_sistem_menandai_pesanan_terlambat_lewat_jatuh_tempo_dan_toleransi(): void
    {
        $pesanan = $this->createPesananDisewa();
        $this->assertEquals(StatusPesanan::Disewa, $pesanan->status);

        // Atur jatuh tempo di masa lalu (melebihi toleransi 60 menit)
        $pesanan->update([
            'jatuh_tempo' => now()->subHours(2),
        ]);

        // Jalankan sinkronisasi status
        $this->pesananService->sinkronStatus();

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Terlambat, $pesanan->status);
    }

    /**
     * BB-53: Keterlambatan - Pengembalian dalam batas toleransi (misal 30 menit) tetap Disewa dan denda 0.
     */
    public function test_bb53_pengembalian_dalam_batas_toleransi_tidak_dikenakan_denda(): void
    {
        $pesanan = $this->createPesananDisewa();

        // Jatuh tempo pukul 12:00, waktu kembali 12:30 (dalam toleransi 60 menit)
        $jatuhTempo = now()->setTime(12, 0, 0);
        $waktuKembali = now()->setTime(12, 30, 0);

        $pesanan->update(['jatuh_tempo' => $jatuhTempo]);

        $hasil = $this->dendaService->hitungKeterlambatan($pesanan, $waktuKembali);

        $this->assertEquals(0, $hasil['hari']);
        $this->assertEquals(0.0, $hasil['jumlah']);
    }

    /**
     * BB-54: Pengembalian - Semua alat kondisi baik, tepat waktu -> Status Dikembalikan, tanpa denda, jaminan dapat dikembalikan.
     */
    public function test_bb54_pengembalian_semua_alat_baik_dan_tepat_waktu(): void
    {
        $pesanan = $this->createPesananDisewa();
        $pesanan->update(['jatuh_tempo' => now()->addHours(2)]);

        $baris = [];
        foreach ($pesanan->detail as $det) {
            $baris[$det->id] = [
                'baik' => $det->jumlah,
                'rusak_ringan' => 0,
                'rusak_berat' => 0,
                'hilang' => 0,
                'biaya' => 0,
            ];
        }

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => now()->toDateTimeString(),
            'baris' => $baris,
            'catatan' => 'Semua lengkap dan bersih',
        ]);

        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));
        $pesanan->refresh();

        $this->assertEquals(StatusPesanan::Dikembalikan, $pesanan->status);
        $this->assertDatabaseHas('pengembalian', [
            'pesanan_id' => $pesanan->id,
            'hari_terlambat' => 0,
            'diterima_oleh' => $this->admin->id,
        ]);

        // Tidak ada denda yang dibuat
        $this->assertEquals(0, Denda::where('pesanan_id', $pesanan->id)->count());
    }

    /**
     * BB-55: Pengembalian - Denda keterlambatan 2 hari (Tarif harian 110.000 * 2 hari = 220.000).
     */
    public function test_bb55_perhitungan_denda_keterlambatan_dua_hari_terhitung_benar(): void
    {
        $pesanan = $this->createPesananDisewa();

        // Total tarif harian: Tenda (60.000) + Carrier (50.000) = 110.000
        // Terlambat tepat 2 hari (48 jam)
        $waktuKembali = now();
        $jatuhTempo = $waktuKembali->copy()->subDays(2);
        $pesanan->update(['jatuh_tempo' => $jatuhTempo]);

        $baris = [];
        foreach ($pesanan->detail as $det) {
            $baris[$det->id] = [
                'baik' => $det->jumlah,
                'rusak_ringan' => 0,
                'rusak_berat' => 0,
                'hilang' => 0,
                'biaya' => 0,
            ];
        }

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => $waktuKembali->toDateTimeString(),
            'baris' => $baris,
        ]);

        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Dikembalikan, $pesanan->status);

        // Pastikan denda keterlambatan 2 hari tercatat: 2 * 110.000 = 220.000
        $this->assertDatabaseHas('denda', [
            'pesanan_id' => $pesanan->id,
            'jenis' => JenisDenda::Keterlambatan->value,
            'jumlah' => 220000.00,
            'status_bayar' => 'belum_lunas',
        ]);
    }

    /**
     * BB-56: Pengembalian - Alat rusak ringan -> stok_perawatan bertambah, ketersediaan berkurang.
     */
    public function test_bb56_alat_rusak_ringan_menambah_stok_perawatan(): void
    {
        $pesanan = $this->createPesananDisewa();
        $pesanan->update(['jatuh_tempo' => now()->addHour()]);

        $stokPerawatanAwal = $this->tenda->fresh()->stok_perawatan;

        $baris = [];
        foreach ($pesanan->detail as $det) {
            if ($det->alat_id === $this->tenda->id) {
                $baris[$det->id] = [
                    'baik' => 0,
                    'rusak_ringan' => 1,
                    'rusak_berat' => 0,
                    'hilang' => 0,
                    'biaya' => 25000, // Biaya perbaikan
                    'catatan' => 'Resleting tenda macet',
                ];
            } else {
                $baris[$det->id] = [
                    'baik' => $det->jumlah,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                    'hilang' => 0,
                    'biaya' => 0,
                ];
            }
        }

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => now()->toDateTimeString(),
            'baris' => $baris,
        ]);

        $this->assertEquals($stokPerawatanAwal + 1, $this->tenda->fresh()->stok_perawatan);

        // Biaya kerusakan tercatat di denda
        $this->assertDatabaseHas('denda', [
            'pesanan_id' => $pesanan->id,
            'jenis' => JenisDenda::Kerusakan->value,
            'jumlah' => 25000.00,
            'status_bayar' => 'belum_lunas',
        ]);
    }

    /**
     * BB-57: Pengembalian - Alat hilang -> stok_total berkurang, denda ganti rugi tercatat.
     */
    public function test_bb57_alat_hilang_mengurangi_stok_total(): void
    {
        $pesanan = $this->createPesananDisewa();
        $pesanan->update(['jatuh_tempo' => now()->addHour()]);

        $stokTotalAwal = $this->carrier->fresh()->stok_total;

        $baris = [];
        foreach ($pesanan->detail as $det) {
            if ($det->alat_id === $this->carrier->id) {
                $baris[$det->id] = [
                    'baik' => 0,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                    'hilang' => 1,
                    'biaya' => 350000, // Ganti rugi carrier hilang
                    'catatan' => 'Carrier hilang di puncak',
                ];
            } else {
                $baris[$det->id] = [
                    'baik' => $det->jumlah,
                    'rusak_ringan' => 0,
                    'rusak_berat' => 0,
                    'hilang' => 0,
                    'biaya' => 0,
                ];
            }
        }

        $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => now()->toDateTimeString(),
            'baris' => $baris,
        ]);

        $this->assertEquals($stokTotalAwal - 1, $this->carrier->fresh()->stok_total);

        $this->assertDatabaseHas('denda', [
            'pesanan_id' => $pesanan->id,
            'jenis' => JenisDenda::Kehilangan->value,
            'jumlah' => 350000.00,
            'status_bayar' => 'belum_lunas',
        ]);
    }

    /**
     * BB-58: Pengembalian - Jumlah kondisi tidak sama dengan jumlah sewa ditolak validasi.
     */
    public function test_bb58_jumlah_kondisi_tidak_sama_dengan_jumlah_sewa_ditolak(): void
    {
        $pesanan = $this->createPesananDisewa();

        $baris = [];
        foreach ($pesanan->detail as $det) {
            $baris[$det->id] = [
                'baik' => 0,
                'rusak_ringan' => 0,
                'rusak_berat' => 0,
                'hilang' => 0, // Total 0 padahal jumlah sewa 1
                'biaya' => 0,
            ];
        }

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => now()->toDateTimeString(),
            'baris' => $baris,
        ]);

        $response->assertSessionHasErrors();
        $pesanan->refresh();
        $this->assertEquals(StatusPesanan::Disewa, $pesanan->status);
    }

    /**
     * BB-59: Pengembalian - Proses ulang pesanan yang sudah Dikembalikan ditolak.
     */
    public function test_bb59_proses_ulang_pesanan_yang_sudah_dikembalikan_ditolak(): void
    {
        $pesanan = $this->createPesananDisewa();
        $pesanan->update(['status' => StatusPesanan::Dikembalikan]);

        $baris = [];
        foreach ($pesanan->detail as $det) {
            $baris[$det->id] = [
                'baik' => $det->jumlah,
                'rusak_ringan' => 0,
                'rusak_berat' => 0,
                'hilang' => 0,
                'biaya' => 0,
            ];
        }

        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.pengembalian.store', $pesanan->id), [
            'dikembalikan_pada' => now()->toDateTimeString(),
            'baris' => $baris,
        ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * BB-60: Jaminan - Kembalikan jaminan -> status jaminan Dikembalikan, waktu dan petugas tercatat.
     */
    public function test_bb60_pengembalian_jaminan_berhasil_mencatat_status_dan_petugas(): void
    {
        $pesanan = $this->createPesananDisewa();
        $jaminan = $pesanan->jaminan;

        $this->assertEquals('dititipkan', $jaminan->status);

        $response = $this->actingAs($this->admin)->patch(route('admin.pesanan.jaminan.kembali', $pesanan->id));
        $response->assertRedirect(route('admin.pesanan.show', $pesanan->id));

        $jaminan->refresh();
        $this->assertEquals('dikembalikan', $jaminan->status);
        $this->assertNotNull($jaminan->dikembalikan_pada);
        $this->assertEquals($this->admin->id, $jaminan->dikembalikan_oleh);
    }

    /**
     * BB-61: Denda - Tandai lunas -> status denda Lunas, waktu dan petugas tercatat.
     */
    public function test_bb61_tandai_denda_lunas_mencatat_status_dan_petugas(): void
    {
        $pesanan = $this->createPesananDisewa();

        $pengembalian = Pengembalian::create([
            'pesanan_id' => $pesanan->id,
            'dikembalikan_pada' => now(),
            'hari_terlambat' => 1,
            'diterima_oleh' => $this->admin->id,
            'catatan' => 'Uji denda',
        ]);

        // Buat denda
        $denda = Denda::create([
            'pesanan_id' => $pesanan->id,
            'pengembalian_id' => $pengembalian->id,
            'jenis' => JenisDenda::Keterlambatan,
            'keterangan' => 'Terlambat 1 hari',
            'jumlah' => 110000,
            'status_bayar' => 'belum_lunas',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.denda.lunas', $denda->id));
        $response->assertSessionHas('status');

        $denda->refresh();
        $this->assertEquals('lunas', $denda->status_bayar);
        $this->assertNotNull($denda->dilunasi_pada);
        $this->assertEquals($this->admin->id, $denda->dilunasi_oleh);
    }

    /**
     * Scheduler / Artisan Command: pesanan:perbarui-status memperbarui status keterlambatan dan pembatalan.
     */
    public function test_scheduler_artisan_command_pesanan_perbarui_status(): void
    {
        $pesananDisewa = $this->createPesananDisewa();
        $pesananDisewa->update([
            'jatuh_tempo' => now()->subHours(2),
        ]);

        $pesananMenunggu = $this->pesananService->buat($this->pelanggan, $this->userPelanggan, [
            'tanggal_mulai' => Carbon::tomorrow()->toDateString(),
            'tanggal_selesai' => Carbon::tomorrow()->addDay()->toDateString(),
            'items' => [['alat_id' => $this->tenda->id, 'jumlah' => 1]],
        ]);
        $pesananMenunggu->update([
            'batas_ambil' => now()->subHours(3),
        ]);

        $this->artisan('pesanan:perbarui-status')
            ->expectsOutputToContain('Berhasil memperbarui status 2 pesanan.')
            ->assertSuccessful();

        $pesananDisewa->refresh();
        $pesananMenunggu->refresh();

        $this->assertEquals(StatusPesanan::Terlambat, $pesananDisewa->status);
        $this->assertEquals(StatusPesanan::Dibatalkan, $pesananMenunggu->status);
    }
}
