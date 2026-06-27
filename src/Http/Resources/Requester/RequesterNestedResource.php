<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Requester;

use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Requester;

/** @mixin Requester */
class RequesterNestedResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'vektis_name' => $this->vektis_name,
            'name' => $this->when(!!$this->own_lastname, $this->name->toArray()),
            'sex' => $this->sex ? $this->sex->value : '',
            'agbcode' => $this->agbcode,
            'phone' => $this->phone ? (string)$this->phone : '',
            'email' => $this->email ? $this->email : '',
            'fax' => $this->fax ? $this->fax : '',
            'ended_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : '',
            //TODO add relations
        ];
    }


}
