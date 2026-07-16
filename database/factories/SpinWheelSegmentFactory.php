<?php

namespace Database\Factories;

use App\Models\SpinAndWin;
use App\Models\SpinWheelSegment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpinWheelSegmentFactory extends Factory
{
    protected $model = SpinWheelSegment::class;

    public function definition(): array
    {
        $prizeTypes = ['cash', 'coupon', 'physical', 'points', 'free_spin', 'nothing'];
        $type = fake()->randomElement($prizeTypes);

        return [
            'spin_and_win_id' => SpinAndWin::factory(),
            'label' => $type === 'nothing' ? 'Try Again' : fake()->words(2, true),
            'color' => fake()->hexColor(),
            'prize_type' => $type,
            'prize_value' => $type === 'cash' ? fake()->randomFloat(2, 1, 500) : ($type === 'points' ? fake()->numberBetween(10, 1000) : 0),
            'prize_description' => $type === 'nothing' ? null : fake()->sentence(),
            'weight' => fake()->numberBetween(1, 20),
            'is_jackpot' => false,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 12),
        ];
    }

    public function jackpot(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'JACKPOT!',
            'color' => '#FF0000',
            'prize_type' => 'cash',
            'prize_value' => 10000,
            'weight' => 1,
            'is_jackpot' => true,
        ]);
    }

    public function nothing(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Try Again',
            'color' => '#888888',
            'prize_type' => 'nothing',
            'prize_value' => 0,
            'weight' => 50,
            'is_jackpot' => false,
        ]);
    }
}
