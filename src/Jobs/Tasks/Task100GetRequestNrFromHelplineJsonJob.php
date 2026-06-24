<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Actions\Tasks\GetRequestNrFromHelplineJson;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Rules\RequestNr;

class Task100GetRequestNrFromHelplineJsonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TaskJobTrait;

//Todo helemaal aanpassen
    public function handle(): void
    {
        try {
            //payload kan een Exchange / Request zijn
            $this->flow->request_nr = new GetRequestNrFromHelplineJson()($this->flow->request);
            $this->flow->save();
            $v = Validator::make(['request_nr' => $this->flow->request_nr], [
                'request_nr' => [new RequestNr],
            ]);
            $v->validate();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(new Error(
                level: ErrorLevelEnum::SYSTEEMBEHEER,
                fromObject: $this->flow->payload,
                exception: $e,
                solution: "Zorg voor een valide aanvraagnr in het Helpline JSON bericht",
                notify: false,
                erroredClass: self::class,
            )->store());
        }
    }
}
