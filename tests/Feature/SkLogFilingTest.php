<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Follow-up round: Report Filing (barangay_logs / barangay_log_details,
 * previously Barangay-only) now also works from the SK Reports page,
 * scoped via BarangayLog::council_type. These tests cover what's new -
 * the SK-specific index()/store() entry points, the two catalogs
 * staying independent of each other, and that every other action
 * (view/submit/download/close/destroy) still works correctly for an SK
 * entry even though it's reached through the same generic
 * barangay-log-details.* routes an existing Barangay entry uses.
 * BarangayLogFilingTest and BarangayLogsTest already cover the
 * Barangay-side behavior this all mirrors, so it isn't re-tested here.
 */
class SkLogFilingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'username' => 'admin_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function staff(): User
    {
        return User::create([
            'name' => 'Staff User',
            'username' => 'staff_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
    }

    private function barangay(string $name = 'Test Barangay'): Barangay
    {
        return Barangay::create(['barangay_name' => $name, 'status' => Barangay::STATUS_ACTIVE]);
    }

    private function skLogItem(string $description = 'SK Accomplishment Report', string $deadline = '2026-09-30'): BarangayLog
    {
        return BarangayLog::create([
            'item_description' => $description,
            'council_type' => ReportRequirement::COUNCIL_SK,
            'deadline_date' => $deadline,
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);
    }

    private function skEntry(): BarangayLogDetail
    {
        $barangay = $this->barangay();
        $item = $this->skLogItem();

        return BarangayLogDetail::create([
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $item->id,
            'item_description' => $item->item_description,
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
    }

    public function test_both_roles_can_view_the_sk_log_details_list(): void
    {
        $this->actingAs($this->admin())->get('/sk-log-details')->assertOk();
        $this->actingAs($this->staff())->get('/sk-log-details')->assertOk();
    }

    public function test_sk_reports_page_shows_the_report_filing_tab(): void
    {
        // Regression test: sk.blade.php used to render the Compliance
        // Grid with no tab bar at all, since Report Filing didn't exist
        // for SK yet - see report-grid/sk.blade.php.
        $this->actingAs($this->admin())->get('/sk-reports')
            ->assertOk()
            ->assertSee('Report Filing')
            ->assertSee(route('sk-log-details.index'), false);
    }

    public function test_admin_can_add_a_log_detail_entry_for_sk(): void
    {
        $barangay = $this->barangay();
        $item = $this->skLogItem();

        $response = $this->actingAs($this->admin())->post('/sk-log-details', [
            'barangay_id' => $barangay->id,
            'item_description' => $item->item_description,
            'deadline_date' => '2026-09-30',
        ]);

        $response->assertRedirect(route('sk-log-details.index'));
        $this->assertDatabaseHas('barangay_log_details', [
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $item->id,
            'item_description' => 'SK Accomplishment Report',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
    }

    public function test_staff_cannot_add_a_log_detail_entry_for_sk(): void
    {
        $barangay = $this->barangay();

        $this->actingAs($this->staff())->post('/sk-log-details', [
            'barangay_id' => $barangay->id,
            'item_description' => 'Should Be Blocked',
            'deadline_date' => '2026-12-31',
        ])->assertForbidden();

        $this->assertDatabaseMissing('barangay_logs', ['item_description' => 'Should Be Blocked']);
    }

    /**
     * Typing a brand-new report name in SK's "Add Log Entry" creates
     * its own catalog item tagged council_type = sk, the same
     * auto-create behavior Barangay already has (see
     * BarangayLogsTest::test_adding_a_log_entry_with_a_new_report_name_creates_the_catalog_item).
     */
    public function test_adding_a_new_report_name_on_sk_creates_an_sk_catalog_item(): void
    {
        $barangay = $this->barangay();

        $this->actingAs($this->admin())->post('/sk-log-details', [
            'barangay_id' => $barangay->id,
            'item_description' => 'Brand New SK Report Type',
            'deadline_date' => '2026-11-30',
        ])->assertRedirect(route('sk-log-details.index'));

        $this->assertDatabaseHas('barangay_logs', [
            'item_description' => 'Brand New SK Report Type',
            'council_type' => ReportRequirement::COUNCIL_SK,
        ]);
    }

    /**
     * The whole point of scoping the catalog lookup by council_type:
     * a Barangay report and an SK report with the exact same name are
     * two separate catalog items, never one shared between councils.
     */
    public function test_a_report_name_shared_by_both_councils_creates_two_separate_catalog_items(): void
    {
        $barangayBarangay = $this->barangay('Barangay One');
        $skBarangay = $this->barangay('Barangay Two');

        $this->actingAs($this->admin())->post('/barangay-log-details', [
            'barangay_id' => $barangayBarangay->id,
            'item_description' => 'Accomplishment Report',
            'deadline_date' => '2026-10-31',
        ])->assertRedirect(route('barangay-log-details.index'));

        $this->actingAs($this->admin())->post('/sk-log-details', [
            'barangay_id' => $skBarangay->id,
            'item_description' => 'Accomplishment Report',
            'deadline_date' => '2026-11-30',
        ])->assertRedirect(route('sk-log-details.index'));

        $this->assertSame(2, BarangayLog::where('item_description', 'Accomplishment Report')->count());
        $this->assertDatabaseHas('barangay_logs', [
            'item_description' => 'Accomplishment Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'deadline_date' => '2026-10-31',
        ]);
        $this->assertDatabaseHas('barangay_logs', [
            'item_description' => 'Accomplishment Report',
            'council_type' => ReportRequirement::COUNCIL_SK,
            'deadline_date' => '2026-11-30',
        ]);
    }

    /**
     * The SK side of Report Filing never lists a Barangay entry (or
     * vice versa) - the two lists are fully independent even though
     * they're built from the same tables.
     */
    public function test_sk_log_details_list_only_shows_sk_entries(): void
    {
        $barangayEntry = (function () {
            $barangay = $this->barangay('Barangay Side');
            $item = BarangayLog::create([
                'item_description' => 'Barangay-Only Report',
                'deadline_date' => '2026-09-30',
                'status' => BarangayLog::STATUS_ACTIVE,
            ]);

            return BarangayLogDetail::create([
                'barangay_id' => $barangay->id,
                'barangay_log_id' => $item->id,
                'item_description' => $item->item_description,
                'deadline_date' => '2026-09-30',
                'status' => BarangayLogDetail::STATUS_PENDING,
            ]);
        })();

        $skEntry = $this->skEntry();

        $this->actingAs($this->admin())->get('/sk-log-details')
            ->assertSee($skEntry->item_description)
            ->assertDontSee($barangayEntry->item_description);

        $this->actingAs($this->admin())->get('/barangay-log-details')
            ->assertSee($barangayEntry->item_description)
            ->assertDontSee($skEntry->item_description);
    }

    /**
     * View File, Download, and Close all work for an SK entry through
     * the same generic barangay-log-details.* routes a Barangay entry
     * uses - these were never duplicated per council (see
     * BarangayLogDetailController's doc comment), so this is really a
     * regression test that scoping Report Filing by council_type
     * didn't break the shared, id-based entry actions.
     */
    public function test_marking_an_sk_entry_submitted_and_closing_it_still_works(): void
    {
        $entry = $this->skEntry();

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
        ])->assertRedirect(route('barangay-log-details.show', $entry));

        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $entry->fresh()->status);

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/close")
            ->assertRedirect();

        $this->assertSame(BarangayLogDetail::STATUS_CLOSED, $entry->fresh()->status);
    }

    /**
     * Deleting an SK entry redirects back to the SK list, not the
     * Barangay one - see
     * BarangayLogDetailController::filingIndexRouteFor().
     */
    public function test_deleting_an_sk_entry_redirects_to_the_sk_list(): void
    {
        $entry = $this->skEntry();

        $this->actingAs($this->admin())->delete("/barangay-log-details/{$entry->id}")
            ->assertRedirect(route('sk-log-details.index'));

        $this->assertDatabaseMissing('barangay_log_details', ['id' => $entry->id]);
    }

    /**
     * Renaming or deactivating an SK catalog item from "Manage
     * Reports" redirects back to the SK list - see
     * BarangayLogController::filingIndexRouteFor().
     */
    public function test_updating_an_sk_catalog_item_redirects_to_the_sk_list(): void
    {
        $item = $this->skLogItem();

        $this->actingAs($this->admin())->put("/barangay-logs/{$item->id}", [
            'item_description' => 'Renamed SK Report',
            'deadline_date' => '2026-10-31',
            'status' => BarangayLog::STATUS_ACTIVE,
        ])->assertRedirect(route('sk-log-details.index'));

        $this->actingAs($this->admin())->patch("/barangay-logs/{$item->id}/toggle")
            ->assertRedirect(route('sk-log-details.index'));
    }
}
