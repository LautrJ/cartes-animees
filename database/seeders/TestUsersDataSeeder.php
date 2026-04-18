<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 admin
        User::create([
            'role'       => UserRole::Admin,
            'first_name' => 'Admin',
            'last_name'  => 'Cartes Animées',
            'email'      => 'admin@cartes-animees.test',
            'password'   => Hash::make('admin123'),
            'is_active'  => true,
        ]);

        // 2 orthophonistes
        $therapists = User::factory(2)->create([
            'role'     => UserRole::Therapist,
            'password' => Hash::make('password'),
        ]);

        // 3 parents avec 2 enfants chacun
        User::factory(3)->create([
            'role'     => UserRole::Parent,
            'password' => Hash::make('password'),
        ])->each(function ($parent) use ($therapists) {
            Child::factory(2)->create(['parent_id' => $parent->id])
                ->each(function ($child) use ($therapists) {
                    // Chaque enfant est associé à un orthophoniste aléatoire
                    $child->therapists()->attach(
                        $therapists->random()->id,
                        [
                            'assigned_by' => null,
                            'assigned_at' => now(),
                            'ended_at'    => null,
                        ]
                    );
                });
        });
    }
}
