<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Roles
        $roleOwner = Role::create(['name' => 'OWNER']);
        $roleAdmin = Role::create(['name' => 'ADMIN']);
        $roleKasir = Role::create(['name' => 'KASIR']);

        // Define Default Users
        $ownerUser = User::updateOrCreate(
            ['email' => 'owner@bengkelpro.com'],
            [
                'name' => 'Owner Bengkel',
                'password' => Hash::make('password123'),
            ]
        );
        $ownerUser->assignRole($roleOwner);

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@bengkelpro.com'],
            [
                'name' => 'Admin Bengkel',
                'password' => Hash::make('password123'),
            ]
        );
        $adminUser->assignRole($roleAdmin);

        $kasirUser = User::updateOrCreate(
            ['email' => 'kasir@bengkelpro.com'],
            [
                'name' => 'Kasir Bengkel',
                'password' => Hash::make('password123'),
            ]
        );
        $kasirUser->assignRole($roleKasir);
    }
}
