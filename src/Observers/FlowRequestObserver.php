<?php

namespace mmerlijn\LaravelSalt\Observers;

use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowExchangeLog;
use mmerlijn\LaravelSalt\Models\FlowRequest;

class FlowRequestObserver
{
    public function deleting(FlowRequest $flowRequest): void
    {
        $fields = ['type', 'payload_type', 'payload_id','stack','attempts'];
        // Haal alleen de noodzakelijke velden op uit de relatie
        $flowData = $flowRequest->flow()
            ->select($fields)
            ->first();

        FlowExchangeLog::create([
            'type' =>       $flowRequest->type,
            'response'    => $flowRequest->response,
            'response_at' => $flowRequest->response_at,
            'flow'        => $flowData ? $flowData->only($fields) : null,
        ]);
    }
}
