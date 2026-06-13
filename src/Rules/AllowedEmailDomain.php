<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class AllowedEmailDomain implements ValidationRule
{
    protected array $allowedDomains = [
      'salt.nl',
      'afnamesalt.nl',
    ];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = substr(strrchr($value, "@"), 1);
        if (!in_array($domain, $this->allowedDomains)) {
            $fail('The :attribute must be an email address with an allowed domain.');
        }
    }
}
