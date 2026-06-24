<?php

use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Models\FlowError;
use Workbench\App\Models\User;

it('denies guests from updating an app error', function () {

    $flowError = FlowError::factory()->create();

    // We loggen hier NIET in en doen direct de request
    $response = $this->patchJson(route('flow-errors.update', $flowError->id), [
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
    FlowError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'notify' => false,
        'class' => 'TestClass',
        'notified' => [],
    ]);

    $response = $this->getJson(route('flow-errors.index'));

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
    $flowError = FlowError::factory()->create([
        'level' => ErrorLevelEnum::MENNO,
        'notify' => false,
        'class' => 'TestClass',
        'notified' => [],
    ]);
    $response = $this->getJson(route('flow-errors.show', $flowError->id));

    $response->assertOk()
        ->assertJsonPath('data.id', $flowError->id)
        ->assertJsonPath('data.level', ErrorLevelEnum::MENNO->value)
        ->assertJsonPath('data.class', 'TestClass')
        ->assertJsonPath('data.notify', false);
    expect($flowError->level)->toBe(ErrorLevelEnum::MENNO)
        ->and($flowError->class)->toBe('TestClass');
});

it('updates an app error through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $flowError = FlowError::factory()->create([
        'solution' => 'old',
        'notify' => false,
        'notified' => [],
    ]);

    $response = $this->patchJson(route('flow-errors.update', $flowError->id), [
        'solution' => 'new solution',
        'notify' => true,
        'notified' => [],
    ]);
    $response->assertOk()
        ->assertJsonPath('data.solution', 'new solution')
        ->assertJsonPath('data.notify', true);

    expect($flowError->refresh()->solution)->toBe('new solution')
        ->and($flowError->fresh()->notify)->toBeTrue();
});

it('deletes an app error through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $flowError = FlowError::factory()->create([
        'notified' => [],
    ]);

    $response = $this->deleteJson(route('flow-errors.destroy', $flowError));

    $response->assertNoContent();

    $this->assertDatabaseHas('flow_errors', [
        'id' => $flowError->id,
    ]);
    expect(FlowError::withTrashed()->find($flowError->id)?->deleted_at)->not->toBeNull();
});









