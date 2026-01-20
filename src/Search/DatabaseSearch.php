<?php

namespace Tightenco\Lectern\Search;

use Illuminate\Pagination\LengthAwarePaginator;
use Tightenco\Lectern\Contracts\SearchDriver;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;

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
