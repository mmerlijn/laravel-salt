<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks\SendResponses;

use mmerlijn\LaravelSalt\Models\FlowExchange;

class ResponseToFile
{
    //File wordt door een andere applicatie uitgelezen dus alleen controleren of het bericht is uitgelezen
    public function __invoke(FlowExchange $output): void
    {
        $is_send = false;

        if ($output->response) {
            $is_send = (bool)$output->response_at;
        } else {
            $is_send = (bool)$output->request_at;
        }
        if ($is_send) {
            return;
        }
        throw new \Exception("Nog niet opgehaald door andere applicatie");

    }

}