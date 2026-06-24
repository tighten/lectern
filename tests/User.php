<?php

namespace Tighten\Lectern\Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tighten\Lectern\Traits\HasLectern;

class User extends Authenticatable
{
    use HasFactory;
    use HasLectern;

    protected $guarded = [];

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
