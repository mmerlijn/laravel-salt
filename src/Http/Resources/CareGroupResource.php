<?php

namespace mmerlijn\LaravelSalt\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\CareGroup;

/** @mixin CareGroup */
class CareGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'agbcode' => $this->agbcode,
            'requester_type' => $this->requester_type,
            'care_group' => $this->care_group,
            'test_type' => $this->test_type,
            'requester' => $this->requester->toResource(),
        ];
    }
}
