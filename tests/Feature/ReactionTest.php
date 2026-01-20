<?php

use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Reaction;
use Tightenco\Lectern\Tests\User;

it('can add a reaction to a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('lectern.posts.reactions.store', $post), [
        'type' => 'like',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.type', 'like');

    $this->assertDatabaseHas('lectern_reactions', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'type' => 'like',
    ]);
});

it('cannot add duplicate reaction of same type', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson(route('lectern.posts.reactions.store', $post), [
        'type' => 'like',
    ]);

    $this->actingAs($user)->postJson(route('lectern.posts.reactions.store', $post), [
        'type' => 'like',
    ]);

    expect(Reaction::where('post_id', $post->id)->where('user_id', $user->id)->count())->toBe(1);
});

it('can remove a reaction from a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    Reaction::create([
        'post_id' => $post->id,
        'user_id' => $user->id,
        'type' => 'like',
    ]);

    $response = $this->actingAs($user)->deleteJson(route('lectern.posts.reactions.destroy', [$post, 'like']));

    $response->assertNoContent();

    $this->assertDatabaseMissing('lectern_reactions', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'type' => 'like',
    ]);
});

it('validates reaction type', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('lectern.posts.reactions.store', $post), [
        'type' => 'invalid-type',
    ]);

    $response->assertUnprocessable();
});
