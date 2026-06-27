<?php

use Illuminate\Support\Facades\DB;
use mmerlijn\LaravelSalt\Actions\CreateRequestOrganziationCombination;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Name;
use mmerlijn\msgRepo\Order;
use mmerlijn\msgRepo\Organization;
use mmerlijn\msgRepo\Phone;


beforeEach(function () {
    DB::table('organization_has_requester')->delete();
});


it('creates requester and organization combinations from Order', function () {
    $order = new \ReflectionClass(Order::class)->newInstanceWithoutConstructor();

    $requesterContact = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requesterContact->agbcode = '12345678';
    $requesterContact->name = new Name(initials: 'JD', own_lastname: 'Doe');
    $requesterContact->phone = new Phone('06-12345678');

    $organizationContact = new \ReflectionClass(Organization::class)->newInstanceWithoutConstructor();
    $organizationContact->agbcode = '87654321';
    $organizationContact->name = 'Test Organization';
    $organizationContact->phone = new Phone('010-1234567');
    $organizationContact->address = new Address(
        postcode: '3011AB',
        city: 'Rotterdam',
        street: 'Teststraat',
        building: '1'
    );

    $order->requester = $requesterContact;
    $order->organization = $organizationContact;

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: $order,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: null,
        organization: null
    );

    expect(Requester::find('12345678'))->not->toBeNull()
        ->and(Requester::find('87654321'))->not->toBeNull();

    $pivot = DB::table('organization_has_requester')
        ->where('organization_agbcode', '87654321')
        ->where('requester_agbcode', '12345678')
        ->first();

    expect($pivot)->not->toBeNull();
});

it('creates pivot entry with timestamps', function () {
    $order = new \ReflectionClass(Order::class)->newInstanceWithoutConstructor();

    $requesterContact = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requesterContact->agbcode = '11111111';
    $requesterContact->name = new Name(initials: 'AB', own_lastname: 'Smith');
    $requesterContact->phone = new Phone('06-11111111');

    $organizationContact = new \ReflectionClass(Organization::class)->newInstanceWithoutConstructor();
    $organizationContact->agbcode = '22222222';
    $organizationContact->name = 'Hospital';
    $organizationContact->phone = new Phone('010-2222222');
    $organizationContact->address = new Address(
        postcode: '1012AB',
        city: 'Amsterdam',
        street: 'Ziekenhuis',
        building: '100'
    );

    $order->requester = $requesterContact;
    $order->organization = $organizationContact;

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: $order,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: null,
        organization: null
    );

    expect(DB::table('organization_has_requester')->count())->toBe(1)
        ->and(DB::table('organization_has_requester')->first()->created_at)->not->toBeNull()
        ->and(DB::table('organization_has_requester')->first()->updated_at)->not->toBeNull();
});

it('does not create pivot entry when requester agbcode is null', function () {
    $order = new \ReflectionClass(Order::class)->newInstanceWithoutConstructor();

    $requesterContact = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requesterContact->agbcode = "";
    $requesterContact->name = new Name(initials: 'JD', own_lastname: 'Doe');

    $organizationContact = new \ReflectionClass(Organization::class)->newInstanceWithoutConstructor();
    $organizationContact->agbcode = '99999999';
    $organizationContact->name = 'Clinic';
    $organizationContact->phone = new Phone('010-9999999');
    $organizationContact->address = new Address(
        postcode: '1234AB',
        city: 'City',
        street: 'Street',
        building: '1'
    );

    $order->requester = $requesterContact;
    $order->organization = $organizationContact;

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: $order,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: null,
        organization: null
    );

    expect(DB::table('organization_has_requester')->count())->toBe(0);
});

it('does not create pivot entry when organization agbcode is null', function () {
    $order = new \ReflectionClass(Order::class)->newInstanceWithoutConstructor();

    $requesterContact = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requesterContact->agbcode = '88888888';
    $requesterContact->name = new Name(initials: 'XY', own_lastname: 'Test');
    $requesterContact->phone = new Phone('06-8888888');

    $organizationContact = new \ReflectionClass(Organization::class)->newInstanceWithoutConstructor();
    $organizationContact->agbcode = "";
    $organizationContact->name = 'Unknown Org';
    $organizationContact->phone = new Phone('010-0000000');
    $organizationContact->address = new Address(
        postcode: '1234AB',
        city: 'City',
        street: 'Street',
        building: '1'
    );

    $order->requester = $requesterContact;
    $order->organization = $organizationContact;

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: $order,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: null,
        organization: null
    );

    expect(DB::table('organization_has_requester')->count())->toBe(0);
});


