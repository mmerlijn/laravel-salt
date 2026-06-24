<?php

namespace mmerlijn\LaravelSalt\Observers;


use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowLog;


class FlowObserver
{


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
