<?php

namespace App\Services;

use App\Enums\JenisDenda;
use App\Enums\StatusPesanan;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Jaminan;
use App\Models\KategoriDenda;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengembalianService
{
    public function __construct(
        protected DendaService $dendaService
    ) {}

    /**
     * Memastikan status pesanan valid sebelum memproses pengembalian.
     *
     * @throws ValidationException
     */
    private function pastikan(Pesanan $p, array $dari): void
    {
        if (! in_array($p->status, $dari, true)) {
            throw ValidationException::withMessages([
                'status' => "Aksi pengembalian tidak valid untuk status {$p->status->value}.",
            ]);
        }
    }

    /**
     * Memproses pengembalian alat sewa, mengecek kondisi unit, memperbarui stok,
     * serta mencatat denda keterlambatan dan kerusakan.
     *
     * @param  array  $data  Format: ['dikembalikan_pada' => string, 'catatan' => ?string, 'baris' => [pesanan_detail_id => ['baik' => int, 'rusak_ringan' => int, 'rusak_berat' => int, 'hilang' => int, 'biaya' => ?float, 'catatan' => ?string]]]
     *
     * @throws ValidationException
     */
    public function proses(Pesanan $pesanan, User $admin, array $data): Pengembalian
    {
        return DB::transaction(function () use ($pesanan, $admin, $data) {
            $p = Pesanan::with('detail.alat')->lockForUpdate()->findOrFail($pesanan->id);
            $this->pastikan($p, [StatusPesanan::Disewa, StatusPesanan::Terlambat]);

            $waktuKembali = Carbon::parse($data['dikembalikan_pada'] ?? now());

            $kembali = Pengembalian::create([
                'pesanan_id' => $p->id,
                'dikembalikan_pada' => $waktuKembali,
                'hari_terlambat' => 0,
                'diterima_oleh' => $admin->id,
                'catatan' => $data['catatan'] ?? null,
            ]);

            foreach ($p->detail as $det) {
                $x = $data['baris'][$det->id] ?? null;
                if (! $x) {
                    throw ValidationException::withMessages([
                        'baris' => "Data kondisi untuk alat {$det->alat->nama} wajib diisi.",
                    ]);
                }

                $baik = (int) ($x['baik'] ?? 0);
                $rusakRingan = (int) ($x['rusak_ringan'] ?? 0);
                $rusakBerat = (int) ($x['rusak_berat'] ?? 0);
                $hilang = (int) ($x['hilang'] ?? 0);
                $jumlahKondisi = $baik + $rusakRingan + $rusakBerat + $hilang;

                if ($jumlahKondisi !== $det->jumlah) {
                    throw ValidationException::withMessages([
                        'baris' => "Jumlah kondisi alat {$det->alat->nama} ({$jumlahKondisi}) harus sama dengan jumlah yang disewa ({$det->jumlah} unit).",
                    ]);
                }

                $kd = $kembali->detail()->create([
                    'pesanan_detail_id' => $det->id,
                    'jml_baik' => $baik,
                    'jml_rusak_ringan' => $rusakRingan,
                    'jml_rusak_berat' => $rusakBerat,
                    'jml_hilang' => $hilang,
                    'catatan' => $x['catatan'] ?? null,
                ]);

                // Update stok alat:
                // unit rusak ringan/berat masuk ke stok perawatan
                // unit hilang mengurangi stok total
                $alat = Alat::withTrashed()->lockForUpdate()->findOrFail($det->alat_id);
                $alat->stok_perawatan += ($rusakRingan + $rusakBerat);
                $alat->stok_total -= $hilang;
                $alat->save();

                // Denda kerusakan atau penggantian unit hilang sesuai harga beli alat
                $biaya = (float) ($x['biaya'] ?? 0);
                if ($hilang > 0 && $biaya <= 0 && (float) ($alat->harga_beli ?? 0) > 0) {
                    $biaya = (float) $alat->harga_beli * $hilang;
                }

                if ($biaya > 0) {
                    $kategoriId = $x['kategori_denda_id'] ?? null;
                    $jenisDenda = JenisDenda::Kerusakan;

                    if ($kategoriId) {
                        $kat = KategoriDenda::find($kategoriId);
                        if ($kat) {
                            $jenisDenda = match ($kat->jenis) {
                                'kehilangan' => JenisDenda::Kehilangan,
                                'keterlambatan' => JenisDenda::Keterlambatan,
                                default => JenisDenda::Kerusakan,
                            };
                        }
                    } elseif ($hilang > 0) {
                        $katHilang = KategoriDenda::where('kode', 'HILANG')->orWhere('jenis', 'kehilangan')->first();
                        $kategoriId = $katHilang?->id;
                        $jenisDenda = JenisDenda::Kehilangan;
                    } else {
                        $katRusak = KategoriDenda::where('kode', 'RUSAK_BERAT')->orWhere('jenis', 'kerusakan')->first();
                        $kategoriId = $katRusak?->id;
                    }

                    if ($hilang > 0) {
                        $keteranganDenda = "Ganti rugi kehilangan {$hilang} unit {$det->alat->nama}";
                        if ((float) ($alat->harga_beli ?? 0) > 0) {
                            $keteranganDenda .= ' (Sesuai harga beli @ Rp'.number_format($alat->harga_beli, 0, ',', '.').')';
                        }
                        if (! empty($x['catatan'])) {
                            $keteranganDenda .= " - {$x['catatan']}";
                        }
                    } else {
                        $keteranganDenda = ! empty($x['catatan'])
                            ? "Biaya kerusakan alat: {$det->alat->nama} ({$x['catatan']})"
                            : "Biaya kerusakan alat: {$det->alat->nama}";
                    }

                    Denda::create([
                        'pesanan_id' => $p->id,
                        'kategori_denda_id' => $kategoriId,
                        'pengembalian_id' => $kembali->id,
                        'pengembalian_detail_id' => $kd->id,
                        'jenis' => $jenisDenda,
                        'keterangan' => $keteranganDenda,
                        'jumlah' => $biaya,
                        'status_bayar' => 'belum_lunas',
                    ]);
                }
            }

            // Hitung denda keterlambatan
            $telat = $this->dendaService->hitungKeterlambatan($p, $waktuKembali);
            if ($telat['jumlah'] > 0) {
                $kembali->update(['hari_terlambat' => $telat['hari']]);
                $katTelat = KategoriDenda::where('kode', 'TELAT')->orWhere('jenis', 'keterlambatan')->first();
                Denda::create([
                    'pesanan_id' => $p->id,
                    'kategori_denda_id' => $katTelat?->id,
                    'pengembalian_id' => $kembali->id,
                    'pengembalian_detail_id' => null,
                    'jenis' => JenisDenda::Keterlambatan,
                    'keterangan' => "Terlambat {$telat['hari']} hari (Tarif harian Rp".number_format($telat['tarif_harian'], 0, ',', '.').')',
                    'jumlah' => $telat['jumlah'],
                    'status_bayar' => 'belum_lunas',
                ]);
            }

            $p->update([
                'status' => StatusPesanan::Dikembalikan,
                'diproses_oleh' => $admin->id,
            ]);

            LogAktivitas::catat(
                $admin,
                'pengembalian.proses',
                "Memproses pengembalian pesanan [{$p->no_resi}]. Status menjadi Selesai/Dikembalikan."
            );

            return $kembali->load(['detail.pesananDetail.alat', 'denda']);
        });
    }

    /**
     * Mengembalikan jaminan fisik kartu identitas kepada penyewa.
     */
    public function kembalikanJaminan(Pesanan $pesanan, User $admin): Jaminan
    {
        return DB::transaction(function () use ($pesanan, $admin) {
            $pesanan = Pesanan::lockForUpdate()->findOrFail($pesanan->id);
            $jaminan = $pesanan->jaminan;

            if (! $jaminan) {
                throw ValidationException::withMessages([
                    'jaminan' => 'Pesanan ini tidak memiliki catatan jaminan kartu identitas.',
                ]);
            }

            if ($jaminan->status === 'dikembalikan') {
                throw ValidationException::withMessages([
                    'jaminan' => 'Jaminan kartu identitas sudah dikembalikan sebelumnya.',
                ]);
            }

            // Aturan Penahanan Jaminan: Jaminan fisik DITAHAN bila pelanggan memiliki denda yang belum dilunasi
            if ($pesanan->denda()->where('status_bayar', 'belum_lunas')->exists()) {
                throw ValidationException::withMessages([
                    'jaminan' => 'Jaminan kartu identitas DITAHAN dan belum dapat dikembalikan karena terdapat denda yang belum dilunasi. Pelanggan wajib mempertanggungjawabkan/melunasi denda terlebih dahulu.',
                ]);
            }

            $jaminan->update([
                'status' => 'dikembalikan',
                'dikembalikan_pada' => now(),
                'dikembalikan_oleh' => $admin->id,
            ]);

            LogAktivitas::catat(
                $admin,
                'jaminan.kembali',
                "Mengembalikan jaminan identitas ({$jaminan->jenis->label()} {$jaminan->nomor_identitas_samar}) untuk pesanan [{$pesanan->no_resi}]."
            );

            return $jaminan;
        });
    }

    /**
     * Menandai denda keterlambatan / kerusakan telah dilunasi di kasir toko.
     */
    public function lunaskanDenda(Denda $denda, User $admin): Denda
    {
        return DB::transaction(function () use ($denda, $admin) {
            $d = Denda::lockForUpdate()->findOrFail($denda->id);

            if ($d->status_bayar === 'lunas') {
                throw ValidationException::withMessages([
                    'denda' => 'Denda ini sudah berstatus lunas.',
                ]);
            }

            $d->update([
                'status_bayar' => 'lunas',
                'dilunasi_pada' => now(),
                'dilunasi_oleh' => $admin->id,
            ]);

            LogAktivitas::catat(
                $admin,
                'denda.lunas',
                "Menandai lunas denda #{$d->id} ({$d->jenis->label()}) sebesar Rp".number_format($d->jumlah, 0, ',', '.')." untuk pesanan [{$d->pesanan->no_resi}]."
            );

            return $d;
        });
    }

    /**
     * Menambahkan denda manual ke pesanan oleh admin (misal saat cek kelengkapan barang hilang/rusak).
     */
    public function tambahDendaManual(Pesanan $pesanan, User $admin, array $data): Denda
    {
        return DB::transaction(function () use ($pesanan, $admin, $data) {
            $p = Pesanan::lockForUpdate()->findOrFail($pesanan->id);

            $kategori = null;
            if (! empty($data['kategori_denda_id'])) {
                $kategori = KategoriDenda::find($data['kategori_denda_id']);
            }

            $jenis = JenisDenda::Kerusakan;
            if ($kategori) {
                $jenis = match ($kategori->jenis) {
                    'keterlambatan' => JenisDenda::Keterlambatan,
                    'kehilangan' => JenisDenda::Kehilangan,
                    'kerusakan' => JenisDenda::Kerusakan,
                    default => JenisDenda::Lainnya,
                };
            }

            $keterangan = trim($data['keterangan'] ?? '');
            if ($keterangan === '') {
                $keterangan = $kategori ? $kategori->nama : 'Denda Tambahan';
            }

            $denda = Denda::create([
                'pesanan_id' => $p->id,
                'kategori_denda_id' => $kategori?->id,
                'pengembalian_id' => $p->pengembalian?->id,
                'pengembalian_detail_id' => $data['pengembalian_detail_id'] ?? null,
                'jenis' => $jenis,
                'keterangan' => $keterangan,
                'jumlah' => (float) $data['jumlah'],
                'status_bayar' => 'belum_lunas',
            ]);

            LogAktivitas::catat(
                $admin,
                'denda.tambah',
                "Menambahkan denda manual #{$denda->id} ({$denda->keterangan}) sebesar Rp".number_format($denda->jumlah, 0, ',', '.')." untuk pesanan [{$p->no_resi}]."
            );

            return $denda;
        });
    }
}
