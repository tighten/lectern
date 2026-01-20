<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thread_id' => $this->thread_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'reactions_count' => $this->whenCounted('reactions'),
            'replies_count' => $this->whenCounted('replies'),
            'reactions' => $this->when(
                $this->relationLoaded('reactions'),
                fn () => $this->reactions
                    ->groupBy('type')
                    ->map(fn ($group, $type) => ['type' => $type, 'count' => $group->count()])
                    ->values()
            ),
            'user_reaction' => $this->when(
                $request->user() && $this->relationLoaded('reactions'),
                fn () => $this->reactions->where('user_id', $request->user()?->id)->first()?->type
            ),
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => PostResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
