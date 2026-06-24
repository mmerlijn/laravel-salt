<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use mmerlijn\LaravelSalt\Models\Flow;

trait TaskJobTrait
{
    public int $tries = 1;

    public int $maxExceptions = 0;

    public function uniqueVia(): Repository
    {
        return Cache::driver('database');
    }

    public function __construct(public Flow $flow)
    {
    }

    public function uniqueId(): string
    {
        return 'flow-' . $this->flow->type . '-' . $this->flow->id;
    }
}