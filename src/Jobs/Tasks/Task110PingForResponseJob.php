<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use mmerlijn\LaravelSalt\Models\Flow;

class Task110PingForResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $maxExceptions = 0;

    public int $uniqueFor = 60;

    public function uniqueVia(): Repository
    {
        return Cache::driver('database');
    }

    public function __construct(public Flow $flow)
    {
    }

    public function uniqueId(): string
    {
        return 'flow-' . $this->flow->task . '-' . $this->flow->id;
    }

    public function handle(): void
    {

        if ($this->flow->exchange_id) {
            if ($this->flow->exchange->response_at) {
                $this->flow->done(self::class); //volgende stap
                $this->flow->exchange->delete(); //niet meer nodig
                return;
            }
            $this->flow->retry(); //gaat later opnieuw kijken
        } else {
            //Deze stap is waarschijnlijk niet nodig
            $this->flow->done(self::class);
            logger()->error("Geen exchange_id bij flow, waarschijnlijk niet nodig (ping)");
        }

    }

}
