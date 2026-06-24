<?php

use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Http\Resources\FlowErrorResource;
use mmerlijn\LaravelSalt\Http\Resources\FlowResource;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowError;

it('can scope app errors by level, notification and class', function () {
    $patient = \mmerlijn\LaravelSalt\Models\Patient::factory()->create();
    $matching = FlowError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'notify' => true,
        'class' => \Workbench\App\Jobs\FlowExamplePatientBsnJob::class,
    ]);

    FlowError::factory()->create([
        'level' => ErrorLevelEnum::COORDINATOR,
        'notify' => false,
        'class' => 'other-class',
    ]);

    expect(FlowError::level(ErrorLevelEnum::MENNO)->notifiable()->forClass(\Workbench\App\Jobs\FlowExamplePatientBsnJob::class)->withExceptionClass()->get())
        ->toHaveCount(1)
        ->first()->id->toBe($matching->id);
});

it('stores the exception class when using the helper', function () {
    $flowError = FlowError::factory()->create([
        'message' => new RuntimeException('Kapot')->getMessage(),
        'level' => ErrorLevelEnum::MENNO,
        'class' => \Workbench\App\Jobs\FlowExamplePatientBsnJob::class,
    ]);

    expect($flowError->class)->toBe(\Workbench\App\Jobs\FlowExamplePatientBsnJob::class)
        ->and($flowError->message)->toBe('Kapot')
        ->and($flowError->level)->toBe(ErrorLevelEnum::MENNO);
});


it('renders app error and flow resources', function () {
    $patient = \mmerlijn\LaravelSalt\Models\Patient::factory()->create();
    $flow = Flow::factory()->create([
        'stack' => [101, 102],
        'payload_type' => 'patient',
        'patient_id' => $patient->id
    ]);
    $flow->fail(exception: new RuntimeException('Kapot'), solution: 'Doe iets', errorAt: $patient, errorClass: Workbench\App\Jobs\FlowExamplePatientBsnJob::class);
    $flow->refresh();
    $flowError = $flow->error;
    $flowError->load('flows');
    $flowErrorData = new FlowErrorResource($flowError)->resolve();
    $flowData = new FlowResource($flow)->resolve();
    expect($flowErrorData['id'])->toBe($flowError->id)
        ->and($flowErrorData['level'])->toBe(1)
        ->and($flowErrorData['class'])->toBe(\Workbench\App\Jobs\FlowExamplePatientBsnJob::class)
        ->and($flowErrorData['flows'])->toHaveCount(1)
        ->and($flowData['id'])->toBe($flow->id)
        ->and($flowData['flow_error_id'])->toBe($flowError->id)
        ->and($flowData['error']['id'])->toBe($flowError->id);
});


