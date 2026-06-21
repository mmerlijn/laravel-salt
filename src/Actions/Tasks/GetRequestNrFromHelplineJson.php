<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks;

class GetRequestNrFromHelplineJson
{
    public function __invoke(string $json): ?string
    {
        $decoded = json_decode($json);

        if (!is_object($decoded)) {
            return null;
        }
        if (isset($decoded->orderNumber) && $decoded->orderNumber !== '') {
            return (string)$decoded->orderNumber;
        }
        if (isset($decoded->Forms[0]) && is_array($decoded->Forms) && is_object($decoded->Forms[0])) {
            $firstForm = $decoded->Forms[0];
            if (isset($firstForm->Fields->orderNumber) && is_object($firstForm->Fields) && $firstForm->Fields->orderNumber !== '') {
                return (string)$firstForm->Fields->orderNumber;
            }
        }
        return null;
    }
}