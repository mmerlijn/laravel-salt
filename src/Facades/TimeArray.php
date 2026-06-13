<?php

namespace mmerlijn\LaravelSalt\Facades;

use Illuminate\Support\Facades\Facade;

class TimeArray extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'timeArray';
    }
}
