<?php

namespace Tightenco\Lectern\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Thread;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny($user, Thread $thread): bool
    {
        return true;
    }

    public function view($user, Post $post): bool
    {
        return true;
    }

    public function create($user, Thread $thread): bool
    {
        return ! $thread->is_locked;
    }

    public function update($user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete($user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }
}
