<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\Phone;

it('Phone passes or fail', function (string $phone, bool $isValid) {
    expect(Validator::make(['attribute' => $phone], ['attribute' => [new Phone()]])->passes())->toBe($isValid);
})->with([
    ['nb', true],
    ['+31612345678', true],
    ['0031612345678', true],
    ['06 12345678', true],
    ['06-12345678', true],
    ['+32912345678', true],
    ['06', false],
    ['061234567', false],
    ['invalid', false],
    ['+1234', false],
]);

