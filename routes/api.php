<?php

use Illuminate\Support\Facades\Route;
use Tightenco\Lectern\Http\Controllers\CategoryController;
use Tightenco\Lectern\Http\Controllers\PostController;
use Tightenco\Lectern\Http\Controllers\ReactionController;
use Tightenco\Lectern\Http\Controllers\SearchController;
use Tightenco\Lectern\Http\Controllers\SubscriptionController;
use Tightenco\Lectern\Http\Controllers\ThreadController;

Route::prefix(config('lectern.prefix'))
    ->middleware(config('lectern.middleware'))
    ->as('lectern.')
    ->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('categories/{category}/threads', [ThreadController::class, 'indexByCategory'])->name('categories.threads.index');

        Route::get('threads', [ThreadController::class, 'index'])->name('threads.index');
        Route::get('threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');
        Route::get('threads/{thread}/posts', [PostController::class, 'index'])->name('threads.posts.index');

        Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::get('posts/{post}/replies', [PostController::class, 'replies'])->name('posts.replies.index');

        Route::get('search', SearchController::class)->name('search');

        Route::middleware(config('lectern.auth_middleware'))->group(function () {
            Route::post('categories/{category}/threads', [ThreadController::class, 'store'])->name('categories.threads.store');

            Route::put('threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
            Route::delete('threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');
            Route::post('threads/{thread}/lock', [ThreadController::class, 'lock'])->name('threads.lock');
            Route::post('threads/{thread}/unlock', [ThreadController::class, 'unlock'])->name('threads.unlock');
            Route::post('threads/{thread}/pin', [ThreadController::class, 'pin'])->name('threads.pin');
            Route::post('threads/{thread}/unpin', [ThreadController::class, 'unpin'])->name('threads.unpin');

            Route::post('threads/{thread}/posts', [PostController::class, 'store'])->name('threads.posts.store');
            Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
            Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

            Route::post('posts/{post}/reactions', [ReactionController::class, 'store'])->name('posts.reactions.store');
            Route::delete('posts/{post}/reactions/{type}', [ReactionController::class, 'destroy'])->name('posts.reactions.destroy');

            Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
            Route::post('threads/{thread}/subscribe', [SubscriptionController::class, 'subscribeToThread'])->name('threads.subscribe');
            Route::delete('threads/{thread}/subscribe', [SubscriptionController::class, 'unsubscribeFromThread'])->name('threads.unsubscribe');
            Route::post('categories/{category}/subscribe', [SubscriptionController::class, 'subscribeToCategory'])->name('categories.subscribe');
            Route::delete('categories/{category}/subscribe', [SubscriptionController::class, 'unsubscribeFromCategory'])->name('categories.unsubscribe');
        });
    });
