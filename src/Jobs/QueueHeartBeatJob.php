<?php

namespace mmerlijn\LaravelSalt\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class QueueHeartBeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct()
    {
        //$this->onQueue('high');
    }

    public function handle(): void
    {

        // Sla de huidige tijd op in de cache
        cache()->put('queue_last_heartbeat', now(), now()->addMinutes(5));
    }
}