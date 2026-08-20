<?php

namespace mmerlijn\LaravelSalt\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\FlowError;

/** @mixin FlowError
 */
class FlowErrorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'level' => $this->level,
            'from_type' => $this->from_type,
            'from_id' => $this->from_id,
            'at_type' => $this->at_type,
            'at_id' => $this->at_id,
            'from' => $this->from?->toResource(),
            'at' => $this->at?->toResource(),
            'class' => str($this->class)->afterLast('\\')->toString(),
            'solution' => $this->solution,
            'message' => $this->message,
            'trace' => $this->trace,
            'notify' => (bool)$this->notify,
            'notified' => $this->notified,
            'flow' => FlowResource::make($this->whenLoaded('flow')),
            //'flow' => $this->flow->toResource(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'flow_id' => $this->flow_id,
        ];
    }
}
