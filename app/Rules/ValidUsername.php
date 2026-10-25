<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 3-30 characters, starts with a letter, then letters, numbers, dots,
 * underscores or hyphens - no spaces or other symbols, no inappropriate
 * words.
 */
class ValidUsername implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^[A-Za-z][A-Za-z0-9._\-]{2,29}$/', $value)) {
            $fail('The :attribute must be 3-30 characters, start with a letter, and contain only letters, numbers, periods, underscores or hyphens (no spaces).');

            return;
        }

        if (NoInappropriateWords::contains($value)) {
            $fail(NoInappropriateWords::MESSAGE);
        }
    }
}
