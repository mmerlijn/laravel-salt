<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum PatientActionsEnum: string
{
    use StringEnumTrait;
    case APPOINTMENT = "appointment";
    case TEST = "test";
    case EMAIL = "email";
    case REQUEST = "request";
    case SMS = 'sms';
    case NO_SHOW = 'now_show';
    case CHANGE = "change";

    public function label(): string
    {
        return match($this) {
            PatientActionsEnum::APPOINTMENT => 'Afspraak',
            PatientActionsEnum::TEST => 'Test',
            PatientActionsEnum::EMAIL => 'Email',
            PatientActionsEnum::REQUEST => 'Aanvraag',
            PatientActionsEnum::SMS => 'Sms',
            PatientActionsEnum::NO_SHOW => 'No show',
            PatientActionsEnum::CHANGE => 'Wijziging'
        };
    }
    public static function labels(): array
    {
        return [
            'APPOINTMENT' => 'Afspraak',
            'TEST' => 'Test',
            'EMAIL' => 'Email',
            'REQUEST' => 'Aanvraag',
            'SMS' => 'Sms',
            'NO_SHOW' => 'No show'
        ];
    }

}
