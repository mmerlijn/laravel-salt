<?php

namespace Workbench\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Models\Flow;

class FlowExampleTaskExchangeJob implements ShouldQueue
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
        return 'flow-request-1-' . $this->flow->id;
    }

    public function handle(): void
    {
        try {
            $request = $this->flow->payload->request;
            $this->flow->payload->patient_id = $request;
            $this->flow->payload->response = "Gedaan";
            $this->flow->payload->response_at = now();
            $this->flow->payload->save();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(new Error(
                level: ErrorLevelEnum::SYSTEEMBEHEER,
                fromObject: $this->flow->payload,
                exception: $e,
                notify: true,
                erroredClass: self::class,
            )->store());
        }
    }
}
