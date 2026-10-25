<?php

namespace App\Services;

/**
 * Built-in word list for ProfanityDetector - shipped with the code, not
 * editable from the app or visible to users. The detector does the hard
 * part (misspellings, symbols, spacing, Filipino spelling, split
 * phrases), so each entry only needs its base word plus the odd variant
 * that rules can't derive.
 *
 * Each entry: [word, language, action, match mode, extra variants].
 *   language: en, fil (Filipino/Tagalog), ceb (Cebuano), ilo (Ilocano) -
 *             fil/ceb/ilo get Filipino spelling folding (o/u, e/i, final h).
 *   action:   block (reject), review (uncertain - accepted), allow (a
 *             legitimate word that must never match).
 *   mode:     exact (spelling/symbol/repeat tolerant only), standard
 *             (+ phonetic folding and split phrases), fuzzy (+ inside
 *             longer words and near-misspellings).
 * Mild words with everyday meanings (hayop = animal, leche = milk) are
 * "review" so they are never rejected.
 */
final class ProfanityDictionary
{
    /** @var list<array{0: string, 1: string, 2: string, 3: string, 4: list<string>}> */
    public const TERMS = [
        // English
        ['fuck', 'en', 'block', 'fuzzy', ['fak', 'fuk', 'fck', 'fvck', 'phuck', 'phuk', 'fuq', 'fucking', 'fucker', 'fucked', 'fking', 'fuking', 'fuck you']],
        ['motherfucker', 'en', 'block', 'fuzzy', ['motherfucking', 'mofo']],
        ['shit', 'en', 'block', 'standard', ['shyt', 'sht', 'shitty', 'bullshit', 'shithead']],
        ['bitch', 'en', 'block', 'standard', ['biatch', 'bytch', 'bitches', 'sonofabitch', 'son of a bitch']],
        ['bastard', 'en', 'block', 'standard', []],
        ['asshole', 'en', 'block', 'fuzzy', ['ashole', 'arsehole']],
        ['cunt', 'en', 'block', 'standard', []],
        ['whore', 'en', 'block', 'standard', []],
        ['slut', 'en', 'block', 'standard', []],
        ['nigger', 'en', 'block', 'fuzzy', ['nigga']],
        ['faggot', 'en', 'block', 'fuzzy', []],
        ['dickhead', 'en', 'block', 'fuzzy', []],
        ['pussy', 'en', 'block', 'standard', []],
        ['ass', 'en', 'block', 'exact', []],
        ['dick', 'en', 'block', 'exact', []],
        ['cock', 'en', 'block', 'exact', []],
        ['porn', 'en', 'block', 'exact', []],
        ['wtf', 'en', 'block', 'exact', []],
        ['stfu', 'en', 'block', 'exact', []],
        ['sex', 'en', 'review', 'exact', []],
        // Filipino / Tagalog
        ['putangina', 'fil', 'block', 'fuzzy', ['putang ina', 'puta ina', 'ptang ina', 'ptangina', 'putanginamo', 'potangina', 'putang ina mo']],
        ['puta', 'fil', 'block', 'standard', ['pota', 'puta ka']],
        ['tangina', 'fil', 'block', 'fuzzy', ['tngina', 'tang ina', 'tangna', 'tanginamo', 'tang ina mo', 'tanginaka']],
        ['gago', 'fil', 'block', 'standard', ['gaga', 'gagu', 'gago ka']],
        ['tanga', 'fil', 'block', 'standard', ['tnga', 'tanga ka']],
        ['bobo', 'fil', 'block', 'exact', ['bubo ka', 'bobo ka']],
        ['ulol', 'fil', 'block', 'exact', ['ulul', 'olol']],
        ['pakyu', 'fil', 'block', 'standard', ['pak yu', 'pakyo', 'pakyou', 'fakyu', 'fak yu']],
        ['kantot', 'fil', 'block', 'fuzzy', ['kantutan']],
        ['jakol', 'fil', 'block', 'standard', ['jakulan']],
        ['pokpok', 'fil', 'block', 'standard', []],
        ['tarantado', 'fil', 'block', 'fuzzy', ['tarantada']],
        ['hinayupak', 'fil', 'block', 'fuzzy', []],
        ['pakshet', 'fil', 'block', 'standard', ['pakshit', 'pak shet']],
        ['siraulo', 'fil', 'block', 'standard', ['sira ulo']],
        ['puki', 'fil', 'block', 'exact', ['pekpek']],
        ['burat', 'fil', 'block', 'exact', []],
        ['etits', 'fil', 'block', 'standard', []],
        ['iyot', 'fil', 'block', 'exact', []],
        ['engot', 'fil', 'block', 'exact', []],
        ['ungas', 'fil', 'review', 'exact', []],
        ['lintik', 'fil', 'review', 'exact', []],
        ['leche', 'fil', 'review', 'exact', []],
        ['bwisit', 'fil', 'review', 'exact', ['buwisit']],
        ['hayop', 'fil', 'review', 'exact', ['hayop ka']],
        ['demonyo', 'fil', 'review', 'exact', []],
        ['inutil', 'fil', 'review', 'exact', []],
        // Regional (reviewed starter set - extend from the admin page)
        ['bilat', 'ceb', 'block', 'exact', []],
        ['oten', 'ceb', 'block', 'exact', []],
        ['yawa', 'ceb', 'review', 'exact', ['yawaa ka']],
        ['pisti', 'ceb', 'review', 'exact', ['pisteng yawa']],
        ['ukinnam', 'ilo', 'block', 'standard', ['okinnam', 'ukininam']],
        // Legitimate words that resemble prohibited ones
        ['shiitake', 'en', 'allow', 'exact', ['shitake']],
        ['cocktail', 'en', 'allow', 'exact', []],
        ['scunthorpe', 'en', 'allow', 'exact', []],
        ['tangan', 'fil', 'allow', 'exact', []],
        ['tanda', 'fil', 'allow', 'exact', []],
        ['puto', 'fil', 'allow', 'exact', []],
    ];
}
