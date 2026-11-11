<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Pesanan;
use App\Models\User;
use App\Support\PengaturanToko;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Perubahan atas pesanan yang sudah ada: perpanjangan sewa (saat disewa) dan ubah jadwal (sebelum pengambilan).
 * Keduanya memakai penguncian baris alat yang sama dengan pembuatan pesanan sehingga tetap anti double-booking.
 */
class PerubahanPesananService
{
    public function __construct(
        protected KetersediaanService $ketersediaan,
        protected PaketBundlingService $paketBundling
    ) {}

    /**
     * Memperpanjang pesanan berstatus disewa hingga $selesaiBaru. Tambahan biaya wajib dibayar saat itu juga
     * dan dicatat sebagai pembayaran berjenis `perpanjangan`.
     *
     * @param  array{metode: string, catatan?: ?string}  $bayar
     * @return float Tambahan biaya sewa
     *
     * @throws ValidationException
     */
    public function perpanjang(Pesanan $pesanan, User $admin, Carbon $selesaiBaru, array $bayar): float
    {
        $this->panaskanPengaturan();

        return DB::transaction(function () use ($pesanan, $admin, $selesaiBaru, $bayar) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id);

            if ($p->status !== StatusPesanan::Disewa) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Perpanjangan hanya dapat dilakukan untuk pesanan yang sedang disewa dan belum terlambat.',
                ]);
            }

            $selesaiBaru = $selesaiBaru->copy()->startOfDay();
            $selesaiLama = $p->tanggal_selesai->copy()->startOfDay();

            if ($selesaiBaru->lte($selesaiLama)) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Tanggal selesai baru harus setelah tanggal selesai saat ini ('.$selesaiLama->format('d/m/Y').').',
                ]);
            }

            if ($p->jatuh_tempo && now()->gt($p->jatuh_tempo)) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Pesanan sudah melewati jam kembali. Selesaikan pengembalian atau denda terlebih dahulu.',
                ]);
            }

            $this->pastikanDurasi($p->tanggal_mulai, $selesaiBaru, 'tanggal_selesai');

            $alatList = $this->kunciAlat($p);

            // Hanya rentang tambahan yang perlu dicek; pesanan ini sendiri diabaikan agar tidak menghitung dirinya
            foreach ($p->detail as $d) {
                $alat = $alatList[$d->alat_id];
                if (! $this->ketersediaan->cukup($alat, $d->jumlah, $selesaiLama, $selesaiBaru, $p->id)) {
                    $sisa = $this->ketersediaan->tersedia($alat, $selesaiLama, $selesaiBaru, $p->id);
                    throw ValidationException::withMessages([
                        'tanggal_selesai' => "Stok {$alat->nama} tidak cukup untuk diperpanjang sampai {$selesaiBaru->format('d/m/Y')}. Sisa tersedia: {$sisa} unit (dipakai: {$d->jumlah} unit).",
                    ]);
                }
            }

            $totalLama = (float) $p->total_sewa;
            $this->hitungUlang($p, $p->tanggal_mulai, $selesaiBaru);
            $tambahan = round((float) $p->total_sewa - $totalLama, 2);

            if ($tambahan > 0) {
                $p->pembayaran()->create([
                    'jumlah' => $tambahan,
                    'metode' => $bayar['metode'],
                    'jenis' => 'perpanjangan',
                    'catatan' => $bayar['catatan'] ?? 'Perpanjangan sewa sampai '.$selesaiBaru->format('d/m/Y'),
                    'dibayar_pada' => now(),
                    'dicatat_oleh' => $admin->id,
                ]);
            }

            LogAktivitas::catat(
                $admin,
                'pesanan.perpanjang',
                "Memperpanjang pesanan [{$p->no_resi}] dari {$selesaiLama->format('d/m/Y')} menjadi {$selesaiBaru->format('d/m/Y')}, tambahan Rp".number_format($tambahan, 0, ',', '.'),
                'pesanan',
                $p->id
            );

            return max(0.0, $tambahan);
        });
    }

    /**
     * Mengubah jadwal sewa sebelum barang diambil.
     * - menunggu: oleh pemilik pesanan atau admin; harga dihitung ulang sesuai durasi baru.
     * - dibayar : hanya admin dan durasi harus tetap, karena uang yang sudah masuk tidak berubah nilainya.
     *
     * @throws ValidationException
     */
    public function ubahJadwal(Pesanan $pesanan, User $pelaku, Carbon $mulaiBaru, Carbon $selesaiBaru): void
    {
        $this->panaskanPengaturan();

        DB::transaction(function () use ($pesanan, $pelaku, $mulaiBaru, $selesaiBaru) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id);
            $isAdmin = $pelaku->punyaRole('admin');

            $diizinkan = $isAdmin ? [StatusPesanan::Menunggu, StatusPesanan::Dibayar] : [StatusPesanan::Menunggu];
            if (! in_array($p->status, $diizinkan, true)) {
                throw ValidationException::withMessages([
                    'status' => "Jadwal tidak dapat diubah untuk pesanan berstatus {$p->status->value}.",
                ]);
            }

            if (! $isAdmin && (! $p->pelanggan || $p->pelanggan->user_id !== $pelaku->id)) {
                throw ValidationException::withMessages(['status' => 'Anda tidak berwenang mengubah pesanan ini.']);
            }

            $mulaiBaru = $mulaiBaru->copy()->startOfDay();
            $selesaiBaru = $selesaiBaru->copy()->startOfDay();

            if ($mulaiBaru->lt(today())) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Tanggal mulai tidak boleh di masa lalu.']);
            }

            $durasiBaru = $this->pastikanDurasi($mulaiBaru, $selesaiBaru, 'tanggal_selesai');

            if ($p->status === StatusPesanan::Dibayar && $durasiBaru !== (int) $p->durasi_hari) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => "Pesanan yang sudah dibayar hanya dapat dipindah jadwal dengan durasi tetap ({$p->durasi_hari} hari).",
                ]);
            }

            $alatList = $this->kunciAlat($p);

            foreach ($p->detail as $d) {
                $alat = $alatList[$d->alat_id];
                if (! $this->ketersediaan->cukup($alat, $d->jumlah, $mulaiBaru, $selesaiBaru, $p->id)) {
                    $sisa = $this->ketersediaan->tersedia($alat, $mulaiBaru, $selesaiBaru, $p->id);
                    throw ValidationException::withMessages([
                        'tanggal_mulai' => "Stok {$alat->nama} tidak cukup pada {$mulaiBaru->format('d/m/Y')} - {$selesaiBaru->format('d/m/Y')}. Sisa tersedia: {$sisa} unit (dipesan: {$d->jumlah} unit).",
                    ]);
                }
            }

            $lama = $p->tanggal_mulai->format('d/m/Y').' - '.$p->tanggal_selesai->format('d/m/Y');
            $this->hitungUlang($p, $mulaiBaru, $selesaiBaru);

            LogAktivitas::catat(
                $pelaku,
                'pesanan.ubah-jadwal',
                "Mengubah jadwal pesanan [{$p->no_resi}] dari {$lama} menjadi {$mulaiBaru->format('d/m/Y')} - {$selesaiBaru->format('d/m/Y')}",
                'pesanan',
                $p->id
            );
        });
    }

    /**
     * Membaca pengaturan sebelum transaksi dimulai agar penulisan cache (driver database) tidak terjadi
     * di dalam bagian kritis yang memegang kunci baris alat.
     */
    private function panaskanPengaturan(): void
    {
        PengaturanToko::jamKembali();
        PengaturanToko::maksDurasiHari();
    }

    /**
     * @return array<int, Alat> Alat milik pesanan, terkunci terurut berdasarkan ID (anti-deadlock)
     *
     * @throws ValidationException
     */
    private function kunciAlat(Pesanan $p): array
    {
        $p->load('detail');

        $alatList = Alat::whereIn('id', $p->detail->pluck('alat_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($p->detail as $d) {
            if (! $alatList->has($d->alat_id)) {
                throw ValidationException::withMessages(['status' => 'Salah satu alat pada pesanan ini sudah tidak tersedia.']);
            }
        }

        return $alatList->all();
    }

    private function pastikanDurasi(Carbon $mulai, Carbon $selesai, string $field): int
    {
        $durasi = (int) $mulai->copy()->startOfDay()->diffInDays($selesai->copy()->startOfDay());

        if ($selesai->copy()->startOfDay()->lte($mulai->copy()->startOfDay())) {
            throw ValidationException::withMessages([$field => 'Tanggal selesai harus setelah tanggal mulai.']);
        }

        $maks = PengaturanToko::maksDurasiHari();
        if ($durasi > $maks) {
            throw ValidationException::withMessages([$field => "Durasi sewa maksimal adalah {$maks} hari."]);
        }

        return $durasi;
    }

    /**
     * Menghitung ulang subtotal detail, diskon paket, total, jatuh tempo, dan batas ambil untuk jadwal baru.
     */
    private function hitungUlang(Pesanan $p, Carbon $mulai, Carbon $selesai): void
    {
        $mulai = $mulai->copy()->startOfDay();
        $selesai = $selesai->copy()->startOfDay();
        $durasi = max(1, (int) $mulai->diffInDays($selesai));

        $total = 0.0;
        $kuantitas = [];
        foreach ($p->detail as $d) {
            $subtotal = (float) $d->harga_per_hari * $d->jumlah * $durasi;
            $d->update(['subtotal' => $subtotal]);
            $total += $subtotal;
            $kuantitas[$d->alat_id] = $d->jumlah;
        }

        $diskonPaket = $this->paketBundling->hitungDiskon($kuantitas, $durasi);
        $diskon = min((float) $diskonPaket['diskon'], $total);

        $p->update([
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'durasi_hari' => $durasi,
            'jatuh_tempo' => $selesai->copy()->setTimeFromTimeString(PengaturanToko::jamKembali()),
            'batas_ambil' => $mulai->copy()->setTime(23, 59, 59),
            'total_sewa' => $total - $diskon,
            'paket_bundling_id' => $diskon > 0 ? $diskonPaket['paket']?->id : null,
            'jumlah_paket' => $diskon > 0 ? $diskonPaket['jumlah_paket'] : 0,
            'diskon_paket' => $diskon,
        ]);
    }
}