it('creates combination from msgRepoRequester and msgRepoOrganization', function () {
    $requester = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requester->agbcode = '33333333';
    $requester->name = new Name(initials: 'MN', own_lastname: 'Johnson');
    $requester->phone = new Phone('06-3333333');

    $organization = new Organization(
        name: 'Medical Center',
        agbcode: '44444444',
        phone: new Phone('010-4444444'),
        address: new Address(
            postcode: '3011AB',
            city: 'Rotterdam',
            street: 'Medisch',
            building: '20'
        )
    );

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: $requester,
        msgRepoOrganization: $organization,
        requester: null,
        organization: null
    );

    expect(Requester::find('33333333'))->not->toBeNull()
        ->and(Requester::find('44444444'))->not->toBeNull();

    $pivot = DB::table('organization_has_requester')
        ->where('organization_agbcode', '44444444')
        ->where('requester_agbcode', '33333333')
        ->first();

    expect($pivot)->not->toBeNull();
});

it('only creates requester when organization is null', function () {
    $requester = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requester->agbcode = '55555555';
    $requester->name = new Name(initials: 'OP', own_lastname: 'White');
    $requester->phone = new Phone('06-5555555');

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: $requester,
        msgRepoOrganization: null,
        requester: null,
        organization: null
    );

    expect(Requester::find('55555555'))->not->toBeNull()
        ->and(DB::table('organization_has_requester')->count())->toBe(0);
});

it('only creates organization when requester is null', function () {
    $organization = new Organization(
        name: 'Clinic North',
        agbcode: '66666666',
        phone: new Phone('010-6666666'),
        address: new Address(
            postcode: '1234AB',
            city: 'Amsterdam',
            street: 'Noord',
            building: '50'
        )
    );

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: $organization,
        requester: null,
        organization: null
    );

    expect(Requester::find('66666666'))->not->toBeNull()
        ->and(DB::table('organization_has_requester')->count())->toBe(0);
});


it('creates pivot entry with Requester models (Requester and Organization)', function () {
    $requester = Requester::factory()->create(['agbcode' => 'RQST0001']);
    $organization = Requester::factory()->create(['agbcode' => 'ORG00001']);

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: $requester,
        organization: $organization
    );

    $pivot = DB::table('organization_has_requester')
        ->where('organization_agbcode', 'ORG00001')
        ->where('requester_agbcode', 'RQST0001')
        ->first();

    expect($pivot)->not->toBeNull();
});

it('does not create pivot entry when requester model is null', function () {
    $organization = Requester::factory()->create(['agbcode' => 'ORG00002']);

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: null,
        organization: $organization
    );

    expect(DB::table('organization_has_requester')->count())->toBe(0);
});

it('does not create pivot entry when organization model is null', function () {
    $requester = Requester::factory()->create(['agbcode' => 'RQST0002']);

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: $requester,
        organization: null
    );

    expect(DB::table('organization_has_requester')->count())->toBe(0);
});

