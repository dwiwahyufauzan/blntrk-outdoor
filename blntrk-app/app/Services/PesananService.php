<?php

namespace App\Services;

use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananService
{
    public function __construct(
        protected KetersediaanService $ketersediaan,
        protected PaketBundlingService $paketBundling
    ) {}

    /**
     * Membuat pesanan baru secara atomic dengan penguncian baris alat (lockForUpdate).
     *
     * @param  array  $data  Format: ['tanggal_mulai' => string, 'tanggal_selesai' => string, 'items' => [['alat_id' => int, 'jumlah' => int]], 'catatan' => ?string]
     *
     * @throws ValidationException
     */
    public function buat(Pelanggan $pelanggan, User $pembuat, array $data): Pesanan
    {
        return DB::transaction(function () use ($pelanggan, $pembuat, $data) {
            $mulai = Carbon::parse($data['tanggal_mulai'])->startOfDay();
            $selesai = Carbon::parse($data['tanggal_selesai'])->startOfDay();
            $durasi = max(1, (int) $mulai->diffInDays($selesai));

            // Kunci baris alat terurut berdasarkan ID agar pemesanan paralel dieksekusi berurutan tanpa deadlock
            $alatIds = collect($data['items'])->pluck('alat_id')->map(fn ($id) => (int) $id)->sort()->values();
            $alatList = Alat::whereIn('id', $alatIds)
                ->where('is_aktif', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $detail = [];
            $kuantitas = [];

            foreach ($data['items'] as $index => $item) {
                $alatId = (int) $item['alat_id'];
                $jumlah = (int) $item['jumlah'];

                $alat = $alatList[$alatId] ?? null;
                if (! $alat) {
                    throw ValidationException::withMessages([
                        "items.{$index}.alat_id" => "Perlengkapan alat dengan ID #{$alatId} tidak tersedia atau dinonaktifkan.",
                    ]);
                }

                // Pengecekan ketersediaan stok setelah row-level lock
                if (! $this->ketersediaan->cukup($alat, $jumlah, $mulai, $selesai)) {
                    $stokTersedia = $this->ketersediaan->tersedia($alat, $mulai, $selesai);
                    $pesanStok = $stokTersedia <= 0
                        ? "Stok {$alat->nama} saat ini sedang kosong/habis untuk periode tanggal {$mulai->format('d/m/Y')} - {$selesai->format('d/m/Y')}. Alat tidak dapat dipesan."
                        : "Stok {$alat->nama} tidak mencukupi untuk tanggal {$mulai->format('d/m/Y')} - {$selesai->format('d/m/Y')}. Sisa tersedia: {$stokTersedia} unit (diminta: {$jumlah} unit).";

                    throw ValidationException::withMessages([
                        "items.{$index}.jumlah" => $pesanStok,
                    ]);
                }

                $subtotal = $alat->harga_per_hari * $jumlah * $durasi;
                $total += $subtotal;
                $kuantitas[$alat->id] = $jumlah;

                $detail[] = [
                    'alat_id' => $alat->id,
                    'jumlah' => $jumlah,
                    'harga_per_hari' => $alat->harga_per_hari,
                    'subtotal' => $subtotal,
                ];
            }

            // Diskon paket bundling otomatis bila isi pesanan memenuhi suatu paket aktif
            $diskonPaket = $this->paketBundling->hitungDiskon($kuantitas, $durasi);
            $diskon = min((float) $diskonPaket['diskon'], (float) $total);
            $total -= $diskon;

            $jamKembali = Pengaturan::ambil('jam_kembali', '12:00');
            $jatuhTempo = $selesai->copy()->setTimeFromTimeString($jamKembali);
            $batasAmbil = $mulai->copy()->setTime(23, 59, 59);

            $pesanan = $this->simpanDenganResi([
                'pelanggan_id' => $pelanggan->id,
                'dibuat_oleh' => $pembuat->id,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'durasi_hari' => $durasi,
                'jatuh_tempo' => $jatuhTempo,
                'batas_ambil' => $batasAmbil,
                'status' => StatusPesanan::Menunggu,
                'total_sewa' => $total,
                'paket_bundling_id' => $diskon > 0 ? $diskonPaket['paket']?->id : null,
                'jumlah_paket' => $diskon > 0 ? $diskonPaket['jumlah_paket'] : 0,
                'diskon_paket' => $diskon,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $pesanan->detail()->createMany($detail);

            LogAktivitas::catat(
                $pembuat,
                'pesanan.buat',
                "Membuat pesanan baru [{$pesanan->no_resi}] untuk {$pelanggan->nama_lengkap} (Total Rp".number_format($total, 0, ',', '.').')'
                .($diskon > 0 ? " dengan diskon {$diskonPaket['paket']->nama} Rp".number_format($diskon, 0, ',', '.') : '')
            );

            return $pesanan->load(['detail.alat.kategori', 'pelanggan', 'pembuat']);
        });
    }

    /**
     * Menyimpan pesanan dengan nomor resi unik. Kolom no_resi bersifat UNIQUE sehingga jika dua
     * permintaan pertama di hari yang sama (tidak ada baris yang bisa dikunci) menghasilkan resi
     * identik, salah satunya diulang di dalam savepoint dengan resi berikutnya.
     */
    private function simpanDenganResi(array $atribut): Pesanan
    {
        $maksPercobaan = 5;

        for ($percobaan = 1; ; $percobaan++) {
            try {
                return DB::transaction(
                    fn () => Pesanan::create($atribut + ['no_resi' => $this->buatResi()])
                );
            } catch (UniqueConstraintViolationException $e) {
                if ($percobaan >= $maksPercobaan) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Menghasilkan nomor resi unik harian dengan format BLN-YYMMDD-####.
     */
    public function buatResi(): string
    {
        $prefix = 'BLN-'.now()->format('ymd').'-';

        // Resi terakhir hari ini (urut leksikografis aman karena nomor di-pad 4 digit)
        $lastResi = Pesanan::where('no_resi', 'like', "{$prefix}%")
            ->orderByDesc('no_resi')
            ->lockForUpdate()
            ->value('no_resi');

        $nextNo = 1;
        if ($lastResi) {
            $parts = explode('-', $lastResi);
            $lastIndex = (int) end($parts);
            $nextNo = $lastIndex + 1;
        }

        $resi = $prefix.str_pad((string) $nextNo, 4, '0', STR_PAD_LEFT);

        // Jika karena kondisi unik terjadi tabrakan, lakukan perulangan pengaman
        while (Pesanan::where('no_resi', $resi)->exists()) {
            $nextNo++;
            $resi = $prefix.str_pad((string) $nextNo, 4, '0', STR_PAD_LEFT);
        }

        return $resi;
    }

    /**
     * Memastikan status pesanan valid sebelum melakukan transisi state machine.
     *
     * @throws ValidationException
     */
    private function pastikan(Pesanan $p, array $dari): void
    {
        if (! in_array($p->status, $dari, true)) {
            throw ValidationException::withMessages([
                'status' => "Aksi tidak valid untuk status {$p->status->value}.",
            ]);
        }
    }

    /**
     * Konfirmasi pembayaran oleh Admin.
     * Transisi: menunggu -> dibayar
     */
    public function bayar(Pesanan $p, User $admin, array $d): void
    {
        DB::transaction(function () use ($p, $admin, $d) {
            $p = Pesanan::lockForUpdate()->findOrFail($p->id);
            $this->pastikan($p, [StatusPesanan::Menunggu]);

            if (abs((float) $d['jumlah'] - (float) $p->total_sewa) > 0.001) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah harus sama dengan total sewa.',
                ]);
            }

            if ($p->pembayaran()->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Pesanan sudah memiliki pembayaran tercatat.',
                ]);
            }

            $p->pembayaran()->create([
                'jumlah' => $d['jumlah'],
                'metode' => $d['metode'],
                'catatan' => $d['catatan'] ?? null,
                'dibayar_pada' => now(),
                'dicatat_oleh' => $admin->id,
            ]);

            $p->update([
                'status' => StatusPesanan::Dibayar,
                'diproses_oleh' => $admin->id,
            ]);

            LogAktivitas::catat(
                $admin,
                'pesanan.bayar',
                "Pembayaran {$p->no_resi} diterima sebesar Rp".number_format($p->total_sewa, 0, ',', '.')." ({$d['metode']})"
            );
        });
    }

    /**
     * Konfirmasi pengambilan barang / serah terima dan pencatatan jaminan identitas oleh Admin.
     * Transisi: dibayar -> disewa
     */
    public function ambil(Pesanan $p, User $admin, array $d): void
    {
        DB::transaction(function () use ($p, $admin, $d) {
            $p = Pesanan::lockForUpdate()->findOrFail($p->id);
            $this->pastikan($p, [StatusPesanan::Dibayar]);

            $rawNomor = preg_replace('/\D/', '', (string) ($d['nomor_identitas'] ?? ''));
            if (strlen($rawNomor) < 4) {
                throw ValidationException::withMessages([
                    'nomor_identitas' => 'Nomor identitas harus memiliki minimal 4 digit angka.',
                ]);
            }

            $nomorSamar = '****'.substr($rawNomor, -4);
            $jenis = $d['jenis_jaminan'] ?? $d['jenis'] ?? 'ktp';

            $p->jaminan()->create([
                'jenis' => $jenis,
                'nomor_identitas_samar' => $nomorSamar,
                'keterangan' => $d['keterangan'] ?? null,
                'status' => 'dititipkan',
                'diterima_pada' => now(),
                'diterima_oleh' => $admin->id,
            ]);

            $p->update([
                'status' => StatusPesanan::Disewa,
                'diambil_pada' => now(),
                'diproses_oleh' => $admin->id,
            ]);

            LogAktivitas::catat(
                $admin,
                'pesanan.ambil',
                "Pengambilan barang / serah terima pesanan {$p->no_resi} dengan jaminan {$jenis} ({$nomorSamar})"
            );
        });
    }

    /**
     * Membatalkan pesanan sesuai aturan state machine.
     * Admin: dari status menunggu atau dibayar. Alasan wajib.
     * Pelanggan: dari status menunggu, pesanan miliknya sendiri.
     * Status disewa/terlambat/dikembalikan/dibatalkan: ditolak.
     */
    public function batal(Pesanan $p, User $pelaku, string $alasan): void
    {
        DB::transaction(function () use ($p, $pelaku, $alasan) {
            $p = Pesanan::lockForUpdate()->findOrFail($p->id);

            $isAdmin = $pelaku->punyaRole('admin');
            $validStatus = $isAdmin ? [StatusPesanan::Menunggu, StatusPesanan::Dibayar] : [StatusPesanan::Menunggu];

            $this->pastikan($p, $validStatus);

            if (! $isAdmin && (! $p->pelanggan || $p->pelanggan->user_id !== $pelaku->id)) {
                throw ValidationException::withMessages([
                    'status' => 'Anda tidak berwenang membatalkan pesanan ini.',
                ]);
            }

            if (trim($alasan) === '') {
                throw ValidationException::withMessages([
                    'alasan_batal' => 'Alasan pembatalan pesanan wajib diisi.',
                ]);
            }

            $p->update([
                'status' => StatusPesanan::Dibatalkan,
                'dibatalkan_pada' => now(),
                'alasan_batal' => $alasan,
                'diproses_oleh' => $pelaku->id,
            ]);

            LogAktivitas::catat(
                $pelaku,
                'pesanan.batal',
                "Membatalkan pesanan [{$p->no_resi}] dengan alasan: {$alasan}"
            );
        });
    }

    /**
     * Wrapper kompatibilitas untuk pembatalan pesanan.
     */
    public function batalkan(Pesanan $pesanan, ?string $alasan = null, ?User $user = null): Pesanan
    {
        $pelaku = $user ?? auth()->user();
        $this->batal($pesanan, $pelaku, $alasan ?? 'Dibatalkan oleh pemesan');

        return $pesanan->refresh();
    }

    /**
     * Memeriksa dan membatalkan pesanan yang melewati batas waktu pengambilan (batas_ambil).
     * Sesuai BB-51 (Scheduler / Task).
     */
    public function batalkanKadaluarsa(): int
    {
        return $this->batalkanMenungguKadaluarsa(now());
    }

    /**
     * Memperbarui status pesanan secara reaktif:
     * - Pesanan disewa yang melewati jatuh_tempo + toleransi -> terlambat
     * - Pesanan menunggu yang melewati batas_ambil -> dibatalkan
     * Setiap perubahan dicatat pada log aktivitas (audit trail, pelaku: sistem).
     */
    public function sinkronStatus(): int
    {
        $now = now();
        $toleransi = (int) Pengaturan::ambil('toleransi_menit', 60);
        $batasTerlambat = $now->copy()->subMinutes($toleransi);

        $terlambatCount = 0;
        $kandidat = Pesanan::where('status', StatusPesanan::Disewa)
            ->where('jatuh_tempo', '<', $batasTerlambat)
            ->pluck('id');

        foreach ($kandidat as $id) {
            DB::transaction(function () use ($id, $batasTerlambat, &$terlambatCount) {
                $p = Pesanan::lockForUpdate()->find($id);
                if ($p && $p->status === StatusPesanan::Disewa && $p->jatuh_tempo < $batasTerlambat) {
                    $p->update(['status' => StatusPesanan::Terlambat]);
                    LogAktivitas::catat(null, 'pesanan.terlambat', "Pesanan [{$p->no_resi}] ditandai terlambat otomatis oleh sistem", 'pesanan', $p->id);
                    $terlambatCount++;
                }
            });
        }

        return $terlambatCount + $this->batalkanMenungguKadaluarsa($now);
    }

    /**
     * Membatalkan pesanan berstatus menunggu yang melewati batas_ambil, per baris dengan lock + log.
     */
    private function batalkanMenungguKadaluarsa(Carbon $now): int
    {
        $count = 0;
        $kandidat = Pesanan::where('status', StatusPesanan::Menunggu)
            ->where('batas_ambil', '<', $now)
            ->pluck('id');

        foreach ($kandidat as $id) {
            DB::transaction(function () use ($id, $now, &$count) {
                $p = Pesanan::lockForUpdate()->find($id);
                if ($p && $p->status === StatusPesanan::Menunggu && $p->batas_ambil < $now) {
                    $p->update([
                        'status' => StatusPesanan::Dibatalkan,
                        'dibatalkan_pada' => $now,
                        'alasan_batal' => 'Dibatalkan otomatis oleh sistem: melewati batas waktu pengambilan',
                    ]);
                    LogAktivitas::catat(null, 'pesanan.batal', "Pesanan [{$p->no_resi}] dibatalkan otomatis oleh sistem (melewati batas pengambilan)", 'pesanan', $p->id);
                    $count++;
                }
            });
        }

        return $count;
    }
}
