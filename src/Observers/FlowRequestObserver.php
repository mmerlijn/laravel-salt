<?php

namespace mmerlijn\LaravelSalt\Observers;

use mmerlijn\LaravelSalt\Models\FlowRequest;
use mmerlijn\LaravelSalt\Models\FlowRequestLog;

class FlowRequestObserver
{
    public function deleting(FlowRequest $flowRequest): void
    {
        $fields = ['type', 'payload_type', 'payload_id', 'attempts'];
        // Haal alleen de noodzakelijke velden op uit de relatie
        $flowData = $flowRequest->flows()
            ->select($fields)
            ->first();
        $f = FlowRequestLog::create([
            'type' => $flowRequest->type,
            'request' => $flowRequest->request,
            'patient_id' => $flowRequest->patient_id,
            'request_nr' => $flowRequest->request_nr,
            'request_at' => $flowRequest->response_at,
            'flow' => $flowData ? $flowData->only($fields) : null,
        ]);
    }
}
