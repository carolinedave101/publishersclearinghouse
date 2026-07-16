<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->randomHtml(),
            'meta_description' => fake()->sentence(),
            'is_published' => true,
            'published_at' => fake()->dateTimeThisYear(),
        ];
    }
}
