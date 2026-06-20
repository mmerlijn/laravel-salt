<?php

namespace mmerlijn\LaravelSalt\Observers;



use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;

class FlowObserver
{

    public function deleted(Flow $flow): void
    {
        //more actions needed
        $flow->error?->delete();
    }
}
