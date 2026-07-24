<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum CareGroupEnum: string
{
    use StringEnumTrait;

    case SEZ = 'SEZ';
    case ROHA = "ROHA";
    case SAG = "SAG";
    case HONK = "HONK";
    case _ = "_";
    case ARTS_EN_ZORG = "ARTS_EN_ZORG";
    case ZORG_VOOR_ZUID = 'ZORG_VOOR_ZUID';

    public function label(): string
    {
        return match ($this) {
            CareGroupEnum::SEZ => 'SEZ',
            CareGroupEnum::ROHA => 'ROHA',
            CareGroupEnum::SAG => 'SAG',
            CareGroupEnum::HONK => 'HONK',
            CareGroupEnum::ARTS_EN_ZORG => 'Arts en Zorg',
            CareGroupEnum::ZORG_VOOR_ZUID => 'Zorg voor Zuid',
            CareGroupEnum::_ => ''
        };
    }

    public static function labels(): array
    {
        return [
            'SEZ' => 'SEZ',
            'ROHA' => 'ROHA',
            'SAG' => 'SAG',
            'HONK' => 'HONK',
            'ARTS_EN_ZORG' => 'Arts en Zorg',
            'ZORG_VOOR_ZUID' => 'Zorg voor Zuid',
            "_" => "",
        ];
    }
}
