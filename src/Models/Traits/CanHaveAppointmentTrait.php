<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait CanHaveAppointmentTrait
{
    public function appointments(): ?MorphMany
    {
        if(config('laravel_salt.classes.appointment')) {
            return $this->morphMany(config('laravel_salt.classes.appointment'), 'owner');
        }
         return null;
    }

    public function appointmentCreation(): ?MorphOne
    {
        if(config('laravel_salt.classes.appointmentCreation')) {
            return $this->morphOne(config('laravel_salt.classes.appointmentCreation'), 'owner')->latestOfMany();
        }
         return null;
    }
}
