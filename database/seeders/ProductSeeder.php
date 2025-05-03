<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Produits de TechPro Solutions (ID: 1)
        Product::create([
            'name' => 'Clavier sans fil Logitech MX Keys',
            'description' => 'Clavier sans fil haut de gamme avec rétroéclairage intelligent et frappe précise.',
            'price' => 72000,
            'quantity' => 25,
            'image' => 'products/keyboard_mx_keys.jpg',
            'category' => 'Périphériques',
            'supplier_id' => 1,
            'alert_threshold' => 5,
        ]);

        Product::create([
            'name' => 'Souris Logitech MX Master 3',
            'description' => 'Souris sans fil haute précision avec défilement ultra-rapide et personnalisation avancée.',
            'price' => 65000,
            'quantity' => 18,
            'image' => 'products/mouse_mx_master.jpg',
            'category' => 'Périphériques',
            'supplier_id' => 1,
            'alert_threshold' => 5,
        ]);

        Product::create([
            'name' => 'Écran Dell UltraSharp 27"',
            'description' => 'Moniteur professionnel 4K avec technologie IPS et large gamme de couleurs.',
            'price' => 328000,
            'quantity' => 10,
            'image' => 'products/monitor_dell_ultrasharp.jpg',
            'category' => 'Écrans',
            'supplier_id' => 1,
            'alert_threshold' => 3,
        ]);

        // Produits de Global Electronics (ID: 2)
        Product::create([
            'name' => 'Ordinateur portable ASUS ZenBook',
            'description' => 'Ultrabook léger et puissant avec écran OLED 13,3" et processeur Intel Core i7.',
            'price' => 850000,
            'quantity' => 7,
            'image' => 'products/laptop_asus_zenbook.jpg',
            'category' => 'Ordinateurs',
            'supplier_id' => 2,
            'alert_threshold' => 2,
        ]);

        Product::create([
            'name' => 'Disque SSD Samsung 1 To',
            'description' => 'SSD interne haute vitesse avec interface NVMe et lecture jusqu\'à 7000 Mo/s.',
            'price' => 98000,
            'quantity' => 30,
            'image' => 'products/ssd_samsung.jpg',
            'category' => 'Stockage',
            'supplier_id' => 2,
            'alert_threshold' => 5,
        ]);

        // Produits de Informatique & Réseaux (ID: 3)
        Product::create([
            'name' => 'Routeur WiFi 6 ASUS',
            'description' => 'Routeur haute performance avec WiFi 6, idéal pour le streaming 4K et le gaming.',
            'price' => 131000,
            'quantity' => 12,
            'image' => 'products/router_asus_wifi6.jpg',
            'category' => 'Réseau',
            'supplier_id' => 3,
            'alert_threshold' => 3,
        ]);

        Product::create([
            'name' => 'Switch réseau Cisco 24 ports',
            'description' => 'Switch manageable niveau 2 avec 24 ports Gigabit Ethernet et 4 ports SFP.',
            'price' => 230000,
            'quantity' => 5,
            'image' => 'products/switch_cisco.jpg',
            'category' => 'Réseau',
            'supplier_id' => 3,
            'alert_threshold' => 2,
        ]);

        // Produits de Bureau Concept (ID: 4)
        Product::create([
            'name' => 'Fauteuil ergonomique Herman Miller',
            'description' => 'Chaise de bureau haut de gamme avec support lombaire ajustable et respirante.',
            'price' => 590000,
            'quantity' => 4,
            'image' => 'products/chair_herman_miller.jpg',
            'category' => 'Mobilier',
            'supplier_id' => 4,
            'alert_threshold' => 1,
        ]);

        Product::create([
            'name' => 'Bureau réglable en hauteur',
            'description' => 'Bureau électrique avec hauteur ajustable, surface en bambou naturel.',
            'price' => 328000,
            'quantity' => 6,
            'image' => 'products/desk_adjustable.jpg',
            'category' => 'Mobilier',
            'supplier_id' => 4,
            'alert_threshold' => 2,
        ]);

        // Produits de Tech Import (ID: 5)
        Product::create([
            'name' => 'Casque Sony WH-1000XM4',
            'description' => 'Casque sans fil à réduction de bruit active avec autonomie de 30 heures.',
            'price' => 197000,
            'quantity' => 15,
            'image' => 'products/headphones_sony.jpg',
            'category' => 'Audio',
            'supplier_id' => 5,
            'alert_threshold' => 3,
        ]);

        Product::create([
            'name' => 'Enceinte Bluetooth JBL Charge 5',
            'description' => 'Enceinte portable waterproof avec batterie longue durée et son puissant.',
            'price' => 118000,
            'quantity' => 22,
            'image' => 'products/speaker_jbl.jpg',
            'category' => 'Audio',
            'supplier_id' => 5,
            'alert_threshold' => 4,
        ]);
        
        // Produits de DistriTech (ID: 6)
        Product::create([
            'name' => 'Tablette Samsung Galaxy Tab S7',
            'description' => 'Tablette Android avec écran 11" 120Hz, processeur Snapdragon et S Pen inclus.',
            'price' => 426000,
            'quantity' => 9,
            'image' => 'products/tablet_samsung.jpg',
            'category' => 'Tablettes',
            'supplier_id' => 6,
            'alert_threshold' => 2,
        ]);

        Product::create([
            'name' => 'Imprimante laser Brother',
            'description' => 'Imprimante multifonction laser avec impression recto-verso automatique.',
            'price' => 164000,
            'quantity' => 8,
            'image' => 'products/printer_brother.jpg',
            'category' => 'Impression',
            'supplier_id' => 6,
            'alert_threshold' => 2,
        ]);

        Product::create([
            'name' => 'Webcam Logitech StreamCam',
            'description' => 'Webcam Full HD 1080p/60fps avec suivi du visage et double micro.',
            'price' => 111000,
            'quantity' => 14,
            'image' => 'products/webcam_logitech.jpg',
            'category' => 'Périphériques',
            'supplier_id' => 1,
            'alert_threshold' => 3,
        ]);

        // Produits en stock critique
        Product::create([
            'name' => 'Carte graphique NVIDIA RTX 3080',
            'description' => 'Carte graphique gaming haut de gamme avec ray tracing et 10 Go de mémoire GDDR6X.',
            'price' => 525000,
            'quantity' => 2,
            'image' => 'products/gpu_rtx3080.jpg',
            'category' => 'Composants',
            'supplier_id' => 2,
            'alert_threshold' => 3,
        ]);

        Product::create([
            'name' => 'Station d\'accueil USB-C universel',
            'description' => 'Hub avec HDMI, Ethernet, USB et lecteur de carte pour ordinateurs portables.',
            'price' => 52000,
            'quantity' => 1,
            'image' => 'products/dock_usbc.jpg',
            'category' => 'Accessoires',
            'supplier_id' => 1,
            'alert_threshold' => 5,
        ]);
    }
}
