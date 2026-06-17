<?php

namespace mmerlijn\LaravelSalt\Http\Resources\Patient;


use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterResource;
use mmerlijn\LaravelSalt\Models\Patient;

/** @mixin Patient
 */
class PatientFullResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->toArray(),
            'address' => $this->address->toArray(),
            'requester' => $this->when($this->last_requester, RequesterResource::make($this->requester)->resolve()),
            'organization' => $this->organization ? RequesterResource::make($this->organization) : null,
            'gp' => $this->general_practitioner ? RequesterResource::make($this->gp) : null,
            'general_practitioner' => $this->general_practitioner,
            'last_requester' => $this->last_requester,
            'dob' => $this->dob->format('d-m-Y'),
            'phone' => (string)$this->phone,
            'phone2' => (string)$this->phone2,
            'phone_note' => $this->phone_note,
            'email' => $this->email,
            'email_ext' => $this->email_ext,
            'notes' => '', //TODO
            'uzovi' => $this->uzovi,
            'policy_nr' => $this->policy_nr,
            'labels' => $this->labels,
            'bsn' => auth()?->user()->can('patient_bsn') ? $this->bsn : 'geen toegang',
            'sex' => $this->sex->value,
            'deceased' => $this->deceased,
            'labtrain_id' => $this->labtrain_id,
            'contact_id' => $this->contact_id,
        ];
    }
}
