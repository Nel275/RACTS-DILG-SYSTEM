<?php

namespace Tests\Feature;

use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Confirms the role split still holds: DILG Monitoring Staff can view
 * monitoring pages but never reach the DILG-Administrator-only
 * management actions.
 *
 * Redesign-phase rewrite (Phase 6): Report Categories, Submissions,
 * Compliance, Tally, and Submission History were all removed by
 * Phases 2-3 and replaced by the Barangay/SK Reports grids, report
 * slot management, and the Analytics & Ranking page - this file now
 * tests those instead. A couple of assertions at the bottom confirm
 * the old routes are actually gone, not just renamed.
 */
class RoleAccessTest extends TestCase
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

    public function test_staff_cannot_view_user_management(): void
    {
        $this->actingAs($this->staff())->get('/users')->assertForbidden();
    }

    public function test_admin_can_view_user_management(): void
    {
        $this->actingAs($this->admin())->get('/users')->assertOk();
    }

    public function test_staff_cannot_create_a_barangay(): void
    {
        $this->actingAs($this->staff())->get('/barangays/create')->assertForbidden();
    }

    public function test_staff_can_view_the_barangay_list(): void
    {
        $this->actingAs($this->staff())->get('/barangays')->assertOk();
    }

    public function test_both_roles_can_view_the_barangay_reports_grid(): void
    {
        $this->actingAs($this->admin())->get('/barangay-reports')->assertOk();
        $this->actingAs($this->staff())->get('/barangay-reports')->assertOk();
    }

    public function test_both_roles_can_view_the_sk_reports_grid(): void
    {
        $this->actingAs($this->admin())->get('/sk-reports')->assertOk();
        $this->actingAs($this->staff())->get('/sk-reports')->assertOk();
    }

    public function test_both_roles_can_view_the_analytics_page(): void
    {
        $this->actingAs($this->admin())->get('/analytics')->assertOk();
        $this->actingAs($this->staff())->get('/analytics')->assertOk();
    }

    public function test_staff_cannot_add_a_report_slot(): void
    {
        $this->actingAs($this->staff())->post('/report-slots', [
            'report_name' => 'Staff-Added Slot',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
        ])->assertForbidden();

        $this->assertDatabaseMissing('report_requirements', ['report_name' => 'Staff-Added Slot']);
    }

    public function test_admin_can_add_a_report_slot(): void
    {
        $this->actingAs($this->admin())->post('/report-slots', [
            'report_name' => 'Admin-Added Slot',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
        ])->assertRedirect();

        $this->assertDatabaseHas('report_requirements', ['report_name' => 'Admin-Added Slot']);
    }

    public function test_the_old_report_categories_and_submissions_pages_are_gone(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)->get('/report-categories')->assertNotFound();
        $this->actingAs($staff)->get('/submissions')->assertNotFound();
    }

    public function test_the_old_compliance_tally_and_submission_history_pages_are_gone(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)->get('/compliance')->assertNotFound();
        $this->actingAs($staff)->get('/tally')->assertNotFound();
        $this->actingAs($staff)->get('/submission-history')->assertNotFound();
    }
}
