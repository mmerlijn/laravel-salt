<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks\Traits;

trait HandleResponseTrait
{
    protected Output $output;

    protected function handleHTTP(mixed $response): bool
    {
        //HTTP response handling
        if ($response->status() == 200) {
            $this->output->delete(); //observer will set output as sent
            return true;
        } else {
            $this->output->error_at = now();
            $this->output->exception = "Failed to send to Mirth: " . $this->getServer() . " {$this->getPort($this->output->type->value)}\nStatus: " . $response->status();
            $this->output->save();
            return false;
        }
    }

    protected function handleMirth(mixed $connector): bool
    {
        usleep(500);
        if ($connector->successful) {
            $this->output->error_at = now();
            $this->output->exception = "Failed to send to Mirth: " . $this->getServer() . " {$this->getPort($this->output->type->value)}\nResponse: " . $connector->response;
            $this->output->delete(); //observer will set output as sent
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param string $msg
     * @param string $type json/hl7/edifact
     * @return string
     */
    private function formatAs(string $msg, string $type): string
    {
        return match ($type) {
            'hl7', 'edifact' => chr(11) . $msg . chr(28) . chr(13),
            'json' => json_encode($msg) . chr(13),
            default => $msg,
        };
    }

    protected function getPort(int $type = 0): int
    {
        if ($type) {
            return config('salt.mirth_msg_ports')[$type] ?? config('salt.mirth_port');
        }
        return config('salt.mirth_port');
    }

    protected function getServer(string $server = ""): string
    {
        if ($server == "") {
            return config('salt.mirth_server');
        }
        return $server;
    }
}