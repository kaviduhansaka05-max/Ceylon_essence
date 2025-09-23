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
        return [
           'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(['Essence', 'Body Mist', 'Perfume', 'Aromatic']),
            'inventory' => $this->faker->numberBetween(10, 100),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'status' => $this->faker->randomElement(['Instock', 'Out of Stock']),
            'sold_pieces' => $this->faker->numberBetween(0, 50),
            'image' => 'product.png', // placeholder
        ];
    }
}
