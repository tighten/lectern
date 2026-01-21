<?php

namespace Tightenco\Lectern\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tightenco\Lectern\Database\Factories\PostFactory;
use Tightenco\Lectern\Events\PostCreated;
use Tightenco\Lectern\Events\PostDeleted;
use Tightenco\Lectern\Events\PostUpdated;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'lectern_posts';

    protected $dispatchesEvents = [
        'created' => PostCreated::class,
        'updated' => PostUpdated::class,
        'deleted' => PostDeleted::class,
    ];

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'body',
    ];

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lectern.user.model'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function mentions(): HasMany
    {
        return $this->hasMany(Mention::class);
    }

    public function registerMediaCollections(): void
    {
        if (! config('lectern.images.enabled', true)) {
            return;
        }

        $this->addMediaCollection('images')
            ->useDisk(config('lectern.images.disk', 'public'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! config('lectern.images.enabled', true)) {
            return;
        }

        $conversions = config('lectern.images.conversions', []);

        foreach ($conversions as $name => $dimensions) {
            $this->addMediaConversion($name)
                ->fit(Fit::Contain, $dimensions[0], $dimensions[1])
                ->nonQueued()
                ->performOnCollections('images');
        }
    }
}
