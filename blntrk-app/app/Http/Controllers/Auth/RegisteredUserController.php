<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterPelangganRequest;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan form pendaftaran akun pelanggan.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun pelanggan baru.
     */
    public function store(RegisterPelangganRequest $request): RedirectResponse
    {
        $rolePelanggan = Role::where('nama', 'pelanggan')->firstOrFail();

        $user = DB::transaction(function () use ($request, $rolePelanggan) {
            $user = User::create([
                'role_id' => $rolePelanggan->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_aktif' => true,
            ]);

            Pelanggan::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->nama,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
            ]);

            return $user;
        });

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Akun Anda telah aktif, silakan masuk untuk melakukan penyewaan.');
    }
}
