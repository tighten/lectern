<?php

use Illuminate\Support\Carbon;
use Tightenco\Lectern\Models\Ban;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Tests\User;

it('can get user threads', function () {
    $user = User::factory()->create();
    Thread::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->lecternThreads)->toHaveCount(3);
});

it('can get user posts', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['user_id' => $user->id]);
    Post::factory()->count(5)->create(['user_id' => $user->id, 'thread_id' => $thread->id]);

    expect($user->lecternPosts)->toHaveCount(5);
});

it('can ban a user', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create();

    $ban = $user->banFromLectern('Spam', null, $admin->id);

    expect($ban)->toBeInstanceOf(Ban::class);
    expect($user->isBannedFromLectern())->toBeTrue();
    expect($ban->reason)->toBe('Spam');
    expect($ban->banned_by_id)->toBe($admin->id);
});

it('can unban a user', function () {
    $user = User::factory()->create();
    $user->banFromLectern('Spam');

    expect($user->isBannedFromLectern())->toBeTrue();

    $user->unbanFromLectern();
    $user->refresh();

    expect($user->isBannedFromLectern())->toBeFalse();
});

it('recognizes permanent bans', function () {
    $user = User::factory()->create();
    $user->banFromLectern('Permanent ban', null);

    expect($user->isBannedFromLectern())->toBeTrue();
    expect($user->lecternBan->isPermanent())->toBeTrue();
});

it('recognizes expired bans', function () {
    $user = User::factory()->create();
    $user->banFromLectern('Temporary ban', Carbon::now()->subDay());

    expect($user->isBannedFromLectern())->toBeFalse();
    expect($user->lecternBan->isExpired())->toBeTrue();
});

it('recognizes active temporary bans', function () {
    $user = User::factory()->create();
    $user->banFromLectern('Temporary ban', Carbon::now()->addDay());

    expect($user->isBannedFromLectern())->toBeTrue();
    expect($user->lecternBan->isExpired())->toBeFalse();
});

it('replaces existing ban when banning again', function () {
    $user = User::factory()->create();

    $user->banFromLectern('First ban');
    $firstBanId = $user->lecternBan->id;

    $user->banFromLectern('Second ban');
    $user->refresh();

    expect(Ban::where('user_id', $user->id)->count())->toBe(1);
    expect($user->lecternBan->reason)->toBe('Second ban');
});
