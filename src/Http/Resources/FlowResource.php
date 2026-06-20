<?php

namespace mmerlijn\LaravelSalt\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Flow;

/** @mixin Flow */
class FlowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->value ?? $this->type,
            'stack' => $this->stack,
            'attempts' => $this->attempts,
            'try_after' => $this->try_after?->toDateTimeString(),
            'app_error_id' => $this->app_error_id,
            'payload_type' => $this->payload_type,
            'payload_id' => $this->payload_id,
            'error' => $this->whenLoaded('error', fn () => AppErrorResource::make($this->error)),
        ];
    }
}
