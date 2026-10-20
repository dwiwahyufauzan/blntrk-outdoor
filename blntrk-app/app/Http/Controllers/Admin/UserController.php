<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['role', 'pelanggan']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleNama = $request->input('role')) {
            $query->whereHas('role', function ($q) use ($roleNama) {
                $q->where('nama', $roleNama);
            });
        }

        $userList = $query->latest('id')->paginate(10)->withQueryString();
        $roles = Role::whereIn('nama', ['admin', 'pelanggan'])->get();

        return view('admin.user.index', compact('userList', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::whereIn('nama', ['admin', 'pelanggan'])->get();

        return view('admin.user.create', compact('roles'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        // Pastikan role yang dipilih hanya admin atau pelanggan
        $role = Role::where('nama', $request->role)->firstOrFail();

        DB::transaction(function () use ($request, $role) {
            $user = User::create([
                'role_id' => $role->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_aktif' => $request->boolean('is_aktif', true),
            ]);

            // Jika role pelanggan, buat record pelanggan
            if ($role->nama === 'pelanggan') {
                Pelanggan::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama,
                    'no_hp' => $request->no_hp ?? '-',
                    'alamat' => $request->alamat ?? '-',
                ]);
            }
        });

        return redirect()->route('admin.user.index')
            ->with('sukses', "Pengguna {$request->nama} (Peran: {$role->label}) berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        $roles = Role::whereIn('nama', ['admin', 'pelanggan'])->get();

        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        // Admin tidak boleh mengubah akun pemilik
        if ($user->punyaRole('pemilik')) {
            return back()->with('error', 'Akun Pemilik Toko tidak dapat dimodifikasi dari panel admin.');
        }

        $role = Role::where('nama', $request->role)->firstOrFail();

        DB::transaction(function () use ($request, $user, $role) {
            $data = [
                'role_id' => $role->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'is_aktif' => $request->boolean('is_aktif', true),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            if ($role->nama === 'pelanggan') {
                $user->pelanggan()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama_lengkap' => $request->nama,
                        'no_hp' => $request->no_hp ?? ($user->pelanggan?->no_hp ?? '-'),
                        'alamat' => $request->alamat ?? ($user->pelanggan?->alamat ?? '-'),
                    ]
                );
            }
        });

        return redirect()->route('admin.user.index')
            ->with('sukses', "Data akun {$user->nama} berhasil diperbarui.");
    }

    public function toggle(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($user->punyaRole('pemilik')) {
            return back()->with('error', 'Status akun Pemilik Toko tidak dapat diubah.');
        }

        $user->update(['is_aktif' => ! $user->is_aktif]);
        $statusText = $user->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('sukses', "Akun {$user->nama} berhasil {$statusText}.");
    }

    public function resetPassword(User $user): RedirectResponse
    {
        if ($user->punyaRole('pemilik')) {
            return back()->with('error', 'Password Pemilik Toko tidak dapat direset dari panel admin.');
        }

        $passwordSementara = 'Bln' . Str::random(6) . '!';
        $user->update(['password' => Hash::make($passwordSementara)]);

        return back()->with('password_baru', [
            'nama' => $user->nama,
            'email' => $user->email,
            'password' => $passwordSementara,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->punyaRole('pemilik')) {
            return back()->with('error', 'Akun Pemilik Toko tidak dapat dihapus.');
        }

        $nama = $user->nama;
        $user->delete();

        return redirect()->route('admin.user.index')
            ->with('sukses', "Akun {$nama} berhasil dihapus.");
    }
}
