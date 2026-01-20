<?php

use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Tests\User;

it('can list threads', function () {
    $user = User::factory()->create();
    Thread::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->getJson(route('lectern.threads.index'));

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

it('can list threads by category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Thread::factory()->count(2)->create(['user_id' => $user->id, 'category_id' => $category->id]);
    Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson(route('lectern.categories.threads.index', $category));

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

it('can show a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->getJson(route('lectern.threads.show', $thread));

    $response->assertOk()
        ->assertJsonPath('data.id', $thread->id)
        ->assertJsonPath('data.title', $thread->title);
});

it('can create a thread', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->postJson(route('lectern.categories.threads.store', $category), [
        'title' => 'Test Thread',
        'body' => 'This is the thread body.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Test Thread');

    $this->assertDatabaseHas('lectern_threads', [
        'title' => 'Test Thread',
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('lectern_posts', [
        'body' => 'This is the thread body.',
        'user_id' => $user->id,
    ]);
});

it('can update a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->putJson(route('lectern.threads.update', $thread), [
        'title' => 'Updated Title',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Title');
});

it('cannot update another users thread', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->putJson(route('lectern.threads.update', $thread), [
        'title' => 'Updated Title',
    ]);

    $response->assertForbidden();
});

it('can delete a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson(route('lectern.threads.destroy', $thread));

    $response->assertNoContent();
    $this->assertSoftDeleted('lectern_threads', ['id' => $thread->id]);
});

it('lists pinned threads first', function () {
    $user = User::factory()->create();
    $regular = Thread::factory()->create(['user_id' => $user->id, 'created_at' => now()]);
    $pinned = Thread::factory()->pinned()->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);

    $response = $this->getJson(route('lectern.threads.index'));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $pinned->id)
        ->assertJsonPath('data.1.id', $regular->id);
});
