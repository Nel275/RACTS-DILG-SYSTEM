<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Letters (incl. Ñ and accents), spaces, periods, apostrophes and
 * hyphens only - at least 2 letters, no digits or symbols, no run of the
 * same character 4+ times ("Jooooohn"), and no inappropriate words.
 */
class ValidPersonName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute is invalid.');

            return;
        }

        if (! preg_match('/^(?=(?:.*\pL){2})[\pL .\'\-]+$/u', $value)) {
            $fail('The :attribute may only contain letters, spaces, periods, apostrophes and hyphens (no numbers or symbols).');

            return;
        }

        if (preg_match('/(.)\1{3,}/u', $value)) {
            $fail('The :attribute looks invalid (a character is repeated too many times).');

            return;
        }

        if (NoInappropriateWords::contains($value)) {
            $fail(NoInappropriateWords::MESSAGE);
        }
    }
}
