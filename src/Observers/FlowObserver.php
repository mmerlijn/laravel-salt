<?php

namespace mmerlijn\LaravelSalt\Observers;


use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowLog;


class FlowObserver
{
    public function updating(Flow $flow): void
    {
        if ($flow->isDirty('app_error_id') && !$flow->app_error_id) {
            $flow->attempts = 0;
            $flow->try_after = Carbon::now()->subSecond();
        }
    }

    public function deleting(Flow $flow): void
    {
        FlowLog::create([
            'type' => $flow->type,
            'patient_id' => $flow->patient_id,
            'request_nr' => $flow->request_nr,
            'labtrain_id' => $flow->labtrain_id,
            'response_at' => $flow->response_at,
            'request_at' => $flow->request_at,
            'payload_id' => $flow->payload_id,
            'payload_type' => $flow->payload_type,
            'attempts' => $flow->attempts,
        ]);
    }

    public function deleted(Flow $flow): void
    {
        //more actions needed
        $flow->error?->delete();
        $flow->errors()->delete();
    }

}
