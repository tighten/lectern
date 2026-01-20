<?php

namespace Tightenco\Lectern\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tightenco\Lectern\Database\Factories\ThreadFactory;
use Tightenco\Lectern\Events\ThreadCreated;
use Tightenco\Lectern\Events\ThreadDeleted;
use Tightenco\Lectern\Events\ThreadLocked;
use Tightenco\Lectern\Events\ThreadUnlocked;
use Tightenco\Lectern\Events\ThreadUpdated;

class Thread extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lectern_threads';

    protected $dispatchesEvents = [
        'created' => ThreadCreated::class,
        'updated' => ThreadUpdated::class,
        'deleted' => ThreadDeleted::class,
    ];

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'is_pinned',
        'is_locked',
    ];

    protected static function newFactory()
    {
        return ThreadFactory::new();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lectern.user.model'));
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function firstPost(): HasOne
    {
        return $this->hasOne(Post::class)->oldest();
    }

    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latest();
    }

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    public function lock(): void
    {
        $this->update(['is_locked' => true]);
        event(new ThreadLocked($this));
    }

    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
        event(new ThreadUnlocked($this));
    }

    public function pin(): void
    {
        $this->update(['is_pinned' => true]);
    }

    public function unpin(): void
    {
        $this->update(['is_pinned' => false]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }
}
