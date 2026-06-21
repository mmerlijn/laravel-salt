<?php

use Illuminate\Support\Facades\Queue;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use Workbench\App\Jobs\FlowExampleTask1Job;
use Workbench\App\Jobs\FlowExampleTask2ErrorJob;
use Workbench\App\Jobs\FlowExampleTask3Job;

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

    config()->set('laravel-salt.tasks.991', FlowExampleTask1Job::class);

    $flow = Flow::factory()->create([
        'type' => 10,
        'stack' => [991],
        'try_after' => now()->addMinute(),
        'app_error_id' => null,
    ]);

    $flow->run();
    Queue::assertNothingPushed();
});

it('dispatches tasks when try_after has passed', function () {
    Queue::fake();

    config()->set('laravel_salt.tasks.992', FlowExampleTask3Job::class);
    config()->set('laravel_salt.flows.10', [992]);

    $flow = Flow::factory()->create([
        'type' => 10,
        'stack' => [992],
        'try_after' => now()->subMinute(),
        'app_error_id' => null,
    ]);

    $flow->run();
    $flow->refresh();
    Queue::assertPushed(FlowExampleTask3Job::class, 1);
});

it('stores an app error when task 2 fails', function () {
    config()->set('laravel_salt.notify.1', []);

    $flow = Flow::factory()->create([
        'app_error_id' => null,
    ]);

    expect(AppError::count())->toBe(0);

    new FlowExampleTask2ErrorJob($flow)->handle();

    $flow->refresh();
    $appError = AppError::query()->first();

    expect($appError)->not->toBeNull()
        ->and($flow->app_error_id)->toBe($appError->id)
        ->and($appError->class)->toBe(FlowExampleTask2ErrorJob::class)
        ->and($appError->message)->not->toBe('');
});


