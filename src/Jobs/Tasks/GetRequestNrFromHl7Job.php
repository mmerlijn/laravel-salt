<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Actions\Tasks\GetRequestNrFromHl7;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Rules\RequestNr;

class GetRequestNrFromHl7Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $maxExceptions = 0;

    public int $uniqueFor = 60;

    public function uniqueVia(): Repository
    {
        return Cache::driver('database');
    }

    public function __construct(public Flow $flow)
    {
    }

    public function uniqueId(): string
    {
        return 'flow-100-' . $this->flow->id;
    }

    public function handle(): void
    {
        try {
            //payload kan een Exchange / Request zijn
            $this->flow->payload->request_nr = new GetRequestNrFromHl7()($this->flow->payload->request);
            $this->flow->payload->save();
            $v = Validator::make(['request_nr' => $this->flow->payload->request_nr], [
                'request_nr' => [new RequestNr],
            ]);
            $v->validate();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(new Error(
                level: ErrorLevelEnum::SYSTEEMBEHEER,
                fromObject: $this->flow->payload,
                exception: $e,
                solution: "Zorg voor een valide aanvraagnr in het HL7 bericht",
                notify: false,
                erroredClass: self::class,
            )->store());
        }
    }
}
