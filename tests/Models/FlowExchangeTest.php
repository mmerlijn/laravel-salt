<?php

use mmerlijn\LaravelSalt\Jobs\FlowRunnerJob;
use mmerlijn\LaravelSalt\Jobs\ListenForExchangesJob;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowExchangeLog;
use Workbench\App\Jobs\FlowExampleTask2ErrorJob;
use Workbench\App\Jobs\FlowExampleTaskExchangeJob;


it('requests from flow-exchange are put to Flow after inserted', function () {
    config()->set('laravel_salt.flows.992', [991]);
    $r = FlowExchange::factory()->create([
        'type' => 992,
        'request' => 1000,
    ]);
    //Zet nieuwe FlowExchanges op de Flow
    ListenForExchangesJob::dispatchSync();

    $r->refresh();
    expect($r)->toBeInstanceOf(FlowExchange::class)
        ->and($r)->not->toBeEmpty();
    // Uitvoeren van alle Flows
});

it('Flows run with inserted requests', function () {
    config()->set('laravel_salt.flows.10', [991]);
    config()->set('laravel_salt.tasks.991', FlowExampleTaskExchangeJob::class);
    $r = FlowExchange::factory()->create([
        'type' => 10,
        'request' => 1000,
    ]);
    $flow = Flow::add($r->type, $r);
    ListenForExchangesJob::dispatchSync();
    FlowRunnerJob::dispatchSync();
    //dd(Flow::all(), AppError::all());
    expect(Flow::all())->toBeEmpty()
        ->and(FlowExchange::all())->toBeEmpty()
        ->and(FlowExchangeLog::all()->toArray())->not->toBeEmpty()
        ->and(FlowExchangeLog::first()->response)->toBe("Gedaan");

});

it('stores an app error when task 2 fails', function () {
    config()->set('laravel-salt.notify.1', []);
    config()->set('laravel_salt.tasks.991', FlowExampleTaskExchangeJob::class);
    config()->set('laravel_salt.tasks.992', FlowExampleTask2ErrorJob::class);
    config()->set('laravel_salt.flows.10', [991, 992, [400, 500], 300]);
    $r = FlowExchange::factory()->create([
        'type' => 10,
        'request' => 1000,
    ]);
    expect(AppError::count())->toBe(0);
    ListenForExchangesJob::dispatchSync();
    FlowRunnerJob::dispatchSync();

    //er treedt een fout op bij Task2 dus FlowExchange blijft bestaan
    expect(FlowExchange::all())->count()->toBe(1)
        ->and(AppError::count())->toBe(1);
    $appError = AppError::query()->first();
    $flow = Flow::first();
    expect($appError)->not->toBeNull()
        ->and($flow->app_error_id)->toBe($appError->id)
        ->and($appError->class)->toBe(FlowExampleTask2ErrorJob::class)
        ->and($appError->message)->not->toBe('')
        ->and($r)->not->toBeNull();
});


