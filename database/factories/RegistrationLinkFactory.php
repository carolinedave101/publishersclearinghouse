<?php

namespace Database\Factories;

use App\Models\RegistrationLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationLinkFactory extends Factory
{
    protected $model = RegistrationLink::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'source' => strtolower(fake()->unique()->word()) . '-' . fake()->randomElement(['fb', 'tt', 'ig', 'yt']),
            'is_active' => true,
        ];
    }
}