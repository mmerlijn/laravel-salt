<?php

namespace mmerlijn\LaravelSalt\Actions;


use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Helpers\Traits\FormatTrait;
use mmerlijn\LaravelSalt\Models\Organization;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Name;


class FindOrCreateRequester
{
    use FormatTrait;

    public function __invoke(array|Organization $organization,array|Contact $data, bool $update = true): Requester
    {
        if ($data instanceof Contact) {
            $requesterArray = [
                'agbcode' => $data->agbcode,
                'initials' => $data->name->initials,
                'lastname' => $data->name->lastname,
                'prefix' => $data->name->prefix,
                'own_lastname' => $data->name->own_lastname,
                'own_prefix' => $data->name->own_prefix,
                'phone' => $data->phone?->number ?? null,
            ];
        } elseif (gettype($data) == 'array') {

            if ($data['name'] ?? null and $data['name'] instanceof Name) {
                $data = array_merge($data, $data['name']->toArray());
                unset($data['name']);
            }
            $requesterArray = $data;

        } else {

            throw new \Exception("INVALID data type");

        }

        // look for requester convertor
        if ($r = RequesterConvertor::requester($requesterArray['agbcode'])) {
            return $r;
        }
        $requesterArray = static::reformatInput($requesterArray);

        $v = Validator::make($requesterArray, [
            'agbcode' => 'required', //|size:8
        ]);

        $v->validate();

        return Caregiver::firstOrCreate([
            'agbcode' => $requesterArray['agbcode']
        ], $requesterArray);
    }

    //public function formatGPName(string $name): string
    //{
    //    if (str_starts_with($name, "Huisartsenpraktijk")) {
    //        //remove "Huisartsenpraktijk" from name
    //        return trim(str_replace("Huisartsenpraktijk", "", $name)) . ", HP";
    //    } elseif (str_starts_with($name, "Verloskundigen")) {
    //        return trim(str_replace("Verloskundigen", "", $name)) . ", Verloskundigen";
    //    } elseif (str_starts_with("Gezondheidscentrum", $name)) {
    //        return trim(str_replace("Gezondheidscentrum", "", $name)) . ", GC";
    //    } else {
    //        return $name;
    //    }
    //}
}
