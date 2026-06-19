<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Phone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value == 'nb') {
            return;
        }
        $value = str_replace([" ", "-", ".", "(0)"], "", $value);
        if(!$value){
            return;
        }
        if (str_starts_with("00", $value)) {
            $value = str_replace("00", "+", $value);
        }
        //country specific validation
        if (str_starts_with($value, '+')) {
            foreach (self::$country as $code => $regex) {
                if (str_starts_with($value, $code)) {
                    if (!preg_match('/' . $regex . '/', $value)) {
                        $fail('Het telefoonnummer is niet correct ingevuld');
                        return;
                    }
                }
            }
        }
        if (!preg_match('/^((\+[1-9]\d{1,2}|0{2}\d{2,3})[1-9]\d{4,12})|0([1-9]\d{8})$/', $value)) {
            $fail('Het telefoonnummer is niet correct ingevuld');
        }
    }

    //TODO https://en.wikipedia.org/wiki/Telephone_numbers_in_Europe add more countries to validation
    private static array $country = [
        '+31' => '^(\+31[1-9]\d{8})$',
        '+32' => '^(\+32[1-9]\d{7,9})$',
        '+33' => '^(\+33[1-9]\d{8})$',
        '+34' => '^(\+34\d{9})$',
        '+36' => '^(\+36\d{8,9})$',
        '+39' => '^(\+39\d{6,12})$',
        '+40' => '^(\+40[1-9]\d{8})$',
        '+43' => '^(\+43[1-9]\d{3,12})$',
        '+45' => '^(\+45\d{8})$',
        '+46' => '^(\+46[1-9]\d{5,8})$',
        '+47' => '^(\+47\d{4,12})$',
        '+48' => '^(\+48\d{9})$',
        '+49' => '^(\+49[1-9]\d{2,11})$',
        '+351' => '^(\+351\d{9})$',
        '+352' => '^(\+352\d{4,12})$',
    ];

}
