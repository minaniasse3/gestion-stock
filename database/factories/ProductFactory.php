<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productTypes = ['Écran', 'Clavier', 'Souris', 'Imprimante', 'Scanner', 'Ordinateur', 'Tablette', 'Smartphone', 'Câble', 'Adaptateur', 'Disque dur', 'Mémoire RAM', 'Processeur'];
        $brands = ['Dell', 'HP', 'Asus', 'Lenovo', 'Samsung', 'Apple', 'Logitech', 'Canon', 'Epson', 'Microsoft', 'Acer', 'LG', 'SanDisk', 'WD', 'Kingston'];
        
        $productType = $this->faker->randomElement($productTypes);
        $brand = $this->faker->randomElement($brands);
        
        return [
            'name' => $brand . ' ' . $productType . ' ' . $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 20, 1500),
            'quantity' => $this->faker->numberBetween(0, 100),
            'image' => 'product-' . $this->faker->numberBetween(1, 10) . '.jpg',
        ];
    }
}
