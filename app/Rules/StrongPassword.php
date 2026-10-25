<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * 8-64 characters with a letter and a number, no spaces, and not
 * containing the account's own username.
 */
class StrongPassword implements ValidationRule
{
    public function __construct(private readonly ?string $username = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || strlen($value) < 8 || strlen($value) > 64) {
            $fail('The :attribute must be 8-64 characters long.');

            return;
        }

        if (preg_match('/\s/', $value)) {
            $fail('The :attribute must not contain spaces.');

            return;
        }

        if (! preg_match('/\pL/u', $value) || ! preg_match('/\pN/u', $value)) {
            $fail('The :attribute must contain at least one letter and one number.');

            return;
        }

        if ($this->username && strlen($this->username) >= 3 && stripos($value, $this->username) !== false) {
            $fail('The :attribute must not contain the username.');
        }
    }
}
