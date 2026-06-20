<?php

namespace mmerlijn\LaravelSalt\Observers;

use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowExchangeLog;

class FlowExchangeObserver
{
    public function deleting(FlowExchange $flowExchange): void
    {
        $fields = ['type', 'payload_type', 'payload_id','stack','attempts'];
        // Haal alleen de noodzakelijke velden op uit de relatie
        $flowData = $flowExchange->flow()
            ->select($fields)
            ->first();

        FlowExchangeLog::create([
            'type' =>       $flowExchange->type,
            'request'     => $flowExchange->request,
            'response'    => $flowExchange->response,
            'response_at' => $flowExchange->response_at,
            'request_at'  => $flowExchange->request_at,
            'flow'        => $flowData ? $flowData->only($fields) : null,
        ]);
    }
}
