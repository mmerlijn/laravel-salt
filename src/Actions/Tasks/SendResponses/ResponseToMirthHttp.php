<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks\SendResponses;

use Http;
use Illuminate\Http\Client\ConnectionException;
use mmerlijn\LaravelSalt\Models\FlowExchange;

class ResponseToMirthHttp
{
    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public function __invoke(FlowExchange $output): void
    {
        $port = $output->port ?? $this->getPort($output->type);
        if ($output->response) {
            $send = $output->response;
        } else {
            $send = $output->request;
        }
        try {
            $response = Http::post('http://' . $this->getServer() . ':' . $port . '/api/', $send);
            if ($response->status() == 200) {
                $output->request_at = now();
                $output->response_at = now();
                if (!$output->response) {
                    $output->response = $response->json();
                }
                usleep(500);
                return;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
        throw new \Exception("Failed to send to HTTP mirth port={$port}\nResponse: " . $response?->body());

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