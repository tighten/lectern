<?php

namespace Tighten\Lectern\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Tighten\Lectern\Contracts\SearchDriver;
use Tighten\Lectern\Search\DatabaseSearch;
use Tighten\Lectern\Search\ScoutSearch;

class SearchService
{
    protected SearchDriver $driver;

    public function __construct()
    {
        $this->driver = $this->resolveDriver();
    }

    public function search(string $query): LengthAwarePaginator
    {
        return $this->driver->search($query);
    }

    protected function resolveDriver(): SearchDriver
    {
        $driver = config('lectern.search.driver');

        return match ($driver) {
            'database' => new DatabaseSearch,
            'scout' => new ScoutSearch,
            default => throw new InvalidArgumentException("Unsupported search driver: {$driver}"),
        };
    }
}
