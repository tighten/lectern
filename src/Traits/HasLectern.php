<?php

namespace Tighten\Lectern\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Tighten\Lectern\Models\Ban;
use Tighten\Lectern\Models\Mention;
use Tighten\Lectern\Models\Post;
use Tighten\Lectern\Models\Reaction;
use Tighten\Lectern\Models\Subscription;
use Tighten\Lectern\Models\Thread;

trait HasLectern
{
    public function lecternThreads(): HasMany
    {
        return $this->hasMany(Thread::class, 'user_id');
    }

    public function lecternPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function lecternReactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'user_id');
    }

    public function lecternSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function lecternMentions(): HasMany
    {
        return $this->hasMany(Mention::class, 'user_id');
    }

    public function lecternBan(): HasOne
    {
        return $this->hasOne(Ban::class, 'user_id');
    }

    public function isBannedFromLectern(): bool
    {
        $ban = $this->lecternBan;

        if (! $ban) {
            return false;
        }

        if ($ban->expires_at === null) {
            return true;
        }

        return $ban->expires_at->isFuture();
    }

    public function banFromLectern(?string $reason = null, ?Carbon $expiresAt = null, ?int $bannedById = null): Ban
    {
        $this->unbanFromLectern();

        return $this->lecternBan()->create([
            'reason' => $reason,
            'expires_at' => $expiresAt,
            'banned_by_id' => $bannedById,
        ]);
    }

    public function unbanFromLectern(): void
    {
        $this->lecternBan()->delete();
    }
}
