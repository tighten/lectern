<?php

namespace Tightenco\Lectern\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Thread;

class ThreadPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Thread $thread): bool
    {
        return true;
    }

    public function create($user, ?Category $category = null): bool
    {
        if ($category?->is_admin_only && ! $user->isAdmin()) {
            return false;
        }

        return true;
    }

    public function update($user, Thread $thread): bool
    {
        return $user->id === $thread->user_id;
    }

    public function delete($user, Thread $thread): bool
    {
        return $user->id === $thread->user_id;
    }

    public function lock($user, Thread $thread): bool
    {
        return false;
    }

    public function unlock($user, Thread $thread): bool
    {
        return false;
    }

    public function pin($user, Thread $thread): bool
    {
        return false;
    }

    public function unpin($user, Thread $thread): bool
    {
        return false;
    }
}
