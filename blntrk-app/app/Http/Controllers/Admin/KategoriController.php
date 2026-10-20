<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KategoriRequest;
use App\Models\KategoriAlat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $kategoriList = KategoriAlat::withCount('alat')
            ->latest('id')
            ->paginate(10);

        return view('admin.kategori.index', compact('kategoriList'));
    }

    public function create(): View
    {
        return view('admin.kategori.create');
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        KategoriAlat::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('sukses', 'Kategori alat berhasil ditambahkan.');
    }

    public function edit(KategoriAlat $kategori): View
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(KategoriRequest $request, KategoriAlat $kategori): RedirectResponse
    {
        $kategori->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('sukses', 'Kategori alat berhasil diperbarui.');
    }

    public function destroy(KategoriAlat $kategori): RedirectResponse
    {
        if ($kategori->alat()->exists()) {
            return back()->with('error', 'Kategori "' . $kategori->nama . '" tidak dapat dihapus karena masih memiliki ' . $kategori->alat()->count() . ' alat terdaftar.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
            ->with('sukses', 'Kategori alat berhasil dihapus.');
    }
}
