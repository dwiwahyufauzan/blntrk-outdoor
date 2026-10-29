<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Di produksi wajib menetapkan SEED_DEFAULT_PASSWORD; kata sandi bawaan 'password' hanya untuk lokal/testing.
        $sandi = env('SEED_DEFAULT_PASSWORD');
        if (app()->environment('production') && blank($sandi)) {
            throw new \RuntimeException('Set SEED_DEFAULT_PASSWORD pada .env sebelum menjalankan UserSeeder di production.');
        }
        $sandi = $sandi ?: 'password';
        $rolePemilik = Role::where('nama', 'pemilik')->firstOrFail();
        $roleAdmin = Role::where('nama', 'admin')->firstOrFail();
        $rolePelanggan = Role::where('nama', 'pelanggan')->firstOrFail();

        // 1. Akun Pemilik
        User::firstOrCreate(
            ['email' => 'pemilik@blntrk.test'],
            [
                'role_id' => $rolePemilik->id,
                'nama' => 'Haji Dadan (Pemilik)',
                'password' => Hash::make($sandi),
                'is_aktif' => true,
            ]
        );

        // 2. Akun Admin / Kasir Toko
        User::firstOrCreate(
            ['email' => 'admin@blntrk.test'],
            [
                'role_id' => $roleAdmin->id,
                'nama' => 'Dwi Wahyu (Admin Toko)',
                'password' => Hash::make($sandi),
                'is_aktif' => true,
            ]
        );

        // 3. Akun Pelanggan Terdaftar 1
        $userPelanggan1 = User::firstOrCreate(
            ['email' => 'budi@blntrk.test'],
            [
                'role_id' => $rolePelanggan->id,
                'nama' => 'Budi Setiawan',
                'password' => Hash::make($sandi),
                'is_aktif' => true,
            ]
        );

        Pelanggan::firstOrCreate(
            ['user_id' => $userPelanggan1->id],
            [
                'nama_lengkap' => 'Budi Setiawan',
                'no_hp' => '081298765432',
                'alamat' => 'Jl. Raya Ciater No. 45, Subang',
            ]
        );

        // 4. Akun Pelanggan Terdaftar 2
        $userPelanggan2 = User::firstOrCreate(
            ['email' => 'siti@blntrk.test'],
            [
                'role_id' => $rolePelanggan->id,
                'nama' => 'Siti Rahmawati',
                'password' => Hash::make($sandi),
                'is_aktif' => true,
            ]
        );

        Pelanggan::firstOrCreate(
            ['user_id' => $userPelanggan2->id],
            [
                'nama_lengkap' => 'Siti Rahmawati',
                'no_hp' => '087811223344',
                'alamat' => 'Desa Tambakmekar, Jalancagak, Subang',
            ]
        );

        // 5. Data Pelanggan Walk-in (Tanpa Akun User Login)
        Pelanggan::firstOrCreate(
            ['no_hp' => '085299887766'],
            [
                'user_id' => null,
                'nama_lengkap' => 'Agus Santoso (Walk-in)',
                'alamat' => 'Desa Kumpay RT 02/01, Jalancagak, Subang',
            ]
        );
    }
}
