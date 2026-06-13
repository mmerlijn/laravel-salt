<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use mmerlijn\msgRepo\Address;

trait AddressModelTrait
{

    protected function address(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => new Address(
                postcode: $attributes['postcode'] ?? '',
                city: $attributes['city'] ?? '',
                street: $attributes['street'] ?? '',
                building: $attributes['building'] ?? ''),
            set: fn(Address $address) => [
                'street' => $address->street ?: null,
                'postcode' => $address->postcode ?: null,
                'city' => $address->city ?: null,
                'building' => $address->building ?: null
            ],
        );
    }
}