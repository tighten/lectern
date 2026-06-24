<?php

namespace Tighten\Lectern\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Tighten\Lectern\Models\Category;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Category $category): bool
    {
        if (! $category->is_private) {
            return true;
        }

        return false;
    }

    public function create($user): bool
    {
        return false;
    }

    public function update($user, Category $category): bool
    {
        return false;
    }

    public function delete($user, Category $category): bool
    {
        return false;
    }
}
