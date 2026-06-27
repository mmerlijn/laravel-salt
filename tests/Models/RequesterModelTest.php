<?php

use Illuminate\Support\Facades\Queue;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterResource;
use mmerlijn\LaravelSalt\Jobs\GetCaregiverJob;
use mmerlijn\LaravelSalt\Models\Requester;

it('connects requesters and organizations through the pivot relation', function () {
    $requester = Requester::factory()->zorgverlener()->createQuietly([
        'agbcode' => '12345671',
    ]);
    $organization = Requester::factory()->organization()->createQuietly([
        'agbcode' => '12345672',
    ]);

    $organization->members()->attach($requester->agbcode);

    expect($organization->members()->pluck('agbcode')->all())->toContain($requester->agbcode)
        ->and($requester->organizations()->pluck('agbcode')->all())->toContain($organization->agbcode);
});

it('returns expected members and organizations via model relations', function () {
    $requester = Requester::factory()->zorgverlener()->createQuietly([
        'agbcode' => '12345673',
    ]);
    $organization = Requester::factory()->organization()->createQuietly([
        'agbcode' => '12345674',
    ]);

    $organization->members()->attach($requester->agbcode);
    expect($requester->organizations()->pluck('agbcode')->all())->toContain($organization->agbcode)
        ->and($organization->members()->pluck('agbcode')->all())->toContain($requester->agbcode);
});

it('renders RequesterResource without triggering RequesterObserver side effects', function () {
    config()->set('laravel_salt.vektis', true);
    Queue::fake();

    $requester = Requester::factory()->zorgverlener()->createQuietly([
        'agbcode' => '12345675',
    ]);
    $organization = Requester::factory()->organization()->createQuietly([
        'agbcode' => '12345676',
    ]);

    $organization->members()->attach($requester->agbcode);

    $data = RequesterResource::make(
        $organization->fresh()->load('members')
    )->resolve();

    expect($data['agbcode'])->toBe('12345676')
        ->and($data['members'])->toHaveCount(1)
        ->and($data['members'][0]['agbcode'])->toBe('12345675');

    Queue::assertNotPushed(GetCaregiverJob::class);
});
