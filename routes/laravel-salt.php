<?php

use Illuminate\Support\Facades\Route;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Http\Controllers\AppErrorController;
use mmerlijn\LaravelSalt\Http\Controllers\FlowController;
use mmerlijn\LaravelSalt\Models\Flow;

Route::bind('appError', function ($value) {
    return AppError::withTrashed()->findOrFail($value);
});

Route::bind('flow', function ($value) {
    return Flow::query()->findOrFail($value);
});



//Route::prefix('laravel-salt')
//    ->name('laravel-salt.')
//    ->middleware(['api','auth'])
//    ->group(function () {
//        Route::get('app-errors', [AppErrorController::class, 'index'])->name('app-errors.index');
//        Route::get('app-errors/edit/{error}', [AppErrorController::class, 'edit'])->name('app-errors.edit');
//        Route::match(['put', 'patch'], 'app-errors/{appError}', [AppErrorController::class, 'update'])->name('app-errors.update');
//        Route::delete('app-errors/{appError}', [AppErrorController::class, 'destroy'])->name('app-errors.destroy');
//    });
Route::prefix('api')
    ->middleware(['api', 'auth'])
    ->group(function () {
        Route::resource('app-errors', AppErrorController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['app-errors' => 'appError']);

        Route::resource('flows', FlowController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['flows' => 'flow']);
    });

/*
 *

 * */


