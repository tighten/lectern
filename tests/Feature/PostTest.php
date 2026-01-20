<?php

use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Tests\User;

it('can list posts for a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    Post::factory()->count(3)->create(['thread_id' => $thread->id, 'user_id' => $user->id]);

    $response = $this->getJson(route('lectern.threads.posts.index', $thread));

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

it('can show a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson(route('lectern.posts.show', $post));

    $response->assertOk()
        ->assertJsonPath('data.id', $post->id);
});

it('can create a post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('lectern.threads.posts.store', $thread), [
        'body' => 'This is a new post.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.body', 'This is a new post.');

    $this->assertDatabaseHas('lectern_posts', [
        'body' => 'This is a new post.',
        'thread_id' => $thread->id,
    ]);
});

it('cannot create a post on a locked thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->locked()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('lectern.threads.posts.store', $thread), [
        'body' => 'This should fail.',
    ]);

    $response->assertForbidden();
});

it('can create a reply to a post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    $post = Post::factory()->create(['thread_id' => $thread->id, 'user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('lectern.threads.posts.store', $thread), [
        'body' => 'This is a reply.',
        'parent_id' => $post->id,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.parent_id', $post->id);
});

it('can update a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->putJson(route('lectern.posts.update', $post), [
        'body' => 'Updated body.',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.body', 'Updated body.');
});

it('cannot update another users post', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->putJson(route('lectern.posts.update', $post), [
        'body' => 'Updated body.',
    ]);

    $response->assertForbidden();
});

it('can delete a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson(route('lectern.posts.destroy', $post));

    $response->assertNoContent();
    $this->assertSoftDeleted('lectern_posts', ['id' => $post->id]);
});

it('can list replies to a post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    $post = Post::factory()->create(['thread_id' => $thread->id, 'user_id' => $user->id]);
    Post::factory()->count(2)->create([
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'parent_id' => $post->id,
    ]);

    $response = $this->getJson(route('lectern.posts.replies.index', $post));

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});
