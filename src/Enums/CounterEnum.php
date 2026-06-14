<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum CounterEnum: string
{
    use StringEnumTrait;

    case SALT_REQUEST_NR = 'SALT_REQUEST_NR';
    case POCT = 'POCT';
    case DWH_MSG = 'DWH_MSG';

    public function label(): string
    {
        return match ($this) {
            CounterEnum::SALT_REQUEST_NR => 'SALT_REQUEST_NR',
            CounterEnum::POCT => 'POCT',
            CounterEnum::DWH_MSG => 'DWH MSG',
        };
    }

    public static function labels(): array
    {
        return [
            CounterEnum::SALT_REQUEST_NR->value => CounterEnum::SALT_REQUEST_NR->label(),
            CounterEnum::POCT->value => CounterEnum::POCT->label(),
            CounterEnum::DWH_MSG->value => CounterEnum::DWH_MSG->label(),
        ];
    }
}
