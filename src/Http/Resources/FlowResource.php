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
            'active' => $this->active,
            'stack' => $this->stack,
            'attempts' => $this->attempts,
            'try_after' => $this->try_after?->toDateTimeString(),
            'flow_error_id' => $this->flow_error_id,
            'payload_type' => $this->payload_type,
            'payload_id' => $this->payload_id,
            'payload' => $this->payload?->toResource(),
            'error' => $this->whenLoaded('error', fn() => $this->error->toResource()),
            'response' => $this->response,
            'request' => $this->request,
            'response_at' => $this->response_at,
            'request_at' => $this->request_at,
            'response_type' => $this->response_type,
            'request_type' => $this->request_type,
            'data' => $this->data,
            'request_nr' => $this->request_nr,
            'patient_id' => $this->patient_id,
            'labtrain_id' => $this->labtrain_id,
        ];
    }
}
