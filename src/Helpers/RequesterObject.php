<?php

namespace mmerlijn\LaravelSalt\Helpers;

use mmerlijn\LaravelSalt\Models\Organization;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Enums\YesNoEnum;

class RequesterObject
{
    public ?Requester $requester=null;
    public ?Organization $organization=null;
    public ?Organization $gp=null;

    public function __construct(null|Organization|\mmerlijn\msgRepo\Organization $organization=null,null|Requester|Contact $requester=null){

        if($organization) {
            $this->organization = match ($organization::class) {
                Organization::class => $organization,
                \mmerlijn\msgRepo\Organization::class => Organization::firstOrCreate([
                    'agbcode' => $organization->agbcode,
                ], [
                    'name' => $organization->name,
                    'email' => $organization->email,
                    'phone' => $organization->phone,
                    'address' => $organization->address,
                ]),
            };
        }
        if($requester) {
            $this->requester = match ($requester::class) {
                Requester::class => $requester,

                Contact::class => Requester::add($requester)
            };
        }
        if($this->organization){
            if($this->organization->is_gp == YesNoEnum::YES){
                $this->gp = $this->organization;
            }
        }
        if($this->requester and $this->organization){
            try {
                $this->organization->requesters()->save($this->requester);
            }catch (\Exception|\Error $exception){
                logger()->error($exception->getMessage());
            }
        }
    }
}