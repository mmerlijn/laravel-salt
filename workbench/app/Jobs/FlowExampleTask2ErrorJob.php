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

class FlowExampleTask2ErrorJob implements ShouldQueue
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
        return 'flow-2-' . $this->flow->id;
    }

    public function handle(): void
    {
        try {
            $patient = $this->flow->from;
            $patient->not_existing_field_id = 12345;
            $patient->save();
            $this->flow->done(self::class);
        } catch (\Exception|\Error $e) {
            $this->flow->fail(new Error(
                level: ErrorLevelEnum::MENNO,
                fromObject: $patient,
                exception: $e,
                solution: "Zorg dat dit gaat werken :)",
                notify: true,
                erroredClass: self::class,
            )->store());
        }
    }
}
