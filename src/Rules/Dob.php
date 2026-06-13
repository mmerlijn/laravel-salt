<?php

namespace mmerlijn\LaravelSalt\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class Dob implements ValidationRule
{
    protected int $maxBeforeYears = 120;
    protected int $minBeforeYears = 0;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dob = $this->parseStrictDate($value);

        if (!$dob) {
            $fail('Geen geldig datum formaat');
            return;
        }

        if ($dob->age > $this->maxBeforeYears) {
            $fail('Ongeldige geboortedatum (ouder dan ' . $this->maxBeforeYears . ' jaar)');
            return;
        }

        if ($this->minBeforeYears && $dob->isAfter(now()->subYears($this->minBeforeYears))) {
            $fail('Clienten onder de ' . $this->minBeforeYears . ' jaar kunnen alleen telefonisch een afspraak maken, bel voor een afspraak: ' . config('salt.phone', '088 9100100'));
            return;
        }

        if ($dob->isAfter(now())) {
            $fail('Datum is in de toekomst');
        }
    }

    public function max(int $years): static
    {
        $this->maxBeforeYears = $years;
        return $this;
    }
    public function min(int $years): static
    {
        $this->minBeforeYears = $years;
        return $this;
    }

    private function parseStrictDate(mixed $value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance(\DateTime::createFromInterface($value))->startOfDay();
        }

        $rawValue = trim((string) $value);
        if ($rawValue === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'd-m-Y',
            'j-n-Y',
            'Y/m/d',
            'd/m/Y',
            'j/n/Y',
        ];

        foreach ($formats as $format) {
            $parsedDate = \DateTimeImmutable::createFromFormat('!' . $format, $rawValue);
            $parseErrors = \DateTimeImmutable::getLastErrors();
            $hasParseErrors = is_array($parseErrors)
                && (($parseErrors['warning_count'] ?? 0) > 0 || ($parseErrors['error_count'] ?? 0) > 0);

            if (!$parsedDate || $hasParseErrors) {
                continue;
            }

            if ($parsedDate->format($format) !== $rawValue) {
                continue;
            }

            return Carbon::instance(\DateTime::createFromImmutable($parsedDate))->startOfDay();
        }

        return null;
    }
}
