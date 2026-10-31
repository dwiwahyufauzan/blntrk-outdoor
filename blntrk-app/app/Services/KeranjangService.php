<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\PaketBundling;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KeranjangService
{
    protected const SESSION_KEY = 'keranjang_sewa';

    protected const JADWAL_KEY = 'keranjang_jadwal';

    public function __construct(
        protected KetersediaanService $ketersediaan,
        protected PaketBundlingService $paketBundling
    ) {}

    /**
     * Mengambil struktur data keranjang mentah dari session.
     * Format: [alat_id => jumlah]
     */
    public function getRaw(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    /**
     * Menyimpan data keranjang mentah ke session.
     */
    public function saveRaw(array $cart): void
    {
        session()->put(self::SESSION_KEY, $cart);
    }

    /**
     * Menambahkan unit alat ke keranjang belanja.
     */
    public function tambah(int $alatId, int $jumlah = 1): bool
    {
        if ($jumlah <= 0) {
            return false;
        }

        $alat = Alat::where('is_aktif', true)->find($alatId);
        if (! $alat) {
            return false;
        }

        $cart = $this->getRaw();
        $current = $cart[$alatId] ?? 0;
        $maxStok = $alat->stok_tersedia;

        if ($maxStok <= 0 || $current >= $maxStok) {
            return false;
        }

        $newQty = min($maxStok, $current + $jumlah);
        if ($newQty <= 0) {
            $newQty = 1;
        }

        $cart[$alatId] = $newQty;
        $this->saveRaw($cart);

        return true;
    }

    /**
     * Menambahkan seluruh item dari suatu Paket Bundling sekaligus ke dalam keranjang.
     * Item yang tidak aktif atau stoknya tidak mencukupi dilewati dan dilaporkan agar pelanggan
     * tahu bahwa paket yang masuk keranjang tidak lengkap.
     *
     * @return array{ditambahkan: int, dilewati: array<int, string>}
     */
    public function tambahPaket(PaketBundling $paket): array
    {
        $paket->load('items.alat');
        $ditambahkan = 0;
        $dilewati = [];

        foreach ($paket->items as $item) {
            $nama = $item->alat?->nama ?? 'Alat tidak tersedia';

            if ($item->alat && $item->alat->is_aktif && $item->alat->stok_tersedia >= $item->jumlah
                && $this->tambah($item->alat_id, $item->jumlah)) {
                $ditambahkan++;
            } else {
                $dilewati[] = $nama;
            }
        }

        return ['ditambahkan' => $ditambahkan, 'dilewati' => $dilewati];
    }

    /**
     * Memperbarui kuantitas alat tertentu di keranjang.
     */
    public function update(int $alatId, int $jumlah): void
    {
        $cart = $this->getRaw();

        if ($jumlah <= 0) {
            unset($cart[$alatId]);
        } else {
            $alat = Alat::where('is_aktif', true)->find($alatId);
            if ($alat) {
                if ($alat->stok_tersedia <= 0) {
                    unset($cart[$alatId]);
                } else {
                    $cart[$alatId] = min($alat->stok_tersedia, $jumlah);
                }
            }
        }

        $this->saveRaw($cart);
    }

    /**
     * Menghapus alat tertentu dari keranjang.
     */
    public function hapus(int $alatId): void
    {
        $cart = $this->getRaw();
        unset($cart[$alatId]);
        $this->saveRaw($cart);
    }

    /**
     * Mengosongkan keranjang belanja.
     */
    public function kosongkan(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Mengambil item keranjang lengkap dengan model Alat, kalkulasi subtotal, dan status stok ketersediaan.
     */
    public function ambil(): Collection
    {
        $cart = $this->getRaw();
        if (empty($cart)) {
            return collect();
        }

        $jadwal = $this->getJadwal();
        $mulai = Carbon::parse($jadwal['mulai'])->startOfDay();
        $selesai = Carbon::parse($jadwal['selesai'])->startOfDay();
        $durasi = max(1, (int) $mulai->diffInDays($selesai));

        $alatList = Alat::with('kategori')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $result = collect();

        foreach ($cart as $alatId => $jumlah) {
            $alat = $alatList->get($alatId);
            if (! $alat) {
                continue;
            }

            $stokTersedia = $this->ketersediaan->tersedia($alat, $mulai, $selesai);
            $subtotalPerHari = (float) $alat->harga_per_hari * $jumlah;
            $subtotalTotal = $subtotalPerHari * $durasi;

            $result->push((object) [
                'alat' => $alat,
                'alat_id' => $alat->id,
                'nama' => $alat->nama,
                'varian' => $alat->varian,
                'foto_url' => $alat->foto_url,
                'kategori' => $alat->kategori?->nama ?? 'Perlengkapan',
                'harga_per_hari' => (float) $alat->harga_per_hari,
                'jumlah' => $jumlah,
                'subtotal_per_hari' => $subtotalPerHari,
                'subtotal' => $subtotalTotal,
                'stok_tersedia' => $stokTersedia,
                'cukup' => ($stokTersedia >= $jumlah),
            ]);
        }

        return $result;
    }

    /**
     * Menghitung total unit barang di keranjang.
     */
    public function totalUnit(): int
    {
        $cart = $this->getRaw();

        return (int) array_sum($cart);
    }

    /**
     * Menghitung total biaya sewa per hari untuk semua item di keranjang.
     */
    public function totalHargaPerHari(): float
    {
        return $this->ambil()->sum('subtotal_per_hari');
    }

    /**
     * Total sewa sebelum diskon paket bundling.
     */
    public function subtotalKotor(): float
    {
        return (float) $this->ambil()->sum('subtotal');
    }

    /**
     * Diskon paket bundling yang otomatis berlaku untuk isi keranjang pada jadwal aktif.
     *
     * @return array{paket: ?PaketBundling, jumlah_paket: int, hemat_per_hari: float, diskon: float}
     */
    public function diskonPaket(): array
    {
        return $this->paketBundling->hitungDiskon($this->getRaw(), $this->getJadwal()['durasi']);
    }

    /**
     * Menghitung grand total sewa (setelah diskon paket) berdasarkan jadwal aktif.
     */
    public function grandTotal(): float
    {
        return max(0.0, $this->subtotalKotor() - $this->diskonPaket()['diskon']);
    }

    /**
     * Menyimpan preferensi jadwal sewa di session.
     */
    public function setJadwal(string $mulai, string $selesai): void
    {
        session()->put(self::JADWAL_KEY, [
            'mulai' => $mulai,
            'selesai' => $selesai,
        ]);
    }

    /**
     * Mengambil preferensi jadwal sewa dari session (default: hari ini s/d besok).
     */
    public function getJadwal(): array
    {
        $default = [
            'mulai' => today()->toDateString(),
            'selesai' => today()->addDay()->toDateString(),
        ];

        $stored = session()->get(self::JADWAL_KEY, $default);

        $mulai = Carbon::parse($stored['mulai'] ?? $default['mulai'])->startOfDay();
        $selesai = Carbon::parse($stored['selesai'] ?? $default['selesai'])->startOfDay();

        if ($selesai->lte($mulai)) {
            $selesai = $mulai->copy()->addDay();
        }

        $durasi = max(1, (int) $mulai->diffInDays($selesai));

        return [
            'mulai' => $mulai->toDateString(),
            'selesai' => $selesai->toDateString(),
            'durasi' => $durasi,
        ];
    }
}
