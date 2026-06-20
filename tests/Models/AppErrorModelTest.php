<?php

use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Http\Resources\AppErrorResource;
use mmerlijn\LaravelSalt\Http\Resources\FlowResource;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use Workbench\App\Jobs\FlowExampleTask2Job;

it('can scope app errors by level, notification and class', function () {
    $matching = AppError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'notify' => true,
        'class' => FlowExampleTask2Job::class,
        'exception_class' => RuntimeException::class,
    ]);

    AppError::factory()->create([
        'level' => ErrorLevelEnum::COORDINATOR,
        'notify' => false,
        'class' => 'other-class',
        'exception_class' => null,
    ]);

    expect(AppError::level(ErrorLevelEnum::MENNO)->notifiable()->forClass(FlowExampleTask2Job::class)->withExceptionClass()->get())
        ->toHaveCount(1)
        ->first()->id->toBe($matching->id);
});

it('stores the exception class when using the helper', function () {
    $appError = Error::forException(
        new RuntimeException('Kapot'),
        ErrorLevelEnum::MENNO,
        erroredClass: FlowExampleTask2Job::class,
    )->store();

    expect($appError->exception_class)->toBe(RuntimeException::class)
        ->and($appError->message)->toBe('Kapot')
        ->and($appError->class)->toBe(FlowExampleTask2Job::class);
});

it('can build an error from a message without exception class', function () {
    $appError = Error::forMessage(
        'Handmatige melding',
        ErrorLevelEnum::SYSTEEMBEHEER,
        erroredClass: FlowExampleTask2Job::class,
    )->store();

    expect($appError->message)->toBe('Handmatige melding')
        ->and($appError->exception_class)->toBeNull()
        ->and($appError->level)->toBe(ErrorLevelEnum::SYSTEEMBEHEER);
});

it('renders app error and flow resources', function () {
    $appError = AppError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'exception_class' => RuntimeException::class,
        'class' => FlowExampleTask2Job::class,
    ]);

    $flow = Flow::factory()->create([
        'app_error_id' => $appError->id,
        'stack' => [101, 102],
    ]);

    $appError->load('flows');
    $flow->load('error');

    $appErrorData = AppErrorResource::make($appError)->resolve();
    $flowData = FlowResource::make($flow)->resolve();

    expect($appErrorData['id'])->toBe($appError->id)
        ->and($appErrorData['level'])->toBe(ErrorLevelEnum::MENNO->value)
        ->and($appErrorData['exception_class'])->toBe(RuntimeException::class)
        ->and($appErrorData['flows'])->toHaveCount(1)
        ->and($flowData['id'])->toBe($flow->id)
        ->and($flowData['app_error_id'])->toBe($appError->id)
        ->and($flowData['error']['id'])->toBe($appError->id);
});


