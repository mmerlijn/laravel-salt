<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\Dob;

it('Dob passes or fail', function (string $date, bool $isValid) {
    expect(Validator::make(['attribute' => $date], ['attribute' => [new Dob()]])->passes())->toBe($isValid);

})->with([
    ['01-05-1900', false],
    ['2000-02-30', false],
    ['2020-02-30', false],
    ['2020-01-01', true],
    ['2020-12-31', true],
    ['2020-13-01', false],
    ['2020-00-01', false],
    ['2020-01-00', false],
    ['2020-01-32', false],
    ['25-12-2000',true],
    ['25-13-2000', false],
    ['1-1-2000', true],
    ['2000/01/01', true],
    ['25/01/2000', true],

]);

it('Dob validator can use max',function (string $date, int $max, bool $isValid) {
    expect(Validator::make(['attribute' => $date], ['attribute' => [new Dob()->max($max)]])->passes())->toBe($isValid);
})->with([
    ['01-05-2020',120 ,true],
    ['01-05-2000',20 ,false],
]);

it('Dob validator can use min',function (string $date, int $min, bool $isValid) {
    expect(Validator::make(['attribute' => $date], ['attribute' => [new Dob()->min($min)]])->passes())->toBe($isValid);
})->with([
    ['01-05-2020',5 ,true],
    ['01-05-2020',30 ,false],
]);
