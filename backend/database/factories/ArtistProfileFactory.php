<?php

namespace Database\Factories;

use App\Models\ArtistProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ArtistProfile>
 */
class ArtistProfileFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->firstName().' '.fake()->lastName();

        return [
            'artist_name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'genre' => fake()->randomElement(['Afrobeats', 'Hip Hop', 'R&B', 'Gospel']),
            'status' => 'Active',
        ];
    }
}
