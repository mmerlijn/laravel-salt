<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Requester;

use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Requester;

/** @mixin Requester */
class RequesterResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
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
