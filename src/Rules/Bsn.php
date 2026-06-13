<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Bsn implements ValidationRule
{
    private bool $strict = true;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $bsn = trim($value);
        if ($bsn) {
            // lijst met nummers die qua check kloppen, maar toch niet geldig zijn
            $aInvalid = [
                '111111110',
                '999999990',
                '000000000',
                '999999999'
            ];

            if (!$this->strict) {
                $aInvalid = ['111111110', '999999990']; //9x0 wel toestaan
            }
            $bsn = strlen($bsn) < 9 ? '0' . $bsn : $bsn;
            if (strlen($bsn) != 9 || !ctype_digit($bsn) || in_array($bsn, $aInvalid)) {
                $fail($this->msg());
            }

            $result = 0;
            $products = range(9, 2);
            $products[] = -1;

            foreach (str_split($bsn) as $i => $char) {
                $result += (int)$char * $products[$i];
            }
            if (!($result % 11 === 0)) {
                $fail($this->msg());
            };
        }
    }

    public function strict(bool $strict = true): Bsn
    {
        $this->strict = $strict;
        return $this;
    }

    private function msg(): string
    {
        if ($this->strict) {
            return 'Dit is geen geldig BSN nummer';
        } else {
            return 'Dit is geen geldig BSN nummer, bij onbekend BSN gebruik 000000000 (9x0)';
        }
    }
}