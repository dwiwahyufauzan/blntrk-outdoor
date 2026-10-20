<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AlatRequest;
use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AlatController extends Controller
{
    public function index(Request $request): View
    {
        $query = Alat::with('kategori');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_alat', 'like', "%{$search}%");
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
            $data['foto'] = $request->file('foto')->store('alat', 'public');
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

        if ($request->hasFile('foto')) {
            if ($alat->foto && Storage::disk('public')->exists($alat->foto)) {
                Storage::disk('public')->delete($alat->foto);
            }
            $data['foto'] = $request->file('foto')->store('alat', 'public');
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')
            ->with('sukses', 'Data alat outdoor berhasil diperbarui.');
    }

    public function toggle(Alat $alat): RedirectResponse
    {
        $alat->update(['is_aktif' => ! $alat->is_aktif]);

        $statusText = $alat->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('sukses', "Status alat {$alat->nama} berhasil {$statusText}.");
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        $namaAlat = $alat->nama;
        $alat->delete();

        return redirect()->route('admin.alat.index')
            ->with('sukses', "Alat \"{$namaAlat}\" berhasil dihapus (riwayat transaksi tetap terjaga).");
    }
}
