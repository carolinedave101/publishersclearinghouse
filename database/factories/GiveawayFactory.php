<?php

namespace Database\Factories;

use App\Models\Giveaway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GiveawayFactory extends Factory
{
    protected $model = Giveaway::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'prize' => fake()->words(4, true),
            'prize_value' => fake()->randomFloat(2, 1000, 1000000),
            'image' => null,
            'starts_at' => fake()->dateTimeBetween('-1 month'),
            'ends_at' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'max_entries' => fake()->numberBetween(1000, 100000),
            'status' => fake()->randomElement(['active', 'upcoming', 'ended']),
            'color' => fake()->hexColor(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
