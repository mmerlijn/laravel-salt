<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\LaravelSalt\Models\Patient;
use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum NoteSubjectEnum: string
{
    use StringEnumTrait;

case patient = 'patient';
case request = 'request';
case appointment = 'appointment';
case requester = 'requester';
case organization = 'organization';


    public function label(): string
    {
        return match ($this) {
            NoteSubjectEnum::patient => 'patient',
            NoteSubjectEnum::request => 'request',
            NoteSubjectEnum::appointment => 'appointment',
            NoteSubjectEnum::requester => 'requester',
            NoteSubjectEnum::organization => 'organization',
        };
    }
    public function table(): string
    {
        return match ($this) {
            NoteSubjectEnum::patient => new Patient()->getTable(),
            NoteSubjectEnum::request => $this->label(),
            NoteSubjectEnum::appointment => $this->label(),
            NoteSubjectEnum::requester => $this->label(),
            NoteSubjectEnum::organization => $this->label(),
        };
    }

}
