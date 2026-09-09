<?php

namespace Database\Factories;

use App\Models\Release;
use App\Models\RightsRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RightsRecord>
 */
class RightsRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'release_id' => Release::factory(),
            'master_owner' => fake()->company(),
            'publishing_owner' => fake()->company(),
            'copyright_status' => 'Active',
            'licensing_status' => 'Unlicensed',
        ];
    }
}
