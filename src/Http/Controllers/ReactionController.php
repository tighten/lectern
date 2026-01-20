<?php

namespace Tightenco\Lectern\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tightenco\Lectern\Http\Requests\StoreReactionRequest;
use Tightenco\Lectern\Http\Resources\ReactionResource;
use Tightenco\Lectern\Models\Post;

class ReactionController extends Controller
{
    public function store(StoreReactionRequest $request, Post $post): ReactionResource
    {
        $reaction = $post->reactions()->firstOrCreate([
            'user_id' => $request->user()->id,
            'type' => $request->input('type'),
        ]);

        return new ReactionResource($reaction);
    }

    public function destroy(Request $request, Post $post, string $type): JsonResponse
    {
        $post->reactions()
            ->where('user_id', $request->user()->id)
            ->where('type', $type)
            ->delete();

        return response()->json(null, 204);
    }
}
