<?php

use Illuminate\Support\Facades\Route;
use mmerlijn\LaravelSalt\Http\Controllers\CareGroupApiController;
use mmerlijn\LaravelSalt\Http\Controllers\EnumApiController;
use mmerlijn\LaravelSalt\Http\Controllers\FlowController;
use mmerlijn\LaravelSalt\Http\Controllers\FlowErrorController;
use mmerlijn\LaravelSalt\Http\Controllers\LockController;
use mmerlijn\LaravelSalt\Http\Controllers\NoteApiController;
use mmerlijn\LaravelSalt\Http\Controllers\PatientApiController;
use mmerlijn\LaravelSalt\Http\Controllers\RequesterApiController;
use mmerlijn\LaravelSalt\Http\Controllers\ServerStatusController;
use mmerlijn\LaravelSalt\Http\Controllers\UzoviApiController;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowError;

Route::bind('flowError', function ($value) {
    return FlowError::query()->findOrFail($value);
});

Route::bind('flow', function ($value) {
    return Flow::query()->findOrFail($value);
});

Route::get('server-status/check-me', ServerStatusController::class)->middleware(['web'])->name('server-status');

Route::prefix('api')
    ->middleware(['web', 'auth'])
    ->group(function () {
        Route::resource('flow-errors', FlowErrorController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['flow-errors' => 'flowError']);

        Route::resource('flows', FlowController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['flows' => 'flow']);
        Route::get('flows-by-type', [FlowController::class, 'showByType'])->name('flows.showByType');
        Route::delete('flows-payload/{flow}', [FlowController::class, 'destroyPayload'])->name('flows.destroyPayload');
        Route::resource('requesters', RequesterApiController::class)
            ->only(['index', 'show', 'store'])
            ->parameters(['requesters' => 'requester']);
        Route::resource('care-groups', CareGroupApiController::class)
            ->only(['index', 'show'])
            ->parameters(['care-groups' => 'careGroup']);
        Route::put('care-groups/attach-requester', [CareGroupApiController::class, 'attachRequester'])->name('care-groups.attach-requester');
        Route::delete('care-groups/detach-requester', [CareGroupApiController::class, 'detachRequester'])->name('care-groups.detach-requester');
        Route::resource('uzovi', UzoviApiController::class)
            ->only(['index', 'show'])
            ->parameters(['uzovi' => 'uzovi']);
        Route::resource('patients', PatientApiController::class)
            ->only(['index'])
            ->parameters(['patients' => 'patient']);
        Route::resource('notes', NoteApiController::class);
        Route::get('enum/{enum}', EnumApiController::class)->name('enum');
        Route::get('locked/{type}/{id}', [LockController::class, 'locked'])->name('locked');
        Route::put('lock/{type}/{id}', [LockController::class, 'lock'])->name('lock');
    });
/*
 *

 * */


