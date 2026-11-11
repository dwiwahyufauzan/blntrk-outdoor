<?php

namespace App\Policies;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use App\Models\User;

class PesananPolicy
{
    /**
     * Menentukan apakah user dapat melihat daftar pesanan.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Menentukan apakah user dapat melihat detail pesanan (resi).
     */
    public function view(User $user, Pesanan $pesanan): bool
    {
        // Admin dan Pemilik Toko berwenang melihat semua pesanan
        if ($user->punyaRole('admin', 'pemilik')) {
            return true;
        }

        // Pelanggan hanya boleh melihat pesanan miliknya sendiri
        return $pesanan->pelanggan && $pesanan->pelanggan->user_id === $user->id;
    }

    /**
     * Menentukan apakah user dapat membuat pesanan.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Menentukan apakah user dapat membatalkan pesanan.
     */
    public function batal(User $user, Pesanan $pesanan): bool
    {
        // Admin dapat membatalkan pesanan pada status menunggu atau dibayar
        if ($user->punyaRole('admin')) {
            return in_array($pesanan->status, [StatusPesanan::Menunggu, StatusPesanan::Dibayar], true);
        }

        // Pelanggan hanya bisa membatalkan pesanan miliknya sendiri yang masih berstatus menunggu
        return $pesanan->status === StatusPesanan::Menunggu
            && $pesanan->pelanggan
            && $pesanan->pelanggan->user_id === $user->id;
    }

    /**
     * Menentukan apakah user dapat mencatat pembayaran pesanan.
     */
    public function bayar(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin') && $pesanan->status === StatusPesanan::Menunggu;
    }

    /**
     * Menentukan apakah user dapat mengonfirmasi serah terima / pengambilan pesanan.
     */
    public function ambil(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin') && $pesanan->status === StatusPesanan::Dibayar;
    }

    /**
     * Pelanggan pemilik pesanan dapat mengunggah bukti transfer selama pembayaran belum dikonfirmasi.
     */
    public function uploadBukti(User $user, Pesanan $pesanan): bool
    {
        return $pesanan->status === StatusPesanan::Menunggu
            && $pesanan->pelanggan
            && $pesanan->pelanggan->user_id === $user->id;
    }

    /**
     * Ubah jadwal sebelum pengambilan: admin (menunggu/dibayar) atau pemilik pesanan (menunggu).
     */
    public function ubahJadwal(User $user, Pesanan $pesanan): bool
    {
        if ($user->punyaRole('admin')) {
            return in_array($pesanan->status, [StatusPesanan::Menunggu, StatusPesanan::Dibayar], true);
        }

        return $pesanan->status === StatusPesanan::Menunggu
            && $pesanan->pelanggan
            && $pesanan->pelanggan->user_id === $user->id;
    }

    /**
     * Perpanjangan sewa dilakukan admin saat pesanan sedang disewa.
     */
    public function perpanjang(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin') && $pesanan->status === StatusPesanan::Disewa;
    }

    /**
     * Pengembalian jaminan kartu identitas ke penyewa dilakukan admin di kasir.
     */
    public function kembalikanJaminan(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin');
    }

    /**
     * Denda hanya dapat ditambahkan setelah alat keluar dari toko: disewa, terlambat, atau sudah dikembalikan.
     */
    public function tambahDenda(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin')
            && in_array($pesanan->status, [StatusPesanan::Disewa, StatusPesanan::Terlambat, StatusPesanan::Dikembalikan], true);
    }

    /**
     * Refund dicatat admin untuk pesanan dibatalkan yang masih menyisakan dana yang belum dikembalikan.
     */
    public function catatRefund(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin')
            && $pesanan->status === StatusPesanan::Dibatalkan
            && $pesanan->dana_hangus > 0;
    }

    /**
     * Bukti transfer pelanggan dibuka admin lewat rute terautentikasi.
     */
    public function lihatBukti(User $user, Pesanan $pesanan): bool
    {
        return $user->punyaRole('admin', 'pemilik');
    }
}
