<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('PCH_ADMIN_EMAIL', 'admin@pch.com')],
            [
                'name' => env('PCH_ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make(env('PCH_ADMIN_PASSWORD', 'password')),
                'is_admin' => true,
                'is_super_admin' => true,
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@pch.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'role' => User::ROLE_MANAGER,
            ]
        );

        User::firstOrCreate(
            ['email' => 'support@pch.com'],
            [
                'name' => 'Support User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'role' => User::ROLE_SUPPORT,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@pch.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'role' => User::ROLE_USER,
            ]
        );
    }
}
