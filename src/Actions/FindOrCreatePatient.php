<?php

namespace mmerlijn\LaravelSalt\Actions;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Helpers\Traits\FormatTrait;
use mmerlijn\LaravelSalt\Models\Patient;
use mmerlijn\LaravelSalt\Models\RequesterConvertor;
use mmerlijn\LaravelSalt\Rules\Bsn;
use mmerlijn\LaravelSalt\Rules\Dob;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Name;
use mmerlijn\msgRepo\Patient as PatientRepo;
use mmerlijn\laravelPostcode\Models\Postcode;

class FindOrCreatePatient
{
    use FormatTrait;

    public function __invoke(PatientRepo|array $data, $update = true): Patient
    {

        if ($data instanceof PatientRepo) {
            $patientArray = ['bsn' => $data->bsn,
                'sex' => $data->sex->value,
                'dob' => $data->dob,
                'initials' => $data->name->initials,
                'lastname' => $data->name->lastname,
                'prefix' => $data->name->prefix,
                'own_lastname' => $data->name->own_lastname,
                'own_prefix' => $data->name->own_prefix,
                'postcode' => $data->address->postcode,
                'building' => $data->address->building,
                'street' => $data->address->street,
                'city' => $data->address->city,
                'country' => $data->address->country,
                'uzovi' => $data->insurance?->uzovi,
                'policy_nr' => $data->insurance?->policy_nr,
                'last_requester' => $data->last_requester,
                'last_organization' => $data->last_organization,
                'general_practitioner' => $data->gp,
                'phone' => $data->phones[0]?->number ?? null,
                'phone2' => $data->phones[1]?->number ?? null,
                'email_ext' => $data->email ?? null
            ];


        } elseif (gettype($data) == "array") {

            if ($data['name'] ?? null and $data['name'] instanceof Name) {
                $data = array_merge($data, $data['name']->toArray());
                unset($data['name']);
            }

            if ($data['address'] ?? null and $data['address'] instanceof Address) {
                $data['street'] = $data['address']->street;
                $data['building'] = $data['address']->building;
                $data['postcode'] = $data['address']->postcode;
                $data['city'] = $data['address']->city;
                $data['country'] = $data['address']->country;
                unset($data['address']);
            }
            $patientArray = $data;

        } else {

            throw new \Exception("INVALID data type");

        }

        $patientArray['dob'] = ($patientArray['dob'] instanceof Carbon) ?
            $patientArray['dob']->format('Y-m-d') :
            $patientArray['dob'];

        $patientArray['sex'] = ($patientArray['sex'] instanceof PatientSexEnum) ?
            $patientArray['sex'] :
            PatientSexEnum::set($patientArray['sex']);

        $patientArray['bsn'] = (in_array($patientArray['bsn'] ?? null, ['000000000', '999999999', null]) ? //escape BSN
            null :
            $patientArray['bsn']);
        if ($patientArray['email_ext'] ?? null) {
            if (!filter_var($patientArray['email_ext'], FILTER_VALIDATE_EMAIL)) {
                $patientArray['email_ext'] = null;
            }
        }
        $patientArray = static::reformatInput($patientArray);

        //Validation on postcode for Dutch postcodes
        if (!($patientArray['country'] ?? null) or $patientArray['country'] == "NL") {
            $v = Validator::make($patientArray, ['postcode' => 'nullable|regex:/\b^[0-9]{4} ?[a-zA-Z]{2}$\b/']);
            if ($v->fails()) {
                //Try to fix invalid postcodes
                if ($postcode = Postcode::getPostcode($patientArray['city'], $patientArray['street'], $patientArray['building'])) {

                    $patientArray['postcode'] = $postcode;
                } elseif ($patientArray['bsn'] ?? null) {
                    //If we have a bsn, we can try to find the postcode in the database
                    $patient = Patient::whereBsn($patientArray['bsn'])
                        ->first();
                    if ($patient) {
                        similar_text($patient->postcode, $patientArray['postcode'], $p1);
                        similar_text($patient->street, $patientArray['street'], $p2);
                        if ($p1 >= 80 && $p2 >= 80) {
                            //If the postcode is not similar enough, we can use the postcode from the patient
                            $patientArray['postcode'] = $patient->postcode;
                            $patientArray['street'] = $patient->street;
                            $patientArray['building'] = $patient->building;
                            $patientArray['city'] = $patient->city;
                        }
                    }
                }
            }
            $v = Validator::make($patientArray, [
                'postcode' => 'nullable|regex:/\b^[0-9]{4} ?[a-zA-Z]{2}$\b/'
            ],
                ['postcode.regex' => 'Invalid Dutch postcode format found: ' . $patientArray['postcode'] ?? '']);
            $v->validate();
        }

        $v = Validator::make($patientArray, [
            'bsn' => [new Bsn],
            'dob' => ['required', new Dob],
            'own_lastname' => 'required|min:1',
        ]);
        $v->validate();
        if ($r = RequesterConvertor::requester($patientArray['last_requester'] ?? '')) {
            $patientArray['last_requester'] = $r->agbcode;
        }
        if(!$patientArray['last_requester']??null){
            unset($patientArray['last_requester']);
        }
        if(!$patientArray['last_organization']??null){
            unset($patientArray['last_organization']);
        }
        if(!$patientArray['general_practitioner']??null){
            unset($patientArray['general_practitioner']);
        }

        if ($patientArray['bsn'] ?? null) {

            return Patient::updateOrCreate(['bsn' => $patientArray['bsn']], $patientArray);

        } else {
            return Patient::updateOrCreate([
                'dob' => $patientArray['dob'],
                'sex' => $patientArray['sex']->value,
                'own_lastname' => $patientArray['own_lastname'],
                'postcode' => $patientArray['postcode'] ?? null,
                'bsn' => $patientArray['bsn'] ?? null
            ],
                $patientArray
            );
        }
    }
}
