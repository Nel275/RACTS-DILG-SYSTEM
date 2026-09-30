<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Notification;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Models\User;
use App\Services\ReportGridService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Redesign-phase rewrite (Phase 6) of the old NotificationGenerationTest,
 * which tested NotificationGenerator (deleted in Phase 2 - it depended
 * on the old deadline column). This tests its replacement instead: the
 * `racts:check-late-submissions` scheduled command (client spec
 * Section 4). Uses Carbon::setTestNow() to control "today" so the
 * day-of-month gate (due the 1st-5th, checked from the 6th on) is
 * exercised deterministically regardless of which day this suite
 * actually runs on.
 */
class LateSubmissionCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function usersAndBarangay(): array
    {
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::create([
            'name' => 'Staff User',
            'username' => 'staff_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
        $barangay = Barangay::create([
            'barangay_name' => 'Test Barangay',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        return [$admin, $staff, $barangay];
    }

    public function test_the_command_does_nothing_before_the_sixth_of_the_month(): void
    {
        Carbon::setTestNow('2026-09-03');
        [, , $barangay] = $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        Artisan::call('racts:check-late-submissions');

        $this->assertSame(0, Notification::count());
    }

    public function test_the_command_notifies_every_user_about_a_missing_monthly_report(): void
    {
        Carbon::setTestNow('2026-09-10');
        [$admin, $staff, $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // No report_records row at all for August 2026 - never touched.
        Artisan::call('racts:check-late-submissions');

        $this->assertSame(2, Notification::count());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'auto_generated' => true,
            'report_record_id' => null,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $staff->id,
            'auto_generated' => true,
        ]);
        $this->assertStringContainsString(
            $barangay->barangay_name,
            Notification::first()->message
        );
        $this->assertStringContainsString('August 2026', Notification::first()->message);
    }

    public function test_the_command_skips_a_barangay_already_graded_green_or_yellow(): void
    {
        Carbon::setTestNow('2026-09-10');
        [, , $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        ReportRecord::create([
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'period' => '2026-08',
            'submission_date' => '2026-08-20',
            'status_color' => 'yellow',
        ]);

        Artisan::call('racts:check-late-submissions');

        $this->assertSame(0, Notification::count());
    }

    public function test_running_the_command_twice_does_not_duplicate_notifications(): void
    {
        Carbon::setTestNow('2026-09-10');
        $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        Artisan::call('racts:check-late-submissions');
        $firstRunCount = Notification::count();

        Artisan::call('racts:check-late-submissions');

        $this->assertSame($firstRunCount, Notification::count());
    }

    public function test_a_user_added_after_the_first_run_still_gets_caught_up(): void
    {
        Carbon::setTestNow('2026-09-10');
        [, , $barangay] = $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // First run: only the two original users exist.
        Artisan::call('racts:check-late-submissions');
        $this->assertSame(2, Notification::count());

        // A brand-new user joins afterward - nothing has changed about
        // the missing report itself, so a naive "does this message
        // already exist anywhere" dedup check would wrongly skip them
        // forever. Running the command again must still notify them.
        $newUser = User::create([
            'name' => 'Late Joiner',
            'username' => 'late_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        Artisan::call('racts:check-late-submissions');

        $this->assertSame(3, Notification::count());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $newUser->id,
            'auto_generated' => true,
        ]);
    }

    public function test_clearing_a_notification_does_not_bring_it_back_on_the_next_check(): void
    {
        Carbon::setTestNow('2026-09-10');
        [, $staff, $barangay] = $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        Artisan::call('racts:check-late-submissions');
        $this->assertSame(1, $staff->notifications()->count());

        // The staff member clears it - the underlying report is still
        // genuinely unrecorded (nothing about the barangay's actual
        // compliance changed), but the alert itself should stay gone.
        $this->actingAs($staff)->post('/notifications/clear');
        $this->assertSame(0, $staff->notifications()->count());

        // Running the checker again for the exact same still-missing
        // report must NOT resurrect it for this user.
        Artisan::call('racts:check-late-submissions');

        $this->assertSame(0, $staff->notifications()->count());
    }

    public function test_a_barangay_and_sk_council_report_with_the_same_name_are_not_treated_as_the_same_notification(): void
    {
        Carbon::setTestNow('2026-09-10');
        [, , $barangay] = $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // First run: only the Barangay-council slot exists, so both
        // users get notified about it.
        Artisan::call('racts:check-late-submissions');
        $this->assertSame(2, Notification::count());

        // An SK-council slot with the *same* report_name, for the same
        // barangay, is added afterward. Before the council label was
        // part of the message text, this produced a byte-identical
        // message to the one above, so the dedup check wrongly treated
        // the SK report as "already notified" and silently skipped it -
        // even though it's a genuinely different, still-missing report.
        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_SK,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        Artisan::call('racts:check-late-submissions');

        // 2 more notifications (one per user) for the distinct
        // SK-council report - 4 total, not still 2.
        $this->assertSame(4, Notification::count());
    }

    public function test_the_command_ignores_yearly_reports(): void
    {
        Carbon::setTestNow('2026-09-10');
        $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Annual Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_YEARLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        Artisan::call('racts:check-late-submissions');

        $this->assertSame(0, Notification::count());
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'username' => 'owner_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
        $otherUser = User::create([
            'name' => 'Someone Else',
            'username' => 'other_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        $notification = Notification::create([
            'user_id' => $owner->id,
            'message' => 'Test alert',
            'notification_date' => now(),
            'is_read' => false,
            'auto_generated' => true,
        ]);

        $response = $this->actingAs($otherUser)->patch("/notifications/{$notification->id}/read");

        $response->assertForbidden();
        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'is_read' => false]);
    }

    public function test_a_user_can_clear_their_own_notifications_without_affecting_anyone_elses(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'username' => 'owner_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);
        $otherUser = User::create([
            'name' => 'Someone Else',
            'username' => 'other_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
        ]);

        Notification::create([
            'user_id' => $owner->id,
            'message' => 'Owner alert',
            'notification_date' => now(),
            'is_read' => false,
            'auto_generated' => true,
        ]);
        $othersNotification = Notification::create([
            'user_id' => $otherUser->id,
            'message' => 'Other user alert',
            'notification_date' => now(),
            'is_read' => false,
            'auto_generated' => true,
        ]);

        $this->actingAs($owner)->post('/notifications/clear')->assertRedirect();

        $this->assertSame(0, Notification::where('user_id', $owner->id)->count());
        $this->assertDatabaseHas('notifications', ['id' => $othersNotification->id]);
    }

    /**
     * Follow-up Round 5: the real-time half of the automatic
     * late-submission feature (ReportGridService::notifyIfLate(),
     * called from saveRow()). These go through the actual grid-save
     * path - the same one the Barangay/SK Reports pages use - rather
     * than the scheduled command, since that's the whole point of the
     * real-time layer: no waiting for racts:check-late-submissions to
     * run at all.
     */
    private function saveOneCell(ReportRequirement $requirement, int $barangayId, string $periodMonth, string $date, int $editorId): void
    {
        app(ReportGridService::class)->saveRow(
            $barangayId,
            collect([$requirement]),
            $periodMonth,
            [$requirement->id => ['date' => $date, 'color' => '']],
            $editorId
        );
    }

    public function test_a_report_submitted_within_the_window_is_on_time_and_not_flagged(): void
    {
        [$admin, , $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // August report, submitted September 3 - inside the 1st-5th window.
        $this->saveOneCell($requirement, $barangay->id, '2026-08', '2026-09-03', $admin->id);

        $this->assertSame(0, Notification::count());
    }

    public function test_a_report_submitted_exactly_on_the_deadline_day_is_on_time(): void
    {
        [$admin, , $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // August report, submitted right on the deadline (September 5).
        $this->saveOneCell($requirement, $barangay->id, '2026-08', '2026-09-05', $admin->id);

        $this->assertSame(0, Notification::count());
    }

    public function test_a_report_submitted_the_day_after_the_deadline_is_flagged_late_immediately(): void
    {
        [$admin, $staff, $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // August report, submitted September 6 - one day past the deadline.
        // No scheduled command is run here at all - this is the real-time
        // check reacting the moment the cell is saved.
        $this->saveOneCell($requirement, $barangay->id, '2026-08', '2026-09-06', $admin->id);

        $this->assertSame(2, Notification::count());
        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'type' => Notification::TYPE_LATE_SUBMISSION,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $staff->id,
            'type' => Notification::TYPE_LATE_SUBMISSION,
        ]);
        $this->assertStringContainsString($barangay->barangay_name, Notification::first()->message);
    }

    public function test_a_december_report_submitted_late_in_january_is_flagged_correctly_across_the_year_boundary(): void
    {
        [$admin, , $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // December report is due January 1-5 of the following year;
        // submitted January 7 - two days late, with the deadline
        // calculation crossing a year boundary.
        $this->saveOneCell($requirement, $barangay->id, '2026-12', '2027-01-07', $admin->id);

        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame(Notification::TYPE_LATE_SUBMISSION, $admin->notifications()->first()->type);
    }

    public function test_editing_the_same_late_cell_again_does_not_duplicate_the_real_time_notification(): void
    {
        [$admin, , $barangay] = $this->usersAndBarangay();

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        $this->saveOneCell($requirement, $barangay->id, '2026-08', '2026-09-06', $admin->id);
        $this->assertSame(2, Notification::count());

        // Re-saving the same still-late cell (e.g. the grader adds a
        // color afterward) must not create a second alert.
        $this->saveOneCell($requirement, $barangay->id, '2026-08', '2026-09-06', $admin->id);
        $this->assertSame(2, Notification::count());
    }

    public function test_the_submission_deadline_day_is_read_from_config_not_hardcoded(): void
    {
        [$admin, , $barangay] = $this->usersAndBarangay();

        config(['racts.submission_deadline_day' => 10]);

        $requirement = ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        // September 7 would be late under the default deadline (the
        // 5th), but on time once the deadline is configured to the 10th.
        $this->saveOneCell($requirement, $barangay->id, '2026-08', '2026-09-07', $admin->id);

        $this->assertSame(0, Notification::count());
    }

    /**
     * Follow-up Round 5: the scheduled command now creates three of the
     * four event types across the window, not just the "late" one -
     * confirmed here by moving "today" through the window for a report
     * that's never actually submitted.
     */
    public function test_the_scheduled_checker_creates_period_open_then_deadline_today_then_late_across_the_window(): void
    {
        [, , $barangay] = $this->usersAndBarangay();

        ReportRequirement::create([
            'report_name' => 'Monthly Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        Carbon::setTestNow('2026-09-01');
        Artisan::call('racts:check-late-submissions');
        $this->assertSame(2, Notification::where('type', Notification::TYPE_PERIOD_OPEN)->count());

        Carbon::setTestNow('2026-09-05');
        Artisan::call('racts:check-late-submissions');
        $this->assertSame(2, Notification::where('type', Notification::TYPE_DEADLINE_TODAY)->count());

        Carbon::setTestNow('2026-09-06');
        Artisan::call('racts:check-late-submissions');
        $this->assertSame(2, Notification::where('type', Notification::TYPE_REPORT_LATE)->count());

        // Running the exact same day again creates nothing further.
        Artisan::call('racts:check-late-submissions');
        $this->assertSame(2, Notification::where('type', Notification::TYPE_REPORT_LATE)->count());
        $this->assertSame(6, Notification::count());
    }
}
