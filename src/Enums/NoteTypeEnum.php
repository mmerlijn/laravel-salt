<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum NoteTypeEnum: string
{
    use StringEnumTrait;

    case PHONE = 'PHONE';
    case EMAIL = 'EMAIL';
    case FAX = 'FAX';
    case NO_RESPONSE = 'NO_RESPONSE';
    case NO_SHOW = 'NO_SHOW';
    case NOTE = 'NOTE';
    case TELEDIA = 'TELEDIA';
    case _ = "_";
    case WRONG_PHONE = 'WRONG_PHONE';

    public function label(): string
    {
        return match ($this) {
            NoteTypeEnum::PHONE => 'Telefoon',
            NoteTypeEnum::EMAIL => 'E-mail',
            NoteTypeEnum::FAX => 'Fax',
            NoteTypeEnum::NO_RESPONSE => 'Geen gehoor',
            NoteTypeEnum::NO_SHOW => 'No show',
            NoteTypeEnum::NOTE => 'Notitie',
            NoteTypeEnum::TELEDIA => 'PacsOnWeb opmerking',
            NoteTypeEnum::WRONG_PHONE => 'Verkeerd telefoonnr',
            NoteTypeEnum::_ => '',
        };
    }

    public static function labels(): array
    {
        return [
            'PHONE' => 'Telefoon',
            'EMAIL' => 'E-mail',
            'FAX' => 'Fax',
            'NO_RESPONSE' => 'Geen gehoor',
            'NO_SHOW' => 'No show',
            'NOTE' => 'Notitie',
            'TELEDIA' => 'PacsOnWeb opmerking',
            'WRONG_PHONE' => 'Verkeerd telefoonnr',
            '_' => '',
        ];
    }
}
