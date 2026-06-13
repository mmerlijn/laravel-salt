<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\AllowedEmailDomain;

it('AllowedEmailDomain passes or fail', function (string $email, bool $isValid) {
    expect(Validator::make(['attribute' => $email], ['attribute' => [new AllowedEmailDomain()]])->passes())->toBe($isValid);
})->with([
    ['user@salt.nl', true],
    ['admin@afnamesalt.nl', true],
    ['test.user@salt.nl', true],
    ['user@example.com', false],
    ['user@gmail.com', false],
    ['user@salt.com', false],
    ['user', false],
]);

