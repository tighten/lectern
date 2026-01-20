<?php

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

    'response_format' => 'json',
];
