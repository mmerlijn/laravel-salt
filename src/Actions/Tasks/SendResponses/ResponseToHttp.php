<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks\SendResponses;

use Http;
use Illuminate\Http\Client\ConnectionException;
use mmerlijn\LaravelSalt\Models\FlowExchange;

class ResponseToHttp
{
    /**
     * @throws ConnectionException
     */
    public function __invoke(FlowExchange $output): void
    {
        if ($output->response) {
            $send = $output->response;
        } else {
            $send = $output->request;
        }
        $response = Http::post($this->getApiUri($output->type), $send);
        if ($response->status() == 200) {
            $output->request_at = now();
            $output->response_at = now();
            if (!$output->response) {
                $output->response = $response->json();
            }
            usleep(500);
            return;
        }
        throw new \Exception("Failed to send to HTTP" . $this->getApiUri($output->type) . "\nResponse: " . $response->body());

    }

    protected function getApiUri(int $type): string
    {
        return config('laravel_salt.api_uri')[$type] ?? '';
    }

}