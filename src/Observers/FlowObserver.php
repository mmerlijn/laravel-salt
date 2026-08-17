<?php

namespace mmerlijn\LaravelSalt\Observers;


use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Models\Flow;


class FlowObserver
{
    public function updating(Flow $flow): void
    {
        if ($flow->isDirty('app_error_id') && !$flow->app_error_id) {
            $flow->attempts = 0;
            $flow->try_after = Carbon::now()->subSecond();
        }
    }


    public function deleted(Flow $flow): void
    {
        //more actions needed
        $flow->error?->delete();
        $flow->errors()->delete();
    }

}
