<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks\SendResponses;

use mmerlijn\LaravelSalt\Helpers\MirthConnector;
use mmerlijn\LaravelSalt\Models\FlowExchange;

class ResponseToMirth
{
    /**
     * @throws \Exception
     */
    public function __invoke(FlowExchange $output): void
    {
        $port = $output->port ?? $this->getPort($output->type);
        $mc = new MirthConnector(
            server: $this->getServer(),
            port: $port
        )->sendMessage($output->response);
        usleep(500); //om te voorkomen dat berichten heel snel achter elkaar worden verstuurd
        if (!$mc->successful) {
            throw new \Exception("Failed to send to Mirth: " . $this->getServer() . " {$port}\nResponse: " . $mc->error);
        }
    }

    protected function getPort(int $type): int
    {
        return config('laravel_salt.mirth_ports')[$type][0] ?? $type;
    }

    protected function getServer(): string
    {
        return config('laravel_salt.mirth_server');
    }
}