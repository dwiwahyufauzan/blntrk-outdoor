<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pelanggan::with('user');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($tipe = $request->input('tipe')) {
            if ($tipe === 'walk_in') {
                $query->whereNull('user_id');
            } elseif ($tipe === 'akun') {
                $query->whereNotNull('user_id');
            }
        }

        $pelangganList = $query->latest('id')->paginate(10)->withQueryString();

        return view('admin.pelanggan.index', compact('pelangganList'));
    }

    public function create(): View
    {
        return view('admin.pelanggan.create');
    }

    public function store(PelangganRequest $request): RedirectResponse
    {
        // Pelanggan yang didaftarkan langsung oleh admin di toko fisik berstatus walk-in (user_id = null)
        Pelanggan::create([
            'user_id' => null,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.pelanggan.index')
            ->with('sukses', 'Data pelanggan walk-in berhasil ditambahkan.');
    }

    public function show(Pelanggan $pelanggan): View
    {
        $pelanggan->load(['user', 'pesanan' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan): View
    {
        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(PelangganRequest $request, Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->update([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.pelanggan.index')
            ->with('sukses', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        $adaPesananAktif = $pelanggan->pesanan()
            ->whereIn('status', ['menunggu', 'dibayar', 'disewa', 'terlambat'])
            ->exists();

        if ($adaPesananAktif) {
            return back()->with('error', 'Pelanggan tidak dapat dihapus karena masih memiliki pesanan rental yang sedang aktif atau belum selesai.');
        }

        $pelanggan->delete();

        return redirect()->route('admin.pelanggan.index')
            ->with('sukses', 'Data pelanggan berhasil dihapus.');
    }
}
