<?php

namespace Database\Factories;

use App\Models\ArtistProfile;
use App\Models\RoyaltyStatement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoyaltyStatement>
 */
class RoyaltyStatementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'artist_profile_id' => ArtistProfile::factory(),
            'period_start' => '2026-01-01',
            'period_end' => '2026-01-31',
            'total_revenue' => 1000,
            'company_share' => 300,
            'artist_share' => 700,
            'status' => 'Draft',
        ];
    }
}
