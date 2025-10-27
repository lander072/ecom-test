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
        $categories = [
            'Janitorial',
            'Hardware',
            'Plumbing',
            'Tile',
            'Construction',
            'Lighting',
            'Tools',
            'Paint',
            'Kitchen & Bath',
            'Seasonal',
        ];

        $category = $this->faker->randomElement($categories);

        return [
            'name' => $this->faker->words(3, true), // e.g. “Heavy Duty Mop Handle”
            'category' => $category,
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 5, 500), // from $5 to $500
            'stock' => $this->faker->numberBetween(5, 100),
        ];
    }
}
