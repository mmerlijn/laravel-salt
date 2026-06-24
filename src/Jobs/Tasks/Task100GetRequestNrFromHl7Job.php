<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Actions\Tasks\GetRequestNrFromHl7;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Rules\RequestNr;

class Task100GetRequestNrFromHl7Job extends TaskJob
{
//Todo helemaal aanpassen
    public function handle(): void
    {
        try {
            //payload kan een Exchange / Request zijn
            $this->flow->request_nr = new GetRequestNrFromHl7()($this->flow->request);
            $this->flow->save();
            $v = Validator::make(['request_nr' => $this->flow->request_nr], [
                'request_nr' => [new RequestNr],
            ]);
            $v->validate();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(
                exception: $e,
                solution: "Zorg voor een valide aanvraagnr in het HL7 bericht",
                errorLevel: ErrorLevelEnum::SYSTEEMBEHEER,
                errorClass: self::class,
            );
        }
    }
}
