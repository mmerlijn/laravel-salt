<?php

namespace mmerlijn\LaravelSalt\Models\Traits;


use mmerlijn\LaravelSalt\Models\AccessLog;

trait AccessLogsTrait
{
    public function accessLogs()
    {
        return $this->morphMany(AccessLog::class, 'loggable');
    }
}
