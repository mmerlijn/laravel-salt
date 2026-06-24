<?php

namespace Workbench\App\Jobs;


use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJob;


class FlowExampleReadRequestNrJob extends TaskJob
{


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
