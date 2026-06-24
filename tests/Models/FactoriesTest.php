<?php

use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowError;
use mmerlijn\LaravelSalt\Models\Note;
use mmerlijn\LaravelSalt\Models\Patient;
use mmerlijn\LaravelSalt\Models\Requester;

it('can create records with package factories', function () {
    $patient = Patient::factory()->create();
    $requester = Requester::factory()->create();
    $flowError = FlowError::factory()->create();

    $flow = Flow::factory()
        ->payload($patient)
        ->error($flowError)
        ->create();

    $note = Note::factory()
        ->forPatient($patient)
        ->create();

    expect($patient->exists)->toBeTrue()
        ->and($requester->exists)->toBeTrue()
        ->and($flowError->exists)->toBeTrue()
        ->and($flow->exists)->toBeTrue()
        ->and($note->exists)->toBeTrue();
});

it('supports requester factory states for gp and organization', function () {
    $gp = Requester::factory()->gp()->create();
    $organization = Requester::factory()->organization()->create();

    expect($gp->is_gp->value)->toBe('Y')
        ->and($gp->type->value)->toBe('zorgverlener')
        ->and($organization->is_gp->value)->toBe('N')
        ->and($organization->type->value)->toBe('onderneming');
});

it('supports requester factory zorgverlener alias', function () {
    $zorgverlener = Requester::factory()->zorgverlener()->create();

    expect($zorgverlener->type->value)->toBe('zorgverlener');
});

