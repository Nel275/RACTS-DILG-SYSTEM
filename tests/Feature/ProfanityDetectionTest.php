<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\ReportRequirement;
use App\Models\User;
use App\Services\ProfanityDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Bad-word detection against a reviewed dataset of prohibited variants
 * and legitimate English/Filipino text.
 */
class ProfanityDetectionTest extends TestCase
{
    use RefreshDatabase;

    private const BLOCKED = [
        // English: exact, variants, repeats, symbols, separators, masks
        'fuck', 'fak', 'fuk', 'fvck', 'fuuuck', 'f*ck', 'f u c k', 'f.u.c.k', 'phuck', 'FUCKING report', 'sh!t', '$hit', 'b1tch', 'motherfucker',
        // Filipino: exact and common misspellings
        'putangina', 'PUTANGINA', 'tangina', 'tngina', 'tangenaa', 'gago', 'gag0', 'gagooo', 'tanga', 'tngaa', 'tangah',
        'bobo', 'b0bo', 'booboo', 'ulol', 'ul0l', 'uloll', 'pakyu', 'pakyo', 'pota', 'tarantado', 'tanginamo',
        // Multi-word and split phrases
        'putang ina', 'puta ina', 'ptang ina', 'tang-ina mo', 'p u t a n g i n a', 'pak yu',
        // Taglish
        'Ang bobo mo naman', 'you are so gago', 'tangina this report', 'wtf bakit late',
        // Regional (enabled by default)
        'bilat', 'ukinnam',
    ];

    private const ALLOWED = [
        'Juan Dela Cruz', 'Santos', 'Class Assessment', 'Bassett', 'Pedro Gagalac', 'Tae-yang Kim', 'Maria Cristina',
        'Batangas City', 'batang inanod ng baha', 'tangan ko ang ulat', 'puto at kutsinta', 'Tanda ng panahon',
        'computation of taxes', 'reputation', 'shiitake mushrooms', 'cocktail party', 'Room 101 report',
        'Ulo ng balita', 'nataranta ang lahat', 'Barangay Accomplishment Report', 'Fake news awareness seminar',
        'Sheet music', 'Gaganapin sa plaza', 'Kapitan Tangan', 'Bobby Reyes', 'Assessment of the Sangguniang Kabataan',
    ];

    private function detector(): ProfanityDetector
    {
        return app(ProfanityDetector::class);
    }

    private function user(string $role, ?Barangay $barangay = null): User
    {
        return User::create([
            'name' => ucfirst($role).' User',
            'username' => $role.'_'.substr(uniqid(), -6),
            'password' => 'password123',
            'role' => $role,
            'barangay_id' => $barangay?->id,
        ]);
    }

    public function test_prohibited_dataset_is_blocked(): void
    {
        $missed = array_filter(self::BLOCKED, fn ($text) => $this->detector()->analyze($text)['outcome'] !== ProfanityDetector::BLOCKED);

        $this->assertSame([], array_values($missed), 'Not blocked: '.implode(', ', $missed));
    }

    public function test_legitimate_dataset_has_no_false_positives(): void
    {
        $hits = array_filter(self::ALLOWED, fn ($text) => $this->detector()->analyze($text)['outcome'] !== ProfanityDetector::ALLOWED);

        $this->assertSame([], array_values($hits), 'False positives: '.implode(', ', $hits));
    }

    public function test_uncertain_matches_are_accepted_not_blocked(): void
    {
        $this->assertFalse(\App\Rules\NoInappropriateWords::contains('Hayop sa galing ang team'));

        // One typo away from a long term, and a review-only term.
        $this->assertSame(ProfanityDetector::FLAGGED, $this->detector()->analyze('tangima')['outcome']);
        $this->assertSame(ProfanityDetector::FLAGGED, $this->detector()->analyze('hayop ka')['outcome']);
    }

    public function test_backend_rejects_bad_words_without_client_side_checks(): void
    {
        $barangay = Barangay::create(['barangay_name' => 'Centro', 'status' => Barangay::STATUS_ACTIVE]);
        $log = BarangayLog::create(['item_description' => 'Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'deadline_date' => now()->addDay(), 'status' => 'active']);
        $entry = BarangayLogDetail::create(['barangay_log_id' => $log->id, 'barangay_id' => $barangay->id, 'item_description' => 'Report', 'deadline_date' => now()->addDay(), 'status' => 'pending']);
        $entry->markSubmitted(now(), 'x');

        $this->actingAs($this->user(User::ROLE_ADMIN))
            ->patch("/barangay-log-details/{$entry->id}/return", ['review_remarks' => 'Ang t@ng@ ng gumawa nito'])
            ->assertSessionHasErrors('review_remarks');

        $this->actingAs($this->user(User::ROLE_ADMIN))
            ->post('/barangay-logs', ['item_description' => 'Putang ina Report', 'deadline_date' => '2026-12-31'])
            ->assertSessionHasErrors('item_description');

        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $entry->fresh()->status);
    }
}
