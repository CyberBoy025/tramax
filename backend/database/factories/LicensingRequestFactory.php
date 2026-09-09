<?php

namespace Database\Factories;

use App\Models\LicensingRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LicensingRequest>
 */
class LicensingRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'project_type' => fake()->randomElement(['Film', 'Advert', 'TV']),
            'status' => 'New',
        ];
    }
}
