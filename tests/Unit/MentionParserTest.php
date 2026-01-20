<?php

use Illuminate\Support\Facades\Event;
use Tightenco\Lectern\Events\UserMentioned;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Services\MentionParser;
use Tightenco\Lectern\Tests\User;

beforeEach(function () {
    $this->parser = new MentionParser;
});

it('parses mentions from post body', function () {
    Event::fake();

    $user = User::factory()->create(['name' => 'JohnDoe']);
    $author = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => 'Hello @JohnDoe, how are you?',
    ]);

    $mentions = $this->parser->parse($post);

    expect($mentions)->toHaveCount(1);
    expect($mentions->first()->user_id)->toBe($user->id);

    Event::assertDispatched(UserMentioned::class);
});

it('does not mention the post author', function () {
    Event::fake();

    $author = User::factory()->create(['name' => 'Author']);
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => 'I mentioned myself @Author',
    ]);

    $mentions = $this->parser->parse($post);

    expect($mentions)->toHaveCount(0);
});

it('handles multiple mentions', function () {
    Event::fake();

    $user1 = User::factory()->create(['name' => 'Alice']);
    $user2 = User::factory()->create(['name' => 'Bob']);
    $author = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => 'Hey @Alice and @Bob!',
    ]);

    $mentions = $this->parser->parse($post);

    expect($mentions)->toHaveCount(2);
});

it('ignores non-existent users', function () {
    Event::fake();

    $author = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => 'Hello @NonExistentUser!',
    ]);

    $mentions = $this->parser->parse($post);

    expect($mentions)->toHaveCount(0);
});

it('does not create duplicate mentions', function () {
    Event::fake();

    $user = User::factory()->create(['name' => 'JohnDoe']);
    $author = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => '@JohnDoe @JohnDoe @JohnDoe',
    ]);

    $mentions = $this->parser->parse($post);

    expect($mentions)->toHaveCount(1);
});

it('syncs mentions on update', function () {
    Event::fake();

    $user1 = User::factory()->create(['name' => 'Alice']);
    $user2 = User::factory()->create(['name' => 'Bob']);
    $author = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => 'Hey @Alice!',
    ]);

    $this->parser->parse($post);
    expect($post->mentions()->count())->toBe(1);

    $post->update(['body' => 'Hey @Bob!']);
    $this->parser->syncMentions($post);

    expect($post->mentions()->count())->toBe(1);
    expect($post->mentions()->first()->user_id)->toBe($user2->id);
});

it('respects mentions enabled config', function () {
    config(['lectern.mentions.enabled' => false]);

    $user = User::factory()->create(['name' => 'JohnDoe']);
    $author = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $author->id]);
    $post = Post::factory()->create([
        'thread_id' => $thread->id,
        'user_id' => $author->id,
        'body' => 'Hello @JohnDoe!',
    ]);

    $mentions = $this->parser->parse($post);

    expect($mentions)->toHaveCount(0);
});
