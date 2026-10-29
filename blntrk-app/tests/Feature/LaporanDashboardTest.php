<?php

namespace Tests\Feature;

use App\Enums\JenisDenda;
use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Pengembalian;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $userPelanggan1;

    protected Pelanggan $pelanggan1;

    protected User $userPelanggan2;

    protected Pelanggan $pelanggan2;

    protected User $userTanpaPesanan;

    protected Pelanggan $pelangganTanpaPesanan;

    protected User $admin;

    protected User $pemilik;

    protected Alat $tenda;

    protected Alat $carrier;

    protected function setUp(): void
    {
        parent::setUp();

        $rolePelanggan = Role::firstOrCreate(['nama' => 'pelanggan'], ['label' => 'Pelanggan']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Admin']);
        $rolePemilik = Role::firstOrCreate(['nama' => 'pemilik'], ['label' => 'Pemilik Toko']);

        // Pelanggan 1
        $this->userPelanggan1 = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Rian Hidayat',
            'email' => 'rian@test.com',
            'is_aktif' => true,
        ]);
        $this->pelanggan1 = Pelanggan::create([
            'user_id' => $this->userPelanggan1->id,
            'nama_lengkap' => 'Rian Hidayat',
            'no_hp' => '081234567891',
            'alamat' => 'Subang Kota',
        ]);

        // Pelanggan 2
        $this->userPelanggan2 = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Dimas Pratama',
            'email' => 'dimas@test.com',
            'is_aktif' => true,
        ]);
        $this->pelanggan2 = Pelanggan::create([
            'user_id' => $this->userPelanggan2->id,
            'nama_lengkap' => 'Dimas Pratama',
            'no_hp' => '081234567892',
            'alamat' => 'Jalancagak Subang',
        ]);

        // Pelanggan tanpa pesanan
        $this->userTanpaPesanan = User::factory()->create([
            'role_id' => $rolePelanggan->id,
            'nama' => 'Citra Lestari',
            'email' => 'citra@test.com',
            'is_aktif' => true,
        ]);
        $this->pelangganTanpaPesanan = Pelanggan::create([
            'user_id' => $this->userTanpaPesanan->id,
            'nama_lengkap' => 'Citra Lestari',
            'no_hp' => '081234567893',
            'alamat' => 'Ciater Subang',
        ]);

        // Admin
        $this->admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'nama' => 'Admin BLNTRK',
            'email' => 'admin@blntrk.com',
            'is_aktif' => true,
        ]);

        // Pemilik
        $this->pemilik = User::factory()->create([
            'role_id' => $rolePemilik->id,
            'nama' => 'Pemilik BLNTRK',
            'email' => 'owner@blntrk.com',
            'is_aktif' => true,
        ]);

        // Kategori & Alat
        $kategori = KategoriAlat::create([
            'nama' => 'Tenda & Shelter',
            'slug' => 'tenda-shelter',
            'deskripsi' => 'Peralatan tenda outdoor',
        ]);

        $this->tenda = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'TND-001',
            'nama' => 'Tenda Dome Arpenaz 4.1',
            'varian' => '4 Orang',
            'deskripsi' => 'Tenda dome double layer',
            'harga_per_hari' => 80000,
            'stok_total' => 10,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);

        $this->carrier = Alat::create([
            'kategori_id' => $kategori->id,
            'kode_alat' => 'CRR-001',
            'nama' => 'Carrier Deuter Aircontact 60L',
            'varian' => '60 Liter',
            'deskripsi' => 'Tas carrier trekking',
            'harga_per_hari' => 60000,
            'stok_total' => 8,
            'stok_perawatan' => 0,
            'is_aktif' => true,
        ]);
    }

    /**
     * BB-63: Riwayat Transaksi - Pelanggan hanya bisa melihat miliknya sendiri,
     * menampilkan pesan ramah saat belum ada pesanan, dan Admin dapat melihat semua
     * serta memfilter rentang tanggal.
     */
    public function test_bb_63_pelanggan_hanya_melihat_riwayat_pesanan_sendiri_dan_pesan_kosong(): void
    {
        // 1. Buat pesanan untuk pelanggan 1
        $pesanan1 = Pesanan::create([
            'no_resi' => 'BLN-261020-0001',
            'pelanggan_id' => $this->pelanggan1->id,
            'dibuat_oleh' => $this->userPelanggan1->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => now()->addDays(2)->setTime(21, 0, 0),
            'batas_ambil' => now()->setTime(21, 0, 0),
            'total_sewa' => 160000,
            'status' => StatusPesanan::Menunggu,
            'catatan' => 'Pesanan Rian',
        ]);

        // 2. Buat pesanan untuk pelanggan 2
        $pesanan2 = Pesanan::create([
            'no_resi' => 'BLN-261020-0002',
            'pelanggan_id' => $this->pelanggan2->id,
            'dibuat_oleh' => $this->userPelanggan2->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'durasi_hari' => 3,
            'jatuh_tempo' => now()->addDays(3)->setTime(21, 0, 0),
            'batas_ambil' => now()->setTime(21, 0, 0),
            'total_sewa' => 180000,
            'status' => StatusPesanan::Disewa,
            'catatan' => 'Pesanan Dimas',
        ]);

        // Pelanggan 1 login dan melihat riwayat
        $response1 = $this->actingAs($this->userPelanggan1)->get(route('pesanan.index'));
        $response1->assertStatus(200);
        $response1->assertSee($pesanan1->no_resi);
        $response1->assertDontSee($pesanan2->no_resi);

        // Pelanggan tanpa pesanan melihat pesan ramah "Belum ada riwayat pemesanan"
        $responseKosong = $this->actingAs($this->userTanpaPesanan)->get(route('pesanan.index'));
        $responseKosong->assertStatus(200);
        $responseKosong->assertSee('Belum ada riwayat pemesanan');

        // Admin dapat melihat kedua pesanan dan memfilter berdasarkan tanggal
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.pesanan.index', [
            'dari' => now()->subDay()->toDateString(),
            'sampai' => now()->addDay()->toDateString(),
        ]));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee($pesanan1->no_resi);
        $responseAdmin->assertSee($pesanan2->no_resi);
    }

    /**
     * BB-64: Akses Laporan & Filter Periode - Admin dan Pemilik dapat mengakses,
     * pelanggan dilarang mengakses laporan (403).
     */
    public function test_bb_64_akses_laporan_dan_filter_periode(): void
    {
        $dari = now()->startOfMonth()->toDateString();
        $sampai = now()->toDateString();

        // 1. Admin dapat mengakses laporan
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'jenis' => 'penyewaan',
            'dari' => $dari,
            'sampai' => $sampai,
        ]));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewIs('admin.laporan.index');

        // 2. Pemilik dapat mengakses laporan (read-only view)
        $responsePemilik = $this->actingAs($this->pemilik)->get(route('pemilik.laporan.index', [
            'jenis' => 'pendapatan',
            'dari' => $dari,
            'sampai' => $sampai,
        ]));
        $responsePemilik->assertStatus(200);
        $responsePemilik->assertViewIs('pemilik.laporan.index');

        // 3. Pelanggan dilarang mengakses laporan admin maupun pemilik (403)
        $this->actingAs($this->userPelanggan1)->get(route('admin.laporan.index'))->assertStatus(403);
        $this->actingAs($this->userPelanggan1)->get(route('pemilik.laporan.index'))->assertStatus(403);
    }

    /**
     * BB-65: Laporan Kosong - Saat rentang tanggal tidak ada transaksi,
     * menampilkan pesan "Data laporan tidak tersedia".
     */
    public function test_bb_65_laporan_kosong_menampilkan_pesan_data_laporan_tidak_tersedia(): void
    {
        // Periode masa lalu yang pasti kosong
        $dari = '2020-01-01';
        $sampai = '2020-01-31';

        $jenisLaporan = ['penyewaan', 'pendapatan', 'denda', 'terlaris'];

        foreach ($jenisLaporan as $jenis) {
            $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
                'jenis' => $jenis,
                'dari' => $dari,
                'sampai' => $sampai,
            ]));

            $response->assertStatus(200);
            $response->assertSee('Data laporan tidak tersedia');
        }
    }

    /**
     * BB-66: Akurasi Formula Pendapatan - Hanya menghitung kas masuk riil
     * (pembayaran.dibayar_pada), denda berstatus lunas, dan mengabaikan pesanan dibatalkan.
     */
    public function test_bb_66_formula_pendapatan_hanya_kas_masuk_dan_denda_lunas_dan_abaikan_dibatalkan(): void
    {
        $bulanMeiMulai = Carbon::parse('2026-05-01');
        $bulanMeiSelesai = Carbon::parse('2026-05-31');

        // 1. Pesanan Sukses (Selesai/Disewa) dengan pembayaran di bulan Mei
        $pesananSukses = Pesanan::create([
            'no_resi' => 'BLN-260501-0001',
            'pelanggan_id' => $this->pelanggan1->id,
            'dibuat_oleh' => $this->userPelanggan1->id,
            'tanggal_mulai' => '2026-05-02',
            'tanggal_selesai' => '2026-05-04',
            'durasi_hari' => 2,
            'jatuh_tempo' => Carbon::parse('2026-05-04 21:00:00'),
            'batas_ambil' => Carbon::parse('2026-05-02 21:00:00'),
            'total_sewa' => 160000,
            'status' => StatusPesanan::Dikembalikan,
        ]);

        Pembayaran::create([
            'pesanan_id' => $pesananSukses->id,
            'jumlah' => 160000,
            'metode' => MetodePembayaran::Tunai,
            'dicatat_oleh' => $this->admin->id,
            'dibayar_pada' => Carbon::parse('2026-05-02 09:00:00'),
        ]);

        // 2. Pesanan Dibatalkan - TIDAK BOLEH masuk hitungan pendapatan sewa
        $pesananBatal = Pesanan::create([
            'no_resi' => 'BLN-260505-0002',
            'pelanggan_id' => $this->pelanggan2->id,
            'dibuat_oleh' => $this->userPelanggan2->id,
            'tanggal_mulai' => '2026-05-06',
            'tanggal_selesai' => '2026-05-08',
            'durasi_hari' => 2,
            'jatuh_tempo' => Carbon::parse('2026-05-08 21:00:00'),
            'batas_ambil' => Carbon::parse('2026-05-06 21:00:00'),
            'total_sewa' => 250000,
            'status' => StatusPesanan::Dibatalkan,
            'dibatalkan_pada' => Carbon::parse('2026-05-05 10:00:00'),
        ]);

        // Walau ada pembayaran tercatat sebelum dibatalkan, filter 'status != dibatalkan' harus mengecualikannya
        Pembayaran::create([
            'pesanan_id' => $pesananBatal->id,
            'jumlah' => 250000,
            'metode' => MetodePembayaran::Transfer,
            'dicatat_oleh' => $this->admin->id,
            'dibayar_pada' => Carbon::parse('2026-05-05 08:00:00'),
        ]);

        // Buat Pengembalian untuk pesanan sukses
        $pengembalian = Pengembalian::create([
            'pesanan_id' => $pesananSukses->id,
            'dikembalikan_pada' => Carbon::parse('2026-05-05 10:30:00'),
            'hari_terlambat' => 1,
            'diterima_oleh' => $this->admin->id,
            'catatan' => 'Kembali terlambat 1 hari',
        ]);

        // 3. Denda yang LUNAS di bulan Mei -> MASUK pendapatan
        Denda::create([
            'pesanan_id' => $pesananSukses->id,
            'pengembalian_id' => $pengembalian->id,
            'jenis' => JenisDenda::Keterlambatan,
            'jumlah' => 80000,
            'status_bayar' => 'lunas',
            'keterangan' => 'Terlambat 1 hari',
            'dilunasi_pada' => Carbon::parse('2026-05-05 11:00:00'),
            'dilunasi_oleh' => $this->admin->id,
            'created_at' => Carbon::parse('2026-05-05 10:30:00'),
        ]);

        // 4. Denda yang BELUM LUNAS -> TIDAK MASUK pendapatan
        Denda::create([
            'pesanan_id' => $pesananSukses->id,
            'pengembalian_id' => $pengembalian->id,
            'jenis' => JenisDenda::Kerusakan,
            'jumlah' => 50000,
            'status_bayar' => 'belum_lunas',
            'keterangan' => 'Frame bengkok',
            'created_at' => Carbon::parse('2026-05-05 10:30:00'),
        ]);

        // Jalankan perhitungan melalui LaporanService
        $service = new LaporanService;
        $hasil = $service->laporanPendapatan($bulanMeiMulai, $bulanMeiSelesai);

        // Verifikasi:
        // Sewa riil: hanya 160.000 (pesanan batal 250.000 diabaikan)
        $this->assertEquals(160000, $hasil['total_pendapatan_sewa']);
        // Denda riil: hanya 80.000 (denda belum lunas 50.000 diabaikan)
        $this->assertEquals(80000, $hasil['total_pendapatan_denda']);
        // Pendapatan bersih = 160.000 + 80.000 = 240.000
        $this->assertEquals(240000, $hasil['total_pendapatan_bersih']);
    }

    /**
     * BB-67: Unduh PDF Laporan - Seluruh 4 jenis laporan mengembalikan HTTP 200
     * dan format application/pdf.
     */
    public function test_bb_67_unduh_pdf_laporan_mengembalikan_respons_pdf_200(): void
    {
        $jenisLaporan = ['penyewaan', 'pendapatan', 'denda', 'terlaris'];

        foreach ($jenisLaporan as $jenis) {
            $response = $this->actingAs($this->admin)->get(route('admin.laporan.pdf', [
                'jenis' => $jenis,
                'dari' => now()->startOfMonth()->toDateString(),
                'sampai' => now()->toDateString(),
            ]));

            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');
        }

        // Pemilik juga dapat mengunduh PDF laporan
        $responsePemilik = $this->actingAs($this->pemilik)->get(route('pemilik.laporan.pdf', [
            'jenis' => 'penyewaan',
            'dari' => now()->startOfMonth()->toDateString(),
            'sampai' => now()->toDateString(),
        ]));
        $responsePemilik->assertStatus(200);
        $responsePemilik->assertHeader('content-type', 'application/pdf');
    }

    /**
     * BB-68: Unduh Resi PDF - Pelanggan dapat mengunduh resi miliknya sendiri,
     * dilarang mengunduh milik pelanggan lain, dan Admin dapat mengunduh resi pesanan apa pun.
     */
    public function test_bb_68_unduh_pdf_resi_oleh_pelanggan_dan_admin(): void
    {
        $pesanan1 = Pesanan::create([
            'no_resi' => 'BLN-261020-0010',
            'pelanggan_id' => $this->pelanggan1->id,
            'dibuat_oleh' => $this->userPelanggan1->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => now()->addDays(2)->setTime(21, 0, 0),
            'batas_ambil' => now()->setTime(21, 0, 0),
            'total_sewa' => 160000,
            'status' => StatusPesanan::Menunggu,
        ]);

        $pesanan2 = Pesanan::create([
            'no_resi' => 'BLN-261020-0020',
            'pelanggan_id' => $this->pelanggan2->id,
            'dibuat_oleh' => $this->userPelanggan2->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => now()->addDays(2)->setTime(21, 0, 0),
            'batas_ambil' => now()->setTime(21, 0, 0),
            'total_sewa' => 120000,
            'status' => StatusPesanan::Menunggu,
        ]);

        // 1. Pelanggan 1 mengunduh resi miliknya sendiri -> Berhasil (200, application/pdf)
        $responsePelanggan = $this->actingAs($this->userPelanggan1)
            ->get(route('pesanan.pdf', $pesanan1->no_resi));
        $responsePelanggan->assertStatus(200);
        $responsePelanggan->assertHeader('content-type', 'application/pdf');

        // 2. Pelanggan 1 mencoba mengunduh resi milik Pelanggan 2 -> 403 Forbidden
        $responseIlegal = $this->actingAs($this->userPelanggan1)
            ->get(route('pesanan.pdf', $pesanan2->no_resi));
        $responseIlegal->assertStatus(403);

        // 3. Admin dapat mengunduh resi pesanan mana pun melalui admin route
        $responseAdmin = $this->actingAs($this->admin)
            ->get(route('admin.pesanan.pdf', $pesanan2));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertHeader('content-type', 'application/pdf');
    }

    /**
     * BB-69: Hak Akses Pemilik Read-Only - Pemilik dapat melihat dashboard dan laporan,
     * tetapi dilarang mengakses form atau aksi pengubahan transaksi admin.
     */
    public function test_bb_69_pemilik_read_only_dan_dilarang_akses_form_transaksi_admin(): void
    {
        $pesanan = Pesanan::create([
            'no_resi' => 'BLN-261020-0030',
            'pelanggan_id' => $this->pelanggan1->id,
            'dibuat_oleh' => $this->userPelanggan1->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'durasi_hari' => 2,
            'jatuh_tempo' => now()->addDays(2)->setTime(21, 0, 0),
            'batas_ambil' => now()->setTime(21, 0, 0),
            'total_sewa' => 160000,
            'status' => StatusPesanan::Menunggu,
        ]);

        // 1. Pemilik dapat melihat dashboard dan laporan pemilik
        $this->actingAs($this->pemilik)->get(route('pemilik.dashboard'))->assertStatus(200);
        $this->actingAs($this->pemilik)->get(route('pemilik.laporan.index'))->assertStatus(200);

        // 2. Pemilik dilarang mengakses modul admin transaksi (403)
        $this->actingAs($this->pemilik)->get(route('admin.pesanan.index'))->assertStatus(403);
        $this->actingAs($this->pemilik)->post(route('admin.pesanan.bayar', $pesanan))->assertStatus(403);
        $this->actingAs($this->pemilik)->post(route('admin.pesanan.ambil', $pesanan))->assertStatus(403);
        $this->actingAs($this->pemilik)->get(route('admin.pesanan.pengembalian.create', $pesanan))->assertStatus(403);
    }

    /**
     * BB-70: Dashboard Metrik & Chart - Dashboard Admin dan Pemilik memuat metrik
     * kartu dan data JSON yang siap digunakan Chart.js.
     */
    public function test_bb_70_dashboard_admin_dan_pemilik_memuat_metrik_dan_data_chart(): void
    {
        // 1. Dashboard Admin
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertViewHas('statistik');
        $responseAdmin->assertViewHas('pesananTerlambat');
        $responseAdmin->assertViewHas('pesananAmbilHariIni');
        $responseAdmin->assertViewHas('chartLabels');
        $responseAdmin->assertViewHas('chartData');

        // Pastikan isi statistik admin lengkap (5 metrik operasional)
        $statistikAdmin = $responseAdmin->viewData('statistik');
        $this->assertArrayHasKey('pesanan_menunggu', $statistikAdmin);
        $this->assertArrayHasKey('perlu_diambil_hari_ini', $statistikAdmin);
        $this->assertArrayHasKey('pesanan_disewa', $statistikAdmin);
        $this->assertArrayHasKey('pesanan_terlambat', $statistikAdmin);
        $this->assertArrayHasKey('denda_belum_lunas', $statistikAdmin);

        // 2. Dashboard Pemilik
        $responsePemilik = $this->actingAs($this->pemilik)->get(route('pemilik.dashboard'));
        $responsePemilik->assertStatus(200);
        $responsePemilik->assertViewHas('ringkasan');
        $responsePemilik->assertViewHas('alatTerlaris');
        $responsePemilik->assertViewHas('stokKritis');
        $responsePemilik->assertViewHas('chartLabels');
        $responsePemilik->assertViewHas('chartData');

        $ringkasan = $responsePemilik->viewData('ringkasan');
        $this->assertArrayHasKey('pendapatan_hari_ini', $ringkasan);
        $this->assertArrayHasKey('pendapatan_bulan_ini', $ringkasan);
        $this->assertArrayHasKey('total_transaksi', $ringkasan);
        $this->assertArrayHasKey('total_alat', $ringkasan);
        $this->assertArrayHasKey('alat_dalam_perawatan', $ringkasan);
        $this->assertArrayHasKey('jumlah_terlambat', $ringkasan);
    }
}
