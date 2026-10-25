<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * For free-text titles (report names, descriptions, designations):
 * letters, numbers, spaces and ordinary punctuation only - no markup
 * characters (< > { } [ ] \ ^ ~ | `), at least one letter, no
 * inappropriate words.
 */
class PlainText implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute is invalid.');

            return;
        }

        if (! preg_match('/^(?=.*\pL)[\pL\pN .,\'"()&\/:;#%+!?\-_]+$/u', $value)) {
            $fail('The :attribute may only contain letters, numbers, spaces and basic punctuation (. , \' " ( ) & / : ; # % + - _).');

            return;
        }

        if (NoInappropriateWords::contains($value)) {
            $fail(NoInappropriateWords::MESSAGE);
        }
    }
}
