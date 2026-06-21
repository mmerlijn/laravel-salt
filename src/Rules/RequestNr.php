<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequestNr implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request_nr = trim($value);
        if (!preg_match('/^((PG|ZD|ZP|CW|CS)\d{8,9})$/i', $request_nr)) {
            $fail('Geen geldig aanvraagnummer.');
        }
    }
}