<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Name;

trait NameModelTrait
{
    protected function name(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => new Name(
                initials: $attributes['initials'] ?? "",
                firstname: $attributes['firstname'] ?? "",
                lastname: $attributes['lastname'] ?? "",
                prefix: $attributes['prefix'] ?? "",
                own_lastname: $attributes['own_lastname'] ?? "",
                own_prefix: $attributes['own_prefix'] ?? "",
                sex: PatientSexEnum::set($attributes['sex'] ?? ""),
            ),
            set: function (Name $name) {
                $name->format();
                return [
                    'initials' => $name->initials ?: null,
                    'lastname' => $name->lastname ?: null,
                    'prefix' => $name->prefix ?: null,
                    'own_lastname' => $name->own_lastname ?: null,
                    'own_prefix' => $name->own_prefix ?: null,
                    'sex' => $name->sex->value,
                ];
            },
        );
    }
}