<?php

namespace mmerlijn\LaravelSalt\Helpers;

use Cache;

class QueueHeartBeat
{

    public function __construct()
    {

    }

    public function online(): bool
    {
        $lastHeartbeat = Cache::get('queue_last_heartbeat');

        // Als er langer dan 3 minuten geen heartbeat is geweest, is de worker waarschijnlijk stuk
        return $lastHeartbeat && $lastHeartbeat->diffInMinutes(now()) < 3;
    }
}