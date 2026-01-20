<?php

namespace Tightenco\Lectern\Search;

use Illuminate\Pagination\LengthAwarePaginator;
use Tightenco\Lectern\Contracts\SearchDriver;
use Tightenco\Lectern\Models\Thread;

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
