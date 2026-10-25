<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Multilingual bad-word detection (English, Filipino/Tagalog, Taglish and
 * Cebuano/Ilocano) against the built-in ProfanityDictionary.
 *
 * The input is never changed - a normalized copy is analysed:
 *   1. lower-case + accents stripped; look-alike symbols mapped only when
 *      they sit inside a word ("sh!t", "gag0", "$hit", but not "Room 101");
 *   2. split into words; runs of single letters re-joined ("f u c k");
 *      repeated letters collapsed ("fuuuck", "gagooo", "booboo");
 *   3. each word compared per the term's match_mode:
 *      exact    - equals the term or a listed variant;
 *      standard - also equal after phonetic folding (ph/f, c/k, v/u in
 *                 "fvck"; for Filipino-family terms also o/u, e/i and a
 *                 trailing h: "pota", "tangena", "tangah"), and 2-4
 *                 adjacent words joined ("tang ina", "puta ina");
 *      fuzzy    - also found inside a longer word ("tanginamo"), and one
 *                 or two typos away (edit distance) -> flagged only;
 *   4. masked words ("f*ck") match a term with 1-3 hidden letters.
 * "allow" terms are exceptions: a word equal to one is never matched.
 *
 * Outcome: blocked (a confident match on a "block" term), flagged (a
 * typo-distance match, or any match on a "review" term - accepted, since
 * it is uncertain) or allowed.
 */
class ProfanityDetector
{
    public const ALLOWED = 'allowed';
    public const FLAGGED = 'flagged';
    public const BLOCKED = 'blocked';

    /** Confidence at or above which a "block" term blocks. */
    private const BLOCK_CONFIDENCE = 90;

    private const LEET = ['@' => 'a', '4' => 'a', '3' => 'e', '0' => 'o', '$' => 's', '5' => 's', '1' => 'i', '!' => 'i', '7' => 't', '+' => 't'];

    private const FILIPINO_FAMILY = ['fil', 'ceb', 'ilo'];

    private const ACTION_BLOCK = 'block';
    private const ACTION_ALLOW = 'allow';
    private const MODE_EXACT = 'exact';
    private const MODE_FUZZY = 'fuzzy';

    /** @var array{terms: list<array<string, mixed>>, allow: array<string, true>}|null */
    private static ?array $dictionary = null;

    /** Returns one of ALLOWED / FLAGGED / BLOCKED. */
    public function check(?string $text): string
    {
        return $this->analyze($text)['outcome'];
    }

    /**
     * @return array{outcome: string, matches: list<array{term_id: int, token: string, confidence: int, blocking: bool}>}
     */
    public function analyze(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return ['outcome' => self::ALLOWED, 'matches' => []];
        }

        $dictionary = $this->dictionary();
        $tokens = array_values(array_filter($this->tokens($text), fn ($t) => ! isset($dictionary['allow'][$t])));

        $best = [];
        $hit = function (array $term, string $token, int $confidence) use (&$best) {
            if (($best[$term['id']]['confidence'] ?? -1) < $confidence) {
                $best[$term['id']] = [
                    'term_id' => $term['id'],
                    'token' => $token,
                    'confidence' => $confidence,
                    'blocking' => $term['action'] === self::ACTION_BLOCK && $confidence >= self::BLOCK_CONFIDENCE,
                ];
            }
        };

        foreach ($dictionary['terms'] as $term) {
            foreach ($tokens as $token) {
                if ($confidence = $this->matchWord($term, $token)) {
                    $hit($term, $token, $confidence);
                }
            }

            // Phrases split over 2-4 words ("tang ina", "puta ina mo").
            if ($term['mode'] !== self::MODE_EXACT || $term['has_phrase']) {
                for ($i = 0, $n = count($tokens); $i < $n; $i++) {
                    for ($size = 2; $size <= 4 && $i + $size <= $n; $size++) {
                        $words = array_slice($tokens, $i, $size);
                        if (array_filter($words, fn ($w) => str_contains($w, '*'))) {
                            continue;
                        }
                        $joined = $this->collapse(implode('', $words));
                        if ($confidence = $this->matchPhrase($term, $joined)) {
                            $hit($term, implode(' ', $words), $confidence);
                        }
                    }
                }
            }
        }

        $matches = array_values($best);
        $outcome = match (true) {
            (bool) array_filter($matches, fn ($m) => $m['blocking']) => self::BLOCKED,
            $matches !== [] => self::FLAGGED,
            default => self::ALLOWED,
        };

