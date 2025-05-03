<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'TechPro Solutions',
            'contact_person' => 'Thomas Dubois',
            'email' => 'contact@techpro-solutions.com',
            'phone' => '+33 1 45 67 89 10',
            'address' => '15 Avenue de l\'Innovation, 75008 Paris',
        ]);

        Supplier::create([
            'name' => 'Global Electronics',
            'contact_person' => 'Marie Lefevre',
            'email' => 'service@global-electronics.fr',
            'phone' => '+33 4 91 23 45 67',
            'address' => '27 Boulevard des Composants, 13006 Marseille',
        ]);

        Supplier::create([
            'name' => 'Informatique & Réseaux',
            'contact_person' => 'Jean-Pierre Moreau',
            'email' => 'jpmoreau@info-reseaux.com',
            'phone' => '+33 5 56 78 90 12',
            'address' => '42 Rue du Serveur, 33000 Bordeaux',
        ]);

        Supplier::create([
            'name' => 'Bureau Concept',
            'contact_person' => 'Sophie Martin',
            'email' => 'contact@bureau-concept.com',
            'phone' => '+33 3 20 45 67 89',
            'address' => '8 Rue des Fournitures, 59000 Lille',
        ]);

        Supplier::create([
            'name' => 'Tech Import',
            'contact_person' => 'Alexandre Petit',
            'email' => 'a.petit@techimport.fr',
            'phone' => '+33 4 72 56 78 90',
            'address' => '123 Avenue du Commerce, 69003 Lyon',
        ]);

        Supplier::create([
            'name' => 'DistriTech',
            'contact_person' => 'Camille Rousseau',
            'email' => 'service.clients@distritech.fr',
            'phone' => '+33 2 40 12 34 56',
            'address' => '5 Boulevard de l\'Industrie, 44000 Nantes',
        ]);
    }
}
