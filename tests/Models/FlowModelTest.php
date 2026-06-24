<?php

use Illuminate\Support\Facades\Queue;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowError;
use Workbench\App\Jobs\FlowExampleChangeLabtrainIdJob;
use Workbench\App\Jobs\FlowExampleErrorJob;
use Workbench\App\Jobs\FlowExampleSetLabtrainIdJob;

it('returns the created flow from add', function () {
    config()->set('laravel_salt.flows.10', [100]);
    $flow = Flow::add(10, null);
    Flow::add(flow: 10, payload: null);
    expect($flow->exists)->toBeTrue()
        ->and($flow->id)->not->toBeNull()
        ->and($flow->payload_id)->toBeNull()
        ->and($flow->payload_type)->toBeNull();
});

it('does not dispatch tasks before try_after', function () {
    Queue::fake();

    config()->set('laravel-salt.tasks.991', FlowExampleSetLabtrainIdJob::class);

    $flow = Flow::factory()->create([
        'type' => 10,
        'stack' => [991],
        'try_after' => now()->addMinute(),
        'flow_error_id' => null,
    ]);

    $flow->run();
    Queue::assertNothingPushed();
});

it('dispatches tasks when try_after has passed', function () {
    Queue::fake();

    config()->set('laravel_salt.tasks.992', FlowExampleChangeLabtrainIdJob::class);
    config()->set('laravel_salt.flows.10', [992]);

    $flow = Flow::factory()->create([
        'type' => 10,
        'stack' => [992],
        'try_after' => now()->subMinute(),
        'flow_error_id' => null,
    ]);

    $flow->run();
    $flow->refresh();
    Queue::assertPushed(FlowExampleChangeLabtrainIdJob::class, 1);
});

it('stores an flow error when task FlowExampleErrorJob fails', function () {
    config()->set('laravel_salt.notify.1', []);

    $flow = Flow::factory()->create([
        'flow_error_id' => null,
    ]);

    expect(FlowError::count())->toBe(0);

    new FlowExampleErrorJob($flow)->handle();

    $flow->refresh();
    $flowError = FlowError::query()->first();

    expect($flowError)->not->toBeNull()
        ->and($flow->flow_error_id)->toBe($flowError->id)
        ->and($flowError->class)->toBe(FlowExampleErrorJob::class)
        ->and($flowError->message)->not->toBe('');
});


