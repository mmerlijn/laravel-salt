<?php

namespace mmerlijn\LaravelSalt\Helpers;

class MirthConnector
{

    public string $response = "";
    public string $error = "";
    public bool $successful = false;


    public function __construct(
        public string $server = '127.0.0.1',
        public int    $port = 8000,
    )
    {
    }

    public function reset(): void
    {
        $this->response = "";
        $this->error = "";
        $this->successful = false;
    }

    /**
     * @param string $msg
     * @param string $type json/hl7/edifact
     * @return MirthConnector
     */
    public function sendMessage(string $msg, string $type = 'hl7'): MirthConnector
    {
        $this->reset();
        try {
            // Voor HL7 en EDIFACT moeten we MLLP wrapping toepassen
            $msg = $this->formatAs($msg, $type);
            // 1. Verbinding maken met een timeout
            $socket = @fsockopen($this->server, $this->port, $errno, $err_str, 5);

            if (!$socket) {
                throw new \Exception("Mirth {$this->server} {$this->port} onbereikbaar: $err_str");
            }

            // 2. Bericht versturen
            fwrite($socket, $msg);

            // 3. Wachten op validatie (de ACK of response)
            stream_set_timeout($socket, 5);


            while (!feof($socket)) {
                // Lees per 1024 bytes
                $buffer = fread($socket, 1024);

                if ($buffer === false) break;

                $this->response .= $buffer;
                $info = stream_get_meta_data($socket);

                if ($info['timed_out']) {
                    throw new \Exception("Timeout bereikt: " . $this->response);
                }

                // HL7 MLLP einde detectie: \x1c (FS) gevolgd door \x0d (CR)
                if (str_contains($this->response, "\x1c")) {  //"\x1c\x0d")
                    break;
                }

                // Backup: Als we MSA|AA| of MSA|AE| zien, weten we vaak ook al genoeg
                if (preg_match('/MSA\|A[AER]\|/', $this->response)) {
                    // Even kort wachten om de laatste bytes (CR) mee te pakken
                    usleep(10000);
                    $this->response .= fread($socket, 128);
                    break;
                }
            }
            fclose($socket);

            // 4. Validatie van de response;
            $this->validateResponse();

            return $this;
        } catch (\Exception|\Error $e) {
            $this->error = $e->getMessage();
            $this->successful = false;
            return $this;
        }
    }

    private function validateResponse(): void
    {
        $this->response = trim($this->response);
        // 1. Check op HL7 ACK patronen
        if (preg_match('/MSA\|([A-Z]{2})/', $this->response, $matches)) {
            $status = $matches[1];
            if ($status === 'AA') {
                $this->successful = true;
                return;
            }
            $this->error = "HL7 Fout ontvangen: " . ($status === 'AE' ? 'Application Error' : 'Application Reject');
            $this->successful = false;
            return;
        }

        // 2. Check op custom Mirth responses (SENT/RECEIVED)
        if (str($this->response)->startsWith(['SENT', 'RECEIVED'])) {
            $this->successful = true;
            return;
        }

        // 3. Fallback naar JSON
        if (strlen($this->response) > 2) {
            $json = json_decode($this->response, true);
            if (json_last_error() === JSON_ERROR_NONE && ($json['status'] ?? '') === 'success') {
                $this->successful = true;
                return;
            }
        }


        // 4. Algemene fallback foutmelding
        $this->error = 'Ongeldige response ontvangen: ' . $this->response;
        $this->successful = false;
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

}
