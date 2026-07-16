<?php

namespace Database\Factories;

use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class GiveawayEntryFactory extends Factory
{
    protected $model = GiveawayEntry::class;

    public function definition(): array
    {
        return [
            'giveaway_id' => Giveaway::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'is_winner' => false,
        ];
    }
}
