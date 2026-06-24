<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use mmerlijn\LaravelSalt\Actions\FindOrCreatePatient;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\msgHl7\Hl7;

class Task102GetPatientIdFromHl7Job extends TaskJob
{

//Todo helemaal aanpassen
    public function handle(): void
    {
        try {
            //payload kan een Exchange / Request zijn
            $msgRepo = new Hl7($this->flow->request)->getMsg();
            $patient = new FindOrCreatePatient()($msgRepo->patient);
            $this->flow->patient_id = $patient->id;
            $this->flow->request_nr = $msgRepo->order->request_nr;
            $this->flow->save();
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            $this->flow->fail(new Error(
                level: ErrorLevelEnum::SYSTEEMBEHEER,
                fromObject: $this->flow,
                exception: $e,
                solution: "Is het HL7 bericht geldig, kan geen patient uitlezen/aanmaken",
                notify: true,
                erroredClass: self::class,
            )->store());
        }
    }
}
