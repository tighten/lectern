<?php

namespace Tightenco\Lectern\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mention extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'lectern_mentions';

    protected $fillable = [
        'post_id',
        'user_id',
        'notified_at',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lectern.user.model'));
    }

    public function markAsNotified(): void
    {
        $this->update(['notified_at' => now()]);
    }

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
        ];
    }
}
