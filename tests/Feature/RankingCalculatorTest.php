<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Services\RankingCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * New in the redesign (Phase 6, replacing the old ComplianceTrackingTest,
 * which tested the now-deleted ComplianceCalculator). Covers the
 * scoring/ranking rules from the client spec (Section 2) that every
 * page - the Barangay/SK Reports grids, Analytics & Ranking, Export,
 * and the Dashboard - all depend on RankingCalculator to get right.
 */
class RankingCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function barangay(string $name): Barangay
    {
        return Barangay::create([
            'barangay_name' => $name,
            'status' => Barangay::STATUS_ACTIVE,
        ]);
    }

    private function requirement(string $name, string $frequency, string $council = ReportRequirement::COUNCIL_BARANGAY): ReportRequirement
    {
        return ReportRequirement::create([
            'report_name' => $name,
            'council_type' => $council,
            'frequency' => $frequency,
            'display_order' => 0,
            'is_active' => true,
        ]);
    }

    public function test_green_yellow_red_score_100_50_and_0(): void
    {
        $barangay = $this->barangay('Barangay A');
        $green = $this->requirement('Green Report', ReportRequirement::FREQUENCY_MONTHLY);
        $yellow = $this->requirement('Yellow Report', ReportRequirement::FREQUENCY_MONTHLY);
        $red = $this->requirement('Red Report', ReportRequirement::FREQUENCY_MONTHLY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $green->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $yellow->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'yellow']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $red->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'red']);

        $row = app(RankingCalculator::class)
            ->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09')
            ->firstWhere('barangay.id', $barangay->id);

        // (100 + 50 + 0) / 3
        $this->assertSame(50.0, $row['percentage']);
    }

    public function test_an_ungraded_cell_is_excluded_from_the_average_rather_than_counted_as_zero(): void
    {
        $barangay = $this->barangay('Barangay A');
        $graded = $this->requirement('Graded Report', ReportRequirement::FREQUENCY_MONTHLY);
        $ungraded = $this->requirement('Ungraded Report', ReportRequirement::FREQUENCY_MONTHLY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $graded->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        // Recorded (has a date) but not graded yet - status_color stays null.
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $ungraded->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => null]);

        $row = app(RankingCalculator::class)
            ->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09')
            ->firstWhere('barangay.id', $barangay->id);

        // Only the graded report counts - 100%, not 50%.
        $this->assertSame(100.0, $row['percentage']);
    }

    public function test_a_barangay_with_nothing_graded_gets_a_null_percentage(): void
    {
        $this->barangay('Barangay With No Data');
        $this->requirement('Some Report', ReportRequirement::FREQUENCY_MONTHLY);

        $row = app(RankingCalculator::class)
            ->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09')
            ->first();

        $this->assertNull($row['percentage']);
    }

    public function test_yearly_reports_count_regardless_of_the_selected_month_range(): void
    {
        $barangay = $this->barangay('Barangay A');
        $yearly = $this->requirement('Annual Report', ReportRequirement::FREQUENCY_YEARLY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $yearly->id, 'period' => '2026', 'submission_date' => now(), 'status_color' => 'green']);

        // A monthly range that has nothing to do with the yearly record's
        // period - the yearly report should still count.
        $row = app(RankingCalculator::class)
            ->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-03', '2026-03')
            ->firstWhere('barangay.id', $barangay->id);

        $this->assertSame(100.0, $row['percentage']);
    }

    public function test_ranking_breaks_a_percentage_tie_by_earliest_average_submission_date(): void
    {
        $early = $this->barangay('Barangay Early');
        $late = $this->barangay('Barangay Late');
        $requirement = $this->requirement('Report', ReportRequirement::FREQUENCY_MONTHLY);

        ReportRecord::create(['barangay_id' => $early->id, 'requirement_id' => $requirement->id, 'period' => '2026-09', 'submission_date' => '2026-09-02', 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $late->id, 'requirement_id' => $requirement->id, 'period' => '2026-09', 'submission_date' => '2026-09-20', 'status_color' => 'green']);

        $ranking = app(RankingCalculator::class)->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09');

        // Both are 100% - the earlier submission should rank first.
        $this->assertSame('Barangay Early', $ranking->first()['barangay']->barangay_name);
        $this->assertSame(1, $ranking->first()['rank']);
        $this->assertSame(2, $ranking->last()['rank']);
    }

    public function test_barangay_and_sk_rankings_are_independent(): void
    {
        $barangay = $this->barangay('Shared Barangay');
        $barangayReport = $this->requirement('Barangay Report', ReportRequirement::FREQUENCY_MONTHLY, ReportRequirement::COUNCIL_BARANGAY);
        $skReport = $this->requirement('SK Report', ReportRequirement::FREQUENCY_MONTHLY, ReportRequirement::COUNCIL_SK);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $barangayReport->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $skReport->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'red']);

        $calculator = app(RankingCalculator::class);

        $barangayRow = $calculator->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09')->first();
        $skRow = $calculator->rank(ReportRequirement::COUNCIL_SK, '2026-09', '2026-09')->first();

        $this->assertSame(100.0, $barangayRow['percentage']);
        $this->assertSame(0.0, $skRow['percentage']);
    }

    /**
     * Follow-up round: filtering by one specific report slot instead of
     * the council-wide average (Analytics page's new report-type filter).
     */
    public function test_filtering_by_one_report_scores_only_that_report_not_the_combined_average(): void
    {
        $barangay = $this->barangay('Barangay A');
        $kasambahay = $this->requirement('Kasambahay Report', ReportRequirement::FREQUENCY_MONTHLY);
        $accomplishment = $this->requirement('Accomplishment Report', ReportRequirement::FREQUENCY_MONTHLY);

        // Kasambahay is perfect, Accomplishment is a total miss - the
        // combined average would be 50%, but filtering to Kasambahay
        // alone should show 100%.
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $kasambahay->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $accomplishment->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'red']);

        $calculator = app(RankingCalculator::class);

        $combined = $calculator->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09')
            ->firstWhere('barangay.id', $barangay->id);
        $filtered = $calculator->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09', $kasambahay->id)
            ->firstWhere('barangay.id', $barangay->id);

        $this->assertSame(50.0, $combined['percentage']);
        $this->assertSame(100.0, $filtered['percentage']);
        $this->assertSame(1, $filtered['graded_count']);
    }

    public function test_filtering_by_a_requirement_from_the_other_council_yields_no_data(): void
    {
        $barangay = $this->barangay('Barangay A');
        $skReport = $this->requirement('SK-Only Report', ReportRequirement::FREQUENCY_MONTHLY, ReportRequirement::COUNCIL_SK);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $skReport->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);

        // Asking the Barangay ranking to filter by an SK requirement id
        // should never leak that SK data in - it should behave as if
        // nothing matched, not throw or silently use the wrong council.
        $row = app(RankingCalculator::class)
            ->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09', $skReport->id)
            ->firstWhere('barangay.id', $barangay->id);

        $this->assertNull($row['percentage']);
    }

    public function test_no_requirement_filter_still_returns_the_combined_average(): void
    {
        $barangay = $this->barangay('Barangay A');
        $a = $this->requirement('Report A', ReportRequirement::FREQUENCY_MONTHLY);
        $b = $this->requirement('Report B', ReportRequirement::FREQUENCY_MONTHLY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $a->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $b->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'yellow']);

        $row = app(RankingCalculator::class)
            ->rank(ReportRequirement::COUNCIL_BARANGAY, '2026-09', '2026-09', null)
            ->firstWhere('barangay.id', $barangay->id);

        $this->assertSame(75.0, $row['percentage']);
    }
}
