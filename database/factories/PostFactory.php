<?php

namespace Tightenco\Lectern\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'thread_id' => Thread::factory(),
            'user_id' => 1,
            'body' => $this->faker->paragraphs(3, true),
        ];
    }

    public function reply(Post $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
            'thread_id' => $parent->thread_id,
        ]);
    }
}
