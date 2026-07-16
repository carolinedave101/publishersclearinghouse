<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pch.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_super_admin' => true,
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@pch.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'role' => User::ROLE_MANAGER,
        ]);

        User::create([
            'name' => 'Support User',
            'email' => 'support@pch.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'role' => User::ROLE_SUPPORT,
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@pch.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'role' => User::ROLE_USER,
        ]);
    }
}
