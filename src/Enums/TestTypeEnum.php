<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum TestTypeEnum: string
{
    use StringEnumTrait;

    case LAB = "LAB";
    case TD = "TD";
    case NIPT = "NIPT";
    case FUNDUS = "FUNDUS";
    case SPIRO = "SPIRO";
    case DEXA = "DEXA";
    case ECG = "ECG";
    case EVENT = "EVENT";
    case EAI = "EAI";
    case FAKE = "FAKE";

    case RR = "RR";
    case ECHO = "ECHO";
    case X = "X";
    case IECG = "IECG";
    case ECHOH = "ECHOH";
    case ECHOG = "ECHOG";

    public function label(): string
    {
        return match ($this) {
            TestTypeEnum::NIPT => 'Nipt',
            TestTypeEnum::FUNDUS => 'Fundus',
            TestTypeEnum::SPIRO => 'Spirometrie',
            TestTypeEnum::DEXA => 'Dexa',
            TestTypeEnum::ECG => 'ECG',
            TestTypeEnum::EVENT => 'Event',
            TestTypeEnum::EAI => 'Enkel arm index',
            TestTypeEnum::FAKE => 'Fake',
            TestTypeEnum::RR => '24RR',
            TestTypeEnum::ECHO => 'Echo algemeen',
            TestTypeEnum::X => 'Rontgen',
            TestTypeEnum::IECG => 'InspanningsECG',
            TestTypeEnum::ECHOH => 'Echo hart',
            TestTypeEnum::ECHOG => 'Echo gynaecologisch',
            TestTypeEnum::LAB => 'Laboratorium',
            TestTypeEnum::TD => 'Trombosedienst',

        };
    }

    public static function labels(): array
    {
        return [
            'NIPT' => 'Nipt',
            'FUNDUS' => 'Fundus',
            'SPIROMETRIE' => 'Spirometrie',
            'DEXA' => 'Dexa',
            'ECG' => 'ECG',
            'EVENT' => 'Event',
            'EAI' => 'Enkel arm index',
            'FAKE' => 'Fake',
            'RR' => '24RR',
            'ECHO' => 'Echo',
            'X' => 'Rontgen',
            'IECG' => 'InspanningsECG',
            'ECHOH' => 'Echo hart',
            'ECHOG' => 'Echo gynaecologisch',
            'LAB' => 'Laboratorium',
            'TD' => 'Trombosedienst',
        ];
    }
}
