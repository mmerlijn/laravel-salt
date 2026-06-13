<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Requester;

use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Organization;

/** @mixin Organization */
class OrganizationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'address' => $this->city ? $this->address->toArray() : '',
            'agbcode' => $this->agbcode,
            'phone' => $this->phone ? (string)$this->phone : '',
            'email' => $this->email ?: '',
            'fax' => $this->fax ?: '',
            'postbus' => $this->postbus ? $this->postbus : '',
            'extra_address_line' => $this->extra_address_line ? $this->extra_address_line : '',
            'is_gp' => $this->is_gp->value,
            'ended_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : '',
            'requesters' => RequesterResource::collection($this->requesters),
            //TODO add relations
        ];
    }
}
