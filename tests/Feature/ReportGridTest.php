<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Redesign-phase rewrite (Phase 6) of the old ReportRecordStatusTest,
 * which tested an auto-computed on_time/late status via the now-removed
 * /submissions routes. Under the new design, status_color is always set
 * by hand by staff via the Barangay/SK Reports grids - nothing in RACTS
 * computes it automatically any more - so these tests exercise that
 * grid save endpoint (ReportGridService::saveRow) directly instead.
 */
class ReportGridTest extends TestCase
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

    private function barangayAndMonthlyRequirement(string $council = ReportRequirement::COUNCIL_BARANGAY): array
    {
        $barangay = Barangay::create([
            'barangay_name' => 'Test Barangay',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $requirement = ReportRequirement::create([
            'report_name' => 'Test Report',
            'council_type' => $council,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        return [$barangay, $requirement];
    }

    public function test_staff_can_record_a_grid_entry(): void
    {
        [$barangay, $requirement] = $this->barangayAndMonthlyRequirement();
        $staff = $this->staff();

        $this->actingAs($staff)->post('/barangay-reports/save', [
            'barangay_id' => $barangay->id,
            'period_month' => '2026-09',
            'cells' => [
                $requirement->id => ['date' => '2026-09-05', 'color' => 'green'],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('report_records', [
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'period' => '2026-09',
            'status_color' => 'green',
            'edited_by' => $staff->id,
        ]);

        $record = ReportRecord::first();
        $this->assertSame('2026-09-05', $record->submission_date->format('Y-m-d'));
        $this->assertNotNull($record->edited_at);
    }

    public function test_a_date_with_no_color_is_recorded_as_ungraded_not_skipped(): void
    {
        [$barangay, $requirement] = $this->barangayAndMonthlyRequirement();

        $this->actingAs($this->staff())->post('/barangay-reports/save', [
            'barangay_id' => $barangay->id,
            'period_month' => '2026-09',
            'cells' => [
                $requirement->id => ['date' => '2026-09-05', 'color' => ''],
            ],
        ]);

        $this->assertDatabaseHas('report_records', [
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'status_color' => null,
        ]);
    }

    public function test_clearing_a_cells_date_deletes_the_record(): void
    {
        [$barangay, $requirement] = $this->barangayAndMonthlyRequirement();

        $record = ReportRecord::create([
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'period' => '2026-09',
            'submission_date' => '2026-09-05',
            'status_color' => 'green',
        ]);

        $this->actingAs($this->staff())->post('/barangay-reports/save', [
            'barangay_id' => $barangay->id,
            'period_month' => '2026-09',
            'cells' => [
                $requirement->id => ['date' => '', 'color' => ''],
            ],
        ]);

        $this->assertDatabaseMissing('report_records', ['id' => $record->id]);
    }

    public function test_editing_an_existing_entry_updates_edited_by_and_edited_at(): void
    {
        [$barangay, $requirement] = $this->barangayAndMonthlyRequirement();

        $record = ReportRecord::create([
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'period' => '2026-09',
            'submission_date' => '2026-09-05',
            'status_color' => 'yellow',
        ]);

        $staff = $this->staff();

        $this->actingAs($staff)->post('/barangay-reports/save', [
            'barangay_id' => $barangay->id,
            'period_month' => '2026-09',
            'cells' => [
                $requirement->id => ['date' => '2026-09-06', 'color' => 'green'],
            ],
        ]);

        $record->refresh();

        $this->assertSame('green', $record->status_color);
        $this->assertSame('2026-09-06', $record->submission_date->format('Y-m-d'));
        $this->assertSame($staff->id, $record->edited_by);
        $this->assertNotNull($record->edited_at);
    }

    public function test_a_yearly_slots_cell_is_keyed_by_year_not_month(): void
    {
        $barangay = Barangay::create(['barangay_name' => 'Test Barangay', 'status' => Barangay::STATUS_ACTIVE]);
        $requirement = ReportRequirement::create([
            'report_name' => 'Annual Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_YEARLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->staff())->post('/barangay-reports/save', [
            'barangay_id' => $barangay->id,
            'period_month' => '2026-09',
            'cells' => [
                $requirement->id => ['date' => '2026-09-05', 'color' => 'green'],
            ],
        ]);

        $this->assertDatabaseHas('report_records', [
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'period' => '2026',
        ]);
    }

    public function test_the_sk_reports_grid_saves_through_its_own_route(): void
    {
        [$barangay, $requirement] = $this->barangayAndMonthlyRequirement(ReportRequirement::COUNCIL_SK);

        $this->actingAs($this->staff())->post('/sk-reports/save', [
            'barangay_id' => $barangay->id,
            'period_month' => '2026-09',
            'cells' => [
                $requirement->id => ['date' => '2026-09-05', 'color' => 'green'],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('report_records', [
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'status_color' => 'green',
        ]);
    }
}
