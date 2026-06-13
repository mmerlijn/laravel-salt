<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\LaravelSalt\Models\Patient;
use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum LockTypeEnum: string
{
    use StringEnumTrait;

    case appointmentCreation = 'appointment-creation';
    case activity = 'activity';
    case activityGroup = 'activity-group';
    case close = 'close';
    case planning = 'planning';
    case location = 'location';
    case room = 'room';
    case otherActivity = 'other-activity';
    case label = 'label';
    case email = 'email';
    case manualPatient = 'manual-patient';
    case test = 'test';
    case employee = 'employee';
    case patient = 'patient';
    case request = 'request';
    case appointment = 'appointment';
    case requester = 'requester';
    case organization = 'organization';


    public function label(): string
    {
        return match ($this) {
            LockTypeEnum::patient => 'patient',
            LockTypeEnum::request => 'request',
            LockTypeEnum::appointment => 'appointment',
            LockTypeEnum::requester => 'requester',
            LockTypeEnum::organization => 'organization',
            LockTypeEnum::otherActivity => 'other-activity',
            LockTypeEnum::label => 'label',
            LockTypeEnum::email => 'email',
            LockTypeEnum::manualPatient => 'manual-patient',
            LockTypeEnum::test => 'test',
            LockTypeEnum::employee => 'employee',
            LockTypeEnum::close => 'close',
        };
    }

    public function table(): string
    {
        return match ($this) {
            LockTypeEnum::patient => new Patient()->getTable(),
            LockTypeEnum::request => self::label(),
            LockTypeEnum::appointment => self::label(),
            LockTypeEnum::requester => self::label(),
            LockTypeEnum::organization => self::label(),
        };
    }

}
