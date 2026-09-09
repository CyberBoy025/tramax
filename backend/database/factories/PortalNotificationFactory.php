<?php

namespace Database\Factories;

use App\Models\PortalNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortalNotification>
 */
class PortalNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'read_at' => null,
        ];
    }
}
