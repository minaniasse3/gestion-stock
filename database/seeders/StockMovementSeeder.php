<?php

namespace Database\Seeders;

use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Entrées de stock récentes
        StockMovement::create([
            'product_id' => 1, // Clavier sans fil Logitech
            'quantity' => 15,
            'type' => 'entrée',
            'reason' => 'Approvisionnement initial',
            'user_id' => 2, // Jean Dupont (manager)
            'reference' => 'ENT-001',
            'created_at' => Carbon::now()->subDays(30)
        ]);

        StockMovement::create([
            'product_id' => 2, // Souris Logitech
            'quantity' => 10,
            'type' => 'entrée',
            'reason' => 'Approvisionnement initial',
            'user_id' => 2,
            'reference' => 'ENT-002',
            'created_at' => Carbon::now()->subDays(30)
        ]);

        StockMovement::create([
            'product_id' => 5, // Disque SSD Samsung
            'quantity' => 25,
            'type' => 'entrée',
            'reason' => 'Commande fournisseur #GE-789',
            'user_id' => 2,
            'reference' => 'ENT-003',
            'created_at' => Carbon::now()->subDays(25)
        ]);

        StockMovement::create([
            'product_id' => 11, // Enceinte JBL
            'quantity' => 15,
            'type' => 'entrée',
            'reason' => 'Commande fournisseur #TI-456',
            'user_id' => 3, // Marie Lambert
            'reference' => 'ENT-004',
            'created_at' => Carbon::now()->subDays(20)
        ]);

        // Entrées récentes
        StockMovement::create([
            'product_id' => 1, // Clavier sans fil Logitech
            'quantity' => 10,
            'type' => 'entrée',
            'reason' => 'Réapprovisionnement',
            'user_id' => 2,
            'reference' => 'ENT-005',
            'created_at' => Carbon::now()->subDays(7)
        ]);

        // Sorties (ventes)
        StockMovement::create([
            'product_id' => 4, // Ordinateur portable ASUS
            'quantity' => -3,
            'type' => 'vente',
            'reason' => 'Vente client #CLI-123',
            'user_id' => 3,
            'reference' => 'SOR-001',
            'created_at' => Carbon::now()->subDays(15)
        ]);

        StockMovement::create([
            'product_id' => 1, // Clavier sans fil Logitech
            'quantity' => -5,
            'type' => 'vente',
            'reason' => 'Vente client #CLI-124',
            'user_id' => 3,
            'reference' => 'SOR-002',
            'created_at' => Carbon::now()->subDays(12)
        ]);

        StockMovement::create([
            'product_id' => 3, // Écran Dell
            'quantity' => -2,
            'type' => 'vente',
            'reason' => 'Vente client #CLI-125',
            'user_id' => 4, // Pierre Martin
            'reference' => 'SOR-003',
            'created_at' => Carbon::now()->subDays(10)
        ]);

        // Sortie récente
        StockMovement::create([
            'product_id' => 10, // Casque Sony
            'quantity' => -3,
            'type' => 'vente',
            'reason' => 'Vente client #CLI-126',
            'user_id' => 3,
            'reference' => 'SOR-004',
            'created_at' => Carbon::now()->subDays(5)
        ]);

        // Ajustements
        StockMovement::create([
            'product_id' => 7, // Routeur WiFi
            'quantity' => -1,
            'type' => 'ajustement',
            'reason' => 'Article défectueux',
            'user_id' => 2,
            'reference' => 'AJU-001',
            'created_at' => Carbon::now()->subDays(18)
        ]);

        StockMovement::create([
            'product_id' => 15, // Station d'accueil
            'quantity' => 1,
            'type' => 'ajustement',
            'reason' => 'Correction d\'inventaire',
            'user_id' => 1, // Admin
            'reference' => 'AJU-002',
            'created_at' => Carbon::now()->subDays(3)
        ]);

        // Retours
        StockMovement::create([
            'product_id' => 5, // Disque SSD
            'quantity' => -5,
            'type' => 'retour',
            'reason' => 'Retour au fournisseur - lot défectueux',
            'user_id' => 2,
            'reference' => 'RET-001',
            'created_at' => Carbon::now()->subDays(8)
        ]);

        // Mouvements pour différents mois (pour les statistiques)
        // Janvier
        StockMovement::create([
            'product_id' => 1,
            'quantity' => 20,
            'type' => 'entrée',
            'reason' => 'Stock initial',
            'user_id' => 1,
            'reference' => 'HIST-JAN-001',
            'created_at' => Carbon::create(Carbon::now()->year, 1, 15)
        ]);

        // Février
        StockMovement::create([
            'product_id' => 2,
            'quantity' => 15,
            'type' => 'entrée',
            'reason' => 'Stock initial',
            'user_id' => 1,
            'reference' => 'HIST-FEV-001',
            'created_at' => Carbon::create(Carbon::now()->year, 2, 15)
        ]);

        // Mars
        StockMovement::create([
            'product_id' => 3,
            'quantity' => 12,
            'type' => 'entrée',
            'reason' => 'Approvisionnement',
            'user_id' => 2,
            'reference' => 'HIST-MAR-001',
            'created_at' => Carbon::create(Carbon::now()->year, 3, 15)
        ]);

        // Avril
        StockMovement::create([
            'product_id' => 4,
            'quantity' => 10,
            'type' => 'entrée',
            'reason' => 'Approvisionnement',
            'user_id' => 2,
            'reference' => 'HIST-AVR-001',
            'created_at' => Carbon::create(Carbon::now()->year, 4, 15)
        ]);
        
        // Mai
        StockMovement::create([
            'product_id' => 5,
            'quantity' => 30,
            'type' => 'entrée',
            'reason' => 'Approvisionnement important',
            'user_id' => 2,
            'reference' => 'HIST-MAI-001',
            'created_at' => Carbon::create(Carbon::now()->year, 5, 15)
        ]);
    }
} 