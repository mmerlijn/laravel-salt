<?php

use Illuminate\Support\Facades\Route;
use mmerlijn\LaravelSalt\Http\Controllers\FlowController;
use mmerlijn\LaravelSalt\Http\Controllers\FlowErrorController;
use mmerlijn\LaravelSalt\Http\Controllers\ServerStatusController;
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
    });

/*
 *

 * */


