<?php

namespace App\Rules;

use App\Services\ProfanityDetector;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Rejects text with prohibited language (English, Filipino/Tagalog,
 * Taglish, Cebuano/Ilocano) - detected automatically by
 * App\Services\ProfanityDetector, including misspellings, symbols and
 * spacing tricks. Uncertain look-alikes are let through, never rejected.
 */
class NoInappropriateWords implements ValidationRule
{
    public const MESSAGE = 'The :attribute may contain prohibited or inappropriate language. Please review and revise your text.';

    public static function contains(?string $text): bool
    {
        return app(ProfanityDetector::class)->check($text) === ProfanityDetector::BLOCKED;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && self::contains($value)) {
            $fail(self::MESSAGE);
        }
    }
}
