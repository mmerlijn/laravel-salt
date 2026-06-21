<?php

namespace mmerlijn\LaravelSalt\Observers;


use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowLog;

class FlowObserver
{

    public function creating(Flow $flow): void
    {
        if (!$flow->try_after) {
            $flow->try_after = now()->subMinute();
        }
    }


    public function deleting(Flow $flow): void
    {
        FlowLog::create([
            'type' => $flow->type,
            'request' => $flow->request,
            'response' => $flow->response,
            'patient_id' => $flow->patient_id,
            'request_nr' => $flow->request_nr,
            'labtrain_id' => $flow->labtrain_id,
            'response_at' => $flow->response_at,
            'request_at' => $flow->request_at,
            'payload_id' => $flow->payload_id,
            'payload_type' => $flow->payload_type,
        ]);
        if (!$flow->payload_id) {
            return;
        }
        if ($flow->payload instanceof AppError) {
            $flow->payload->delete();
            return;
        }
    }

    public function deleted(Flow $flow): void
    {
        //more actions needed
        $flow->error?->delete();
    }

}
