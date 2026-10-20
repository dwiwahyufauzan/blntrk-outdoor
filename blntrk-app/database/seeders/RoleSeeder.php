<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama' => 'pemilik', 'label' => 'Pemilik Toko'],
            ['nama' => 'admin', 'label' => 'Admin / Staf'],
            ['nama' => 'pelanggan', 'label' => 'Pelanggan'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama' => $role['nama']], $role);
        }
    }
}
