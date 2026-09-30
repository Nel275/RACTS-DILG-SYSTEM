<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Redesign-phase rewrite (Phase 6): `barangay_code` was removed from
 * `barangays` in Phase 1, so every test here that used to assert
 * against it now just doesn't set or check that field. The "has a
 * recorded submission" test was rebuilt against the new
 * report_requirements/report_records schema (council_type/frequency,
 * period/status_color) instead of the old deadline/reporting_period/
 * status columns.
 */
class BarangayManagementTest extends TestCase
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

    public function test_admin_can_create_a_barangay(): void
    {
        $response = $this->actingAs($this->admin())->post('/barangays', [
            'barangay_name' => 'Barangay Test',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $response->assertRedirect('/barangays');
        $this->assertDatabaseHas('barangays', ['barangay_name' => 'Barangay Test']);
    }

    public function test_a_barangay_with_a_linked_user_account_cannot_be_deleted(): void
    {
        $barangay = Barangay::create([
            'barangay_name' => 'Barangay With User',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        User::create([
            'name' => 'Linked Staff',
            'username' => 'linked_'.uniqid(),
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
            'barangay_id' => $barangay->id,
        ]);

        $this->actingAs($this->admin())->delete("/barangays/{$barangay->id}");

        $this->assertDatabaseHas('barangays', ['id' => $barangay->id]);
    }

    public function test_a_barangay_with_a_recorded_report_entry_cannot_be_deleted(): void
    {
        $barangay = Barangay::create([
            'barangay_name' => 'Barangay With Report Entry',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $requirement = ReportRequirement::create([
            'report_name' => 'Test Report',
            'council_type' => ReportRequirement::COUNCIL_BARANGAY,
            'frequency' => ReportRequirement::FREQUENCY_MONTHLY,
            'display_order' => 0,
            'is_active' => true,
        ]);

        ReportRecord::create([
            'barangay_id' => $barangay->id,
            'requirement_id' => $requirement->id,
            'period' => '2026-09',
            'submission_date' => now(),
            'status_color' => ReportRecord::COLOR_GREEN,
        ]);

        $this->actingAs($this->admin())->delete("/barangays/{$barangay->id}");

        $this->assertDatabaseHas('barangays', ['id' => $barangay->id]);
    }

    public function test_a_barangay_with_no_dependencies_can_be_deleted(): void
    {
        $barangay = Barangay::create([
            'barangay_name' => 'Barangay No Deps',
            'status' => Barangay::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->admin())->delete("/barangays/{$barangay->id}");

        $this->assertDatabaseMissing('barangays', ['id' => $barangay->id]);
    }
}
