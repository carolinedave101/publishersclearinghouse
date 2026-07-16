<?php

namespace Database\Factories;

use App\Models\MembershipSubscription;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipSubscriptionFactory extends Factory
{
    protected $model = MembershipSubscription::class;

    public function definition(): array
    {
        return [
            'subscriber_name' => fake()->name(),
            'subscriber_email' => fake()->safeEmail(),
            'membership_tier_id' => MembershipTier::factory(),
            'status' => fake()->randomElement(['active', 'cancelled', 'expired']),
            'starts_at' => fake()->dateTimeBetween('-1 year'),
            'ends_at' => fake()->dateTimeBetween('+1 month', '+1 year'),
        ];
    }
}
