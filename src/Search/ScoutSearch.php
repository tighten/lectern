<?php

namespace Tighten\Lectern\Search;

use Illuminate\Pagination\LengthAwarePaginator;
use Tighten\Lectern\Contracts\SearchDriver;
use Tighten\Lectern\Models\Thread;

class ScoutSearch implements SearchDriver
{
    public function search(string $query): LengthAwarePaginator
    {
        return Thread::search($query)
            ->query(function ($builder) {
                $builder->with(['user', 'category'])->withCount('posts');
            })
            ->paginate(config('lectern.pagination.threads'));
    }
}
