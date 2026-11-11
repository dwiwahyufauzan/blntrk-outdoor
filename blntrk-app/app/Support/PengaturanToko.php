<?php

namespace App\Support;

use App\Models\Pengaturan;

/**
 * Akses bertipe ke tabel `pengaturan`. Seluruh nilai bawaan berada di sini (identitas toko dari
 * config/toko.php) sehingga tidak tersebar di controller, service, dan view.
 */
final class PengaturanToko
{
    /**
     * Nilai bawaan setiap kunci beserta keterangannya, dipakai juga oleh seeder.
     *
     * @return array<string, array{nilai: string, keterangan: string}>
     */
    public static function bawaan(): array
    {
        return [
            'nama_toko' => ['nilai' => (string) config('toko.nama'), 'keterangan' => 'Nama toko outdoor yang dicetak pada resi dan laporan'],
            'alamat_toko' => ['nilai' => (string) config('toko.alamat'), 'keterangan' => 'Alamat operasional toko'],
            'no_wa_toko' => ['nilai' => (string) config('toko.no_wa'), 'keterangan' => 'Nomor WhatsApp konfirmasi pesanan toko (format internasional 62...)'],
            'jam_kembali' => ['nilai' => '12:00', 'keterangan' => 'Jam jatuh tempo pengembalian alat sewa'],
            'toleransi_menit' => ['nilai' => '60', 'keterangan' => 'Toleransi keterlambatan pengembalian sebelum denda dihitung (menit)'],
            'persen_denda_harian' => ['nilai' => '100', 'keterangan' => 'Persentase tarif harian alat per hari keterlambatan (%)'],
            'maks_durasi_hari' => ['nilai' => '14', 'keterangan' => 'Maksimum durasi peminjaman sewa alat (hari)'],
            'ambang_stok_kritis' => ['nilai' => '2', 'keterangan' => 'Batas minimum unit alat untuk peringatan stok kritis'],
            'noshow_batal_otomatis' => ['nilai' => '1', 'keterangan' => 'Batalkan otomatis pesanan sudah dibayar yang tidak diambil (1 = aktif, 0 = nonaktif)'],
            'noshow_toleransi_hari' => ['nilai' => '1', 'keterangan' => 'Toleransi hari setelah batas pengambilan sebelum pesanan dibayar dianggap tidak diambil (no-show)'],
            'batas_bayar_jam' => ['nilai' => '24', 'keterangan' => 'Batas waktu pembayaran pesanan menunggu, dihitung sejak pesanan dibuat (jam). 0 = hanya dibatasi batas pengambilan'],
            'maks_pesanan_menunggu' => ['nilai' => '3', 'keterangan' => 'Jumlah maksimum pesanan berstatus menunggu per pelanggan. 0 = tanpa batas'],
        ];
    }

    public static function namaToko(): string
    {
        return (string) self::ambil('nama_toko');
    }

    public static function alamatToko(): string
    {
        return (string) self::ambil('alamat_toko');
    }

    public static function noWa(): string
    {
        return (string) self::ambil('no_wa_toko');
    }

    /**
     * Nomor WhatsApp toko hanya berisi angka, siap dipakai pada tautan wa.me.
     */
    public static function noWaDigit(): string
    {
        return preg_replace('/\D/', '', self::noWa());
    }

    public static function jamKembali(): string
    {
        return (string) self::ambil('jam_kembali');
    }

    public static function toleransiMenit(): int
    {
        return (int) self::ambil('toleransi_menit');
    }

    public static function persenDendaHarian(): float
    {
        return (float) self::ambil('persen_denda_harian');
    }

    public static function maksDurasiHari(): int
    {
        return max(1, (int) self::ambil('maks_durasi_hari'));
    }

    public static function ambangStokKritis(): int
    {
        return (int) self::ambil('ambang_stok_kritis');
    }

    public static function noshowBatalOtomatis(): bool
    {
        return (bool) (int) self::ambil('noshow_batal_otomatis');
    }

    public static function noshowToleransiHari(): int
    {
        return max(0, (int) self::ambil('noshow_toleransi_hari'));
    }

    public static function batasBayarJam(): int
    {
        return max(0, (int) self::ambil('batas_bayar_jam'));
    }

    public static function maksPesananMenunggu(): int
    {
        return max(0, (int) self::ambil('maks_pesanan_menunggu'));
    }

    /**
     * Seluruh nilai pengaturan bertipe string untuk pengisian formulir.
     *
     * @return array<string, string>
     */
    public static function semua(): array
    {
        $hasil = [];
        foreach (array_keys(self::bawaan()) as $kunci) {
            $hasil[$kunci] = (string) self::ambil($kunci);
        }

        return $hasil;
    }

    private static function ambil(string $kunci): mixed
    {
        return Pengaturan::ambil($kunci, self::bawaan()[$kunci]['nilai']);
    }
}
