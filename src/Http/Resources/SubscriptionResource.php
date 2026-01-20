<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subscribable_type' => $this->subscribable_type,
            'subscribable_id' => $this->subscribable_id,
            'subscribable' => $this->whenLoaded('subscribable', function () {
                return match ($this->subscribable_type) {
                    'lectern_thread' => new ThreadResource($this->subscribable),
                    'lectern_category' => new CategoryResource($this->subscribable),
                    default => null,
                };
            }),
            'created_at' => $this->created_at,
        ];
    }
}
