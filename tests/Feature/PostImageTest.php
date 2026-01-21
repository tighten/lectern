<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Tests\User;

beforeEach(function () {
    Storage::fake('public');
    config(['lectern.images.enabled' => true]);
    config(['lectern.images.max_size' => 2048]);
    config(['lectern.images.max_per_post' => 10]);
    config(['lectern.images.disk' => 'public']);
});

it('can upload an image to a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->image('test.jpg', 800, 600);

    $response = $this->actingAs($user)->postJson(route('lectern.posts.images.store', $post), [
        'image' => $file,
    ]);

    $response->assertOk()
        ->assertJsonStructure(['id', 'url', 'conversions']);

    expect($post->getMedia('images'))->toHaveCount(1);
});

it('cannot upload an image when images are disabled', function () {
    config(['lectern.images.enabled' => false]);

    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->actingAs($user)->postJson(route('lectern.posts.images.store', $post), [
        'image' => $file,
    ]);

    $response->assertForbidden();
});

it('cannot upload more images than max_per_post allows', function () {
    config(['lectern.images.max_per_post' => 2]);

    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $post->addMedia(UploadedFile::fake()->image('one.jpg'))->toMediaCollection('images');
    $post->addMedia(UploadedFile::fake()->image('two.jpg'))->toMediaCollection('images');

    $file = UploadedFile::fake()->image('three.jpg');

    $response = $this->actingAs($user)->postJson(route('lectern.posts.images.store', $post), [
        'image' => $file,
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Maximum of 2 images per post allowed.');
});

it('cannot upload an image to another users post', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);
    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->actingAs($user)->postJson(route('lectern.posts.images.store', $post), [
        'image' => $file,
    ]);

    $response->assertForbidden();
});

it('cannot upload a file that exceeds max size', function () {
    config(['lectern.images.max_size' => 1024]);

    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $file = UploadedFile::fake()->image('large.jpg')->size(2048);

    $response = $this->actingAs($user)->postJson(route('lectern.posts.images.store', $post), [
        'image' => $file,
    ]);

    $response->assertUnprocessable();
});

it('can delete an image from a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $media = $post->addMedia(UploadedFile::fake()->image('test.jpg'))->toMediaCollection('images');

    $response = $this->actingAs($user)->deleteJson(route('lectern.posts.images.destroy', [$post, $media->id]));

    $response->assertNoContent();
    expect($post->fresh()->getMedia('images'))->toHaveCount(0);
});

it('cannot delete an image from another users post', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);
    $media = $post->addMedia(UploadedFile::fake()->image('test.jpg'))->toMediaCollection('images');

    $response = $this->actingAs($user)->deleteJson(route('lectern.posts.images.destroy', [$post, $media->id]));

    $response->assertForbidden();
});

it('returns 404 when deleting non-existent image', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson(route('lectern.posts.images.destroy', [$post, 99999]));

    $response->assertNotFound();
});

it('includes images in post resource response', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $post->addMedia(UploadedFile::fake()->image('test.jpg'))->toMediaCollection('images');

    $response = $this->getJson(route('lectern.posts.show', $post));

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'images' => [
                    '*' => ['id', 'url', 'conversions'],
                ],
            ],
        ]);
});
