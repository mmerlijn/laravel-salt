<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use mmerlijn\LaravelSalt\Helpers\QueueHeartBeat;

class ServerStatusController
{

    public function __invoke()
    {
        if (!new QueueHeartBeat()->serverCheck()) {
            return "Queue probleem";
        }
        if (!$this->checkDatabaseConnection()) {
            return "Database Probleem";
        }
        return "ok";

    }

    private function checkDatabaseConnection(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}