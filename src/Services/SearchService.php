<?php

namespace Tightenco\Lectern\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Tightenco\Lectern\Contracts\SearchDriver;
use Tightenco\Lectern\Search\DatabaseSearch;
use Tightenco\Lectern\Search\ScoutSearch;

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
