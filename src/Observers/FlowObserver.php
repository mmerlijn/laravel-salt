<?php

namespace mmerlijn\LaravelSalt\Observers;


use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowRequest;
use mmerlijn\LaravelSalt\Models\FlowResponse;

class FlowObserver
{

    public function deleting(Flow $flow): void
    {
        if (!$flow->payload_id) {
            return;
        }
        if ($flow->payload instanceof FlowRequest) {
            $flow->payload->delete();
            return;
        }
        if ($flow->payload instanceof FlowExchange) {
            $flow->payload->delete();
            return;
        }
        if ($flow->payload instanceof FlowResponse) {
            $flow->payload->delete();
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
