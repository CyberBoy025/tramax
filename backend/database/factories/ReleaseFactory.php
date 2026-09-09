<?php

namespace Database\Factories;

use App\Models\ArtistProfile;
use App\Models\Release;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Release>
 */
class ReleaseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'artist_profile_id' => ArtistProfile::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'type' => fake()->randomElement(['Single', 'EP', 'Album']),
            'status' => 'Published',
        ];
    }
}
