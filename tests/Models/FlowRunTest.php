<?php

use mmerlijn\LaravelSalt\Jobs\FlowRunnerJob;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowError;
use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowExchangeLog;
use mmerlijn\LaravelSalt\Models\FlowLog;
use mmerlijn\LaravelSalt\Models\Patient;
use Workbench\App\Jobs\FlowExampleErrorJob;
use Workbench\App\Jobs\FlowExamplePatientBsnJob;


it('requests from flow-exchange are put to Flow after inserted', function () {
    config()->set('laravel_salt.flows.992', [991]);
    $f = Flow::factory()->create([
        'request' => 1000,
        'type' => 992
    ]);
    FlowRunnerJob::dispatchSync();


    $f->refresh();
    expect($f)->toBeInstanceOf(Flow::class)
        ->and($f)->not->toBeEmpty()
        ->and(count($f->stack))->toBe(1);
    // Uitvoeren van alle Flows
});

it('Flows run with inserted requests', function () {
    config()->set('laravel_salt.flows.10', [991]);
    config()->set('laravel_salt.tasks.991', FlowExamplePatientBsnJob::class);
    $patient = Patient::factory()->create();
    $f = Flow::factory()->payload($patient)->create([
        'request' => 1000,
        'type' => 10
    ]);

    FlowRunnerJob::dispatchSync();
    //dd(Flow::all(), AppError::all());
    expect(Flow::all())->toBeEmpty()
        ->and(FlowLog::all()->toArray())->not->toBeEmpty();

});

it('stores an app error when task 2 fails', function () {
    config()->set('laravel-salt.notify.1', []);
    config()->set('laravel_salt.tasks.991', FlowExamplePatientBsnJob::class);
    config()->set('laravel_salt.tasks.992', FlowExampleErrorJob::class);
    config()->set('laravel_salt.flows.10', [991, 992, [400, 500], 300]);
    $patient = Patient::factory()->create();
    $f = Flow::factory()->payload($patient)->create([
        'request' => 1000,
        'type' => 10
    ]);
    expect(FlowError::count())->toBe(0);
    FlowRunnerJob::dispatchSync();
    //er treedt een fout op bij Task2 dus FlowExchange blijft bestaan
    expect(Flow::all())->count()->toBe(1)
        ->and(FlowError::count())->toBe(1);

    $flowError = FlowError::query()->first();
    $flow = Flow::first();
    expect($flowError)->not->toBeNull()
        ->and($flow->flow_error_id)->toBe($flowError->id)
        ->and($flowError->class)->toBe(FlowExampleErrorJob::class)
        ->and($flowError->message)->not->toBe('')
        ->and($f)->not->toBeNull();
});


