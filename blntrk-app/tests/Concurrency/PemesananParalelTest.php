<?php

namespace Tests\Concurrency;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * Bukti nyata klaim anti double-booking: banyak proses PHP terpisah memesan stok yang sama pada saat bersamaan.
 *
 * Tidak berjalan di `php artisan test` biasa karena memerlukan database sungguhan (bukan SQLite :memory:) dan
 * memicu puluhan proses. Jalankan dengan:
 *   RUN_CONCURRENCY_TESTS=1 DB_CONNECTION=pgsql DB_HOST=... php artisan test --testsuite=Concurrency
 * Di CI, job `concurrency` menjalankannya terhadap PostgreSQL dan MySQL.
 */
class PemesananParalelTest extends TestCase
{
    use DatabaseMigrations;

    private const PROSES = 10;

    private User $admin;

    private Pelanggan $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        if (! env('RUN_CONCURRENCY_TESTS')) {
            $this->markTestSkipped('Set RUN_CONCURRENCY_TESTS=1 untuk menjalankan uji concurrency.');
        }

        if (config('database.connections.'.config('database.default').'.database') === ':memory:') {
            $this->markTestSkipped('Uji concurrency memerlukan database yang dapat diakses lintas proses (bukan :memory:).');
        }

        $roleAdmin = Role::create(['nama' => 'admin', 'label' => 'Administrator']);
        $rolePelanggan = Role::create(['nama' => 'pelanggan', 'label' => 'Pelanggan']);
        $this->admin = User::create(['role_id' => $roleAdmin->id, 'nama' => 'Admin', 'email' => 'a@blntrk.test', 'password' => bcrypt('x'), 'is_aktif' => true]);
        $user = User::create(['role_id' => $rolePelanggan->id, 'nama' => 'P', 'email' => 'p@blntrk.test', 'password' => bcrypt('x'), 'is_aktif' => true]);
        $this->pelanggan = Pelanggan::create(['user_id' => $user->id, 'nama_lengkap' => 'P', 'no_hp' => '081234567890', 'alamat' => '-']);
    }

    private function alat(string $kode, int $stok): Alat
    {
        $kategori = KategoriAlat::firstOrCreate(['slug' => 'umum'], ['nama' => 'Umum']);

        return Alat::create([
            'kategori_id' => $kategori->id, 'kode_alat' => $kode, 'nama' => "Alat {$kode}",
            'harga_per_hari' => 10000, 'stok_total' => $stok, 'stok_perawatan' => 0, 'is_aktif' => true,
        ]);
    }

    /**
     * Menjalankan beberapa proses pemesanan serentak.
     *
     * @param  array<int, array<int, string>>  $itemsPerProses  Daftar "alat_id:jumlah" untuk tiap proses
     * @return array<int, array<string, mixed>>
     */
    private function jalankanSerentak(array $itemsPerProses): array
    {
        // Seluruh data uji sudah ter-commit (tanpa transaksi pembungkus), sehingga terlihat oleh proses anak
        $mulaiPada = microtime(true) + 3.0; // memberi waktu semua proses selesai booting sebelum menembak bersamaan
        $mulai = today()->addDay()->toDateString();
        $selesai = today()->addDays(3)->toDateString();

        $proses = [];
        foreach ($itemsPerProses as $i => $items) {
            $p = new Process([
                PHP_BINARY, __DIR__.'/pesan.php',
                (string) $this->pelanggan->id, (string) $this->admin->id, $mulai, $selesai, sprintf('%.6F', $mulaiPada), ...$items,
            ], base_path(), array_merge(getenv(), ['APP_ENV' => 'testing']), null, 120);
            $p->start();
            $proses[$i] = $p;
        }

        $hasil = [];
        foreach ($proses as $i => $p) {
            $p->wait();
            $baris = trim($p->getOutput());
            $json = json_decode(strtok($baris, "\n") ?: '', true);
            $hasil[$i] = $json ?? ['status' => 'error', 'pesan' => "Keluaran tidak valid: {$baris} / {$p->getErrorOutput()}"];
        }

        return $hasil;
    }

    private function ringkas(array $hasil): array
    {
        return array_count_values(array_column($hasil, 'status')) + ['ok' => 0, 'validasi' => 0, 'error' => 0];
    }

    private function dukungKunciBaris(): bool
    {
        return in_array(config('database.default'), ['pgsql', 'mysql', 'mariadb'], true);
    }

    public function test_pemesanan_serentak_tidak_pernah_melebihi_stok(): void
    {
        $alat = $this->alat('TND', 3);

        $hasil = $this->jalankanSerentak(array_fill(0, self::PROSES, ["{$alat->id}:1"]));
        $ringkas = $this->ringkas($hasil);

        $terpesan = Pesanan::count();
        $this->assertLessThanOrEqual(3, $terpesan, 'Double-booking: pesanan melebihi stok 3 unit. '.json_encode($hasil));
        $this->assertSame($ringkas['ok'], $terpesan);

        if ($this->dukungKunciBaris()) {
            // Dengan penguncian baris: tepat 3 berhasil, sisanya ditolak validasi, tanpa error/deadlock
            $this->assertSame(0, $ringkas['error'], 'Ada error tak terduga: '.json_encode($hasil));
            $this->assertSame(3, $ringkas['ok'], json_encode($hasil));
            $this->assertSame(self::PROSES - 3, $ringkas['validasi']);
        }
    }

    public function test_nomor_resi_unik_saat_banyak_pesanan_pertama_hari_itu_dibuat_serentak(): void
    {
        // Setiap proses memesan alat berbeda sehingga tidak ada rebutan stok; hanya penomoran resi yang bersaing
        $itemsPerProses = [];
        for ($i = 0; $i < self::PROSES; $i++) {
            $itemsPerProses[] = [$this->alat("A{$i}", 1)->id.':1'];
        }

        $hasil = $this->jalankanSerentak($itemsPerProses);
        $ringkas = $this->ringkas($hasil);

        $resi = Pesanan::pluck('no_resi');
        $this->assertSame($resi->count(), $resi->unique()->count(), 'Nomor resi duplikat');
        $this->assertSame($ringkas['ok'], $resi->count());

        if ($this->dukungKunciBaris()) {
            $this->assertSame(self::PROSES, $ringkas['ok'], json_encode($hasil));
        }
    }

    public function test_pemesanan_multi_alat_urutan_berlawanan_tidak_menyebabkan_deadlock(): void
    {
        $a = $this->alat('KA', 2);
        $b = $this->alat('KB', 2);

        // Separuh proses menyebut A lalu B, separuh B lalu A: kunci diambil terurut berdasarkan ID di dalam service
        $itemsPerProses = [];
        for ($i = 0; $i < self::PROSES; $i++) {
            $itemsPerProses[] = $i % 2 === 0 ? ["{$a->id}:1", "{$b->id}:1"] : ["{$b->id}:1", "{$a->id}:1"];
        }

        $hasil = $this->jalankanSerentak($itemsPerProses);
        $ringkas = $this->ringkas($hasil);

        $this->assertLessThanOrEqual(2, Pesanan::count(), 'Double-booking pada pesanan multi-alat. '.json_encode($hasil));

        if ($this->dukungKunciBaris()) {
            $this->assertSame(0, $ringkas['error'], 'Kemungkinan deadlock: '.json_encode($hasil));
            $this->assertSame(2, $ringkas['ok']);
        }
    }
}
