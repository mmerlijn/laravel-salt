<?php

namespace mmerlijn\LaravelSalt\Helpers;

class QueueHeartBeat
{

    public function __construct()
    {

    }

    //true = online, false = down
    public function online(): bool
    {
        $lastHeartbeat = cache('queue_last_heartbeat');

        // Als er langer dan 3 minuten geen heartbeat is geweest, is de worker waarschijnlijk stuk
        return $lastHeartbeat && $lastHeartbeat->diffInMinutes(now()) < 3;
    }

    public function serverCheck(): bool
    {
        $lastHeartbeat = cache('queue_last_heartbeat');

        // Als er langer dan 3 minuten geen heartbeat is geweest, is de worker waarschijnlijk stuk
        return $lastHeartbeat && $lastHeartbeat->diffInMinutes(now()) < 6;
    }
}