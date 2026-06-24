<?php

namespace Tighten\Lectern\Search;

use Illuminate\Pagination\LengthAwarePaginator;
use Tighten\Lectern\Contracts\SearchDriver;
use Tighten\Lectern\Models\Post;
use Tighten\Lectern\Models\Thread;

class DatabaseSearch implements SearchDriver
{
    public function search(string $query): LengthAwarePaginator
    {
        $threads = Thread::query()
            ->where('title', 'like', "%{$query}%")
            ->with(['user', 'category'])
            ->withCount('posts')
            ->latest()
            ->paginate(config('lectern.pagination.threads'));

        return $threads;
    }

    public function searchPosts(string $query): LengthAwarePaginator
    {
        return Post::query()
            ->where('body', 'like', "%{$query}%")
            ->with(['user', 'thread'])
            ->latest()
            ->paginate(config('lectern.pagination.posts'));
    }
}
