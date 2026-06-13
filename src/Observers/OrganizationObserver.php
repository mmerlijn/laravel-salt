<?php

namespace mmerlijn\LaravelSalt\Observers;




use mmerlijn\LaravelSalt\Jobs\GetCaregiverJob;
use mmerlijn\LaravelSalt\Models\Organization;
use mmerlijn\msgRepo\Enums\VektisType;

class OrganizationObserver
{


    public function created(Organization $organization): void
    {
        if(config('laravel_salt.vektis', false)) {
            GetCaregiverJob::dispatch(VektisType::ONDERNEMING, $organization->agbcode);
        }
    }


}
