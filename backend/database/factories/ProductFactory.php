<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true).' merch';

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'price' => fake()->randomFloat(2, 5, 100),
            'category' => fake()->randomElement(['Apparel', 'Accessories']),
            'status' => 'Draft',
        ];
    }
}
