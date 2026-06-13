<?php

namespace mmerlijn\LaravelSalt\Actions;

use mmerlijn\LaravelSalt\Helpers\GetCaregiver;
use App\Enums\VektisType;
use App\Models\Tool\RequesterConvertor;
use App\Models\Vektis\Caregiver;
use App\Traits\FormatTrait;
use Illuminate\Support\Facades\Validator;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Name;
use mmerlijn\msgRepo\Organisation;

class FindOrCreateOrganization
{
    use FormatTrait;

    public function __invoke(array|Contact|Organisation $data, bool $update = true): Caregiver
    {
        if ($data instanceof Contact) {
            $requesterArray = [
                'agbcode' => $data->agbcode,
                'initials' => $data->name->initials,
                'lastname' => $data->name->lastname,
                'prefix' => $data->name->prefix,
                'own_lastname' => $data->name->own_lastname,
                'own_prefix' => $data->name->own_prefix,
                'postcode' => $data->address?->postcode,
                'building' => $data->address?->building,
                'street' => $data->address?->street,
                'postbus' => $data->address?->postbus,
                'city' => $data->address?->city,
                'phone' => $data->phone?->number ?? null,
            ];
        } elseif ($data instanceof Organisation) {
            $requesterArray = [
                'agbcode' => $data->agbcode,
                'own_lastname' => $data->name,
                'phone' => $data->phone?->number ?? null,
            ];
        } elseif (gettype($data) == 'array') {

            if ($data['name'] ?? null and $data['name'] instanceof Name) {
                $data = array_merge($data, $data['name']->toArray());
                unset($data['name']);
            }

            if ($data['address'] ?? null and $data['address'] instanceof Address) {
                $data['street'] = $data['address']->street;
                $data['building'] = $data['address']->building;
                $data['postcode'] = $data['address']->postcode;
                $data['city'] = $data['address']->city;
                $data['postbus'] = $data['address']->postbus;
                unset($data['address']);
            }
            $requesterArray = $data;

        } else {

            throw new \Exception("INVALID data type");

        }
        $caregiver = (new GetCaregiver)(VektisType::ZORGVERLENER, $requesterArray['agbcode'], true);
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
