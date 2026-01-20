<?php

use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Subscription;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Tests\User;

it('can subscribe to a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('lectern.threads.subscribe', $thread));

    $response->assertCreated();

    $this->assertDatabaseHas('lectern_subscriptions', [
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_thread',
        'subscribable_id' => $thread->id,
    ]);
});

it('can unsubscribe from a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);

    Subscription::create([
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_thread',
        'subscribable_id' => $thread->id,
    ]);

    $response = $this->actingAs($user)->deleteJson(route('lectern.threads.unsubscribe', $thread));

    $response->assertNoContent();

    $this->assertDatabaseMissing('lectern_subscriptions', [
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_thread',
        'subscribable_id' => $thread->id,
    ]);
});

it('can subscribe to a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->postJson(route('lectern.categories.subscribe', $category));

    $response->assertCreated();

    $this->assertDatabaseHas('lectern_subscriptions', [
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_category',
        'subscribable_id' => $category->id,
    ]);
});

it('can unsubscribe from a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_category',
        'subscribable_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->deleteJson(route('lectern.categories.unsubscribe', $category));

    $response->assertNoContent();

    $this->assertDatabaseMissing('lectern_subscriptions', [
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_category',
        'subscribable_id' => $category->id,
    ]);
});

it('can list user subscriptions', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create();

    Subscription::create([
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_thread',
        'subscribable_id' => $thread->id,
    ]);

    Subscription::create([
        'user_id' => $user->id,
        'subscribable_type' => 'lectern_category',
        'subscribable_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->getJson(route('lectern.subscriptions.index'));

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});
