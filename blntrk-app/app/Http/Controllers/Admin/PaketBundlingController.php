<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\PaketBundling;
use App\Models\PaketBundlingDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaketBundlingController extends Controller
{
    /**
     * Menampilkan daftar paket bundling di panel admin.
     */
    public function index(Request $request): View
    {
        $q = $request->query('q');

        $paketList = PaketBundling::with(['items.alat'])
            ->when($q, function ($query, $q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode_paket', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            })
            ->urut()
            ->paginate(15)
            ->withQueryString();

        return view('admin.paket-bundling.index', compact('paketList', 'q'));
    }

    /**
     * Menampilkan formulir pembuatan paket bundling baru.
     */
    public function create(): View
    {
        $alatList = Alat::with('kategori')->where('is_aktif', true)->orderBy('nama')->get();

        return view('admin.paket-bundling.create', compact('alatList'));
    }

    /**
     * Menyimpan paket bundling baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_paket' => ['required', 'integer', 'min:1', 'max:20'],
            'kode_paket' => ['required', 'string', 'max:20', 'unique:paket_bundling,kode_paket'],
            'nama' => ['required', 'string', 'max:100'],
            'kapasitas_orang' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'harga_paket_per_hari' => ['required', 'numeric', 'min:0'],
            'is_aktif' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.alat_id' => ['required', 'integer', 'exists:alat,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $paket = PaketBundling::create([
            'nomor_paket' => $validated['nomor_paket'],
            'kode_paket' => strtoupper($validated['kode_paket']),
            'nama' => $validated['nama'],
            'slug' => Str::slug($validated['nama']),
            'kapasitas_orang' => $validated['kapasitas_orang'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga_paket_per_hari' => $validated['harga_paket_per_hari'],
            'harga_asli_per_hari' => 0,
            'is_aktif' => $request->boolean('is_aktif', true),
            'urutan' => $validated['nomor_paket'],
        ]);

        $totalAsli = 0;
        foreach ($validated['items'] as $it) {
            $alat = Alat::find($it['alat_id']);
            if ($alat) {
                PaketBundlingDetail::create([
                    'paket_bundling_id' => $paket->id,
                    'alat_id' => $alat->id,
                    'jumlah' => $it['jumlah'],
                ]);
                $totalAsli += ($alat->harga_per_hari * $it['jumlah']);
            }
        }

        $paket->update(['harga_asli_per_hari' => $totalAsli]);

        LogAktivitas::catat($request->user(), 'paket_bundling.create', "Membuat paket bundling baru: {$paket->nama}", 'paket_bundling', $paket->id);

        return redirect()->route('admin.paket-bundling.index')
            ->with('status', "Paket bundling {$paket->nama} berhasil dibuat!");
    }

    /**
     * Menampilkan formulir edit paket bundling.
     */
    public function edit(PaketBundling $paketBundling): View
    {
        $paketBundling->load('items.alat');
        $alatList = Alat::with('kategori')->where('is_aktif', true)->orderBy('nama')->get();

        return view('admin.paket-bundling.edit', [
            'paket' => $paketBundling,
            'alatList' => $alatList,
        ]);
    }

    /**
     * Memperbarui paket bundling di database.
     */
    public function update(Request $request, PaketBundling $paketBundling): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_paket' => ['required', 'integer', 'min:1', 'max:20'],
            'kode_paket' => ['required', 'string', 'max:20', 'unique:paket_bundling,kode_paket,'.$paketBundling->id],
            'nama' => ['required', 'string', 'max:100'],
            'kapasitas_orang' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'harga_paket_per_hari' => ['required', 'numeric', 'min:0'],
            'is_aktif' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.alat_id' => ['required', 'integer', 'exists:alat,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $paketBundling->update([
            'nomor_paket' => $validated['nomor_paket'],
            'kode_paket' => strtoupper($validated['kode_paket']),
            'nama' => $validated['nama'],
            'slug' => Str::slug($validated['nama']),
            'kapasitas_orang' => $validated['kapasitas_orang'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga_paket_per_hari' => $validated['harga_paket_per_hari'],
            'is_aktif' => $request->boolean('is_aktif', true),
            'urutan' => $validated['nomor_paket'],
        ]);

        PaketBundlingDetail::where('paket_bundling_id', $paketBundling->id)->delete();

        $totalAsli = 0;
        foreach ($validated['items'] as $it) {
            $alat = Alat::find($it['alat_id']);
            if ($alat) {
                PaketBundlingDetail::create([
                    'paket_bundling_id' => $paketBundling->id,
                    'alat_id' => $alat->id,
                    'jumlah' => $it['jumlah'],
                ]);
                $totalAsli += ($alat->harga_per_hari * $it['jumlah']);
            }
        }

        $paketBundling->update(['harga_asli_per_hari' => $totalAsli]);

        LogAktivitas::catat($request->user(), 'paket_bundling.update', "Memperbarui paket bundling: {$paketBundling->nama}", 'paket_bundling', $paketBundling->id);

        return redirect()->route('admin.paket-bundling.index')
            ->with('status', "Paket bundling {$paketBundling->nama} berhasil diperbarui!");
    }

    /**
     * Menghapus paket bundling.
     */
    public function destroy(PaketBundling $paketBundling): RedirectResponse
    {
        $nama = $paketBundling->nama;
        $id = $paketBundling->id;
        $paketBundling->delete();

        LogAktivitas::catat(auth()->user(), 'paket_bundling.delete', "Menghapus paket bundling: {$nama}", 'paket_bundling', $id);

        return redirect()->route('admin.paket-bundling.index')
            ->with('status', "Paket bundling {$nama} berhasil dihapus.");
    }

    /**
     * Mengubah status aktif paket bundling.
     */
    public function toggle(PaketBundling $paketBundling): RedirectResponse
    {
        $paketBundling->update([
            'is_aktif' => ! $paketBundling->is_aktif,
        ]);

        $statusStr = $paketBundling->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        LogAktivitas::catat(auth()->user(), 'paket_bundling.toggle', "Mengubah status paket {$paketBundling->nama} menjadi {$statusStr}", 'paket_bundling', $paketBundling->id);

        return back()->with('status', "Paket {$paketBundling->nama} berhasil {$statusStr}.");
    }
}
