<?php

use Illuminate\Support\Facades\DB;
use mmerlijn\LaravelSalt\Actions\FindOrCreateRequester;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Name;
use mmerlijn\msgRepo\Organization as MsgOrganization;
use mmerlijn\msgRepo\Phone;

it('creates a requester from Contact input', function () {
    $contact = (new \ReflectionClass(Contact::class))->newInstanceWithoutConstructor();
    $contact->agbcode = '12345678';
    $contact->name = new Name(initials: 'JD', own_lastname: 'Doe');
    $contact->phone = new Phone('06-12345678');

    $requester = app(FindOrCreateRequester::class)($contact);

    expect($requester)->toBeInstanceOf(Requester::class)
        ->and($requester->agbcode)->toBe('12345678')
        ->and($requester->type)->toBe(VektisType::ZORGVERLENER);

    $stored = DB::table('requesters')->where('agbcode', '12345678')->first();

    expect($stored)->not->toBeNull()
        ->and($stored->phone)->toBe('0612345678')
        ->and($stored->own_lastname)->toBe('Doe')
        ->and($stored->vektis_name)->toBe('Doe, J.D.');
});

it('creates a requester from Organization input', function () {
    $organization = new MsgOrganization(
        name: 'Test Organization',
        agbcode: '23456789',
        phone: new Phone('06 11112222'),
        address: new Address(
            postcode: '1234AB',
            city: 'Utrecht',
            street: 'Straat',
            building: '12',
            postbus: '34'
        )
    );

    $requester = app(FindOrCreateRequester::class)($organization);

    expect($requester)->toBeInstanceOf(Requester::class)
        ->and($requester->agbcode)->toBe('23456789')
        ->and($requester->type)->toBe(VektisType::ONDERNEMING);

    $stored = DB::table('requesters')->where('agbcode', '23456789')->first();

    expect($stored)->not->toBeNull()
        ->and($stored->city)->toBe('Utrecht')
        ->and($stored->postcode)->toBe('1234AB')
        ->and($stored->phone)->toBe('0611112222')
        ->and($stored->vektis_name)->toBe('Test Organization');
});





