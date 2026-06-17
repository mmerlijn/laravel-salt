<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Patient;
use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Patient;

/** @mixin Patient */
class PatientContactResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->toArray(),
            'address' => $this->address->toArray(),
            'dob' => $this->dob->format('Y-m-d'),
            'phone' => (string)$this->phone,
            'phone2' => (string)$this->phone2,
            'email' => $this->email,
            'email_ext' => $this->email_ext,
            'notes' => '', //TODO
            'labels' => $this->labels,
            'sex' => $this->sex->value,
            'deceased' => $this->deceased,
            'labtrain_id' => $this->labtrain_id,
            'contact_id' => $this->contact_id,
        ];
    }
}
