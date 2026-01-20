<?php

use Tightenco\Lectern\Models\Category;

it('can list categories', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson(route('lectern.categories.index'));

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

it('can show a category', function () {
    $category = Category::factory()->create();

    $response = $this->getJson(route('lectern.categories.show', $category));

    $response->assertOk()
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonPath('data.name', $category->name);
});

it('can list nested categories', function () {
    $parent = Category::factory()->create();
    Category::factory()->count(2)->create(['parent_id' => $parent->id]);

    $response = $this->getJson(route('lectern.categories.index'));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonCount(2, 'data.0.children');
});
