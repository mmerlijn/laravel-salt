<?php

namespace Workbench\App\Jobs;


use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJob;


class FlowExampleErrorJob extends TaskJob
{

    public function handle(): void
    {
        try {
            $patient = $this->flow->payload;
            $patient->not_existing_field_id = 12345;
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
