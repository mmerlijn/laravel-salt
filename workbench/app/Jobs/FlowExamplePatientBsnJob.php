<?php

namespace Workbench\App\Jobs;

use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJob;


class FlowExamplePatientBsnJob extends TaskJob
{

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
