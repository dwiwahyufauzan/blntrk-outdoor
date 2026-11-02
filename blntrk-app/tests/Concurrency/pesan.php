<?php

/**
 * Proses anak untuk uji concurrency: membuat satu pesanan lewat PesananService dalam proses PHP terpisah
 * (koneksi database sendiri), menunggu waktu mulai bersama agar semua proses menekan database bersamaan.
 *
 * Argumen: <pelanggan_id> <pembuat_id> <tanggal_mulai> <tanggal_selesai> <mulai_pada_epoch_float> <alat_id:jumlah>...
 * Keluaran: satu baris JSON {"status": "ok|validasi|error", ...}
 */

use App\Models\Pelanggan;
use App\Models\User;
use App\Services\PesananService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

[, $pelangganId, $pembuatId, $mulai, $selesai, $mulaiPada] = $argv;
$items = array_map(function (string $a) {
    [$id, $jumlah] = explode(':', $a);

    return ['alat_id' => (int) $id, 'jumlah' => (int) $jumlah];
}, array_slice($argv, 6));

// Koneksi dibuka lebih dulu agar waktu tunggu tidak tercampur dengan waktu koneksi
DB::select('select 1');

while (microtime(true) < (float) $mulaiPada) {
    usleep(200);
}

try {
    $pesanan = app(PesananService::class)->buat(
        Pelanggan::findOrFail((int) $pelangganId),
        User::findOrFail((int) $pembuatId),
        ['tanggal_mulai' => $mulai, 'tanggal_selesai' => $selesai, 'items' => $items]
    );
    echo json_encode(['status' => 'ok', 'resi' => $pesanan->no_resi]).PHP_EOL;
} catch (ValidationException $e) {
    echo json_encode(['status' => 'validasi', 'pesan' => collect($e->errors())->flatten()->first()]).PHP_EOL;
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'pesan' => get_class($e).': '.$e->getMessage()]).PHP_EOL;
}
