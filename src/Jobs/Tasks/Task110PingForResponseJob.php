<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

class Task110PingForResponseJob extends TaskJob
{

    public function handle(): void
    {
        if ($this->flow->response_at) {
            $this->flow->done(self::class); //volgende stap
            return;
        }
        $this->flow->fail(); //gaat later opnieuw kijken

    }

}