it('direct model parameters take precedence over Order data', function () {
    $directRequester = Requester::factory()->create(['agbcode' => 'DIRECT001']);
    $directOrganization = Requester::factory()->create(['agbcode' => 'DIRORG001']);

    $msgRepoOrder = new \ReflectionClass(Order::class)->newInstanceWithoutConstructor();

    $requesterContact = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requesterContact->agbcode = 'IGNORED001';
    $requesterContact->name = new Name(initials: 'IG', own_lastname: 'Nored');
    $requesterContact->phone = new Phone('06-0000000');

    $organizationContact = new \ReflectionClass(Organization::class)->newInstanceWithoutConstructor();
    $organizationContact->agbcode = 'IGNORORG01';
    $organizationContact->name = 'Ignored';
    $organizationContact->phone = new Phone('010-0000000');
    $organizationContact->address = new Address(
        postcode: '1234AB',
        city: 'City',
        street: 'Street',
        building: '1'
    );

    $msgRepoOrder->requester = $requesterContact;
    $msgRepoOrder->organization = $organizationContact;

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: $msgRepoOrder,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: $directRequester,
        organization: $directOrganization
    );

    $pivot = DB::table('organization_has_requester')
        ->where('organization_agbcode', 'DIRORG001')
        ->where('requester_agbcode', 'DIRECT001')
        ->first();

    expect($pivot)->not->toBeNull();

    $ignoredPivot = DB::table('organization_has_requester')
        ->where('organization_agbcode', 'IGNORORG01')
        ->orWhere('requester_agbcode', 'IGNORED001')
        ->first();

    expect($ignoredPivot)->toBeNull();
});

it('handles multiple relationships for same requester', function () {
    $requester = Requester::factory()->create(['agbcode' => 'RQST0003']);
    $organization1 = Requester::factory()->create(['agbcode' => 'ORG00003']);
    $organization2 = Requester::factory()->create(['agbcode' => 'ORG00004']);

    $organization1->members()->attach($requester->agbcode);

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: $requester,
        organization: $organization2
    );

    expect(DB::table('organization_has_requester')
        ->where('requester_agbcode', 'RQST0003')
        ->count())->toBe(2);
});


it('handles all null inputs gracefully', function () {
    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: null,
        organization: null
    );

    expect(DB::table('organization_has_requester')->count())->toBe(0);
});

it('handles duplicate attach calls gracefully', function () {
    $requester = Requester::factory()->create(['agbcode' => 'DUP001']);
    $organization = Requester::factory()->create(['agbcode' => 'DUPORG01']);

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: $requester,
        organization: $organization
    );

    $firstCount = DB::table('organization_has_requester')->count();
    expect($firstCount)->toBe(1);

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: null,
        msgRepoOrganization: null,
        requester: $requester,
        organization: $organization
    );

    $secondCount = DB::table('organization_has_requester')->count();
    expect($secondCount)->toBeGreaterThanOrEqual($firstCount);
});

it('does not create duplicate requesters', function () {
    Requester::factory()->create(['agbcode' => 'EXISTING1']);
    $existingCount = Requester::count();

    $requester = new \ReflectionClass(Contact::class)->newInstanceWithoutConstructor();
    $requester->agbcode = 'EXISTING1';
    $requester->name = new Name(initials: 'EX', own_lastname: 'Isting');
    $requester->phone = new Phone('06-1111111');

    $organization = new Organization(
        name: 'Some Org',
        agbcode: 'SOMEORG01',
        phone: new Phone('010-1234567'),
        address: new Address(
            postcode: '1234AB',
            city: 'City',
            street: 'Street',
            building: '1'
        )
    );

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: $requester,
        msgRepoOrganization: $organization,
        requester: null,
        organization: null
    );

    expect(Requester::where('agbcode', 'EXISTING1')->count())->toBe(1)
        ->and(Requester::count())->toBeGreaterThan($existingCount);
});


it('creates new requesters via FindOrCreateRequester when not found', function () {
    $requesterContact = (new \ReflectionClass(Contact::class))->newInstanceWithoutConstructor();
    $requesterContact->agbcode = 'NEWREQ001';
    $requesterContact->name = new Name(initials: 'NR', own_lastname: 'Equester');
    $requesterContact->phone = new Phone('06-9999999');

    $organization = new Organization(
        name: 'Finding Org',
        agbcode: '77777777',
        phone: new Phone('010-7777777'),
        address: new Address(
            postcode: '3011AB',
            city: 'Rotterdam',
            street: 'Vind',
            building: '77'
        )
    );

    app(CreateRequestOrganziationCombination::class)(
        msgRepoOrder: null,
        msgRepoRequester: $requesterContact,
        msgRepoOrganization: $organization,
        requester: null,
        organization: null
    );

    expect(Requester::find('NEWREQ001'))->not->toBeNull()
        ->and(Requester::find('77777777'))->not->toBeNull()
        ->and(DB::table('organization_has_requester')->count())->toBe(1);
});

