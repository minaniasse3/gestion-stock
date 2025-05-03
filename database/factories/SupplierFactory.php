<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyTypes = ['Technologies', 'Informatique', 'Equipment', 'Electronics', 'Digital', 'Solutions', 'Computers', 'Hardware', 'Office', 'Supplies'];
        $companySuffixes = ['SARL', 'Inc.', 'Ltd', 'SAS', 'SA', 'Group', 'International', 'Corp.', 'GmbH', '& Co.'];
        
        return [
            'name' => $this->faker->lastName() . ' ' . $this->faker->randomElement($companyTypes) . ' ' . $this->faker->randomElement($companySuffixes),
            'email' => $this->faker->companyEmail(),
            'phone' => '+221 ' . $this->faker->numerify('## ### ## ##'),
            'address' => $this->faker->streetAddress() . ', ' . $this->faker->city() . ', ' . 'Sénégal',
            'contact_person' => $this->faker->name(),
        ];
    }
}
