<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrateur
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@stockgestion.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'active' => true,
        ]);

        // Gestionnaire
        User::create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@stockgestion.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'active' => true,
        ]);

        // Utilisateur standard
        User::create([
            'name' => 'Marie Lambert',
            'email' => 'marie.lambert@stockgestion.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'active' => true,
        ]);

        User::create([
            'name' => 'Pierre Martin',
            'email' => 'pierre.martin@stockgestion.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'active' => true,
        ]);

        User::create([
            'name' => 'Sophie Blanc',
            'email' => 'sophie.blanc@stockgestion.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'active' => false, // Utilisateur inactif
        ]);
    }
}
