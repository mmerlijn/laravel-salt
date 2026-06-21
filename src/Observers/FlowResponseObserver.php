<?php

namespace mmerlijn\LaravelSalt\Observers;

use mmerlijn\LaravelSalt\Models\FlowResponse;
use mmerlijn\LaravelSalt\Models\FlowResponseLog;

class FlowResponseObserver
{
    public function deleting(FlowResponse $flowResponse): void
    {
        $fields = ['type', 'payload_type', 'payload_id', 'attempts'];
        // Haal alleen de noodzakelijke velden op uit de relatie
        $flowData = $flowResponse->flows()
            ->select($fields)
            ->first();

        FlowResponseLog::create([
            'type' => $flowResponse->type,
            'port' => $flowResponse->port,
            'response' => $flowResponse->response,
            'patient_id' => $flowResponse->patient_id,
            'request_nr' => $flowResponse->request_nr,
            'response_at' => $flowResponse->response_at,
            'flow' => $flowData ? $flowData->only($fields) : null,
        ]);
    }
}
