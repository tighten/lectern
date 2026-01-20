<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'emoji' => $this->emoji,
            'order' => $this->order,
            'is_private' => $this->is_private,
            'is_admin_only' => $this->is_admin_only,
            'threads_count' => $this->whenCounted('threads'),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
