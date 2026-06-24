<?php

namespace mmerlijn\LaravelSalt\Observers;


use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use mmerlijn\LaravelSalt\Models\FlowError;
use mmerlijn\LaravelSalt\Notifications\FlowErrorNotification;

class FlowErrorObserver
{

    public function created(FlowError $FlowError): void
    {
        $level = $FlowError->level?->value ?? $FlowError->level ?? null;

        if ($FlowError->notify) {
            foreach (config('laravel_salt.notify.' . $level, []) as $toNotify) {
                if (is_int($toNotify)) {
                    $user = config('auth.providers.users.model');
                    if ($user && class_exists($user) && Schema::hasTable((new $user)->getTable()) && ($foundUser = $user::find($toNotify))) {
                        $foundUser->notify(new FlowErrorNotification($FlowError));
                    }
                } else {
                    //email adres
                    Notification::route('mail', $toNotify)->notify(new FlowErrorNotification($FlowError));
                }
            }
        }
    }

    public function deleted(FlowError $FlowError): void
    {
        //more actions needed
        $FlowError->flows()?->update(['flow_error_id' => null]);
    }
}
