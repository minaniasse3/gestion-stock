<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Appel de nos seeders dans l'ordre approprié
        $this->call([
            UserSeeder::class,     // Création des utilisateurs en premier
            SupplierSeeder::class, // Création des fournisseurs ensuite
            ProductSeeder::class,  // Création des produits en dernier
            StockMovementSeeder::class,
            // ActivityLogSeeder::class, // Commenté car non importé pour l'instant
            // ReportSeeder::class, // Commenté car non importé pour l'instant
        ]);
    }
}
