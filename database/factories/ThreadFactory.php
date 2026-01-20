<?php

namespace Tightenco\Lectern\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Thread;

class ThreadFactory extends Factory
{
    protected $model = Thread::class;

    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'category_id' => Category::factory(),
            'user_id' => 1,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(8),
            'is_pinned' => false,
            'is_locked' => false,
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn () => ['is_pinned' => true]);
    }

    public function locked(): static
    {
        return $this->state(fn () => ['is_locked' => true]);
    }
}
