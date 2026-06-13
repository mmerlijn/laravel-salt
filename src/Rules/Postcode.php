<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Postcode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/', $value) !== 1) {
            $fail('Geen geldige Nederlandse postcode.');
        }
    }
}