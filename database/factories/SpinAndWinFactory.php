<?php

namespace Database\Factories;

use App\Models\SpinAndWin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpinAndWinFactory extends Factory
{
    protected $model = SpinAndWin::class;

    public function definition(): array
    {
        $title = 'Spin & Win ' . fake()->unique()->numberBetween(1, 99999);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'rules' => fake()->paragraph(),
            'image' => null,
            'is_active' => true,
            'sort_order' => 1,
            'max_spins_per_day' => 3,
            'cooldown_minutes' => 0,
            'requires_login' => false,
            'success_message' => 'Congratulations! You won {prize}!',
        ];
    }
}