        return ['outcome' => $outcome, 'matches' => $matches];
    }

    /** @param  array<string, mixed>  $term */
    private function matchWord(array $term, string $token): int
    {
        if (str_contains($token, '*')) {
            return $this->matchMasked($term, $token);
        }

        $key = $this->key($token, $term['filipino']);

        foreach ($term['forms'] as $form) {
            if ($form['phrase_only']) {
                continue;
            }
            if ($token === $form['text']) {
                return 100;
            }
            if ($term['mode'] !== self::MODE_EXACT && $key === $form['key']) {
                return 95;
            }
        }

        if ($term['mode'] === self::MODE_FUZZY) {
            foreach ($term['forms'] as $form) {
                $length = strlen($form['text']);
                if ($form['phrase_only'] || $length < 4) {
                    continue;
                }
                if (str_contains($token, $form['text'])) {
                    return 95;
                }
                // Typo distance: only for longer words, same first letter,
                // similar length - and only ever flags, never blocks.
                if ($length >= 6 && $token[0] === $form['text'][0] && abs(strlen($token) - $length) <= 1
                    && levenshtein($key, $form['key']) <= ($length >= 9 ? 2 : 1)) {
                    return 70;
                }
            }
        }

        return 0;
    }

    /** @param  array<string, mixed>  $term */
    private function matchPhrase(array $term, string $joined): int
    {
        $key = $this->key($joined, $term['filipino']);

        foreach ($term['forms'] as $form) {
            // Joining words only counts toward multi-word forms or long
            // words, so "Bo Bo" or "as s" can't be glued into a match.
            if (! $form['phrase_only'] && strlen($form['text']) < 6) {
                continue;
            }
            if ($joined === $form['text'] || ($term['mode'] !== self::MODE_EXACT && $key === $form['key'])) {
                return 95;
            }
        }

        return 0;
    }

    /** "f*ck", "sh**": each run of masks stands for 1-3 letters. */
    private function matchMasked(array $term, string $token): int
    {
        if (strlen(str_replace('*', '', $token)) < 2) {
            return 0;
        }

        $pattern = '/^'.implode('[a-z]{1,3}', array_map('preg_quote', preg_split('/\*+/', $token))).'$/';

        foreach ($term['forms'] as $form) {
            if (! $form['phrase_only'] && preg_match($pattern, $form['text'])) {
                return 90;
            }
        }

        return 0;
    }

    /**
     * Normalized words of $text. Masks (* and #) are kept as "*" inside
     * words so "f*ck" can be matched; everything else splits words.
     *
     * @return list<string>
     */
    public function tokens(string $text): array
    {
        $text = Str::ascii(mb_strtolower($text, 'UTF-8'));
        $text = str_replace('#', '*', $text);

        // Look-alikes count as letters only inside a word; "!" only before a letter.
        $text = preg_replace_callback('/!(?=[a-z])/', fn () => 'i', $text);
        // Twice, so runs like "gag00" (second 0 sits next to a digit) are reached too.
        $text = preg_replace_callback('/(?<=[a-z*])[@43105$7+]|[@43105$7+](?=[a-z*])/', fn ($m) => self::LEET[$m[0]], $text);
        $text = preg_replace_callback('/(?<=[a-z*])[@43105$7+]|[@43105$7+](?=[a-z*])/', fn ($m) => self::LEET[$m[0]], $text);

        $tokens = [];
        $run = '';
        foreach (preg_split('/[^a-z*]+/', $text, -1, PREG_SPLIT_NO_EMPTY) as $token) {
            if (strlen($token) === 1 && $token !== '*') {
                $run .= $token;

                continue;
            }
            if ($run !== '') {
                $tokens[] = $this->collapse($run);
                $run = '';
            }
            $tokens[] = $this->collapse($token);
        }
        if ($run !== '') {
            $tokens[] = $this->collapse($run);
        }

        return $tokens;
    }

    /** Phonetic key; Filipino-family terms also fold o/u, e/i and a final h. */
    public function key(string $word, bool $filipino): string
    {
        $key = str_replace(['ph', 'ck', 'q', 'c', 'x', 'z'], ['f', 'k', 'k', 'k', 'ks', 's'], $word);
        $key = preg_replace('/(?<=[^aeiou])v(?=[^aeiou])/', 'u', $key);

        if ($filipino) {
            $key = preg_replace('/(?<=.)h$/', '', strtr($key, ['o' => 'u', 'e' => 'i']));
        }

        return $this->collapse($key);
    }

    private function collapse(string $text): string
    {
        return preg_replace('/(.)\1+/', '$1', $text);
    }

    /**
     * ProfanityDictionary, pre-normalized once per process.
     *
     * @return array{terms: list<array<string, mixed>>, allow: array<string, true>}
     */
    private function dictionary(): array
    {
        if (self::$dictionary !== null) {
            return self::$dictionary;
        }

        $terms = [];
        $allow = [];

        foreach (ProfanityDictionary::TERMS as $id => [$word, $language, $action, $mode, $variants]) {
            $filipino = in_array($language, self::FILIPINO_FAMILY, true);
            $forms = [];
            foreach (array_unique(array_merge([$word], $variants)) as $raw) {
                $words = $this->tokens($raw);
                if ($words === []) {
                    continue;
                }
                $text = $this->collapse(implode('', $words));
                $forms[] = ['text' => $text, 'key' => $this->key($text, $filipino), 'phrase_only' => count($words) > 1];
            }

            if ($action === self::ACTION_ALLOW) {
                // Single words only: an allowed phrase must not exempt its glued-together form.
                foreach ($forms as $form) {
                    if (! $form['phrase_only']) {
                        $allow[$form['text']] = true;
                    }
                }

                continue;
            }

            $terms[] = [
                'id' => $id,
                'action' => $action,
                'mode' => $mode,
                'filipino' => $filipino,
                'has_phrase' => (bool) array_filter($forms, fn ($f) => $f['phrase_only']),
                'forms' => $forms,
            ];
        }

        return self::$dictionary = ['terms' => $terms, 'allow' => $allow];
    }
}
