<?php

namespace Tighten\Lectern\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface SearchDriver
{
    public function search(string $query): LengthAwarePaginator;
}
