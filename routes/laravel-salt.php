<?php

use Illuminate\Support\Facades\Route;
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
    return FlowError::withTrashed()->findOrFail($value);
});

Route::bind('flow', function ($value) {
    return Flow::query()->findOrFail($value);
});

Route::get('server-status/check-me', ServerStatusController::class)->middleware(['web'])->name('server-status');

Route::prefix('api')
    ->middleware(['api', 'auth'])
    ->group(function () {
        Route::resource('flow-errors', FlowErrorController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['flow-errors' => 'flowError']);

        Route::resource('flows', FlowController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['flows' => 'flow']);
        Route::resource('requesters', RequesterApiController::class)
            ->only(['index', 'show'])
            ->parameters(['requesters' => 'requester']);
        Route::resource('uzovi', UzoviApiController::class)
            ->only(['index', 'show'])
            ->parameters(['uzovi' => 'uzovi']);
        Route::resource('patients', PatientApiController::class)
            ->only(['index'])
            ->parameters(['patients' => 'patient']);
        Route::resource('notes', NoteApiController::class);
        Route::resource('locks', LockController::class)->only(['update', 'show']);
        Route::get('enum/{enum}', EnumApiController::class)->name('enum');
    });
/*
 *

 * */


