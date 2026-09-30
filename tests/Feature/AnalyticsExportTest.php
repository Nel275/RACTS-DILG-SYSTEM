<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Follow-up round: the Analytics page's report-type filter, and making
 * sure PDF/Excel export automatically inherits whatever filter is
 * active rather than always exporting the combined council view.
 */
class AnalyticsExportTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::create([
            'name' => 'Staff User',
            'username' => 'staff_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
    }

    private function barangay(string $name): Barangay
    {
        return Barangay::create(['barangay_name' => $name, 'status' => Barangay::STATUS_ACTIVE]);
    }

    private function requirement(string $name, string $council): ReportRequirement
    {
        return ReportRequirement::create([
            'report_name' => $name,
            'council_type' => $council,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);
    }

    public function test_the_analytics_page_shows_a_report_filter_dropdown_for_each_council(): void
    {
        $this->requirement('Kasambahay Report', ReportRequirement::COUNCIL_BARANGAY);
        $this->requirement('Accomplishment Report', ReportRequirement::COUNCIL_SK);

        $response = $this->actingAs($this->staff())->get('/analytics');

        $response->assertOk();
        $response->assertSee('Kasambahay Report');
        $response->assertSee('Accomplishment Report');
        $response->assertSee('All Reports');
    }

    public function test_selecting_a_report_filter_recalculates_the_ranking_table(): void
    {
        $barangay = $this->barangay('Barangay A');
        $kasambahay = $this->requirement('Kasambahay Report', ReportRequirement::COUNCIL_BARANGAY);
        $accomplishment = $this->requirement('Other Report', ReportRequirement::COUNCIL_BARANGAY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $kasambahay->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $accomplishment->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'red']);

        $response = $this->actingAs($this->staff())->get('/analytics?start=2026-09&end=2026-09&barangay_report='.$kasambahay->id);

        $response->assertOk();
        // Filtered to just Kasambahay - 100%, not the 50% combined average.
        $response->assertSee('100%');
    }

    public function test_pdf_export_inherits_the_active_report_filter_and_shows_it_in_the_header(): void
    {
        $barangay = $this->barangay('Barangay A');
        $kasambahay = $this->requirement('Kasambahay Report', ReportRequirement::COUNCIL_BARANGAY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $kasambahay->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);

        $response = $this->actingAs($this->staff())->get(
            '/analytics/export/pdf?council=barangay&start=2026-09&end=2026-09&requirement='.$kasambahay->id
        );

        $response->assertOk();
        $response->assertSee('Kasambahay Report');
        $response->assertSee('Report:');
    }

    public function test_pdf_export_with_no_filter_shows_all_reports_in_the_header(): void
    {
        $this->barangay('Barangay A');

        $response = $this->actingAs($this->staff())->get(
            '/analytics/export/pdf?council=barangay&start=2026-09&end=2026-09'
        );

        $response->assertOk();
        $response->assertSee('All Reports');
    }

    public function test_csv_export_inherits_the_active_report_filter(): void
    {
        $barangay = $this->barangay('Barangay A');
        $kasambahay = $this->requirement('Kasambahay Report', ReportRequirement::COUNCIL_BARANGAY);
        $other = $this->requirement('Other Report', ReportRequirement::COUNCIL_BARANGAY);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $kasambahay->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);
        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $other->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'red']);

        $response = $this->actingAs($this->staff())->get(
            '/analytics/export/csv?council=barangay&start=2026-09&end=2026-09&requirement='.$kasambahay->id
        );

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('Kasambahay Report', $content);
        // 100, not the 50% combined average.
        $this->assertStringContainsString(',100,', $content);
    }

    public function test_a_requirement_id_from_the_wrong_council_is_ignored_rather_than_trusted(): void
    {
        $barangay = $this->barangay('Barangay A');
        $skOnly = $this->requirement('SK-Only Report', ReportRequirement::COUNCIL_SK);

        ReportRecord::create(['barangay_id' => $barangay->id, 'requirement_id' => $skOnly->id, 'period' => '2026-09', 'submission_date' => now(), 'status_color' => 'green']);

        // Asking for the Barangay export while passing an SK requirement id.
        $response = $this->actingAs($this->staff())->get(
            '/analytics/export/pdf?council=barangay&start=2026-09&end=2026-09&requirement='.$skOnly->id
        );

        $response->assertOk();
        // Falls back to "All Reports" rather than showing SK data under
        // the Barangay export.
        $response->assertSee('All Reports');
    }
}
