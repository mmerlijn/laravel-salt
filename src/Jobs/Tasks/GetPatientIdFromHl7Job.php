<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use mmerlijn\LaravelSalt\Actions\FindOrCreatePatient;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\msgHl7\Hl7;

class GetPatientIdFromHl7Job implements ShouldQueue
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
        return 'flow-102-' . $this->flow->id;
    }

    public function handle(): void
    {
        try {
            //payload kan een Exchange / Request zijn
            $msgRepo = new Hl7($this->flow->payload->request)->getMsg();
            $patient = new FindOrCreatePatient()($msgRepo->patient);
            $this->flow->payload->patient_id = $patient->id;
            $this->flow->payload->request_nr = $msgRepo->order->request_nr;
            $this->flow->payload->save();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(new Error(
                level: ErrorLevelEnum::SYSTEEMBEHEER,
                fromObject: $this->flow->payload,
                exception: $e,
                solution: "Is het HL7 bericht geldig, kan geen patient uitlezen/aanmaken",
                notify: true,
                erroredClass: self::class,
            )->store());
        }
    }
}
