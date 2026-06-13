<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Agbcode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Dutch AGB code validation: 8 digits
        if (!(preg_match('/^\d{8}$/', trim($value)) == 1)) {
            $fail('Dit is geen geldig AGBcode');
        }
    }
}