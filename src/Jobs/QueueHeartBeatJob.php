<?php

namespace mmerlijn\LaravelSalt\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;

class QueueHeartBeatJob implements ShouldQueue
{
    public function handle(): void
    {
// Sla de huidige tijd op in de cache
        cache()->put('queue_last_heartbeat', now(), now()->addMinutes(5));
    }
}