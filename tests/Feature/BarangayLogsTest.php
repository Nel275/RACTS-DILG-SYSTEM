<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Follow-up round: Barangay Logs (the report/item catalog) and Barangay
 * Log Details (the per-barangay list built from the "Purchases"
 * reference screenshot - Number -> Barangay, Supplier -> Report).
 */
class BarangayLogsTest extends TestCase
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

    private function logItem(string $description = 'Monthly Accomplishment Report', string $deadline = '2026-09-30'): BarangayLog
    {
        return BarangayLog::create([
            'item_description' => $description,
            'deadline_date' => $deadline,
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_add_a_barangay_log_item(): void
    {
        $response = $this->actingAs($this->admin())->post('/barangay-logs', [
            'item_description' => 'Quarterly Financial Report',
            'deadline_date' => '2026-12-31',
        ]);

        // Follow-up round: the standalone Barangay Logs catalog page is
        // gone - its management panel now lives inline on the Log
        // Details / Report Filing tab, so store() redirects there
        // instead of to a page that no longer exists.
        $response->assertRedirect(route('barangay-log-details.index'));
        $this->assertDatabaseHas('barangay_logs', ['item_description' => 'Quarterly Financial Report']);
    }

    public function test_staff_cannot_add_a_barangay_log_item(): void
    {
        $this->actingAs($this->staff())->post('/barangay-logs', [
            'item_description' => 'Should Be Blocked',
            'deadline_date' => '2026-12-31',
        ])->assertForbidden();

        $this->assertDatabaseMissing('barangay_logs', ['item_description' => 'Should Be Blocked']);
    }

    public function test_both_roles_can_view_the_barangay_log_details_list(): void
    {
        $this->actingAs($this->admin())->get('/barangay-log-details')->assertOk();
        $this->actingAs($this->staff())->get('/barangay-log-details')->assertOk();
    }

    public function test_admin_can_add_a_log_detail_entry_for_a_barangay(): void
    {
        $barangay = $this->barangay();
        $item = $this->logItem();

        $response = $this->actingAs($this->admin())->post('/barangay-log-details', [
            'barangay_id' => $barangay->id,
            'item_description' => $item->item_description,
            'deadline_date' => '2026-09-30',
        ]);

        $response->assertRedirect(route('barangay-log-details.index'));
        $this->assertDatabaseHas('barangay_log_details', [
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $item->id,
            'item_description' => 'Monthly Accomplishment Report',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
    }

    /**
     * Follow-up round: Add Log Entry's Report field used to require
     * picking from the existing catalog - you had to add a report
     * under "Manage Reports" first before you could log any barangay
     * against it. It's now a single free-text field: typing a name
     * that matches an existing catalog item (case/whitespace-
     * insensitive) reuses it...
     */
    public function test_adding_a_log_entry_with_an_existing_report_name_reuses_the_catalog_item(): void
    {
        $barangay = $this->barangay();
        $item = $this->logItem('Monthly Accomplishment Report', '2026-09-30');

        $this->actingAs($this->admin())->post('/barangay-log-details', [
            'barangay_id' => $barangay->id,
            'item_description' => '  monthly accomplishment report  ',
            'deadline_date' => '2026-10-15',
        ])->assertRedirect(route('barangay-log-details.index'));

        // Reused the existing catalog item - no second "Monthly
        // Accomplishment Report" row was created.
        $this->assertSame(1, BarangayLog::where('item_description', 'Monthly Accomplishment Report')->count());
        $this->assertDatabaseHas('barangay_log_details', [
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $item->id,
            'deadline_date' => '2026-10-15',
        ]);
    }

    /**
     * ...and typing a name that doesn't match anything in the catalog
     * yet adds it there automatically, using this same deadline, so a
     * brand-new report type and its first log entry can be created in
     * one step.
     */
    public function test_adding_a_log_entry_with_a_new_report_name_creates_the_catalog_item(): void
    {
        $barangay = $this->barangay();

        $this->actingAs($this->admin())->post('/barangay-log-details', [
            'barangay_id' => $barangay->id,
            'item_description' => 'Brand New Report Type',
            'deadline_date' => '2026-11-30',
        ])->assertRedirect(route('barangay-log-details.index'));

        $this->assertDatabaseHas('barangay_logs', [
            'item_description' => 'Brand New Report Type',
            'deadline_date' => '2026-11-30',
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('barangay_log_details', [
            'barangay_id' => $barangay->id,
            'item_description' => 'Brand New Report Type',
        ]);
    }

    public function test_submitting_before_the_deadline_is_marked_submitted_not_late(): void
    {
        $entry = BarangayLogDetail::create([
            'barangay_id' => $this->barangay()->id,
            'barangay_log_id' => $this->logItem()->id,
            'item_description' => 'Monthly Accomplishment Report',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-09-28 10:00:00',
        ])->assertRedirect(route('barangay-log-details.show', $entry));

        $this->assertSame(BarangayLogDetail::STATUS_SUBMITTED, $entry->fresh()->status);
    }

    public function test_submitting_after_the_deadline_is_marked_submitted_late(): void
    {
        $entry = BarangayLogDetail::create([
            'barangay_id' => $this->barangay()->id,
            'barangay_log_id' => $this->logItem()->id,
            'item_description' => 'Monthly Accomplishment Report',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $this->actingAs($this->staff())->patch("/barangay-log-details/{$entry->id}/submit", [
            'date_submitted' => '2026-10-02 09:00:00',
        ]);

        $this->assertSame(BarangayLogDetail::STATUS_LATE, $entry->fresh()->status);
    }

    public function test_a_pending_entry_past_its_deadline_is_automatically_flagged_missed_on_load(): void
    {
        $entry = BarangayLogDetail::create([
            'barangay_id' => $this->barangay()->id,
            'barangay_log_id' => $this->logItem('Old Report', '2020-01-01')->id,
            'item_description' => 'Old Report',
            'deadline_date' => '2020-01-01',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin())->get('/barangay-log-details')->assertOk();

        $this->assertSame(BarangayLogDetail::STATUS_MISSED, $entry->fresh()->status);
    }

    public function test_filtering_by_report_only_shows_entries_for_that_report(): void
    {
        $barangay = $this->barangay();
        $itemA = $this->logItem('Report A', '2026-09-30');
        $itemB = $this->logItem('Report B', '2026-09-30');

        BarangayLogDetail::create([
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $itemA->id,
            'item_description' => 'Report A',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
        BarangayLogDetail::create([
            'barangay_id' => $barangay->id,
            'barangay_log_id' => $itemB->id,
            'item_description' => 'Report B',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin())->get('/barangay-log-details?report='.$itemA->id);

        $response->assertOk();

        // Follow-up round: checking the *filtered entries* passed to the
        // view, rather than scraping the whole page for "Report B", since
        // that text now legitimately appears elsewhere on this same page
        // regardless of the filter - the Report filter dropdown itself
        // lists every catalog item, and so does the admin-only "Manage
        // Reports" panel added when Barangay Logs was merged into this
        // page. Asserting against the actual filtered data is what this
        // test is really about, and it stays correct no matter what else
        // gets added to the page around it.
        $response->assertViewHas('entries', function ($entries) {
            return $entries->count() === 1
                && $entries->first()->item_description === 'Report A';
        });
    }

    public function test_report_detail_page_only_shows_barangays_logged_with_that_report(): void
    {
        $malvar = $this->barangay('Malvar');
        $rosario = $this->barangay('Rosario');
        $otherBarangay = $this->barangay('Calao East');

        $kasambahay = $this->logItem('Kasambahay Report', '2026-12-31');
        $otherReport = $this->logItem('Other Report', '2026-12-31');

        BarangayLogDetail::create([
            'barangay_id' => $malvar->id,
            'barangay_log_id' => $kasambahay->id,
            'item_description' => 'Kasambahay Report',
            'deadline_date' => '2026-12-31',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
        BarangayLogDetail::create([
            'barangay_id' => $rosario->id,
            'barangay_log_id' => $kasambahay->id,
            'item_description' => 'Kasambahay Report',
            'deadline_date' => '2026-12-31',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);
        // A different report, same barangay catalog - must not leak in.
        BarangayLogDetail::create([
            'barangay_id' => $otherBarangay->id,
            'barangay_log_id' => $otherReport->id,
            'item_description' => 'Other Report',
            'deadline_date' => '2026-12-31',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin())->get("/barangay-logs/{$kasambahay->id}");

        $response->assertOk();
        $response->assertViewHas('entries', function ($entries) {
            return $entries->count() === 2
                && $entries->pluck('barangay.barangay_name')->sort()->values()->all() === ['Malvar', 'Rosario'];
        });
    }

    public function test_both_roles_can_view_the_report_detail_page(): void
    {
        $item = $this->logItem();

        $this->actingAs($this->admin())->get("/barangay-logs/{$item->id}")->assertOk();
        $this->actingAs($this->staff())->get("/barangay-logs/{$item->id}")->assertOk();
    }

    public function test_admin_can_delete_a_log_detail_entry(): void
    {
        $entry = BarangayLogDetail::create([
            'barangay_id' => $this->barangay()->id,
            'barangay_log_id' => $this->logItem()->id,
            'item_description' => 'Monthly Accomplishment Report',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin())->delete("/barangay-log-details/{$entry->id}")
            ->assertRedirect(route('barangay-log-details.index'));

        $this->assertDatabaseMissing('barangay_log_details', ['id' => $entry->id]);
    }

    public function test_staff_cannot_delete_a_log_detail_entry(): void
    {
        $entry = BarangayLogDetail::create([
            'barangay_id' => $this->barangay()->id,
            'barangay_log_id' => $this->logItem()->id,
            'item_description' => 'Monthly Accomplishment Report',
            'deadline_date' => '2026-09-30',
            'status' => BarangayLogDetail::STATUS_PENDING,
        ]);

        $this->actingAs($this->staff())->delete("/barangay-log-details/{$entry->id}")->assertForbidden();

        $this->assertDatabaseHas('barangay_log_details', ['id' => $entry->id]);
    }
}
