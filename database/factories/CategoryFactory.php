<?php

namespace Tightenco\Lectern\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Tightenco\Lectern\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'order' => 0,
            'is_private' => false,
        ];
    }

    public function private(): static
    {
        return $this->state(fn () => ['is_private' => true]);
    }
}
