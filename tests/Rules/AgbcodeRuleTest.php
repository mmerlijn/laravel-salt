<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\Agbcode;

it('Agbcode passes or fail', function (mixed $agbcode, bool $isValid) {
    expect(Validator::make(['attribute' => $agbcode], ['attribute' => [new Agbcode()]])->passes())->toBe($isValid);
})->with([
    ['12345678', true],
    [' 87654321 ', true],
    ['1234567', false],
    ['123456789', false],
    ['12A45678', false],
    ['1234 678', false],
    ['', true],
]);

