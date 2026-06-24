<?php

namespace Workbench\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJobTrait;

class FlowExamplePatientBsnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TaskJobTrait;

    public function handle(): void
    {
        try {
            $request = $this->flow->request;
            $this->flow->payload->bsn = $request['bsn'] ?? null;
            $this->flow->payload->save();
            $this->flow->response = "Gedaan";
            $this->flow->response_at = now();
            $this->flow->save();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(
                exception: $e,
                solution: "Zorg dat dit gaat werken :)",
                errorLevel: 2,
                errorAt: $this->flow->payload,
                errorClass: self::class,
            );
        }
    }
}
