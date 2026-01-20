<?php

namespace Tightenco\Lectern\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Lectern\Traits\HasLectern;

class LecternBanCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! in_array(HasLectern::class, class_uses_recursive($user))) {
            return $next($request);
        }

        if ($user->isBannedFromLectern()) {
            return response()->json([
                'message' => 'You are banned from the forum.',
            ], 403);
        }

        return $next($request);
    }
}
