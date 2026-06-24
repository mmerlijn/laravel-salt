<?php

namespace Workbench\App\Jobs;


use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJob;


class FlowExampleChangeLabtrainIdJob extends TaskJob
{

    public function handle(): void
    {
        try {
            $patient = $this->flow->payload;
            $patient->labtrain_id = 54321;
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
