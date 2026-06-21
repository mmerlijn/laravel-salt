<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\RequestNr;

it('RequestNr passes or fail', function (string $requestNr, bool $isValid) {
    expect(Validator::make(['attribute' => $requestNr], ['attribute' => [new RequestNr()]])->passes())->toBe($isValid);
})->with([
    ['ZD12345678', true],
    ['ZP99999999', true],
    ['CW00000000', true],
    ['PG123456789', true],
    [' pg123456789 ', true],
    ['ZD1234567', false],
    ['PG12345678', true],
    ['XX12345678', false],
    ['ZD1234567A', false],
    ['', true],
]);

