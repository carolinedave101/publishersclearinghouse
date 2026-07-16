<?php

namespace Database\Factories;

use App\Models\SpinAndWin;
use App\Models\SpinResult;
use App\Models\SpinWheelSegment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpinResultFactory extends Factory
{
    protected $model = SpinResult::class;

    public function definition(): array
    {
        return [
            'spin_and_win_id' => SpinAndWin::factory(),
            'spin_wheel_segment_id' => SpinWheelSegment::factory(),
            'winner_name' => fake()->name(),
            'winner_email' => fake()->safeEmail(),
            'prize_label' => fake()->words(2, true),
            'prize_type' => fake()->randomElement(['cash', 'coupon', 'physical', 'points', 'free_spin', 'nothing']),
            'prize_value' => fake()->randomFloat(2, 0, 500),
            'is_claimed' => false,
            'claimed_at' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_claimed' => true,
            'claimed_at' => now(),
        ]);
    }
}
