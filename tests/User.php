<?php

namespace Tightenco\Lectern\Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tightenco\Lectern\Traits\HasLectern;

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
