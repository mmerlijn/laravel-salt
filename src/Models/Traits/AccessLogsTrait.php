<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use App\Models\Log\AccessLog;

trait AccessLogsTrait
{
    public function accessLogs()
    {
        return $this->morphMany(AccessLog::class, 'loggable');
    }
}
