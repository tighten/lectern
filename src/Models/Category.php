<?php

namespace Tightenco\Lectern\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tightenco\Lectern\Database\Factories\CategoryFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'lectern_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'emoji',
        'order',
        'is_private',
        'is_admin_only',
    ];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'is_admin_only' => 'boolean',
        ];
    }
}
