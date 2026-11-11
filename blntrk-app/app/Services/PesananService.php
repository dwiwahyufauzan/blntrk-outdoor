<?php

namespace App\Services;

use App\Data\PesananBaru;
use App\Data\RefundBaru;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Refund;
use App\Models\User;
use App\Support\PengaturanToko;
use Carbon\CarbonInterface;
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
     * @throws ValidationException
     */
    public function buat(Pelanggan $pelanggan, User $pembuat, PesananBaru $data): Pesanan
    {
        // Dibaca sebelum transaksi: cache bertipe database menulis baris saat miss, dan itu tidak boleh
        // terjadi di dalam bagian kritis yang memegang kunci baris alat.
        $jamKembali = PengaturanToko::jamKembali();
        $batasBayarJam = PengaturanToko::batasBayarJam();
        $maksMenunggu = PengaturanToko::maksPesananMenunggu();

        return DB::transaction(function () use ($pelanggan, $pembuat, $data, $jamKembali, $batasBayarJam, $maksMenunggu) {
            $mulai = $data->tanggalMulai->copy()->startOfDay();
            $selesai = $data->tanggalSelesai->copy()->startOfDay();
            $durasi = max(1, (int) $mulai->diffInDays($selesai));

            $this->batasiPesananMenunggu($pelanggan, $pembuat, $maksMenunggu);

            // Kunci baris alat terurut berdasarkan ID agar pemesanan paralel dieksekusi berurutan tanpa deadlock
            $alatIds = collect($data->items)->pluck('alatId')->sort()->values();
            $alatList = Alat::whereIn('id', $alatIds)
                ->where('is_aktif', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $detail = [];
            $kuantitas = [];

            foreach ($data->items as $index => $item) {
                $alatId = $item->alatId;
                $jumlah = $item->jumlah;

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

            $jatuhTempo = $selesai->copy()->setTimeFromTimeString($jamKembali);
            $batasAmbil = $mulai->copy()->setTime(23, 59, 59);
            $batasBayar = $this->hitungBatasBayar($batasBayarJam, $batasAmbil);

            $pesanan = $this->simpanDenganResi([
                'pelanggan_id' => $pelanggan->id,
                'dibuat_oleh' => $pembuat->id,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'durasi_hari' => $durasi,
                'jatuh_tempo' => $jatuhTempo,
                'batas_ambil' => $batasAmbil,
                'batas_bayar' => $batasBayar,
                'status' => StatusPesanan::Menunggu,
                'total_sewa' => $total,
                'paket_bundling_id' => $diskon > 0 ? $diskonPaket['paket']?->id : null,
                'jumlah_paket' => $diskon > 0 ? $diskonPaket['jumlah_paket'] : 0,
                'diskon_paket' => $diskon,
                'catatan' => $data->catatan,
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
     * Batas pembayaran: sekian jam sejak dibuat, tetapi tidak pernah melewati batas pengambilan.
     * Pengaturan 0 jam berarti pesanan hanya dibatasi batas pengambilan.
     */
    public function hitungBatasBayar(int $batasBayarJam, CarbonInterface $batasAmbil): CarbonInterface
    {
        if ($batasBayarJam <= 0) {
            return $batasAmbil->copy();
        }

        $batas = now()->addHours($batasBayarJam);

        return $batas->lt($batasAmbil) ? $batas : $batasAmbil->copy();
    }

    /**
     * Mencegah satu pelanggan menimbun stok lewat banyak pesanan menunggu. Hanya berlaku untuk pesanan
     * yang dibuat pelanggan sendiri; admin (walk-in) tidak dibatasi. Baris pelanggan dikunci agar dua
     * permintaan bersamaan tidak sama-sama lolos.
     *
     * @throws ValidationException
     */
    private function batasiPesananMenunggu(Pelanggan $pelanggan, User $pembuat, int $maks): void
    {
        if ($maks <= 0 || $pembuat->punyaRole('admin', 'pemilik')) {
            return;
        }

        Pelanggan::whereKey($pelanggan->id)->lockForUpdate()->first();

        $menunggu = Pesanan::where('pelanggan_id', $pelanggan->id)
            ->where('status', StatusPesanan::Menunggu)
            ->count();

        if ($menunggu >= $maks) {
            throw ValidationException::withMessages([
                'pesanan' => "Anda masih memiliki {$menunggu} pesanan yang belum dibayar (batas {$maks}). Selesaikan pembayaran atau batalkan salah satunya sebelum membuat pesanan baru.",
            ]);
        }
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
                "Pembayaran {$p->no_resi} diterima sebesar Rp".number_format((float) $p->total_sewa, 0, ',', '.')." ({$d['metode']})"
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

            $deposit = ($d['jenis_jaminan'] ?? $d['jenis'] ?? null) === 'deposit';
            $nominal = null;

            if ($deposit) {
                $nominal = round((float) ($d['nominal_deposit'] ?? 0), 2);
                if ($nominal <= 0) {
                    throw ValidationException::withMessages([
                        'nominal_deposit' => 'Nominal deposit uang wajib diisi dan lebih dari nol.',
                    ]);
                }
                $nomorSamar = 'DEPOSIT';
                $jenis = 'lainnya';
            } else {
                $rawNomor = preg_replace('/\D/', '', (string) ($d['nomor_identitas'] ?? ''));
                if (strlen($rawNomor) < 4) {
                    throw ValidationException::withMessages([
                        'nomor_identitas' => 'Nomor identitas harus memiliki minimal 4 digit angka.',
                    ]);
                }

                $nomorSamar = '****'.substr($rawNomor, -4);
                $jenis = $d['jenis_jaminan'] ?? $d['jenis'] ?? 'ktp';
            }

            $p->jaminan()->create([
                'jenis' => $jenis,
                'nomor_identitas_samar' => $nomorSamar,
                'keterangan' => $d['keterangan'] ?? null,
                'nominal' => $nominal,
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
                "Pengambilan barang / serah terima pesanan {$p->no_resi} dengan jaminan "
                    .($deposit ? 'deposit uang Rp'.number_format($nominal, 0, ',', '.') : "{$jenis} ({$nomorSamar})")
            );
        });
    }

    /**
     * Membatalkan pesanan sesuai aturan state machine.
     * Admin: dari status menunggu atau dibayar. Alasan wajib.
     * Pelanggan: dari status menunggu, pesanan miliknya sendiri.
     * Status disewa/terlambat/dikembalikan/dibatalkan: ditolak.
     *
     * Pesanan yang sudah dibayar harus ditutup dengan salah satu kebijakan dana: dikembalikan ($refund diisi,
     * penuh atau sebagian) atau hangus ($refund null). Selisih yang tidak dikembalikan tetap menjadi pendapatan.
     */
    public function batal(Pesanan $p, User $pelaku, string $alasan, ?RefundBaru $refund = null): void
    {
        DB::transaction(function () use ($p, $pelaku, $alasan, $refund) {
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

            $dibayar = $p->total_dibayar;
            if ($refund) {
                $this->pastikanRefundWajar($dibayar, 0.0, $refund);
            }

            $p->update([
                'status' => StatusPesanan::Dibatalkan,
                'dibatalkan_pada' => now(),
                'alasan_batal' => $alasan,
                'diproses_oleh' => $pelaku->id,
            ]);

            $keteranganDana = '';
            if ($dibayar > 0) {
                if ($refund) {
                    $this->simpanRefund($p, $pelaku, $refund);
                    $hangus = round($dibayar - $refund->jumlah, 2);
                    $keteranganDana = ' [Dana dikembalikan Rp'.number_format($refund->jumlah, 0, ',', '.')." ({$refund->metode->value})"
                        .($hangus > 0 ? ', hangus Rp'.number_format($hangus, 0, ',', '.') : '').']';
                } else {
                    $keteranganDana = ' [Dana Rp'.number_format($dibayar, 0, ',', '.').' hangus, tidak dikembalikan]';
                }
            }

            LogAktivitas::catat(
                $pelaku,
                'pesanan.batal',
                "Membatalkan pesanan [{$p->no_resi}] dengan alasan: {$alasan}{$keteranganDana}"
            );
        });
    }

    /**
     * Mencatat refund pada pesanan yang sudah dibatalkan, misalnya pesanan no-show yang dibatalkan sistem
     * dengan dana hangus lalu diputuskan pemilik/admin untuk dikembalikan.
     *
     * @throws ValidationException
     */
    public function catatRefund(Pesanan $p, User $admin, RefundBaru $refund): Refund
    {
        return DB::transaction(function () use ($p, $admin, $refund) {
            $p = Pesanan::lockForUpdate()->findOrFail($p->id);

            if ($p->status !== StatusPesanan::Dibatalkan) {
                throw ValidationException::withMessages([
                    'status' => 'Refund hanya dapat dicatat untuk pesanan yang sudah dibatalkan.',
                ]);
            }

            $this->pastikanRefundWajar($p->total_dibayar, $p->total_refund, $refund);

            return $this->simpanRefund($p, $admin, $refund);
        });
    }

    /**
     * Refund tidak boleh melebihi uang yang pernah diterima dikurangi refund sebelumnya.
     *
     * @throws ValidationException
     */
    private function pastikanRefundWajar(float $dibayar, float $sudahDirefund, RefundBaru $refund): void
    {
        $sisa = round($dibayar - $sudahDirefund, 2);

        if ($dibayar <= 0) {
            throw ValidationException::withMessages([
                'jumlah_refund' => 'Pesanan ini belum memiliki pembayaran sehingga tidak ada dana yang dapat dikembalikan.',
            ]);
        }

        if ($refund->jumlah <= 0 || $refund->jumlah - $sisa > 0.001) {
            throw ValidationException::withMessages([
                'jumlah_refund' => 'Jumlah refund harus lebih dari nol dan tidak boleh melebihi sisa dana Rp'.number_format(max(0, $sisa), 0, ',', '.').'.',
            ]);
        }
    }

    private function simpanRefund(Pesanan $p, User $pencatat, RefundBaru $refund): Refund
    {
        $baris = $p->refund()->create([
            'jumlah' => $refund->jumlah,
            'metode' => $refund->metode,
            'direfund_pada' => now(),
            'dicatat_oleh' => $pencatat->id,
            'catatan' => $refund->catatan,
        ]);

        LogAktivitas::catat(
            $pencatat,
            'pesanan.refund',
            "Mencatat refund pesanan [{$p->no_resi}] sebesar Rp".number_format($refund->jumlah, 0, ',', '.')." ({$refund->metode->value})",
            'pesanan',
            $p->id
        );

        return $baris;
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
     * Memeriksa dan membatalkan pesanan menunggu yang melewati batas waktu pengambilan (batas_ambil).
     * Sesuai BB-51 (Scheduler / Task). Logika sebenarnya ada di PesananStatusSyncService.
     */
    public function batalkanKadaluarsa(): int
    {
        return app(PesananStatusSyncService::class)->batalkanMenungguKadaluarsa(now());
    }

    /**
     * Menjalankan seluruh sinkronisasi status otomatis (terlambat, kedaluwarsa, no-show).
     * Dipertahankan sebagai pintu masuk lama; implementasi ada di PesananStatusSyncService.
     */
    public function sinkronStatus(): int
    {
        return app(PesananStatusSyncService::class)->sinkron();
    }
}
