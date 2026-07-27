<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Requester;

use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Enums\VektisType;

/** @mixin Requester */
class RequesterResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'vektis_name' => $this->vektis_name,
            'name' => $this->name->toArray(),
            'sex' => $this->sex ? $this->sex->value : '',
            'agbcode' => $this->agbcode,
            'phone' => $this->phone ? (string)$this->phone : '',
            'email' => $this->email ? $this->email : '',
            'fax' => $this->fax ? $this->fax : '',
            'ended_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : '',
            'address' => $this->address,
            'gp' => $this->is_gp?->value,
            'members' => $this->when($this->type != VektisType::ZORGVERLENER,
                $this->members?->toResourceCollection(RequesterNestedResource::class), null),
            'organizations' => $this->when($this->type == VektisType::ZORGVERLENER,
                $this->organizations?->toResourceCollection(RequesterNestedResource::class), null),

            //TODO add relations
        ];
    }


}
