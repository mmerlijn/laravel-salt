<?php

namespace mmerlijn\LaravelSalt\Observers;


use mmerlijn\LaravelSalt\Jobs\GetCaregiverJob;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Enums\VektisType;

class RequesterObserver
{

    public function creating(Requester $requester): void
    {
    }

    public function created(Requester $requester): void
    {
        if (config('laravel_salt.vektis', false)) {
            GetCaregiverJob::dispatch(VektisType::ZORGVERLENER, $requester->agbcode);
        }
    }

    public function updating(Requester $requester): void
    {
    }

    public function deleting(Requester $requester)
    {
    }
}
