<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\Postcode;

it('Postcode passes or fail', function (string $postcode, bool $isValid) {
    expect(Validator::make(['attribute' => $postcode], ['attribute' => [new Postcode()]])->passes())->toBe($isValid);
})->with([
    ['1234AB', true],
    ['1234 AB', true],
    ['9999ZZ', true],
    ['1234ab', true],
    ['0234AB', false],
    ['1234A', false],
    ['12345AB', false],
    ['1234', false],
]);

