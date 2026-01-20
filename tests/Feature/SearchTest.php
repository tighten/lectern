<?php

use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Tests\User;

it('can search threads', function () {
    $user = User::factory()->create();
    Thread::factory()->create(['user_id' => $user->id, 'title' => 'Laravel is awesome']);
    Thread::factory()->create(['user_id' => $user->id, 'title' => 'PHP best practices']);
    Thread::factory()->create(['user_id' => $user->id, 'title' => 'Another topic']);

    $response = $this->getJson(route('lectern.search', ['q' => 'Laravel']));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Laravel is awesome');
});

it('requires a search query', function () {
    $response = $this->getJson(route('lectern.search'));

    $response->assertUnprocessable();
});

it('requires minimum query length', function () {
    $response = $this->getJson(route('lectern.search', ['q' => 'a']));

    $response->assertUnprocessable();
});
