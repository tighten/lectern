<?php

namespace Tightenco\Lectern\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reporter_id' => $this->reporter_id,
            'reportable_type' => $this->reportable_type,
            'reportable_id' => $this->reportable_id,
            'reason' => $this->reason,
            'details' => $this->details,
            'status' => $this->status,
            'resolved_by_id' => $this->resolved_by_id,
            'resolution_notes' => $this->resolution_notes,
            'resolved_at' => $this->resolved_at,
            'reporter' => new UserResource($this->whenLoaded('reporter')),
            'reportable' => $this->whenLoaded('reportable'),
            'resolved_by' => new UserResource($this->whenLoaded('resolvedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
