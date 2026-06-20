<?php

namespace mmerlijn\LaravelSalt\Observers;

use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowExchangeLog;
use mmerlijn\LaravelSalt\Models\FlowResponse;

class FlowResponseObserver
{
    public function deleting(FlowResponse $flowResponse): void
    {
        $fields = ['type', 'payload_type', 'payload_id','stack','attempts'];
        // Haal alleen de noodzakelijke velden op uit de relatie
        $flowData = $flowResponse->flow()
            ->select($fields)
            ->first();

        FlowExchangeLog::create([
            'type' =>       $flowResponse->type,
            'response'    => $flowResponse->response,
            'response_at' => $flowResponse->response_at,
            'flow'        => $flowData ? $flowData->only($fields) : null,
        ]);
    }
}
