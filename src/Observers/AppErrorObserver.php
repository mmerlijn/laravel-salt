<?php

namespace mmerlijn\LaravelSalt\Observers;


use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Notifications\AppErrorNotification;

class AppErrorObserver
{

    public function created(AppError $appError):void{
        $level = $appError->level?->value ?? $appError->level ?? null;

        if($appError->notify){
            foreach (config('laravel_salt.notify.'.$level, []) as $toNotify) {
                if(is_int($toNotify)){
                    $user = config('auth.providers.users.model');
                    if ($user && class_exists($user) && Schema::hasTable((new $user)->getTable()) && ($foundUser = $user::find($toNotify))) {
                        $foundUser->notify(new AppErrorNotification($appError));
                    }
                }else{
                    //email adres
                    Notification::route('mail', $toNotify)->notify(new AppErrorNotification($appError));
                }
            }
        }
    }
    public function deleted(AppError $appError): void
    {
        //more actions needed
        $appError->flows()?->update(['app_error_id' => null]);
    }
}
