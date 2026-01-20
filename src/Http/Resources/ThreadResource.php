<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'is_pinned' => $this->is_pinned,
            'is_locked' => $this->is_locked,
            'posts_count' => $this->whenCounted('posts'),
            'latest_post_at' => $this->whenLoaded('latestPost', fn () => $this->latestPost?->created_at),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'first_post' => new PostResource($this->whenLoaded('firstPost')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
