<?php

use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use Workbench\App\Models\User;

it('lists flows through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $flow = Flow::factory()->create([
        'stack' => [101],
    ]);

    $response = $this->getJson(route('flows.index'));

    $response->assertOk()
        ->assertJsonPath('data.0.id', $flow->id)
        ->assertJsonPath('data.0.stack.0', 101);
});

it('shows and edits a flow through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $appError = AppError::factory()->create([
        'notified' => [],
    ]);

    $flow = Flow::factory()->create([
        'app_error_id' => $appError->id,
    ]);

    $showResponse = $this->getJson(route('flows.show', $flow));
    $editResponse = $this->getJson(route('flows.edit', $flow));

    $showResponse->assertOk()
        ->assertJsonPath('data.id', $flow->id)
        ->assertJsonPath('data.app_error_id', $appError->id);

    $editResponse->assertOk()
        ->assertJsonPath('data.id', $flow->id)
        ->assertJsonPath('data.app_error_id', $appError->id);
});

it('resets try_after to now when updating a flow', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $flow = Flow::factory()->create([
        'try_after' => now()->addHour(),
    ]);

    $response = $this->patchJson(route('flows.update', $flow), [
        'try_after' => now()->addDays(2)->toDateTimeString(),
    ]);

    $response->assertOk()
        ->assertJsonPath('data.id', $flow->id);

    $flow->refresh();

    expect($flow->try_after->lte(now()->addSecond()))->toBeTrue();
});

it('deletes a flow through the package route', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $flow = Flow::factory()->create();

    $response = $this->deleteJson(route('flows.destroy', $flow));

    $response->assertNoContent();

    expect(Flow::query()->find($flow->id))->toBeNull();
});


