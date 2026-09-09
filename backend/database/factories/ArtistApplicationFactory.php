<?php

namespace Database\Factories;

use App\Models\ArtistApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArtistApplication>
 */
class ArtistApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'artist_name' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'genre' => fake()->randomElement(['Afrobeats', 'Hip Hop', 'R&B', 'Gospel']),
            'biography' => fake()->paragraph(),
            'status' => 'Submitted',
        ];
    }
}
