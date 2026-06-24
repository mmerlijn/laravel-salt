<?php

namespace Workbench\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJobTrait;

class FlowExampleSetLabtrainIdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TaskJobTrait;

    public function handle(): void
    {
        try {
            $patient = $this->flow->payload;
            $patient->labtrain_id = 12345;
            $patient->save();
            $this->flow->done(self::class);
        } catch (\Exception|\Error $e) {
            $this->flow->fail(
                exception: $e,
                solution: "Zorg dat dit gaat werken :)",
                errorAt: $this->flow->payload,
                errorClass: self::class,
            );
        }
    }
}
