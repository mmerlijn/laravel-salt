<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Tool;

use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Uzovi;

/** @mixin Uzovi
 */
class UzoviResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'active_from' => $this->active_from?->format('Y-m-d'),
            'active_to' => $this->active_to?->format('Y-m-d'),
            'website' => $this->website,
            'note' => $this->note,
        ];
    }
}
