<?php

namespace mmerlijn\LaravelSalt\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\FlowError;

/** @mixin FlowError */
class FlowErrorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'level' => $this->level?->value ?? $this->level,
            'from_type' => $this->from_type,
            'from_id' => $this->from_id,
            'at_type' => $this->at_type,
            'at_id' => $this->at_id,
            'class' => $this->class,
            'exception_class' => $this->exception_class,
            'solution' => $this->solution,
            'message' => $this->message,
            'trace' => $this->trace,
            'notify' => (bool) $this->notify,
            'notified' => $this->notified,
            'flows' => FlowResource::collection($this->whenLoaded('flows')),
        ];
    }
}
