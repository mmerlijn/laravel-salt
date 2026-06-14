<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Patient;



use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterResource;
use mmerlijn\LaravelSalt\Models\Patient;

/** @mixin Patient */
class PatientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->toArray(),
            'address' => $this->address->toArray(),
            'requester' => $this->when($this->last_requester, RequesterResource::make($this->requester)->resolve()), //RequesterResource::make($this->requester),
            'gp' => $this->general_practitioner ? RequesterResource::make($this->gp) : null,
            'last_requester' => $this->last_requester,
            'last_organization' => $this->last_organization,
            'general_practitioner' => $this->general_practitioner,
            'dob' => $this->dob->format('Y-m-d'),
            'age' => $this->dob->age,
            'phone' => (string)$this->phone,
            'phone2' => (string)$this->phone2,
            'phone_note' => $this->phone_note,
            'email' => $this->email,
            'email_ext' => $this->email_ext,
            'notes' => '', //TODO
            'uzovi' => $this->uzovi,
            'policy_nr' => $this->policy_nr,
            'labels' => $this->labels,
            'bsn' => $this->bsn,// auth()?->user()?->can('patient_bsn') ? $this->bsn : 'geen toegang',
            'has_bsn' => (bool)$this->bsn,
            'sex' => $this->sex->value,
            'lang' => $this->lang,
            'deceased' => (bool)$this->deceased,
        ];
    }
}
