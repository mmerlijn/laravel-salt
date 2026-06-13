<?php

namespace mmerlijn\LaravelSalt\Observers;

use Illuminate\Support\Facades\Auth;
use mmerlijn\LaravelSalt\Enums\PatientActionsEnum;
use mmerlijn\LaravelSalt\Models\Patient;


class PatientObserver
{

    public function retrieved(Patient $patient): void
    {
        if (Auth::user()) {
            $patient->accessLogs()->create(['user_id' => Auth::user()->id]);
        }
    }

    public function creating(Patient $patient): void
    {
        if (!($patient->getAttributes()['phone'] ?? false) and ($patient->getAttributes()['phone2'] ?? false)) {
            $patient->phone = $patient->phone2;
            $patient->phone2 = null;
        }

    }


    public function updating(Patient $patient): void
    {
        if ($patient->phone2->number and !$patient->phone->number) {
            $patient->phone = $patient->phone2;
        }
        if ($patient->phone->number == $patient->phone2->number) {
            $patient->phone2 = null;
        }
        if ($patient->email_ext and !$patient->email) {
            $patient->email = $patient->email_ext;
            $patient->email_ext = null;
        } else if ($patient->email == $patient->email_ext) {
            $patient->email_ext = null;
        }

        if ($patient->deceased) {
            if(config('laravel_salt.application') =='agenda') {
                $patient->followups()?->update(['comment' => 'Overleden', 'stop' => now()]);
                //verwijder appointments
                $patient->appointments()?->each(fn($a) => $a->forceDelete());
                //verwijder requests
                $patient->requests()?->each(fn($r) => $r->delete());
                //verwijder tests
                $patient->tests()?->delete();
                //set patient in mijn.salt deceased
                if (config('laravel_salt.classes.mijnsaltContact')) {
                    config('laravel_salt.classes.mijnsaltContact')::where('patient_id', $patient->id)->update([
                        'overleden' => true,
                        'overleden_op' => now()
                    ]);
                }
            }
        }
    }

    public function updated(Patient $patient): void
    {
        $cols = ['sex', 'initials', 'lastname', 'own_lastname', 'own_prefix', 'prefix', 'bsn', 'postcode',
            'city', 'street', 'building_nr', 'last_requester','last_organization', 'general_practitioner',
            'phone', 'phone2', 'uzovi', 'policy_nr', 'lbsnr', 'labels', 'email', 'email_ext', 'labtrain_id'];

        $field_changed = [];
        foreach ($cols as $col) {
            if ($patient->isDirty($col)) {
                if ($patient->getAttributes()[$col] != $patient->getRawOriginal($col)) {
                    $field_changed[$col] = [
                        'new' => $patient->getAttributes()[$col],
                        'old' => $patient->getRawOriginal($col)
                    ];
                }

            }
        }
        if ($patient->isDirty('dob')) {
            $field_changed['dob'] = [
                'new' => $patient->dob->format('Y-m-d'),
                'old' => $patient->getOriginal('dob')->format('Y-m-d')
            ];
        }
        if(config('laravel_salt.classes.followup')) {
            if ($patient->isDirty('general_practitioner')) {
                config('laravel_salt.classes.followup')::where('patient_id', $patient->id)->update(['last_requester' => $patient->general_practitioner]);
            }
        }
        if (!empty($field_changed)) {
            $patient->actions()->create([
                "type" => PatientActionsEnum::CHANGE,
                "at" => now(),
                "comment" => "Wijziging door: " . (Auth::check() ? auth()->user()->name : 'web'),
                "detail" => $field_changed,
            ]);
        }
    }

    public function deleting(Patient $patient): void
    {
        //more actions needed
        $patient->appointments()?->delete();
    }
}
