<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'banned_by_id' => $this->banned_by_id,
            'reason' => $this->reason,
            'expires_at' => $this->expires_at,
            'is_permanent' => $this->isPermanent(),
            'is_expired' => $this->isExpired(),
            'user' => new UserResource($this->whenLoaded('user')),
            'banned_by' => new UserResource($this->whenLoaded('bannedBy')),
            'created_at' => $this->created_at,
        ];
    }
}
