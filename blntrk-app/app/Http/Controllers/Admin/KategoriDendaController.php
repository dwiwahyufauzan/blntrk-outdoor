<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriDenda;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriDendaController extends Controller
{
    /**
     * Menampilkan daftar master kategori denda.
     */
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $jenis = $request->query('jenis');

        $kategoriList = KategoriDenda::withCount('denda')
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', "%{$q}%")
                        ->orWhere('kode', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%");
                });
            })
            ->when($jenis, fn ($query, $j) => $query->where('jenis', $j))
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.kategori-denda.index', compact('kategoriList', 'q', 'jenis'));
    }

    /**
     * Menampilkan form penambahan kategori denda baru.
     */
    public function create(): View
    {
        return view('admin.kategori-denda.create');
    }

    /**
     * Menyimpan data kategori denda baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:30', 'uppercase', 'alpha_dash', 'unique:kategori_denda,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'jenis' => ['required', 'string', 'in:keterlambatan,kehilangan,kerusakan,lainnya'],
            'tarif_standar' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        $kategori = KategoriDenda::create($validated);

        LogAktivitas::catat(
            auth()->user(),
            'kategori_denda.tambah',
            "Menambahkan kategori denda baru [{$kategori->kode}] {$kategori->nama}."
        );

        return redirect()->route('admin.kategori-denda.index')
            ->with('status', "Kategori denda [{$kategori->kode}] {$kategori->nama} berhasil ditambahkan.");
    }

    /**
     * Menampilkan form edit kategori denda.
     */
    public function edit(KategoriDenda $kategoriDenda): View
    {
        return view('admin.kategori-denda.edit', compact('kategoriDenda'));
    }

    /**
     * Memperbarui data kategori denda.
     */
    public function update(Request $request, KategoriDenda $kategoriDenda): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:30', 'uppercase', 'alpha_dash', 'unique:kategori_denda,kode,'.$kategoriDenda->id],
            'nama' => ['required', 'string', 'max:100'],
            'jenis' => ['required', 'string', 'in:keterlambatan,kehilangan,kerusakan,lainnya'],
            'tarif_standar' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        $kategoriDenda->update($validated);

        LogAktivitas::catat(
            auth()->user(),
            'kategori_denda.ubah',
            "Memperbarui data kategori denda [{$kategoriDenda->kode}] {$kategoriDenda->nama}."
        );

        return redirect()->route('admin.kategori-denda.index')
            ->with('status', "Kategori denda [{$kategoriDenda->kode}] berhasil diperbarui.");
    }

    /**
     * Mengubah status aktif / non-aktif kategori denda.
     */
    public function toggle(KategoriDenda $kategoriDenda): RedirectResponse
    {
        $kategoriDenda->update([
            'is_aktif' => ! $kategoriDenda->is_aktif,
        ]);

        $statusText = $kategoriDenda->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        LogAktivitas::catat(
            auth()->user(),
            'kategori_denda.toggle',
            "Mengubah status kategori denda [{$kategoriDenda->kode}] menjadi {$statusText}."
        );

        return back()->with('status', "Kategori denda [{$kategoriDenda->kode}] berhasil {$statusText}.");
    }

    /**
     * Menghapus kategori denda jika belum pernah digunakan dalam transaksi denda.
     */
    public function destroy(KategoriDenda $kategoriDenda): RedirectResponse
    {
        if ($kategoriDenda->denda()->exists()) {
            return back()->withErrors([
                'kategori' => "Kategori denda [{$kategoriDenda->kode}] tidak dapat dihapus karena sudah memiliki riwayat denda pada pesanan. Anda dapat menonaktifkannya saja.",
            ]);
        }

        $nama = $kategoriDenda->nama;
        $kategoriDenda->delete();

        LogAktivitas::catat(
            auth()->user(),
            'kategori_denda.hapus',
            "Menghapus kategori denda [{$nama}]."
        );

        return redirect()->route('admin.kategori-denda.index')
            ->with('status', "Kategori denda [{$nama}] berhasil dihapus.");
    }
}
