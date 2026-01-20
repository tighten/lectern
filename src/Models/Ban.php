<?php

namespace Tightenco\Lectern\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ban extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'lectern_bans';

    protected $fillable = [
        'user_id',
        'banned_by_id',
        'reason',
        'expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lectern.user.model'));
    }

    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(config('lectern.user.model'), 'banned_by_id');
    }

    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function isPermanent(): bool
    {
        return $this->expires_at === null;
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
