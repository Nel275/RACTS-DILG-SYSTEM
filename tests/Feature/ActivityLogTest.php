<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Follow-up round: expanding the activity log beyond report-submission
 * entries (already covered by ReportGridTest) to every admin action -
 * user management, barangay management, and report slot management -
 * plus the Dashboard preview/"View All" split and the new full Activity
 * Logs page.
 */
class ActivityLogTest extends TestCase
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

    /**
     * `log_date` is a plain `dateTime` column (whole-second precision -
     * see the audit_logs migration), so two rows created back-to-back
     * in the same test can land in the very same second and tie on
     * `->latest('log_date')`. MySQL doesn't promise that a tie breaks
     * in insertion order, so a couple of these tests were flaky -
     * `->latest('log_date')->first()` could return the OLDER of two
     * tied rows. `id` is the actual guarantee (auto-increment, always
     * matches insertion order), so every "most recent entry" lookup in
     * this file goes through this helper instead of querying directly.
     */
    private function mostRecentLog(): AuditLog
    {
        return AuditLog::orderByDesc('id')->firstOrFail();
    }

    public function test_creating_a_user_account_is_recorded_in_the_activity_log(): void
    {
        $this->actingAs($this->admin())->post('/users', [
            'name' => 'New Staff',
            'username' => 'new_staff_'.uniqid(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        $this->assertStringContainsString('New Staff', $this->mostRecentLog()->action);
    }

    public function test_updating_a_user_account_is_recorded_in_the_activity_log(): void
    {
        $admin = $this->admin();
        $user = User::create([
            'name' => 'Old Name',
            'username' => 'target_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->put("/users/{$user->id}", [
            'name' => 'New Name',
            'username' => $user->username,
            'role' => User::ROLE_STAFF,
        ]);

        $this->assertStringContainsString('New Name', $this->mostRecentLog()->action);
    }

    public function test_deleting_a_user_account_is_recorded_in_the_activity_log(): void
    {
        $admin = $this->admin();
        $user = User::create([
            'name' => 'To Delete',
            'username' => 'delete_me_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->delete("/users/{$user->id}");

        $this->assertStringContainsString('To Delete', $this->mostRecentLog()->action);
    }

    public function test_barangay_create_update_and_delete_are_all_recorded(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/barangays', [
            'barangay_name' => 'Test Barangay',
            'status' => Barangay::STATUS_ACTIVE,
        ]);
        $barangay = Barangay::where('barangay_name', 'Test Barangay')->firstOrFail();
        $this->assertStringContainsString('Test Barangay', $this->mostRecentLog()->action);

        $this->actingAs($admin)->put("/barangays/{$barangay->id}", [
            'barangay_name' => 'Renamed Barangay',
            'status' => Barangay::STATUS_ACTIVE,
        ]);
        $this->assertStringContainsString('Renamed Barangay', $this->mostRecentLog()->action);

        $this->actingAs($admin)->delete("/barangays/{$barangay->id}");
        $this->assertStringContainsString('Renamed Barangay', $this->mostRecentLog()->action);
    }

    public function test_adding_a_report_slot_is_recorded_in_the_activity_log(): void
    {
        $this->actingAs($this->admin())->post('/report-slots', [
            'report_name' => 'New Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
        ]);

        $this->assertStringContainsString('New Monthly Report', $this->mostRecentLog()->action);
    }

    public function test_toggling_a_report_slot_is_recorded_in_the_activity_log(): void
    {
        $requirement = ReportRequirement::create([
            'report_name' => 'Toggle Me',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin())->patch("/report-slots/{$requirement->id}/toggle");

        $this->assertStringContainsString('Deactivated', $this->mostRecentLog()->action);
        $this->assertStringContainsString('Toggle Me', $this->mostRecentLog()->action);
    }

    public function test_the_dashboard_shows_at_most_five_recent_entries(): void
    {
        $admin = $this->admin();

        // Explicit, strictly increasing timestamps rather than
        // AuditLog::record()'s now() for every row - MySQL's default
        // datetime precision is whole seconds, so seven rows created
        // back-to-back in the same test could otherwise tie and make
        // "the 5 most recent" ambiguous.
        for ($i = 1; $i <= 7; $i++) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => "Test action {$i}",
                'log_date' => now()->addMinutes($i),
            ]);
        }

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('View All');
        // The three oldest entries should not appear on the dashboard
        // preview - only the 5 most recent do.
        $response->assertDontSee('Test action 1');
        $response->assertDontSee('Test action 2');
        $response->assertSee('Test action 7');
    }

    public function test_the_full_activity_log_page_shows_every_entry(): void
    {
        $admin = $this->admin();

        for ($i = 1; $i <= 7; $i++) {
            AuditLog::record($admin, "Test action {$i}");
        }

        $response = $this->actingAs($admin)->get('/activity-logs');

        $response->assertOk();
        $response->assertSee('Test action 1');
        $response->assertSee('Test action 7');
    }

    public function test_staff_can_view_the_activity_log_page_but_not_clear_it(): void
    {
        $staff = User::create([
            'name' => 'Staff User',
            'username' => 'staff_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        $response = $this->actingAs($staff)->get('/activity-logs');
        $response->assertOk();

        $clearResponse = $this->actingAs($staff)->post('/dashboard/clear-activity');
        $clearResponse->assertForbidden();
    }
}
