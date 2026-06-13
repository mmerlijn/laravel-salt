<?php

namespace mmerlijn\LaravelSalt\Facades;


class Distance extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'distance';
    }
}