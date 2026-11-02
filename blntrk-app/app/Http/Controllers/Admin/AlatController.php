<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatRequest;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\RiwayatPerawatan;
use App\Services\KetersediaanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AlatController extends Controller
{
    public function __construct(protected KetersediaanService $ketersediaan) {}

    public function index(Request $request): View
    {
        $query = Alat::with('kategori');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', like_pattern($search))
                    ->orWhere('kode_alat', 'like', like_pattern($search));
            });
        }

        if ($kategoriId = $request->input('kategori_id')) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_aktif', (bool) $request->status);
        }

        $alatList = $query->latest('id')->paginate(10)->withQueryString();
        $kategoriList = KategoriAlat::orderBy('nama')->get();

        return view('admin.alat.index', compact('alatList', 'kategoriList'));
    }

    public function create(): View
    {
        $kategoriList = KategoriAlat::orderBy('nama')->get();

        return view('admin.alat.create', compact('kategoriList'));
    }

    public function store(AlatRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alat', foto_disk());
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')
            ->with('sukses', 'Alat outdoor berhasil ditambahkan ke inventaris.');
    }

    public function show(Alat $alat): View
    {
        $alat->load(['kategori', 'pesananDetail.pesanan.pelanggan']);

        return view('admin.alat.show', compact('alat'));
    }

    public function edit(Alat $alat): View
    {
        $kategoriList = KategoriAlat::orderBy('nama')->get();

        return view('admin.alat.edit', compact('alat', 'kategoriList'));
    }

    public function update(AlatRequest $request, Alat $alat): RedirectResponse
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        // Kapasitas efektif baru tidak boleh lebih kecil dari pemakaian tertinggi pesanan aktif (menunggu s/d terlambat)
        $kapasitasBaru = max(0, (int) $data['stok_total'] - (int) $data['stok_perawatan']);
        $puncak = $this->ketersediaan->puncakTerpakai($alat->id);
        if ($kapasitasBaru < $puncak) {
            return back()->withInput()->withErrors([
                'stok_total' => "Stok tersedia tidak boleh di bawah {$puncak} unit karena masih dibutuhkan pesanan aktif. "
                    .'Selesaikan atau batalkan pesanan terkait terlebih dahulu.',
            ]);
        }

        $perawatanLama = (int) $alat->stok_perawatan;

        if ($request->hasFile('foto')) {
            if ($alat->foto && Storage::disk(foto_disk())->exists($alat->foto)) {
                Storage::disk(foto_disk())->delete($alat->foto);
            }
            $data['foto'] = $request->file('foto')->store('alat', foto_disk());
        }

        $alat->update($data);

        // Perubahan stok perawatan lewat form edit tetap meninggalkan jejak di riwayat perawatan
        $selisih = (int) $alat->stok_perawatan - $perawatanLama;
        if ($selisih !== 0) {
            RiwayatPerawatan::catat(
                $alat,
                $selisih > 0 ? RiwayatPerawatan::MASUK : RiwayatPerawatan::SELESAI,
                abs($selisih),
                'Penyesuaian stok perawatan lewat form edit alat',
                auth()->user()
            );
        }

        return redirect()->route('admin.alat.index')
            ->with('sukses', 'Data alat outdoor berhasil diperbarui.');
    }

    public function toggle(Alat $alat): RedirectResponse
    {
        $alat->update(['is_aktif' => ! $alat->is_aktif]);

        $statusText = $alat->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        $redirect = back()->with('sukses', "Status alat {$alat->nama} berhasil {$statusText}.");

        // Menonaktifkan alat hanya menyembunyikannya dari katalog; pesanan aktif tetap berjalan, jadi admin diberi tahu.
        if (! $alat->is_aktif && $this->ketersediaan->puncakTerpakai($alat->id) > 0) {
            $redirect->with('peringatan', "Alat {$alat->nama} masih dipakai pesanan aktif. Pesanan tersebut tetap berjalan, tetapi alat tidak lagi muncul di katalog.");
        }

        return $redirect;
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        if ($this->ketersediaan->puncakTerpakai($alat->id) > 0) {
            return redirect()->route('admin.alat.index')
                ->with('error', "Alat \"{$alat->nama}\" tidak dapat dihapus karena masih dipakai pesanan aktif. Nonaktifkan alat atau selesaikan pesanan terkait terlebih dahulu.");
        }

        $namaAlat = $alat->nama;
        $alat->delete();

        return redirect()->route('admin.alat.index')
            ->with('sukses', "Alat \"{$namaAlat}\" berhasil dihapus (riwayat transaksi tetap terjaga).");
    }
}
