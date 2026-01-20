<?php

use Illuminate\Support\Facades\Event;
use Tightenco\Lectern\Events\ThreadLocked;
use Tightenco\Lectern\Events\ThreadUnlocked;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Tests\User;

it('can lock a thread', function () {
    Event::fake();

    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $thread->lock();

    expect($thread->fresh()->is_locked)->toBeTrue();
    Event::assertDispatched(ThreadLocked::class);
});

it('can unlock a thread', function () {
    Event::fake();

    $user = User::factory()->create();
    $thread = Thread::factory()->locked()->create(['user_id' => $user->id]);

    $thread->unlock();

    expect($thread->fresh()->is_locked)->toBeFalse();
    Event::assertDispatched(ThreadUnlocked::class);
});

it('can pin a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $thread->pin();

    expect($thread->fresh()->is_pinned)->toBeTrue();
});

it('can unpin a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->pinned()->create(['user_id' => $user->id]);

    $thread->unpin();

    expect($thread->fresh()->is_pinned)->toBeFalse();
});

it('can get first post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    $firstPost = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'created_at' => now()->subHour(),
    ]);
    Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'created_at' => now(),
    ]);

    expect($thread->firstPost->id)->toBe($firstPost->id);
});

it('can get latest post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'created_at' => now()->subHour(),
    ]);
    $latestPost = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'created_at' => now(),
    ]);

    expect($thread->latestPost->id)->toBe($latestPost->id);
});

it('uses slug as route key', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    expect($thread->getRouteKeyName())->toBe('slug');
});
