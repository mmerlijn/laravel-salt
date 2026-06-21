<?php

namespace mmerlijn\LaravelSalt\Actions\Tasks;

class GetRequestNrFromHl7
{
    public function __invoke(string $hl7): ?string
    {
        //uit de OBR: OBR|1|ZD12345678|... of OBR|1|ZD12345678_01|... => ZD12345678
        preg_match('/OBR\|1\|([^\|_]+)/', $hl7, $matches);
        if ($matches) {
            return $matches[1];
        }
        // of uit PID: ~ZD12345678^^^ZorgDomein^VN
        preg_match('/~([^~\^]+)\^\^\^ZorgDomein\^VN\|\|/', $hl7, $matches);
        return $matches[1] ?? null;
    }
}