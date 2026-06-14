<?php

namespace mmerlijn\LaravelSalt\Actions;


use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Helpers\Traits\FormatTrait;
use mmerlijn\LaravelSalt\Jobs\GetCaregiverJob;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\LaravelSalt\Models\RequesterConvertor;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Name;


class FindOrCreateRequester
{
    use FormatTrait;

    public function __invoke(array|Contact|\mmerlijn\msgRepo\Organization $data, bool $update = true): Requester
    {
        if ($data instanceof Contact) {
            $requesterArray = [
                'type' => VektisType::ZORGVERLENER,
                'agbcode' => $data->agbcode,
                'vektis_name'=> $data->name->getNameReverse(),
                'initials' => $data->name->initials,
                'lastname' => $data->name->lastname,
                'prefix' => $data->name->prefix,
                'own_lastname' => $data->name->own_lastname,
                'own_prefix' => $data->name->own_prefix,
                'phone' => $data->phone?->number ?? null,
            ];
        }elseif ($data instanceof \mmerlijn\msgRepo\Organization) {
            $requesterArray = [
                'type' => VektisType::ONDERNEMING,
                'agbcode' => $data->agbcode,
                'vektis_name' => $data->name,
                'phone' => $data->phone?->number ?? null,
                'postcode' => $data->address?->postcode,
                'building' => $data->address?->building,
                'street' => $data->address?->street,
                'postbus' => $data->address?->postbus,
                'city' => $data->address?->city,
            ];
        } elseif (gettype($data) == 'array') {

            if ($data['name'] ?? null and $data['name'] instanceof Name) {
                $data = array_merge($data, $data['name']->toArray());
                unset($data['name']);
            }
            if ($data['address'] ?? null and $data['address'] instanceof Address) {
                $data = array_merge($data, $data['address']->toArray());
                unset($data['address']);
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

        if($update){
            $r= Requester::updateOrCreate([
                'agbcode' => $requesterArray['agbcode']
            ], $requesterArray);
        }
        $r= Requester::firstOrCreate([
            'agbcode' => $requesterArray['agbcode']
        ], $requesterArray);

        if(config('laravel_salt.vektis',false)){
            GetCaregiverJob::dispatch($r->agbcode,$r->type);
        }
        return $r;
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
