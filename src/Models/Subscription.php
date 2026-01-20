<?php

namespace Tightenco\Lectern\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subscription extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'lectern_subscriptions';

    protected $fillable = [
        'user_id',
        'subscribable_id',
        'subscribable_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lectern.user.model'));
    }

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
}
