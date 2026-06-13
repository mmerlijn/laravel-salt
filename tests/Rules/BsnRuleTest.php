<?php

use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Rules\Bsn;

it('bsn passes - fails', function () {
  expect(Validator::make(['attribute' => '123456782'], ['attribute' => [new Bsn]])->passes())->toBeTrue()
      ->and(Validator::make(['attribute' => ''], ['attribute' => [new Bsn]])->passes())->toBeTrue()
      ->and(Validator::make(['attribute' => '111111110'], ['attribute' => [new Bsn]])->passes())->toBeFalse()
      ->and(Validator::make(['attribute' => '123456789'], ['attribute' => [new Bsn]])->passes())->toBeFalse()
      ->and(Validator::make(['attribute' => 'abc'], ['attribute' => [new Bsn]])->passes())->toBeFalse()
      ->and(Validator::make(['attribute' => '000000000'], ['attribute' => [new Bsn]])->passes())->toBeFalse()
      ->and(Validator::make(['attribute' => '000000000'], ['attribute' => [new Bsn()->strict(false)]])->passes())->toBeTrue()
  ;
});
