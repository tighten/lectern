<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $displayNameAttribute = config('lectern.user.display_name_attribute', 'name');

        return [
            'id' => $this->id,
            'name' => $this->{$displayNameAttribute},
            'avatar_url' => method_exists($this->resource, 'getFirstMediaUrl')
                ? $this->getFirstMediaUrl('avatar', 'thumb') ?: null
                : null,
        ];
    }
}
