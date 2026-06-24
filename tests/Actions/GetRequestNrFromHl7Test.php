<?php

use mmerlijn\LaravelSalt\Actions\Tasks\GetRequestNrFromHl7;
use mmerlijn\LaravelSalt\Jobs\Tasks\Task100GetRequestNrFromHl7Job;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowLog;

it('Can find request_nr from HL7 or return null', function (string $hl7, ?string $expected) {

    $request_nr = new GetRequestNrFromHl7()($hl7);
    expect($request_nr)->toBe($expected);

    config()->set('laravel_salt.tasks.100', Task100GetRequestNrFromHl7Job::class);
    config()->set('laravel_salt.flows.10', [100]);
    //Nu via de Job on the Flow
    $r = Flow::factory()->create([
        'type' => 10,
        'request' => $hl7,
        'request_at' => now(),
    ]);
    \mmerlijn\LaravelSalt\Jobs\FlowRunnerJob::dispatchSync();

    $flowError = \mmerlijn\LaravelSalt\Models\FlowError::first();
    if ($flowError) {
        expect($flowError)->message->toBe("Geen geldig aanvraagnummer.");
    } else {
        expect(FlowLog::first())
            ->request_nr->toBe($expected)
            ->and(Flow::all())->toBeEmpty();
    }
})->with([
    ['MSH|^~\&|SALTNET|SALTNET|ZORGDOMEIN|ZORGDOMEIN|202406061200||ORM^O01|1234567890|P|2.4
PID|1||1234567890^^^SALTNET^MRN||Doe^John||19800101|M|||123 Main St^^Anytown^NY^12345||555-1234|||M||1234567890^^^SALTNET^MRN
PV1|1|I|WARD^123^A^SALTNET||||1234^Smith^Jane^A^^^Dr.|||||||||||1234567890^^^SALTNET^MRN|||||||||||||||||||||||||202406061200
ORC|NW|REQ1234567890|||||202406061200|||1234^Smith^Jane^A^^^Dr.|||||||||||1234567890^^^SALTNET^MRN
OBR|1|REQ1234567890||TEST^Test Order|||202406061200|||||||||||1234^Smith^Jane^A^^^Dr.||||||||||||||||||||||||||||202406061200
SPM|1|SPEC1234567890||TEST^Test Specimen|||202406061200|||||||||||1234^Smith^Jane^A^^^Dr.||||||||||||||||||||||||||||202406061200
', 'REQ1234567890'],
    ['MSH|^~\&|SALTNET|SALTNET|ZORGDOMEIN|ZORGDOMEIN|202406061200||ORM^O01|1234567890|P|2.4
PID|1||1234567890^^^NLMINBIZA^NNNLD~REQ1234567890^^^ZorgDomein^VN||Doe^John||19800101|M|||123 Main St^^Anytown^NY^12345||555-1234|||M||1234567890^^^SALTNET^MRN
PV1|1|I|WARD^123^A^SALTNET||||1234^Smith^Jane^A^^^Dr.|||||||||||1234567890^^^SALTNET^MRN|||||||||||||||||||||||||202406061200
', 'REQ1234567890'],
    ['', null],
    ['MSH|^~\&|SALTNET|SALTNET|ZORGDOMEIN|ZORGDOMEIN|202406061200||ORM^O01|1234567890|P|2.4
PID|1||1234567890^^^SALTNET^MRN||Doe^John||19800101|M|||123 Main St^^Anytown^NY^12345||555-1234|||M||1234567890^^^SALTNET^MRN
PV1|1|I|WARD^123^A^SALTNET||||1234^Smith^Jane^A^^^Dr.|||||||||||1234567890^^^SALTNET^MRN|||||||||||||||||||||||||202406061200
ORC|NW|REQ1234567890|||||202406061200|||1234^Smith^Jane^A^^^Dr.|||||||||||1234567890^^^SALTNET^MRN
OBR|1|REQ1234567890_01||TEST^Test Order|||202406061200|||||||||||1234^Smith^Jane^A^^^Dr.||||||||||||||||||||||||||||202406061200
SPM|1|SPEC1234567890||TEST^Test Specimen|||202406061200|||||||||||1234^Smith^Jane^A^^^Dr.||||||||||||||||||||||||||||202406061200
', 'REQ1234567890'],
]);