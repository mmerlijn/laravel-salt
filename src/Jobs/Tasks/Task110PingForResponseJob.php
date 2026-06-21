<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Task110PingForResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TaskJobTrait;

    public function handle(): void
    {
        if ($this->flow->response_at) {
            $this->flow->done(self::class); //volgende stap
            return;
        }
        $this->flow->retry(); //gaat later opnieuw kijken

    }

}
