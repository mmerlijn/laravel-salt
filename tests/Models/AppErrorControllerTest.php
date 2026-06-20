<?php

use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Models\AppError;
use Workbench\App\Models\User;

it('denies guests from updating an app error', function () {

    $appError = AppError::factory()->create();

    // We loggen hier NIET in en doen direct de request
    $response = $this->patchJson(route('app-errors.update', $appError->id), [
        'solution' => 'new solution',
    ]);

    // Omdat het een JSON request is (patchJson), verwacht je een 401 Unauthorized statuscode
    $response->assertUnauthorized();

    // Of als je de web-middleware gebruikt die naar een loginpagina redirect, gebruik je:
    // $response->assertRedirect('/login');
});

it('lists app errors through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    AppError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'notify' => false,
        'class' => 'TestClass',
        'notified' => [],
    ]);

    $response = $this->getJson(route('app-errors.index'));

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'id',
                'level',
                'class',
                'notify',
            ]],
        ]);
});

it('shows an app error through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $appError = AppError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'notify' => false,
        'class' => 'TestClass',
        'notified' => [],
    ]);
    $response = $this->getJson(route('app-errors.show', $appError->id));

    $response->assertOk()
        ->assertJsonPath('data.id', $appError->id)
        ->assertJsonPath('data.level', ErrorLevelEnum::MENNO->value)
        ->assertJsonPath('data.class', 'TestClass')
        ->assertJsonPath('data.notify', false);
    expect($appError->level)->toBe(ErrorLevelEnum::MENNO)
        ->and($appError->class)->toBe('TestClass');
});

it('updates an app error through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $appError = AppError::factory()->create([
        'solution' => 'old',
        'notify' => false,
        'notified' => [],
    ]);

    $response = $this->patchJson(route('app-errors.update', $appError->id), [
        'solution' => 'new solution',
        'notify' => true,
        'notified' => [],
    ]);
    $response->assertOk()
        ->assertJsonPath('data.solution', 'new solution')
        ->assertJsonPath('data.notify', true);

    expect($appError->refresh()->solution)->toBe('new solution')
        ->and($appError->fresh()->notify)->toBeTrue();
});

it('deletes an app error through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $appError = AppError::factory()->create([
        'notified' => [],
    ]);

    $response = $this->deleteJson(route('app-errors.destroy', $appError));

    $response->assertNoContent();

    $this->assertDatabaseHas('app_errors', [
        'id' => $appError->id,
    ]);
    expect(AppError::withTrashed()->find($appError->id)?->deleted_at)->not->toBeNull();
});









