<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Patient;


use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Patient;

/** @mixin Patient */
class PatientNameResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->toArray(),
            'dob' => $this->dob->format('Y-m-d'),
            'sex' => $this->sex->value,
            'deceased' => $this->deceased,
        ];
    }
}
