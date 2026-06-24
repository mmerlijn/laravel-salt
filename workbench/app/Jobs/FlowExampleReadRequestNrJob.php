<?php

namespace Workbench\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJobTrait;

class FlowExampleReadRequestNrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TaskJobTrait;

    public function handle(): void
    {
        try {
            $this->flow->request_nr = "ZD123456789";
            $this->flow->save();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(
                exception: $e,
                solution: "Zorg dat dit gaat werken :)",
                errorClass: self::class,
            );
        }
    }
}
