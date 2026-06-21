<?php

namespace mmerlijn\LaravelSalt\Observers;

use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowExchangeLog;

class FlowExchangeObserver
{

    public function deleting(FlowExchange $flowExchange): void
    {
        $fields = ['type', 'payload_type', 'payload_id', 'attempts'];
        // Haal alleen de noodzakelijke velden op uit de relatie
        $flowData = $flowExchange->flows()
            ->select($fields)
            ->first();

        FlowExchangeLog::create([
            'type' => $flowExchange->type,
            'port' => $flowExchange->port,
            'request' => $flowExchange->request,
            'response' => $flowExchange->response,
            'patient_id' => $flowExchange->patient_id,
            'request_nr' => $flowExchange->request_nr,
            'response_at' => $flowExchange->response_at,
            'request_at' => $flowExchange->request_at,
            'flow' => $flowData ? $flowData->only($fields) : null,
        ]);
    }
}
