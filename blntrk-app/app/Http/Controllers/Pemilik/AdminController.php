<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pengembalian;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar staf pengelola toko (Admin).
     */
    public function index(Request $request): View
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();

        $query = User::where('role_id', $roleAdmin->id);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->latest('id')->paginate(10)->withQueryString();

        return view('pemilik.admin.index', compact('admins'));
    }

    /**
     * Form penambahan akun Admin baru.
     */
    public function create(): View
    {
        return view('pemilik.admin.create');
    }

    /**
     * Menyimpan data akun Admin baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_aktif' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama staf admin wajib diisi.',
            'email.required' => 'Email staf admin wajib diisi.',
            'email.unique' => 'Alamat email ini telah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $admin = User::create([
            'role_id' => $roleAdmin->id,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_aktif' => $request->boolean('is_aktif', true),
        ]);

        LogService::catat(
            auth()->user(),
            'user.buat_admin',
            'pengguna',
            $admin->id,
            "Menambahkan akun Admin baru: {$admin->nama} ({$admin->email})."
        );

        return redirect()->route('pemilik.admin.index')
            ->with('status', "Akun Admin {$admin->nama} berhasil dibuat.");
    }

    /**
     * Form edit data akun Admin.
     */
    public function edit(User $admin): View
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();
        if ($admin->role_id !== $roleAdmin->id) {
            abort(404);
        }

        return view('pemilik.admin.edit', compact('admin'));
    }

    /**
     * Memperbarui informasi akun Admin.
     */
    public function update(Request $request, User $admin): RedirectResponse
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();
        if ($admin->role_id !== $roleAdmin->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'is_aktif' => $request->boolean('is_aktif', true),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);

        LogService::catat(
            auth()->user(),
            'user.ubah_admin',
            'pengguna',
            $admin->id,
            "Memperbarui profil akun Admin: {$admin->nama} ({$admin->email})."
        );

        return redirect()->route('pemilik.admin.index')
            ->with('status', "Data akun Admin {$admin->nama} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan atau menonaktifkan akses akun Admin.
     */
    public function toggle(User $admin): RedirectResponse
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();
        if ($admin->role_id !== $roleAdmin->id) {
            abort(404);
        }

        $admin->is_aktif = ! $admin->is_aktif;
        $admin->save();

        $statusTeks = $admin->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        LogService::catat(
            auth()->user(),
            'user.toggle_admin',
            'pengguna',
            $admin->id,
            "Status akun Admin {$admin->nama} diubah menjadi {$statusTeks}."
        );

        return back()->with('status', "Status akun Admin {$admin->nama} berhasil {$statusTeks}.");
    }

    /**
     * Mereset password akun Admin ke password standar aman.
     */
    public function resetPassword(User $admin): RedirectResponse
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();
        if ($admin->role_id !== $roleAdmin->id) {
            abort(404);
        }

        $defaultPassword = 'password123';
        $admin->password = Hash::make($defaultPassword);
        $admin->save();

        LogService::catat(
            auth()->user(),
            'user.reset_password',
            'pengguna',
            $admin->id,
            "Mereset password akun Admin {$admin->nama} ke password default."
        );

        return back()->with('status', "Password akun Admin {$admin->nama} berhasil direset menjadi: {$defaultPassword}");
    }

    /**
     * Menghapus akun Admin (dengan proteksi audit jejak transaksi).
     */
    public function destroy(User $admin): RedirectResponse
    {
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();
        if ($admin->role_id !== $roleAdmin->id) {
            abort(404);
        }

        // Cek jejak riwayat transaksi
        $hasHistory = Pesanan::where('diproses_oleh', $admin->id)->exists()
            || Pembayaran::where('dicatat_oleh', $admin->id)->exists()
            || Pengembalian::where('diterima_oleh', $admin->id)->exists();

        if ($hasHistory) {
            return back()->with('error', "Akun Admin {$admin->nama} memiliki rekam jejak operasional transaksi sewa. Untuk menjaga integritas audit, nonaktifkan akun ini alih-alih menghapusnya.");
        }

        $nama = $admin->nama;
        $admin->delete();

        LogService::catat(
            auth()->user(),
            'user.hapus_admin',
            'pengguna',
            $admin->id,
            "Menghapus akun Admin: {$nama}."
        );

        return redirect()->route('pemilik.admin.index')
            ->with('status', "Akun Admin {$nama} berhasil dihapus.");
    }
}
