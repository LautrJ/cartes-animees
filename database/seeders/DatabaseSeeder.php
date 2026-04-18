<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'role'       => UserRole::Admin,
            'first_name' => 'Admin',
            'last_name'  => 'Cartes Animées',
            'email'      => 'admin@cartes-animees.test',
            'password'   => Hash::make('admin123'),
            'is_active'  => true,
        ]);
    }
}
