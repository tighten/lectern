# Lectern

A headless forum API package for Laravel.

## Requirements

- PHP 8.2+
- Laravel 12.0+

## Installation

```bash
composer require tightenco/lectern
```

Run the install command to publish the configuration and migrations:

```bash
php artisan lectern:install
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=lectern-config
```

### Available Options

```php
return [
    'prefix' => 'lectern',
    'middleware' => ['api'],
    'auth_middleware' => 'auth:sanctum',

    'threading' => [
        'mode' => 'flat',
        'max_depth' => 3,
    ],

    'user' => [
        'model' => 'App\\Models\\User',
        'display_name_attribute' => 'name',
    ],

    'reactions' => [
        'enabled' => true,
        'types' => ['like', 'love', 'laugh', 'wow', 'sad', 'angry'],
    ],

    'mentions' => [
        'enabled' => true,
        'pattern' => '/@([a-zA-Z0-9_]+)/',
    ],

    'search' => [
        'driver' => 'database',
    ],

    'pagination' => [
        'threads' => 20,
        'posts' => 15,
    ],
];
```

## User Model Setup

Add the `HasLectern` trait to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tightenco\Lectern\Traits\HasLectern;

class User extends Authenticatable
{
    use HasLectern;
}
```

This provides the following relationships and methods:

- `lecternThreads()` - User's threads
- `lecternPosts()` - User's posts
- `lecternReactions()` - User's reactions
- `lecternSubscriptions()` - User's subscriptions
- `lecternMentions()` - User's mentions
- `lecternBan()` - User's ban record
- `isBannedFromLectern()` - Check if user is banned
- `banFromLectern($reason, $expiresAt, $bannedById)` - Ban the user
- `unbanFromLectern()` - Remove ban

## API Endpoints

All endpoints are prefixed with `/lectern` by default.

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/categories` | List all categories |
| GET | `/categories/{category}` | Show a category |
| GET | `/categories/{category}/threads` | List threads in a category |
| GET | `/threads` | List all threads |
| GET | `/threads/{thread}` | Show a thread |
| GET | `/threads/{thread}/posts` | List posts in a thread |
| GET | `/posts/{post}` | Show a post |
| GET | `/posts/{post}/replies` | List replies to a post |
| GET | `/search` | Search threads and posts |

### Authenticated Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/categories/{category}/threads` | Create a thread |
| PUT | `/threads/{thread}` | Update a thread |
| DELETE | `/threads/{thread}` | Delete a thread |
| POST | `/threads/{thread}/lock` | Lock a thread |
| POST | `/threads/{thread}/unlock` | Unlock a thread |
| POST | `/threads/{thread}/pin` | Pin a thread |
| POST | `/threads/{thread}/unpin` | Unpin a thread |
| POST | `/threads/{thread}/posts` | Create a post |
| PUT | `/posts/{post}` | Update a post |
| DELETE | `/posts/{post}` | Delete a post |
| POST | `/posts/{post}/reactions` | Add a reaction |
| DELETE | `/posts/{post}/reactions/{type}` | Remove a reaction |
| GET | `/subscriptions` | List user's subscriptions |
| POST | `/threads/{thread}/subscribe` | Subscribe to a thread |
| DELETE | `/threads/{thread}/subscribe` | Unsubscribe from a thread |
| POST | `/categories/{category}/subscribe` | Subscribe to a category |
| DELETE | `/categories/{category}/subscribe` | Unsubscribe from a category |

## Search

Lectern supports two search drivers:

### Database Driver (Default)

Uses SQL LIKE queries for searching. No additional setup required.

### Scout Driver

For more advanced search capabilities, set the driver to `scout` in the config and install Laravel Scout with your preferred driver.

```php
'search' => [
    'driver' => 'scout',
],
```

## Events

Lectern dispatches events for major actions:

- `ThreadCreated`
- `ThreadUpdated`
- `ThreadDeleted`
- `ThreadLocked`
- `ThreadUnlocked`
- `PostCreated`
- `PostUpdated`
- `PostDeleted`

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
